<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\API\UserActivityController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\InstallmentController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\UserSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // رفع وتحديث موقع المستخدم
    Route::post('/location/update', [LocationController::class, 'updateLocation']);
});

// واجهات API العامة
Route::prefix('location')->group(function () {
    // الحصول على قائمة الدول المدعومة
    Route::get('/countries', [LocationController::class, 'getSupportedCountries']);
    
    // الحصول على قائمة المدن للدولة
    Route::get('/cities/{country_code}', [LocationController::class, 'getCitiesByCountry']);
});

// Nearby entities routes
Route::get('/nearby-agents', [LocationController::class, 'getNearbyAgents']);
Route::get('/nearby-stores', [LocationController::class, 'getNearbyStores']);

// Rutas de la API para actividad de usuarios
Route::middleware('auth:sanctum')->prefix('user-activity')->group(function () {
    Route::post('/log', [UserActivityController::class, 'logActivity']);
    Route::get('/history', [UserActivityController::class, 'getUserActivities']);
    Route::get('/stats', [UserActivityController::class, 'getUserStats']);
    Route::get('/recommendations', [UserActivityController::class, 'getRecommendations']);
});

// Rutas para integración bancaria
Route::middleware('auth:sanctum')->prefix('banking')->group(function () {
    // Rutas para bancos
    Route::get('/banks', [BankController::class, 'getBanks']);
    
    // Rutas para cuentas bancarias del usuario
    Route::get('/accounts', [BankController::class, 'getUserBankAccounts']);
    Route::post('/accounts', [BankController::class, 'addBankAccount']);
    Route::post('/accounts/{accountId}/primary', [BankController::class, 'setAsPrimaryAccount']);
    Route::delete('/accounts/{accountId}', [BankController::class, 'deleteBankAccount']);
    
    // Rutas para transacciones
    Route::post('/deposit', [BankController::class, 'initiateDeposit']);
    Route::post('/verify-transaction', [BankController::class, 'verifyTransaction']);
});

// Rutas para planes de pago a plazos
Route::middleware('auth:sanctum')->prefix('installments')->group(function () {
    Route::get('/options', [InstallmentController::class, 'getInstallmentOptions']);
    Route::post('/plans', [InstallmentController::class, 'createInstallmentPlan']);
    Route::get('/plans', [InstallmentController::class, 'getUserInstallmentPlans']);
    Route::get('/plans/{planId}', [InstallmentController::class, 'getInstallmentPlanDetails']);
    Route::post('/pay', [InstallmentController::class, 'payInstallment']);
});

// Rutas para manejo de OTP
Route::middleware('auth:sanctum')->prefix('otp')->group(function () {
    Route::post('/generate', [OtpController::class, 'generateOtp']);
    Route::post('/verify', [OtpController::class, 'verifyOtp']);
    Route::post('/resend', [OtpController::class, 'resendOtp']);
});

// Rutas para configuración de usuario
Route::middleware('auth:sanctum')->prefix('settings')->group(function () {
    Route::get('/', [UserSettingsController::class, 'getSettings']);
    Route::put('/', [UserSettingsController::class, 'updateSettings']);
    Route::put('/otp', [UserSettingsController::class, 'updateOtpSettings']);
    Route::put('/notifications', [UserSettingsController::class, 'updateNotificationPreferences']);
    Route::put('/ui', [UserSettingsController::class, 'updateUiPreferences']);
});
