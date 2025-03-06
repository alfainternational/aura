<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerController;

Route::middleware(['auth', 'check-role:agent'])->group(function () {
    // Dashboard Routes
    Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');

    // Profile Management Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'agentProfile'])->name('profile');
        Route::put('/update', [ProfileController::class, 'updateAgentProfile'])->name('profile.update');
        Route::get('/documents', [ProfileController::class, 'agentDocuments'])->name('profile.documents');
        Route::put('/documents/upload', [ProfileController::class, 'uploadAgentDocuments'])->name('profile.documents.upload');
    });

    // Customer Management Routes
    Route::prefix('customers')->group(function () {
        Route::get('/', [AgentController::class, 'customers'])->name('customers');
        Route::get('/create', [AgentController::class, 'createCustomer'])->name('customers.create');
        Route::post('/store', [AgentController::class, 'storeCustomer'])->name('customers.store');
        Route::get('/{customer}/details', [AgentController::class, 'customerDetails'])->name('customers.details');
        Route::get('/{customer}/onboarding', [AgentController::class, 'customerOnboarding'])->name('customers.onboarding');
    });

    // Wallet and Financial Routes
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'agentWallet'])->name('wallet');
        Route::get('/transactions', [WalletController::class, 'agentTransactions'])->name('wallet.transactions');
        Route::get('/deposit', [WalletController::class, 'showAgentDepositForm'])->name('wallet.deposit');
        Route::post('/deposit', [WalletController::class, 'agentDeposit'])->name('wallet.deposit.process');
    });

    // Commission Routes
    Route::prefix('commissions')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('commissions');
        Route::get('/history', [CommissionController::class, 'history'])->name('commissions.history');
        Route::get('/withdraw', [CommissionController::class, 'showWithdrawForm'])->name('commissions.withdraw');
        Route::post('/withdraw', [CommissionController::class, 'withdrawCommissions'])->name('commissions.withdraw.process');
    });

    // Reporting and Analytics Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [AgentController::class, 'reports'])->name('reports');
        Route::get('/customers', [AgentController::class, 'customerReports'])->name('reports.customers');
        Route::get('/sales', [AgentController::class, 'salesReports'])->name('reports.sales');
        Route::get('/performance', [AgentController::class, 'performanceReports'])->name('reports.performance');
    });

    // KYC and Verification Routes
    Route::prefix('kyc')->group(function () {
        Route::get('/', [AgentController::class, 'kycDashboard'])->name('kyc');
        Route::get('/pending', [AgentController::class, 'pendingKyc'])->name('kyc.pending');
        Route::get('/{customer}/verify', [AgentController::class, 'verifyCustomer'])->name('kyc.verify');
        Route::post('/{customer}/submit', [AgentController::class, 'submitKycVerification'])->name('kyc.submit');
    });

    // Messaging Routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [AgentController::class, 'messages'])->name('messages');
        Route::get('/customers', [AgentController::class, 'customerMessages'])->name('messages.customers');
        Route::post('/send', [AgentController::class, 'sendMessage'])->name('messages.send');
    });
});
