<?php

namespace App\Http\Middleware;

use App\Services\Security\SecurityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityAnalyzer
{
    /**
     * El servicio de seguridad.
     *
     * @var SecurityService
     */
    protected $securityService;

    /**
     * Crear una nueva instancia del middleware.
     *
     * @param SecurityService $securityService
     * @return void
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si la IP está bloqueada
        if ($this->securityService->isIpBlocked($request->ip())) {
            Log::warning('Intento de acceso desde IP bloqueada: ' . $request->ip());
            
            // Registrar el incidente de seguridad
            $this->securityService->logSecurityIncident(
                'blocked_ip_access',
                'Intento de acceso desde IP bloqueada',
                ['ip_address' => $request->ip()],
                null,
                'high'
            );
            
            // Devolver respuesta de acceso denegado
            return response()->json([
                'message' => 'Acceso denegado. Tu dirección IP ha sido bloqueada temporalmente debido a actividad sospechosa.'
            ], 403);
        }
        
        // Analizar la solicitud en busca de patrones de ataque
        $attackDetection = $this->securityService->analyzeRequest($request);
        
        if ($attackDetection) {
            // Si se detectó un ataque SQL Injection o Command Injection, bloquear inmediatamente
            if (isset($attackDetection['sql_injection']) || isset($attackDetection['command_injection'])) {
                Log::alert('Ataque detectado y bloqueado: ' . json_encode($attackDetection));
                
                // Registrar el incidente de seguridad
                $this->securityService->logSecurityIncident(
                    'attack_detected',
                    'Ataque detectado y bloqueado',
                    ['attack_type' => array_keys($attackDetection)[0]],
                    Auth::user(),
                    'critical'
                );
                
                // Bloquear la IP temporalmente
                $this->securityService->blockIp($request->ip(), 'Ataque detectado', null, config('security.block_duration.attack_detected', 1440));
                
                return response()->json([
                    'message' => 'Acceso denegado. Se ha detectado un intento de ataque.'
                ], 403);
            }
            
            // Para otros tipos de ataques, registrar pero permitir continuar (con monitoreo)
            Log::warning('Posible ataque detectado: ' . json_encode($attackDetection));
            
            // Registrar el incidente de seguridad
            $this->securityService->logSecurityIncident(
                'possible_attack',
                'Posible ataque detectado',
                ['attack_type' => array_keys($attackDetection)[0]],
                Auth::user(),
                'high'
            );
        }
        
        // Verificar actividad anómala para usuarios autenticados
        if (Auth::check()) {
            $user = Auth::user();
            $anomalies = $this->securityService->detectAnomalies($user, $request);
            
            if ($anomalies) {
                // Registrar las anomalías pero permitir continuar
                Log::notice('Actividad anómala detectada para el usuario #' . $user->id . ': ' . json_encode($anomalies));
                
                // Registrar el incidente de seguridad
                $this->securityService->logSecurityIncident(
                    'anomalous_activity',
                    'Actividad anómala detectada',
                    ['anomalies' => $anomalies],
                    $user,
                    'medium'
                );
                
                // Si hay cambios significativos de ubicación o dispositivo, podríamos
                // forzar una verificación adicional aquí
                if (isset($anomalies['location_change']) || isset($anomalies['device_change'])) {
                    // Almacenar información en la sesión para posible verificación adicional
                    session(['security_verification_required' => true, 'security_anomalies' => $anomalies]);
                }
            }
        }
        
        // Procesar la solicitud
        $response = $next($request);
        
        // Analizar la respuesta si es necesario
        // ...
        
        return $response;
    }
}
