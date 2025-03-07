<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckQuickVotePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user has admin status or specific quick vote permission
        if ($user && (
            $user->keycloakRoles()->where('role_name', 'admin')->exists() ||
            $user->keycloakRoles()->where('role_name', 'developer')->exists() ||
            $user->keycloakRoles()->where('role_name', 'quick-vote-access')->exists()
        )) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to Quick Vote feature.');
    }
} 