<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CommerceController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\AgentsController;
use App\Http\Controllers\ServicesController;

Route::middleware(['web', 'auth'])->group(function () {
    // Wallet Services
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('wallet');
        Route::get('/balance', [WalletController::class, 'getBalance'])->name('wallet.balance');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
        Route::get('/deposit', [WalletController::class, 'showDepositForm'])->name('wallet.deposit');
        Route::post('/deposit', [WalletController::class, 'processDeposit'])->name('wallet.deposit.process');
        Route::get('/withdraw', [WalletController::class, 'showWithdrawForm'])->name('wallet.withdraw');
        Route::post('/withdraw', [WalletController::class, 'processWithdraw'])->name('wallet.withdraw.process');
    });

    // Commerce Services
    Route::get('/commerce', [CommerceController::class, 'index'])->name('commerce');

    Route::group(['prefix' => 'commerce', 'as' => 'commerce.', 'middleware' => ['auth']], function () {
        Route::get('/products', [CommerceController::class, 'products'])->name('products');
        Route::get('/categories', [CommerceController::class, 'categories'])->name('categories');
        Route::get('/orders', [CommerceController::class, 'orders'])->name('orders');
        Route::get('/product/{id}', [CommerceController::class, 'productDetails'])->name('product.details');
    });

    // Messaging Services
    Route::prefix('messaging')->group(function () {
        Route::get('/', [MessagingController::class, 'index'])->name('messaging');
        Route::get('/chat', [MessagingController::class, 'chat'])->name('messaging.chat');
        Route::get('/support', [MessagingController::class, 'support'])->name('messaging.support');
    });

    // Delivery Services
    Route::prefix('delivery')->group(function () {
        Route::get('/', [DeliveryController::class, 'index'])->name('delivery');
        Route::get('/tracking', [DeliveryController::class, 'tracking'])->name('delivery.tracking');
        Route::get('/rates', [DeliveryController::class, 'rates'])->name('delivery.rates');
    });

    // AI Assistant Services
    Route::prefix('ai-assistant')->group(function () {
        Route::get('/', [AIAssistantController::class, 'index'])->name('ai-assistant');
        Route::post('/query', [AIAssistantController::class, 'query'])->name('ai-assistant.query');
    });

    // Agents Services
    Route::prefix('agents')->group(function () {
        Route::get('/', [AgentsController::class, 'index'])->name('agents');
        Route::get('/list', [AgentsController::class, 'list'])->name('agents.list');
        Route::get('/performance', [AgentsController::class, 'performance'])->name('agents.performance');
    });

    // Index route for all services
    Route::get('/', [ServicesController::class, 'index'])->name('index');
});
