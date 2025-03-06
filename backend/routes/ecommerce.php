<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;

Route::middleware(['auth'])->group(function () {
    // Product Routes (Accessible to all authenticated users)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        
        // Reviews
        Route::prefix('/{product}/reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('reviews');
            Route::post('/', [ReviewController::class, 'store'])->name('reviews.store');
        });
    });

    // Merchant-Specific Product Management
    Route::middleware(['check-role:merchant'])->group(function () {
        Route::prefix('merchant/products')->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('merchant.products.create');
            Route::post('/store', [ProductController::class, 'store'])->name('merchant.products.store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('merchant.products.edit');
            Route::put('/{product}/update', [ProductController::class, 'update'])->name('merchant.products.update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('merchant.products.delete');
        });
    });

    // Cart Routes (Accessible to customers)
    Route::middleware(['check-role:customer'])->group(function () {
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('cart');
            Route::post('/add', [CartController::class, 'add'])->name('cart.add');
            Route::put('/update', [CartController::class, 'update'])->name('cart.update');
            Route::delete('/remove', [CartController::class, 'remove'])->name('cart.remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
        });
    });

    // Order Routes
    Route::prefix('orders')->group(function () {
        // Customer Order Routes
        Route::middleware(['check-role:customer'])->group(function () {
            Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
            Route::post('/store', [OrderController::class, 'store'])->name('orders.store');
            Route::get('/history', [OrderController::class, 'customerHistory'])->name('orders.customer.history');
        });

        // Merchant Order Management Routes
        Route::middleware(['check-role:merchant'])->group(function () {
            Route::get('/merchant', [OrderController::class, 'merchantOrders'])->name('orders.merchant');
            Route::get('/{order}/details', [OrderController::class, 'merchantOrderDetails'])->name('orders.merchant.details');
            Route::put('/{order}/status', [OrderController::class, 'updateOrderStatus'])->name('orders.merchant.status');
        });

        // Common Order Routes
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Review Routes
    Route::prefix('reviews')->group(function () {
        // Customer Review Routes
        Route::middleware(['check-role:customer'])->group(function () {
            Route::post('/{product}', [ReviewController::class, 'store'])->name('reviews.store');
            Route::get('/my-reviews', [ReviewController::class, 'customerReviews'])->name('reviews.customer');
        });

        // Merchant Review Management
        Route::middleware(['check-role:merchant'])->group(function () {
            Route::get('/merchant', [ReviewController::class, 'merchantReviews'])->name('reviews.merchant');
        });
    });
});
