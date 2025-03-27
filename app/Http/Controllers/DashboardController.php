<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all permissions the user has
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        // Return the User Dashboard view with permissions data
        return Inertia::render('Dashboard', [
            'permissions' => $permissions,
        ]);
    }
} 