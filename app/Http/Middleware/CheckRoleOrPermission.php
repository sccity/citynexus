<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleOrPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $roleOrPermission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // First check if the user has admin or developer role
        if ($user->hasRole('admin') || $user->hasRole('developer')) {
            return $next($request);
        }
        
        // If not admin/developer, check for the specific permission
        if ($user->hasPermission($roleOrPermission)) {
            return $next($request);
        }
        
        // If there's an AJAX request
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized. Insufficient permissions.'], 403);
        }
        
        // For regular requests
        abort(403, 'Unauthorized. Insufficient permissions.');
    }
} 