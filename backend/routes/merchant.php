<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth', 'check-role:merchant'])->group(function () {
    // Dashboard Routes
    Route::get('/', [MerchantController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('dashboard');

    // Profile Management Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'merchantProfile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateMerchantProfile'])->name('profile.update');
        Route::get('/business', [ProfileController::class, 'businessDetails'])->name('profile.business');
        Route::put('/business/update', [ProfileController::class, 'updateBusinessDetails'])->name('profile.business.update');
    });

    // Product Management Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}/update', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.delete');
        Route::get('/{product}/reviews', [ProductController::class, 'reviews'])->name('products.reviews');
    });

    // Order Management Routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders');
        Route::get('/active', [OrderController::class, 'activeOrders'])->name('orders.active');
        Route::get('/completed', [OrderController::class, 'completedOrders'])->name('orders.completed');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.details');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });

    // Wallet and Payment Routes
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'merchantWallet'])->name('wallet');
        Route::get('/transactions', [WalletController::class, 'merchantTransactions'])->name('wallet.transactions');
        Route::get('/withdraw', [WalletController::class, 'showWithdrawForm'])->name('wallet.withdraw');
        Route::post('/withdraw', [WalletController::class, 'withdrawMerchantFunds'])->name('wallet.withdraw.process');
    });

    // Analytics and Reporting Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', [MerchantController::class, 'analytics'])->name('analytics');
        Route::get('/sales', [MerchantController::class, 'salesReport'])->name('analytics.sales');
        Route::get('/products', [MerchantController::class, 'productPerformance'])->name('analytics.products');
        Route::get('/customers', [MerchantController::class, 'customerInsights'])->name('analytics.customers');
    });

    // Messaging Routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [MerchantController::class, 'messages'])->name('messages');
        Route::get('/customers', [MerchantController::class, 'customerMessages'])->name('messages.customers');
        Route::post('/send', [MerchantController::class, 'sendMessage'])->name('messages.send');
    });
});
