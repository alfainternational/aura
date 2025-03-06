<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\MerchantAuthController;
use App\Http\Controllers\Auth\AgentAuthController;
use App\Http\Controllers\Auth\MessengerAuthController;
use App\Http\Controllers\Auth\BiometricController;
use App\Http\Controllers\Auth\BiometricAuthController;

/*
|--------------------------------------------------------------------------
| مسارات المصادقة
|--------------------------------------------------------------------------
|
| هنا تسجل كل مسارات المصادقة والتفويض للتطبيق.
| يتم تحميل هذه المسارات بواسطة RouteServiceProvider ضمن مجموعة برفيكس "web".
|
*/

// مسارات التسجيل
Route::middleware('guest')->group(function () {
    // صفحة التسجيل
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    
    // معالجة التسجيل
    Route::post('/register', [AuthController::class, 'register']);
    
    // تسجيل التاجر
    Route::get('/register/merchant', [MerchantAuthController::class, 'showRegistrationForm'])->name('register.merchant');
    Route::post('/register/merchant', [MerchantAuthController::class, 'register'])->name('register.merchant.submit');
    
    // تسجيل الوكيل
    Route::get('/register/agent', [AgentAuthController::class, 'showRegistrationForm'])->name('register.agent');
    Route::post('/register/agent', [AgentAuthController::class, 'register'])->name('register.agent.submit');
    
    // تسجيل المندوب
    Route::get('/register/messenger', [MessengerAuthController::class, 'showRegistrationForm'])->name('register.messenger');
    Route::post('/register/messenger', [MessengerAuthController::class, 'register'])->name('register.messenger.submit');
    
    // تسجيل العميل
    Route::get('/register/customer', [CustomerAuthController::class, 'showRegistrationForm'])->name('register.customer');
    Route::post('/register/customer', [CustomerAuthController::class, 'register'])->name('register.customer.submit');
});

// Admin Authentication Routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// مسارات تسجيل الدخول
Route::middleware('guest')->group(function () {
    // صفحة تسجيل الدخول
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    
    // معالجة تسجيل الدخول
    Route::post('/login', [AuthController::class, 'login']);
    
    // نسيت كلمة المرور
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    
    // إعادة تعيين كلمة المرور
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    
    // التحقق من رمز إعادة تعيين كلمة المرور
    Route::get('/reset-password/code', [AuthController::class, 'showResetCodeForm'])->name('password.reset.code');
    Route::post('/reset-password/code/verify', [AuthController::class, 'verifyResetCode'])->name('password.reset.code.verify');
    Route::post('/reset-password/new', [AuthController::class, 'resetPasswordWithCode'])->name('password.reset.new');
});

// Biometric Authentication Routes
Route::post('/biometric/register', [BiometricAuthController::class, 'register'])->name('biometric.register');
Route::post('/biometric/authenticate', [BiometricAuthController::class, 'authenticate'])->name('biometric.authenticate');

// مسارات الخروج وإدارة الحساب
Route::middleware(['auth'])->group(function () {
    // تسجيل الخروج
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // التحقق من البريد الإلكتروني
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name('verification.send');
    
    // تفعيل المصادقة الثنائية
    Route::post('/2fa/setup', [AuthController::class, 'setupTwoFactor'])->name('2fa.setup');
    Route::post('/2fa/confirm', [AuthController::class, 'confirmTwoFactor'])->name('2fa.confirm');
    Route::post('/2fa/disable', [AuthController::class, 'disableTwoFactor'])->name('2fa.disable');
    
    // إدارة المصادقة البيومترية
    Route::get('/biometric/setup', [BiometricAuthController::class, 'showSetupForm'])->name('biometric.setup');
    Route::post('/biometric/enable', [BiometricAuthController::class, 'enable'])->name('biometric.enable');
    Route::post('/biometric/disable', [BiometricAuthController::class, 'disable'])->name('biometric.disable');
    Route::post('/biometric/sessions/revoke', [BiometricAuthController::class, 'revokeSessions'])->name('biometric.sessions.revoke');
    Route::post('/biometric/sessions/revoke/{id}', [BiometricAuthController::class, 'revokeSession'])->name('biometric.sessions.revoke.single');
});

// مسارات المصادقة الخاصة بالتطبيق الجوال (API)
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
    Route::post('/logout', [AuthController::class, 'apiLogout'])->middleware('auth:api');
    Route::post('/password/reset', [AuthController::class, 'apiResetPassword']);
    
    // المصادقة البيومترية للتطبيق الجوال
    Route::post('/biometric/register', [BiometricAuthController::class, 'apiRegister']);
    Route::post('/biometric/authenticate', [BiometricAuthController::class, 'apiAuthenticate']);
    Route::post('/biometric/sessions', [BiometricAuthController::class, 'apiGetSessions'])->middleware('auth:api');
    Route::post('/biometric/sessions/revoke', [BiometricAuthController::class, 'apiRevokeSessions'])->middleware('auth:api');
    Route::post('/biometric/sessions/revoke/{id}', [BiometricAuthController::class, 'apiRevokeSession'])->middleware('auth:api');
});
