<?php

namespace App\Providers;

use App\Services\Security\SecurityService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Failed as FailedLoginEvent;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SecurityService::class, function ($app) {
            return new SecurityService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar eventos de autenticación
        Event::listen(FailedLoginEvent::class, function (FailedLoginEvent $event) {
            $securityService = $this->app->make(SecurityService::class);
            
            // Registrar intento fallido de inicio de sesión
            $identifier = $event->credentials['email'] ?? 'unknown';
            $attempts = $securityService->logFailedAttempt('login_attempts', $identifier, request());
            
            Log::info("Intento fallido de inicio de sesión para {$identifier}. Intentos: {$attempts}");
        });
        
        Event::listen(LoginEvent::class, function (LoginEvent $event) {
            $securityService = $this->app->make(SecurityService::class);
            $user = $event->user;
            
            // Restablecer contador de intentos fallidos
            $securityService->resetFailedAttempts('login_attempts', $user->email);
            
            // Detectar actividad anómala
            $anomalies = $securityService->detectAnomalousActivity($user, request());
            
            if ($anomalies) {
                Log::notice('Actividad anómala detectada durante el inicio de sesión: ' . json_encode($anomalies));
                
                // Si hay cambios significativos, podríamos requerir verificación adicional
                if (isset($anomalies['location_change']) || isset($anomalies['device_change'])) {
                    session(['security_verification_required' => true, 'security_anomalies' => $anomalies]);
                }
            }
            
            // Actualizar información de último inicio de sesión
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent(request()->userAgent());
            
            $deviceInfo = [
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
            ];
            
            $locationData = $securityService->getLocationData(request()->ip());
            
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_login_device' => json_encode($deviceInfo),
                'last_login_location' => $locationData ? json_encode($locationData) : null,
            ]);
        });
        
        Event::listen(LogoutEvent::class, function (LogoutEvent $event) {
            // Registrar cierre de sesión si es necesario
        });
        
        Event::listen(PasswordResetEvent::class, function (PasswordResetEvent $event) {
            $securityService = $this->app->make(SecurityService::class);
            
            // Registrar restablecimiento de contraseña como incidente de seguridad (para seguimiento)
            $securityService->logSecurityIncident(
                'password_reset',
                request(),
                [],
                $event->user
            );
        });
        
        // Limpiar caché de IPs bloqueadas expiradas periódicamente
        $this->app->terminating(function () {
            $this->cleanupExpiredBlockedIps();
        });
    }
    
    /**
     * Limpiar IPs bloqueadas expiradas de la caché.
     *
     * @return void
     */
    protected function cleanupExpiredBlockedIps()
    {
        try {
            $keys = Cache::get('cache_keys:blocked_ip:*', []);
            
            foreach ($keys as $key) {
                if (strpos($key, 'blocked_ip:') === 0 && !Cache::has($key)) {
                    // La clave ha expirado, eliminarla de la lista
                    $keys = array_diff($keys, [$key]);
                }
            }
            
            Cache::put('cache_keys:blocked_ip:*', $keys, now()->addDay());
        } catch (\Exception $e) {
            Log::error('Error al limpiar IPs bloqueadas: ' . $e->getMessage());
        }
    }
}
