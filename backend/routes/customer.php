<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth', 'check-role:customer'])->group(function () {
    // Dashboard Routes
    Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

    // Profile Management Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'customerProfile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateCustomerProfile'])->name('profile.update');
        Route::get('/location', [ProfileController::class, 'locationSettings'])->name('profile.location');
        Route::put('/location/update', [ProfileController::class, 'updateLocation'])->name('profile.location.update');
    });

    // Wallet and Payment Routes
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet');
        Route::get('/deposit', [WalletController::class, 'showDepositForm'])->name('wallet.deposit');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit.process');
        Route::get('/withdraw', [WalletController::class, 'showWithdrawForm'])->name('wallet.withdraw');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw.process');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    });

    // Messaging Routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('messages');
        Route::get('/contacts', [MessageController::class, 'contacts'])->name('messages.contacts');
        Route::get('/{conversation}', [MessageController::class, 'show'])->name('messages.conversation');
        Route::post('/{conversation}/send', [MessageController::class, 'sendMessage'])->name('messages.send');
        Route::delete('/{message}', [MessageController::class, 'deleteMessage'])->name('messages.delete');
    });

    // Voice Call Routes
    Route::prefix('calls')->group(function () {
        Route::get('/', [MessageController::class, 'callHistory'])->name('calls');
        Route::post('/initiate', [MessageController::class, 'initiateCall'])->name('calls.initiate');
        Route::post('/accept/{call}', [MessageController::class, 'acceptCall'])->name('calls.accept');
        Route::post('/reject/{call}', [MessageController::class, 'rejectCall'])->name('calls.reject');
    });

    // Delivery and Order Routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [CustomerController::class, 'orders'])->name('orders');
        Route::get('/create', [CustomerController::class, 'createOrder'])->name('orders.create');
        Route::post('/store', [CustomerController::class, 'storeOrder'])->name('orders.store');
        Route::get('/{order}', [CustomerController::class, 'orderDetails'])->name('orders.details');
        Route::put('/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('orders.cancel');
    });

    // Ratings and Feedback
    Route::prefix('ratings')->group(function () {
        Route::post('/messenger/{messenger}', [CustomerController::class, 'rateMessenger'])->name('ratings.messenger');
        Route::get('/history', [CustomerController::class, 'ratingHistory'])->name('ratings.history');
    });
});
