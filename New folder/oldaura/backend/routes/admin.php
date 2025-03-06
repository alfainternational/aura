<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\KycVerificationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات لوحة تحكم المسؤول (الآدمن) والمشرفين
|
*/

// صفحة استكمال بيانات المسؤول
Route::get('/complete-profile', [AdminAuthController::class, 'completeProfile'])->name('complete-profile');
Route::post('/store-profile', [AdminAuthController::class, 'storeProfile'])->name('store-profile');

// مسارات لوحة تحكم المسؤول
Route::middleware(['auth', 'check-role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // لوحة التحكم الرئيسية
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // إدارة المستخدمين
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    
    // إدارة المشرفين
    Route::get('/supervisors', [AdminController::class, 'supervisors'])->name('supervisors');
    Route::get('/supervisors/create', [AdminController::class, 'createSupervisor'])->name('supervisors.create');
    Route::post('/supervisors', [AdminController::class, 'storeSupervisor'])->name('supervisors.store');
    Route::get('/supervisors/{id}/edit', [AdminController::class, 'editSupervisor'])->name('supervisors.edit');
    Route::put('/supervisors/{id}', [AdminController::class, 'updateSupervisor'])->name('supervisors.update');
    Route::delete('/supervisors/{id}', [AdminController::class, 'deleteSupervisor'])->name('supervisors.delete');
    
    // إدارة المناديب
    Route::get('/messengers', [AdminController::class, 'messengers'])->name('messengers');
    Route::get('/messengers/create', [AdminController::class, 'createMessenger'])->name('messengers.create');
    Route::post('/messengers', [AdminController::class, 'storeMessenger'])->name('messengers.store');
    Route::get('/messengers/{id}/edit', [AdminController::class, 'editMessenger'])->name('messengers.edit');
    Route::put('/messengers/{id}', [AdminController::class, 'updateMessenger'])->name('messengers.update');
    Route::delete('/messengers/{id}', [AdminController::class, 'deleteMessenger'])->name('messengers.delete');
    
    // إدارة طلبات التحقق من الهوية (KYC)
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/dashboard', [KycVerificationController::class, 'dashboard'])->name('dashboard');
        Route::get('/pending', [KycVerificationController::class, 'pendingVerifications'])->name('pending');
        Route::get('/approved', [KycVerificationController::class, 'approvedVerifications'])->name('approved');
        Route::get('/rejected', [KycVerificationController::class, 'rejectedVerifications'])->name('rejected');
        Route::get('/verification/{id}', [KycVerificationController::class, 'viewVerification'])->name('view');
        Route::post('/verification/{id}/approve', [KycVerificationController::class, 'approveVerification'])->name('approve');
        Route::post('/verification/{id}/reject', [KycVerificationController::class, 'rejectVerification'])->name('reject');
        Route::post('/verification/{id}/request-info', [KycVerificationController::class, 'requestAdditionalInfo'])->name('request-info');
    });
});

// مسارات المشرفين
Route::middleware(['auth', 'check-role:admin,supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    // لوحة التحكم الرئيسية
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
    
    // إدارة المناديب
    Route::get('/messengers', [SupervisorController::class, 'messengers'])->name('messengers');
    Route::get('/messengers/track', [SupervisorController::class, 'trackMessengers'])->name('messengers.track');
    Route::get('/messengers/{id}', [SupervisorController::class, 'showMessenger'])->name('messengers.show');
    Route::post('/messengers/{id}/toggle-status', [SupervisorController::class, 'toggleMessengerStatus'])->name('messengers.toggle-status');
    
    // تتبع المناديب
    Route::get('/track', [SupervisorController::class, 'trackMessengers'])->name('track');
    
    // تقارير المناديب
    Route::get('/reports', [SupervisorController::class, 'reports'])->name('reports');
});