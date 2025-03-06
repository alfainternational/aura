<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\ConnectedDeviceController;

/*
|--------------------------------------------------------------------------
| User Dashboard Routes
|--------------------------------------------------------------------------
|
| Estas rutas son para el panel de control del usuario normal
| y están protegidas por el middleware de autenticación.
|
*/

Route::middleware(['auth', 'check-role:user'])->prefix('dashboard/user')->name('user.')->group(function () {
    // Rutas principales del dashboard
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // KYC (Know Your Customer) - Accesible sin verificación KYC
    Route::get('/kyc', [UserDashboardController::class, 'kyc'])->name('kyc');
    Route::post('/kyc/submit', [UserDashboardController::class, 'submitKyc'])->name('kyc.submit');
    
    // Notificaciones - Accesible sin verificación KYC
    Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-all-read', [UserDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
    
    // Perfil básico - Accesible sin verificación KYC
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar', [UserDashboardController::class, 'updateAvatar'])->name('profile.avatar');
    
    // Rutas que requieren verificación KYC
    Route::middleware(['kyc.verified'])->group(function() {
        // Estadísticas
        Route::get('/statistics', [UserDashboardController::class, 'statistics'])->name('statistics');
        
        // Perfil avanzado
        Route::put('/profile/contact-info', [UserDashboardController::class, 'updateContactInfo'])->name('profile.contact-info');
        Route::put('/profile/preferences', [UserDashboardController::class, 'updatePreferences'])->name('profile.preferences');
        Route::put('/profile/status', [UserDashboardController::class, 'updateStatus'])->name('profile.status');
        
        // Navegación y menús
        Route::get('/navigation', [UserDashboardController::class, 'navigation'])->name('navigation');
        
        // Configuración
        Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
        Route::put('/settings/general', [UserDashboardController::class, 'updateGeneralSettings'])->name('settings.general');
        Route::put('/settings/notifications', [UserDashboardController::class, 'updateNotificationSettings'])->name('settings.notifications');
        Route::put('/settings/privacy', [UserDashboardController::class, 'updatePrivacySettings'])->name('settings.privacy');
        Route::put('/settings/appearance', [UserDashboardController::class, 'updateAppearanceSettings'])->name('settings.appearance');
        Route::put('/settings/language', [UserDashboardController::class, 'updateLanguageSettings'])->name('settings.language');
        
        // Seguridad (protegida por middleware de verificación 2FA)
        Route::middleware(['2fa.verify'])->group(function () {
            Route::get('/security', [UserDashboardController::class, 'security'])->name('security');
            Route::put('/password/update', [UserDashboardController::class, 'updatePassword'])->name('password.update');
            Route::put('/privacy/update', [UserDashboardController::class, 'updatePrivacy'])->name('privacy.update');
            
            // Gestión de dispositivos conectados
            Route::get('/devices', [ConnectedDeviceController::class, 'index'])->name('devices.index');
            Route::post('/devices/{id}/toggle-trust', [ConnectedDeviceController::class, 'toggleTrust'])->name('devices.toggle-trust');
            Route::post('/devices/{id}/logout', [ConnectedDeviceController::class, 'logout'])->name('devices.logout');
            Route::post('/devices/logout-all', [ConnectedDeviceController::class, 'logoutAll'])->name('devices.logout-all');
            
            // Gestión de cuenta
            Route::put('/account/deactivate', [UserDashboardController::class, 'deactivateAccount'])->name('account.deactivate');
            Route::delete('/account/delete', [UserDashboardController::class, 'deleteAccount'])->name('account.delete');
            Route::get('/data/export', [UserDashboardController::class, 'exportData'])->name('data.export');
        });
    });
    
    // Autenticación de dos factores - Accesible sin verificación KYC
    Route::get('/two-factor/setup', [TwoFactorAuthController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [TwoFactorAuthController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [TwoFactorAuthController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/regenerate-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-recovery-codes');
});

// Rutas de autenticación de dos factores (accesibles sin verificación 2FA)
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/verify', [TwoFactorAuthController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])->name('two-factor.verify-submit');
    Route::get('/two-factor/recovery', [TwoFactorAuthController::class, 'showRecoveryForm'])->name('two-factor.recovery');
    Route::post('/two-factor/recovery', [TwoFactorAuthController::class, 'verifyRecoveryCode'])->name('two-factor.recovery-submit');
});
