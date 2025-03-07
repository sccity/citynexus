<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Protected routes - using web middleware for Keycloak auth
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/system-status', [SystemStatusController::class, 'getStatus'])
        ->name('api.system-status');
}); 