<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KeycloakAdminService;
use App\Models\User; // Assuming you have a User model
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Exception;

class AdminUserController extends Controller
{
    protected KeycloakAdminService $keycloakAdminService;

    public function __construct(KeycloakAdminService $keycloakAdminService)
    {
        $this->keycloakAdminService = $keycloakAdminService;
        // Add middleware for authorization if needed (e.g., only allow admins)
        // $this->middleware('can:manage_users'); 
    }

    /**
     * Display the user management page.
     *
     * Fetches users from the local database and available realm roles from Keycloak.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        try {
            // Fetch users from your local database
            // Ensure they have 'provider_id' (Keycloak user ID) populated
            $users = User::select(['id', 'name', 'email', 'provider_id'])
                        ->whereNotNull('provider_id') // Only manage users linked to Keycloak
                        ->orderBy('name')
                        ->get();

            // Fetch all available realm roles from Keycloak
            $availableRoles = $this->keycloakAdminService->getRealmRoles(true); // Use brief representation

            // Fetch current roles for each user (can be slow with many users)
            // Consider fetching roles async on the frontend per user if needed
            $usersWithRoles = $users->map(function ($user) {
                try {
                    $user->keycloak_roles = $this->keycloakAdminService->getUserRealmRoles($user->provider_id);
                } catch (RequestException $e) {
                    // Handle cases where a user might exist locally but not in Keycloak
                    // or if there are permission issues fetching roles
                     if ($e->response->status() === 404) {
                        Log::warning('User found locally but not in Keycloak or roles not fetchable.', ['user_id' => $user->id, 'provider_id' => $user->provider_id]);
                        $user->keycloak_roles = []; // Assign empty roles if user not found in KC
                        $user->fetch_error = 'User not found in Keycloak.';
                    } else {
                        Log::error('Failed to fetch Keycloak roles for user.', ['user_id' => $user->id, 'provider_id' => $user->provider_id, 'error' => $e->getMessage()]);
                        $user->keycloak_roles = []; // Assign empty roles on error
                        $user->fetch_error = 'Error fetching roles.';
                    }
                } catch (Exception $e) {
                     Log::error('Generic error fetching Keycloak roles for user.', ['user_id' => $user->id, 'provider_id' => $user->provider_id, 'error' => $e->getMessage()]);
                    $user->keycloak_roles = [];
                    $user->fetch_error = 'Error fetching roles.';
                }
                return $user;
            });


            return Inertia::render('Admin/Users/Index', [
                'users' => $usersWithRoles,
                'availableRoles' => $availableRoles,
            ]);

        } catch (RequestException $e) {
            Log::error('Failed to fetch initial data for user management page (Realm Roles).', ['error' => $e->getMessage(), 'status' => $e->response->status()]);
             // Return Inertia response with error message
            return Inertia::render('Admin/Users/Index', [
                'users' => [],
                'availableRoles' => [],
                'error' => 'Could not fetch realm roles from Keycloak. Please check configuration and Keycloak status.',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load user management page.', ['error' => $e->getMessage()]);
            // Return Inertia response with generic error message
            return Inertia::render('Admin/Users/Index', [
                'users' => [],
                'availableRoles' => [],
                'error' => 'An unexpected error occurred while loading the page.',
            ]);
        }
    }

    /**
     * Update the Keycloak realm roles for a specific user.
     *
     * @param Request $request
     * @param int $userId Local User ID
     * @return JsonResponse
     */
    public function updateRoles(Request $request, int $userId): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array', // Expecting an array of role names
            'roles.*' => 'string',     // Each item in the array should be a string (role name)
        ]);

        try {
            $user = User::whereNotNull('provider_id')->findOrFail($userId);
            $keycloakUserId = $user->provider_id;
            $targetRoleNames = $validated['roles'];

            // 1. Get current roles assigned to the user in Keycloak
            $currentUserRoles = $this->keycloakAdminService->getUserRealmRoles($keycloakUserId);
            $currentUserRoleNames = collect($currentUserRoles)->pluck('name')->toArray();

            // 2. Calculate differences
            $rolesToAddNames = array_diff($targetRoleNames, $currentUserRoleNames);
            $rolesToRemoveNames = array_diff($currentUserRoleNames, $targetRoleNames);

            // 3. Get full Role Representations for adding/removing
            $rolesToAddRepresentations = $this->keycloakAdminService->getRoleRepresentations(array_values($rolesToAddNames));
            $rolesToRemoveRepresentations = $this->keycloakAdminService->getRoleRepresentations(array_values($rolesToRemoveNames));

             // Log the actions planned
            Log::info('Updating Keycloak roles for user.', [
                'user_id' => $userId,
                'keycloak_user_id' => $keycloakUserId,
                'target_roles' => $targetRoleNames,
                'current_roles' => $currentUserRoleNames,
                'roles_to_add' => array_column($rolesToAddRepresentations, 'name'),
                'roles_to_remove' => array_column($rolesToRemoveRepresentations, 'name'),
            ]);

            // 4. Perform Additions
            if (!empty($rolesToAddRepresentations)) {
                $this->keycloakAdminService->addUserRealmRoles($keycloakUserId, $rolesToAddRepresentations);
            }

            // 5. Perform Removals
            if (!empty($rolesToRemoveRepresentations)) {
                $this->keycloakAdminService->removeUserRealmRoles($keycloakUserId, $rolesToRemoveRepresentations);
            }
            
            // Fetch the updated roles to return to the frontend
            $updatedRoles = $this->keycloakAdminService->getUserRealmRoles($keycloakUserId);

            return response()->json([
                'message' => 'User roles updated successfully.',
                'updated_roles' => $updatedRoles // Send back the actual assigned roles
            ]);

        } catch (RequestException $e) {
            Log::error('Keycloak API error updating user roles.', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'status' => $e->response->status(),
                'response' => $e->response->body(),
            ]);
            return response()->json(['message' => 'Failed to update roles: Keycloak API error. ' . $e->getMessage()], $e->response->status() ?: 500);
        } catch (Exception $e) {
            Log::error('Error updating user roles.', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all available Keycloak realm roles.
     *
     * @return JsonResponse
     */
    public function getRealmRoles(): JsonResponse
    {
        try {
            $roles = $this->keycloakAdminService->getRealmRoles(false); // Get full representation
            return response()->json($roles);
        } catch (RequestException $e) {
             Log::error('Failed to fetch realm roles via API endpoint.', [
                'error' => $e->getMessage(),
                'status' => $e->response->status(),
                'response' => $e->response->body(),
            ]);
            return response()->json(['message' => 'Failed to fetch realm roles from Keycloak.'], $e->response->status() ?: 500);
        } catch (Exception $e) {
            Log::error('Error fetching realm roles via API endpoint.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
} 