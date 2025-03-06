<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;

Route::prefix('admin')->group(function () {
    // Admin Login Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Password Reset Routes
    Route::get('/password/reset', [AdminAuthController::class, 'showPasswordResetForm'])->name('admin.password.request');
    Route::post('/password/email', [AdminAuthController::class, 'sendPasswordResetLink'])->name('admin.password.email');
    Route::get('/password/reset/{token}', [AdminAuthController::class, 'showPasswordResetConfirmForm'])->name('admin.password.reset');
    Route::post('/password/reset', [AdminAuthController::class, 'resetPassword'])->name('admin.password.update');
});
