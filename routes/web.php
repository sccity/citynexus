<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\KeycloakController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\FinanceApiController;
use App\Http\Controllers\QuickVoteController;
use App\Http\Controllers\GovTxtConfigController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->to(str_replace('https://', 'http://', route('dashboard')));
    }
    return redirect()->to(str_replace('https://', 'http://', route('login.keycloak')));
});

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Regular user dashboard
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes
    Route::middleware(['role:admin|developer'])->prefix('admin')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('health', [App\Http\Controllers\Admin\DashboardController::class, 'health'])->name('admin.health');
        Route::get('settings', [App\Http\Controllers\Admin\DashboardController::class, 'settings'])->name('admin.settings');
    });
    
    // Budget routes with permissions
    Route::middleware(['permission:access-budget'])->prefix('budget')->group(function () {
        Route::get('/', function () {
            // In a real app, we'd pass actual permissions from the database
            // This is just an example
            $user_permissions = [
                'budget-view',
                'budget-edit',
                'budget-create',
                'budget-export',
            ];
            
            return Inertia::render('Budget/Index', [
                'user_permissions' => $user_permissions
            ]);
        })->name('budget.index');
        
        // Finance API data routes
        Route::get('/finance-dashboard', [FinanceApiController::class, 'dashboard'])->name('finance.dashboard');
        Route::get('/finance-budget', [FinanceApiController::class, 'budget'])->name('finance.budget');
        Route::get('/finance-employees', [FinanceApiController::class, 'employees'])->name('finance.employees');
        Route::get('/finance-expense-details', [FinanceApiController::class, 'expenseDetails'])->name('finance.expense-details');
        Route::get('/finance-past-due', [FinanceApiController::class, 'pastDue'])->name('finance.past-due');
        Route::get('/finance-revenue-details', [FinanceApiController::class, 'revenueDetails'])->name('finance.revenue-details');
    });
    
    // Business License routes with permissions
    Route::middleware(['permission:access-business-license'])->prefix('business-license')->group(function () {
        Route::get('/', function () {
            // In a real app, we'd pass actual permissions from the database
            // This is just a limited set for example
            $user_permissions = [
                'license-view',
                'license-search',
                // Excluding 'license-edit', 'license-approve', 'license-create'
            ];
            
            return Inertia::render('BusinessLicense/Index', [
                'user_permissions' => $user_permissions
            ]);
        })->name('business-license.index');
    });
    
    // Example of permission-based route
    Route::get('users/manage', function () {
        return Inertia::render('Users/Manage');
    })->middleware('permission:manage-users')->name('users.manage');

    // Quick Vote routes with permissions
    Route::middleware(['quick.vote.permission'])->prefix('tools')->group(function () {
        Route::get('/quick-vote', [QuickVoteController::class, 'index'])->name('quick-vote.index');
        Route::post('/quick-vote', [QuickVoteController::class, 'store'])->name('quick-vote.store');
        Route::get('/quick-vote/{id}/results', [QuickVoteController::class, 'getResults'])->name('quick-vote.results');
        Route::post('/quick-vote/{id}/toggle', [QuickVoteController::class, 'toggleActive'])->name('quick-vote.toggle');
        Route::get('/quick-vote/qr-code/{accessCode}', [QuickVoteController::class, 'getQrCode'])->name('quick-vote.qr-code');

        // GovTxt Config routes
        Route::middleware(['role.or.permission:govtxt-config-access'])->group(function () {
            Route::get('/govtxt-config', [GovTxtConfigController::class, 'index'])->name('govtxt-config.index');
            Route::post('/govtxt-config', [GovTxtConfigController::class, 'store'])->name('govtxt-config.store');
            Route::put('/govtxt-config/{autoResponse}', [GovTxtConfigController::class, 'update'])->name('govtxt-config.update');
            Route::delete('/govtxt-config/{autoResponse}', [GovTxtConfigController::class, 'destroy'])->name('govtxt-config.destroy');
        });
    });

    // Public vote routes (no auth required)
    Route::get('/vote/{accessCode}', [QuickVoteController::class, 'show'])->name('quick-vote.show');
    Route::post('/vote/{accessCode}', [QuickVoteController::class, 'submitVote'])->name('quick-vote.submit');
});

// Keycloak Authentication Routes
Route::get('/auth/keycloak', [KeycloakController::class, 'redirectToKeycloak'])
    ->name('login.keycloak');
Route::get('/auth/callback', [KeycloakController::class, 'handleKeycloakCallback']);

// Login route that redirects to Keycloak
Route::get('login', function () {
    return redirect()->route('login.keycloak');
})->middleware(['guest'])->name('login');

// Logout route
Route::post('logout', function () {
    auth()->logout();
    return redirect('/');
})->middleware(['auth'])->name('logout');

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

require __DIR__.'/settings.php';
