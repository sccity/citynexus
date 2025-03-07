<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Check if user has the required role
        // If multiple roles are specified (comma separated), user must have at least one
        if (strpos($role, '|') !== false) {
            $roles = explode('|', $role);
            if (!$user->hasAnyRole($roles)) {
                // If there's an AJAX request
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized. You do not have the required role.'], 403);
                }
                
                // For regular requests
                abort(403, 'Unauthorized. You do not have the required role.');
            }
        } else {
            if (!$user->hasRole($role)) {
                // If there's an AJAX request
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized. You do not have the required role.'], 403);
                }
                
                // For regular requests
                abort(403, 'Unauthorized. You do not have the required role.');
            }
        }

        return $next($request);
    }
}
