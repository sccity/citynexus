<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();
        $userData = null;
        
        if ($user) {
            // Include roles and Keycloak roles with the user data
            $roles = $user->roles()->get(['id', 'name', 'display_name']);
            $keycloakRoles = $user->keycloakRoles()->get(['role_name', 'role_type']);
            
            // Get all permissions the user has (direct + via roles)
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            
            // Add this data to the user object sent to the frontend
            $userData = $user->toArray();
            $userData['roles'] = $roles;
            $userData['keycloak_roles'] = $keycloakRoles;
            $userData['permissions'] = $permissions;
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $userData,
                'user_permissions' => $userData ? $userData['permissions'] : [], // For easier access in components
            ],
            'keycloak' => [
                'realm' => config('services.keycloak.realm'),
                'client_id' => config('services.keycloak.client_id'),
            ],
        ];
    }
}
