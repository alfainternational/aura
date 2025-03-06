<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

Route::middleware(['auth'])->group(function () {
    // Universal Wallet Routes
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/balance', [WalletController::class, 'balance'])->name('balance');
        
        // Deposit Routes
        Route::prefix('deposit')->group(function () {
            Route::get('/', [WalletController::class, 'showDepositForm'])->name('deposit');
            Route::post('/', [WalletController::class, 'deposit'])->name('deposit.process');
            Route::get('/methods', [WalletController::class, 'depositMethods'])->name('deposit.methods');
        });

        // Withdrawal Routes
        Route::prefix('withdraw')->group(function () {
            Route::get('/', [WalletController::class, 'showWithdrawForm'])->name('withdraw');
            Route::post('/', [WalletController::class, 'withdraw'])->name('withdraw.process');
            Route::get('/methods', [WalletController::class, 'withdrawalMethods'])->name('withdraw.methods');
        });

        // Transaction Routes
        Route::prefix('transactions')->group(function () {
            Route::get('/', [WalletController::class, 'transactions'])->name('transactions');
            Route::get('/recent', [WalletController::class, 'recentTransactions'])->name('transactions.recent');
            Route::get('/{transaction}', [WalletController::class, 'transactionDetails'])->name('transactions.details');
        });
    });

    // Role-Specific Wallet Routes
    Route::middleware(['check-role:customer'])->group(function () {
        Route::get('/customer/wallet', [WalletController::class, 'customerWallet'])->name('customer.wallet');
    });

    Route::middleware(['check-role:merchant'])->group(function () {
        Route::get('/merchant/wallet', [WalletController::class, 'merchantWallet'])->name('merchant.wallet');
    });

    Route::middleware(['check-role:agent'])->group(function () {
        Route::get('/agent/wallet', [WalletController::class, 'agentWallet'])->name('agent.wallet');
    });

    Route::middleware(['check-role:messenger'])->group(function () {
        Route::get('/messenger/wallet', [WalletController::class, 'messengerWallet'])->name('messenger.wallet');
    });
});
