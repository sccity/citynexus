<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            // If there's an AJAX request
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. You do not have the required permissions.'], 403);
            }
            
            // For regular requests
            abort(403, 'Unauthorized. You do not have the required permissions.');
        }

        return $next($request);
    }
}
