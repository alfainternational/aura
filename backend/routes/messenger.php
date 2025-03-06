<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;

Route::middleware(['auth', 'check-role:messenger'])->group(function () {
    // Dashboard Routes
    Route::get('/', [MessengerController::class, 'dashboard'])->name('dashboard');

    // Profile Management Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'messengerProfile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateMessengerProfile'])->name('profile.update');
        Route::get('/vehicle', [ProfileController::class, 'vehicleDetails'])->name('profile.vehicle');
        Route::put('/vehicle/update', [ProfileController::class, 'updateVehicleDetails'])->name('profile.vehicle.update');
    });

    // Status and Location Routes
    Route::prefix('status')->group(function () {
        Route::get('/', [StatusController::class, 'index'])->name('status');
        Route::put('/update', [StatusController::class, 'update'])->name('status.update');
        Route::post('/location', [StatusController::class, 'updateLocation'])->name('status.location');
    });

    // Delivery Routes
    Route::prefix('deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/active', [DeliveryController::class, 'activeDeliveries'])->name('deliveries.active');
        Route::get('/completed', [DeliveryController::class, 'completedDeliveries'])->name('deliveries.completed');
        Route::get('/{delivery}/details', [DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('/{delivery}/accept', [DeliveryController::class, 'accept'])->name('deliveries.accept');
        Route::post('/{delivery}/complete', [DeliveryController::class, 'complete'])->name('deliveries.complete');
    });

    // Earnings and Payments Routes
    Route::prefix('earnings')->group(function () {
        Route::get('/', [MessengerController::class, 'earnings'])->name('earnings');
        Route::get('/history', [MessengerController::class, 'earningsHistory'])->name('earnings.history');
        Route::get('/withdraw', [MessengerController::class, 'withdrawEarnings'])->name('earnings.withdraw');
    });

    // Performance Routes
    Route::prefix('performance')->group(function () {
        Route::get('/', [MessengerController::class, 'performance'])->name('performance');
        Route::get('/ratings', [MessengerController::class, 'ratings'])->name('performance.ratings');
        Route::get('/statistics', [MessengerController::class, 'statistics'])->name('performance.statistics');
    });
});
