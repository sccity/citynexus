<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
use Exception;

class KeycloakAdminService
{
    protected string $baseUrl;
    protected string $realm;
    protected string $clientId;
    protected string $clientSecret;
    protected string $grantType;
    protected ?string $username;
    protected ?string $password;
    protected string $tokenCacheKey = 'keycloak_admin_token';
    protected int $tokenLeeway = 60; // Seconds before expiry to refresh token

    public function __construct()
    {
        $config = config('services.keycloak_admin');

        // Basic validation for essential parameters
        if (empty($config['base_url']) || empty($config['realm']) || empty($config['client_id'])) {
            Log::error('Keycloak Admin configuration is missing essential parameters (base_url, realm, client_id).', ['config_keys' => array_keys($config ?? [])]);
            throw new Exception('Keycloak Admin configuration is missing essential parameters in config/services.php. Please check base_url, realm, and client_id.');
        }

        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->realm = $config['realm'];
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'] ?? '';
        $this->grantType = $config['grant_type'] ?? 'client_credentials';
        $this->username = $config['username'] ?? null;
        $this->password = $config['password'] ?? null;

        // Validate credentials based on grant type
        if ($this->grantType === 'password' && (empty($this->username) || empty($this->password))) {
            Log::error('Keycloak Admin username or password missing for password grant type.');
            throw new Exception('Keycloak Admin username or password must be provided for password grant type in config/services.php.');
        }
        if ($this->grantType === 'client_credentials' && empty($this->clientSecret)) {
            // Allow empty secret but log a warning as it's unusual
            Log::warning('Keycloak Admin client secret is empty for client_credentials grant type. Ensure this is intended.');
        }
    }

