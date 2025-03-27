<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all permissions the user has
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        // Return the Admin Dashboard view with permissions data
        return Inertia::render('Admin/Dashboard', [
            'permissions' => $permissions,
        ]);
    }

    public function health()
    {
        return Inertia::render('Admin/Health');
    }

    public function settings()
    {
        return Inertia::render('Admin/Settings');
    }
}
