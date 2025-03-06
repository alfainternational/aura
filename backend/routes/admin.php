<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KycVerificationController;
use App\Http\Controllers\SupervisorController;

Route::middleware(['auth', 'check-role:admin'])->group(function () {
    // Main Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'listUsers'])->name('users.index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/store', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/{user}/update', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/{user}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
    });

    // KYC Verification Management
    Route::prefix('kyc')->group(function () {
        Route::get('/', [KycVerificationController::class, 'index'])->name('kyc.index');
        Route::get('/pending', [KycVerificationController::class, 'pendingVerifications'])->name('kyc.pending');
        Route::get('/{verification}/details', [KycVerificationController::class, 'showDetails'])->name('kyc.details');
        Route::post('/{verification}/approve', [KycVerificationController::class, 'approve'])->name('kyc.approve');
        Route::post('/{verification}/reject', [KycVerificationController::class, 'reject'])->name('kyc.reject');
    });

    // Supervisor Management
    Route::prefix('supervisors')->group(function () {
        Route::get('/', [SupervisorController::class, 'index'])->name('supervisors.index');
        Route::get('/create', [SupervisorController::class, 'create'])->name('supervisors.create');
        Route::post('/store', [SupervisorController::class, 'store'])->name('supervisors.store');
        Route::get('/{supervisor}/edit', [SupervisorController::class, 'edit'])->name('supervisors.edit');
        Route::put('/{supervisor}/update', [SupervisorController::class, 'update'])->name('supervisors.update');
    });
});
