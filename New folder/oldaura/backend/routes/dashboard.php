<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| هنا حيث يمكنك تسجيل مسارات لوحات التحكم لتطبيقك.
|
*/

// مسارات لوحة تحكم العميل
Route::prefix('customer')->middleware(['auth', 'check-role:customer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customerDashboard'])->name('customer.dashboard');
});

// مسارات لوحة تحكم التاجر
Route::prefix('merchant')->middleware(['auth', 'check-role:merchant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'merchantDashboard'])->name('merchant.dashboard');
    Route::get('/complete-profile', [DashboardController::class, 'completeMerchantProfile'])->name('merchant.complete-profile');
});

// مسارات لوحة تحكم الوكيل
Route::prefix('agent')->middleware(['auth', 'check-role:agent'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'agentDashboard'])->name('agent.dashboard');
    Route::get('/complete-profile', [DashboardController::class, 'completeAgentProfile'])->name('agent.complete-profile');
});

// مسارات لوحة تحكم المندوب
Route::prefix('messenger')->middleware(['auth', 'check-role:messenger'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'messengerDashboard'])->name('messenger.dashboard');
    Route::get('/complete-profile', [DashboardController::class, 'completeMessengerProfile'])->name('messenger.complete-profile');
});

// مسارات لوحة تحكم المشرف
Route::prefix('admin')->middleware(['auth', 'check-role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
});
