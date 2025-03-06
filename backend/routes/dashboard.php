<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// User-specific dashboard routes
Route::middleware(['auth'])->group(function () {
    // Customer Dashboard
    Route::get('/customer/dashboard', [DashboardController::class, 'customerDashboard'])
        ->name('customer.dashboard')
        ->middleware('check-role:customer');

    // Merchant Dashboard
    Route::get('/merchant/dashboard', [DashboardController::class, 'merchantDashboard'])
        ->name('merchant.dashboard')
        ->middleware('check-role:merchant');

    // Agent Dashboard
    Route::get('/agent/dashboard', [DashboardController::class, 'agentDashboard'])
        ->name('agent.dashboard')
        ->middleware('check-role:agent');

    // Messenger Dashboard
    Route::get('/messenger/dashboard', [DashboardController::class, 'messengerDashboard'])
        ->name('messenger.dashboard')
        ->middleware('check-role:messenger');

    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('admin.dashboard')
        ->middleware('check-role:admin');

    // Supervisor Dashboard
    Route::get('/supervisor/dashboard', [DashboardController::class, 'supervisorDashboard'])
        ->name('supervisor.dashboard')
        ->middleware('check-role:admin,supervisor');
});
