<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\DeliveryController;

Route::middleware(['auth', 'check-role:admin,supervisor'])->group(function () {
    // Supervisor Dashboard
    Route::get('/', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');

    // Messenger Management
    Route::prefix('messengers')->group(function () {
        Route::get('/', [MessengerController::class, 'index'])->name('messengers.index');
        Route::get('/create', [MessengerController::class, 'create'])->name('messengers.create');
        Route::post('/store', [MessengerController::class, 'store'])->name('messengers.store');
        Route::get('/{messenger}/edit', [MessengerController::class, 'edit'])->name('messengers.edit');
        Route::put('/{messenger}/update', [MessengerController::class, 'update'])->name('messengers.update');
        Route::get('/{messenger}/performance', [MessengerController::class, 'performance'])->name('messengers.performance');
    });

    // Delivery Management
    Route::prefix('deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/active', [DeliveryController::class, 'activeDeliveries'])->name('deliveries.active');
        Route::get('/completed', [DeliveryController::class, 'completedDeliveries'])->name('deliveries.completed');
        Route::get('/{delivery}/details', [DeliveryController::class, 'show'])->name('deliveries.show');
    });

    // Performance Reports
    Route::get('/reports', [SupervisorController::class, 'reports'])->name('reports');
    Route::get('/reports/messengers', [SupervisorController::class, 'messengerReports'])->name('reports.messengers');
    Route::get('/reports/deliveries', [SupervisorController::class, 'deliveryReports'])->name('reports.deliveries');
});
