<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Models\Role;

class KeycloakController extends Controller
{
    /**
     * Redirect the user to the Keycloak authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToKeycloak()
    {
        try {
            // Debug current Keycloak configuration
            $keycloakConfig = config('services.keycloak');
            \Log::info('Keycloak Config:', $keycloakConfig);
            
            // Build the authorization URL manually to ensure correct realm
            $baseUrl = config('services.keycloak.base_url');
            $realm = config('services.keycloak.realm');
            $clientId = config('services.keycloak.client_id');
            $redirectUri = config('services.keycloak.redirect');
            $openidPath = config('services.keycloak.openid_connect_path');
            
            // Force HTTP for local development
            if (app()->environment('local')) {
                $redirectUri = str_replace('https://', 'http://', $redirectUri);
            }
            
            // Build the query parameters manually to avoid double encoding
            $state = bin2hex(random_bytes(16));
            $params = [
                'client_id=' . urlencode($clientId),
                'redirect_uri=' . urlencode($redirectUri),
                'response_type=code',
                'scope=' . urlencode('openid email profile'),
                'state=' . urlencode($state)
            ];
            
            // Store state in session for security
            session()->put('keycloak_state', $state);
            
            $authUrl = "{$baseUrl}/realms/{$realm}{$openidPath}/auth?" . implode('&', $params);
            return redirect($authUrl);
            
        } catch (\Exception $e) {
            \Log::error('Keycloak Redirect Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Return a friendly error page
            return response()->view('errors.keycloak', [
                'message' => 'Unable to connect to authentication service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtain the user information from Keycloak.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleKeycloakCallback(Request $request)
    {
        try {
            // Validate the request has an authorization code
            if (!$request->has('code')) {
                throw new Exception('Authorization code not received from Keycloak');
            }
            
            // Get configurations
            $baseUrl = config('services.keycloak.base_url');
            $realm = config('services.keycloak.realm');
            $clientId = config('services.keycloak.client_id');
            $clientSecret = config('services.keycloak.client_secret');
            $redirectUri = config('services.keycloak.redirect');
            $openidPath = config('services.keycloak.openid_connect_path');
            
            // Force HTTP for local development
            if (app()->environment('local')) {
                $redirectUri = str_replace('https://', 'http://', $redirectUri);
            }
            
            // Exchange authorization code for tokens
            $tokenUrl = "{$baseUrl}/realms/{$realm}{$openidPath}/token";
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $request->code,
                'redirect_uri' => $redirectUri,
            ]);
            
            if (!$response->successful()) {
                \Log::error('Keycloak Token Error:', $response->json());
                throw new Exception('Failed to exchange authorization code for tokens: ' . $response->body());
            }
            
            $tokens = $response->json();
            
            // Get user info from the access token
            $userInfoUrl = "{$baseUrl}/realms/{$realm}{$openidPath}/userinfo";
            $userResponse = Http::withToken($tokens['access_token'])->get($userInfoUrl);
            
            if (!$userResponse->successful()) {
                \Log::error('Keycloak UserInfo Error:', $userResponse->json());
                throw new Exception('Failed to retrieve user information from Keycloak');
            }
            
            $keycloakUser = $userResponse->json();
            
            // Map the Keycloak user info to the format expected by our app
            $userInfo = [
                'id' => $keycloakUser['sub'],
                'name' => $keycloakUser['name'] ?? ($keycloakUser['preferred_username'] ?? ''),
                'email' => $keycloakUser['email'] ?? '',
                'token' => $tokens['access_token'],
            ];
            
            // Extract role information from the tokens if available
            // Decode the access token (it's a JWT) to get the roles
            $tokenParts = explode('.', $tokens['access_token']);
            if (count($tokenParts) === 3) {
                $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $tokenParts[1]))), true);
                
                // Extract client roles for our app
                $roles = $payload['resource_access'][config('services.keycloak.client_id')]['roles'] ?? [];
                
                // Also check for realm roles
                $realmRoles = $payload['realm_access']['roles'] ?? [];
                
                // Log the roles for debugging
                \Log::info('Keycloak roles for user:', [
                    'client_roles' => $roles,
                    'realm_roles' => $realmRoles
                ]);
                
                // Store roles with user info
                $userInfo['roles'] = array_merge($roles, $realmRoles);
            }
            
            // Find or create a local user based on Keycloak user information
            $authUser = $this->findOrCreateUser((object)$userInfo);
            
            // Log the user in with remember me
            Auth::login($authUser, true);
            
            // Store the access token in the session
            session(['keycloak_token' => $tokens['access_token']]);
            
            // Ensure session is saved before redirecting
            session()->save();
            
            // Redirect to the dashboard with an absolute URL
            return redirect()->to(config('app.url') . '/dashboard');
            
        } catch (Exception $e) {
            \Log::error('Keycloak Callback Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            // Handle exceptions (e.g., authentication failure)
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Find an existing user or create a new one based on Keycloak data.
     *
     * @param  \Laravel\Socialite\Contracts\User  $keycloakUser
     * @return \App\Models\User
     */
    protected function findOrCreateUser($keycloakUser)
    {
        // Try to find a user with the Keycloak ID
        $authUser = User::where('keycloak_id', $keycloakUser->id)->first();
        
        if ($authUser) {
            // Update existing user data if needed
            $authUser->update([
                'name' => $keycloakUser->name,
                'email' => $keycloakUser->email,
            ]);
            
            // Update the user's Keycloak roles
            $this->syncKeycloakRoles($authUser, $keycloakUser->roles ?? []);
            
            return $authUser;
        }
        
        // Try to find a user with the same email
        $authUser = User::where('email', $keycloakUser->email)->first();
        
        if ($authUser) {
            // Update user with Keycloak ID
            $authUser->update([
                'keycloak_id' => $keycloakUser->id,
                'name' => $keycloakUser->name,
            ]);
            
            // Update the user's Keycloak roles
            $this->syncKeycloakRoles($authUser, $keycloakUser->roles ?? []);
            
            return $authUser;
        }
        
        // Create a new user
        $authUser = User::create([
            'name' => $keycloakUser->name,
            'email' => $keycloakUser->email,
            'keycloak_id' => $keycloakUser->id,
        ]);
        
        // Set the user's Keycloak roles
        $this->syncKeycloakRoles($authUser, $keycloakUser->roles ?? []);
        
        return $authUser;
    }
    
    /**
     * Sync Keycloak roles for a user.
     *
     * @param  \App\Models\User  $user
     * @param  array  $roles
     * @return void
     */
    protected function syncKeycloakRoles($user, array $roles)
    {
        // Delete existing roles
        $user->keycloakRoles()->delete();
        
        // Add new roles
        foreach ($roles as $role) {
            $user->keycloakRoles()->create([
                'role_name' => $role,
                'role_type' => 'client', // Assuming these are client roles
            ]);
            
            // Also sync with local roles - create local role if it doesn't exist
            $localRole = Role::firstOrCreate(
                ['name' => $role],
                [
                    'display_name' => ucwords(str_replace('_', ' ', $role)),
                    'description' => 'Imported from Keycloak',
                    'is_system_role' => true
                ]
            );
            
            // Attach the role to the user if not already attached
            if (!$user->roles->contains($localRole->id)) {
                $user->roles()->attach($localRole->id);
            }
        }
    }
} 