    /**
     * Get an authenticated Http client instance.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     * @throws Exception If unable to obtain an access token.
     */
    protected function getHttpClient()
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::critical('Unable to obtain Keycloak admin access token for HTTP client.');
            throw new Exception('Unable to obtain Keycloak admin access token.');
        }
        return Http::baseUrl($this->baseUrl)
                   ->withToken($token)
                   ->acceptJson()
                   ->asJson();
    }

    /**
     * Get a fresh access token from Keycloak.
     *
     * @return string|null The access token or null on failure.
     */
    protected function fetchNewAccessToken(): ?string
    {
        $tokenUrl = "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token";
        $params = [
            'client_id' => $this->clientId,
            'grant_type' => $this->grantType,
        ];

        if ($this->grantType === 'client_credentials') {
            $params['client_secret'] = $this->clientSecret;
        } elseif ($this->grantType === 'password') {
            $params['username'] = $this->username;
            $params['password'] = $this->password;
        }

        try {
            // Use 'application/x-www-form-urlencoded' content type for token endpoint
            $response = Http::asForm()->post($tokenUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 300; // Default to 5 minutes if not provided

                if (!$accessToken) {
                    Log::error('Keycloak admin token response missing access_token.', ['response_keys' => array_keys($data)]);
                    return null;
                }

                // Cache the token with a buffer before actual expiry
                $cacheDuration = max(1, $expiresIn - $this->tokenLeeway); // Ensure cache duration is at least 1 second
                Cache::put($this->tokenCacheKey, $accessToken, now()->addSeconds($cacheDuration));
                Log::info('Successfully fetched and cached new Keycloak admin token.', ['expires_in' => $expiresIn]);
                return $accessToken;
            } else {
                Log::error('Failed to fetch Keycloak admin token', [
                    'status' => $response->status(),
                    'body' => $response->body(), // Be cautious logging body if it might contain sensitive info
                    'url' => $tokenUrl
                ]);
                return null;
            }
        } catch (Exception $e) {
            Log::error('Exception fetching Keycloak admin token', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Log trace for debugging
            ]);
            return null;
        }
    }

    /**
     * Get access token from cache or fetch a new one.
     *
     * @return string|null The access token or null if fetching fails.
     */
    protected function getAccessToken(): ?string
    {
        return Cache::get($this->tokenCacheKey) ?? $this->fetchNewAccessToken();
    }

    /**
     * Invalidate the cached token.
     */
    protected function invalidateToken(): void
    {
        Cache::forget($this->tokenCacheKey);
        Log::info('Invalidated cached Keycloak admin token.');
    }

    /**
     * Make a request to the Keycloak Admin API and handle token expiry.
     *
     * @param string $method HTTP method (get, post, put, delete, etc.)
     * @param string $url Relative API endpoint URL
     * @param array $data Request data/body
     * @return \Illuminate\Http\Client\Response
     * @throws RequestException If the request fails after potential retry.
     * @throws Exception If token refresh fails or other exceptions occur.
     */
    protected function makeRequest(string $method, string $url, array $data = []): Response
    {
        try {
            $response = $this->getHttpClient()->{$method}($url, $data);

            // If unauthorized (401), token might have expired *just* before the request
            // or was revoked. Invalidate cache and retry ONCE.
            if ($response->status() === 401) {
                Log::warning('Keycloak admin request received 401, invalidating token and retrying.', [
                    'method' => $method,
                    'url' => $this->baseUrl . $url
                 ]);
                $this->invalidateToken();

                // Attempt the request again with a potentially fresh token
                $response = $this->getHttpClient()->{$method}($url, $data);

                // If it still fails with 401 after retry, something else is wrong (permissions?)
                if ($response->status() === 401) {
                    Log::error('Keycloak admin request failed with 401 even after token refresh.', [
                        'method' => $method,
                        'url' => $this->baseUrl . $url
                    ]);
                    // Throw specific exception for persistent auth failure
                     throw new RequestException($response);
                }
            }

            // Check for other client or server errors after potential retry
            if ($response->failed()) {
                Log::error('Keycloak admin API request failed', [
                    'method' => $method,
                    'url' => $this->baseUrl . $url,
                    'status' => $response->status(),
                    'response_body' => $response->body() // Log response body for debugging
                ]);
                // Throw an exception to be handled by the caller (e.g., Controller)
                throw new RequestException($response);
            }

            return $response;

        } catch (RequestException $e) {
            // Log RequestExceptions specifically (includes response details)
            Log::error("Keycloak admin API request failed ({$method} {$url})", [
                'status' => $e->response->status(),
                'response' => $e->response->body(),
                'exception_message' => $e->getMessage()
            ]);
            throw $e; // Re-throw to be handled by caller
        } catch (Exception $e) {
            // Catch other potential exceptions (e.g., token fetch failure in getHttpClient)
            Log::error("Keycloak admin service exception ({$method} {$url})", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
             ]);
            throw $e; // Re-throw
        }
    }

    // --- Public API Methods --- 

    /**
     * Get available realm roles.
     *
     * @param bool $briefRepresentation If true, returns a minimal representation.
     * @return array Array of role objects.
     * @throws RequestException If the API request fails.
     */
    public function getRealmRoles(bool $briefRepresentation = false): array
    {
        $url = "/admin/realms/{$this->realm}/roles";
        $params = $briefRepresentation ? ['briefRepresentation' => 'true'] : [];
        $response = $this->makeRequest('get', $url, $params);
        return $response->json() ?? []; // Return empty array if body is null or invalid JSON
    }

    /**
     * Get a user's effective realm roles (includes roles inherited from groups).
     *
     * @param string $userId Keycloak User ID (GUID)
     * @return array Array of role objects.
     * @throws RequestException If the API request fails.
     */
    public function getUserRealmRoles(string $userId): array
    {
        // Note: 'composite=true' gets effective roles. 'composite=false' gets only directly assigned realm roles.
        $url = "/admin/realms/{$this->realm}/users/{$userId}/role-mappings/realm/composite";
        $response = $this->makeRequest('get', $url);
        return $response->json() ?? [];
    }

    /**
     * Get Keycloak Role Representation objects by name.
     * Needed because add/remove endpoints require the full role object, not just the name.
     *
     * @param array $roleNames Array of role names to find.
     * @return array Array of Keycloak role representation objects found. Missing roles are ignored.
     * @throws RequestException If fetching all realm roles fails.
     */
    public function getRoleRepresentations(array $roleNames): array
    {
        if (empty($roleNames)) {
            return [];
        }

        // Fetch all realm roles once to avoid multiple API calls per role name
        try {
            $allRealmRoles = $this->getRealmRoles(false); // Need full representation
            // Create a map of name => role object for efficient lookup
            $rolesByName = collect($allRealmRoles)->keyBy('name');
        } catch (RequestException $e) {
            Log::error('Failed to fetch all realm roles for representation lookup', ['exception' => $e->getMessage()]);
            // Re-throw the exception as we cannot proceed without the roles list
            throw $e;
        }

        $representations = [];
        foreach ($roleNames as $name) {
            if ($rolesByName->has($name)) {
                // Ensure we add the *full object* Keycloak expects
                $representations[] = $rolesByName->get($name);
            } else {
                // Log a warning if a requested role name doesn't exist in the realm
                Log::warning('Role name not found in Keycloak realm during representation lookup.', [
                    'roleName' => $name,
                    'realm' => $this->realm
                ]);
            }
        }
        return $representations;
    }

    /**
     * Add realm-level roles to a user.
     *
     * @param string $userId Keycloak User ID (GUID)
     * @param array $rolesToAdd Array of Keycloak Role Representation objects (obtained via getRoleRepresentations).
     * @return void
     * @throws RequestException If the API request fails.
     * @throws Exception If role representations are invalid or missing.
     */
    public function addUserRealmRoles(string $userId, array $rolesToAdd): void
    {
        if (empty($rolesToAdd)) {
            Log::info('No roles provided to add.', ['userId' => $userId]);
            return; // Nothing to add
        }

        // Basic validation of input structure
        foreach ($rolesToAdd as $role) {
            if (!is_array($role) || empty($role['id']) || empty($role['name'])) {
                 throw new Exception('Invalid role representation provided to addUserRealmRoles. Roles must be arrays with id and name.');
            }
        }

        $url = "/admin/realms/{$this->realm}/users/{$userId}/role-mappings/realm";
        $this->makeRequest('post', $url, $rolesToAdd); // API expects an array of role representation objects in the body

        Log::info('Successfully added roles to Keycloak user', [
            'userId' => $userId,
            'roles_added_count' => count($rolesToAdd),
            'role_names' => array_column($rolesToAdd, 'name')
         ]);
    }

    /**
     * Remove realm-level roles from a user.
     *
     * @param string $userId Keycloak User ID (GUID)
     * @param array $rolesToRemove Array of Keycloak Role Representation objects (obtained via getRoleRepresentations).
     * @return void
     * @throws RequestException If the API request fails.
     * @throws Exception If role representations are invalid or missing.
     */
    public function removeUserRealmRoles(string $userId, array $rolesToRemove): void
    {
        if (empty($rolesToRemove)) {
             Log::info('No roles provided to remove.', ['userId' => $userId]);
            return; // Nothing to remove
        }

        // Basic validation of input structure
        foreach ($rolesToRemove as $role) {
            if (!is_array($role) || empty($role['id']) || empty($role['name'])) {
                 throw new Exception('Invalid role representation provided to removeUserRealmRoles. Roles must be arrays with id and name.');
            }
        }

        // Keycloak uses the 'DELETE' HTTP method with the roles to remove *in the request body*.
        $url = "/admin/realms/{$this->realm}/users/{$userId}/role-mappings/realm";
        $this->makeRequest('delete', $url, $rolesToRemove); // API expects array of role representations in body

        Log::info('Successfully removed roles from Keycloak user', [
            'userId' => $userId,
            'roles_removed_count' => count($rolesToRemove),
            'role_names' => array_column($rolesToRemove, 'name')
         ]);
    }

    /**
     * Get a list of users from the Keycloak realm.
     *
     * @param array $queryParams Optional query parameters (e.g., ['search' => 'john', 'first' => 0, 'max' => 10])
     * @return array Array of user objects.
     * @throws RequestException If the API request fails.
     */
    public function getUsers(array $queryParams = []): array
    {
        $url = "/admin/realms/{$this->realm}/users";
        $response = $this->makeRequest('get', $url, $queryParams);
        return $response->json() ?? [];
    }

    /**
     * Get details for a specific user by their Keycloak ID.
     *
     * @param string $userId Keycloak User ID (GUID)
     * @return array|null User object or null if not found.
     * @throws RequestException If the API request fails (excluding 404).
     */
    public function getUserById(string $userId): ?array
    {
        $url = "/admin/realms/{$this->realm}/users/{$userId}";
        try {
            $response = $this->makeRequest('get', $url);
            return $response->json();
        } catch (RequestException $e) {
            // If the user is not found (404), return null gracefully
            if ($e->response->status() === 404) {
                Log::info('User not found in Keycloak by ID.', ['userId' => $userId]);
                return null;
            }
            // Re-throw other errors
            throw $e;
        }
    }
} 