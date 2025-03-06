<?php

namespace App\Services\Security;

use App\Models\SecurityIncident;
use App\Models\User;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class SecurityService
{
    /**
     * Tiempo de expiración para el caché de intentos (en minutos).
     *
     * @var int
     */
    protected $cacheExpirationMinutes = 60;

    /**
     * Umbral de intentos fallidos antes de considerar una posible intrusión.
     *
     * @var array
     */
    protected $thresholds = [
        'login_attempts' => 5,
        'password_reset_attempts' => 3,
        'api_requests' => 100,
        'admin_access_attempts' => 3,
        'suspicious_activity' => 5,
    ];

    /**
     * Patrones de ataques conocidos para detectar en solicitudes.
     *
     * @var array
     */
    protected $attackPatterns = [
        'sql_injection' => [
            '/\s*SELECT\s+.*\s+FROM\s+/i',
            '/\s*INSERT\s+INTO\s+/i',
            '/\s*UPDATE\s+.*\s+SET\s+/i',
            '/\s*DELETE\s+FROM\s+/i',
            '/\s*DROP\s+TABLE\s+/i',
            '/\s*UNION\s+SELECT\s+/i',
            '/\'\s*OR\s*\'1\'\s*=\s*\'1/i',
            '/\'\s*OR\s*1\s*=\s*1/i',
        ],
        'xss' => [
            '/<script.*?>.*?<\/script>/is',
            '/javascript\s*:/i',
            '/on(load|click|mouseover|submit|focus|blur|change|select|error)=["\']/i',
            '/document\.(location|cookie|write)/i',
            '/eval\s*\(/i',
        ],
        'path_traversal' => [
            '/\.\.\//i',
            '/\.\.\\\/i',
        ],
        'command_injection' => [
            '/;\s*rm\s+-rf/i',
            '/;\s*wget/i',
            '/;\s*curl/i',
            '/;\s*bash/i',
            '/;\s*chmod/i',
            '/\|\s*bash/i',
        ],
    ];

    /**
     * Lista de IPs bloqueadas.
     *
     * @var array
     */
    protected $blacklistedIps = [];

    /**
     * Lista de agentes de usuario sospechosos.
     *
     * @var array
     */
    protected $suspiciousUserAgents = [
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'dirbuster',
        'hydra',
        'burpsuite',
        'wget',
        'curl',
        'python-requests',
    ];

    /**
     * Crear una nueva instancia del servicio.
     *
     * @return void
     */
    public function __construct()
    {
        // Cargar IPs bloqueadas desde la configuración o base de datos
        $this->blacklistedIps = config('security.blacklisted_ips', []);
    }

    /**
     * Registrar un intento fallido.
     *
     * @param string $type
     * @param string $identifier
     * @param Request|null $request
     * @return int
     */
    public function logFailedAttempt(string $type, string $identifier, ?Request $request = null): int
    {
        $key = "failed_attempts:{$type}:{$identifier}";
        $attempts = Cache::get($key, 0) + 1;
        
        Cache::put($key, $attempts, now()->addMinutes($this->cacheExpirationMinutes));
        
        // Si se supera el umbral, registrar un incidente de seguridad
        if (isset($this->thresholds[$type]) && $attempts >= $this->thresholds[$type]) {
            $this->logSecurityIncident(
                'threshold_exceeded',
                "Se superó el umbral de {$this->thresholds[$type]} intentos fallidos para {$type}",
                ['attempts' => $attempts, 'identifier' => $identifier],
                null,
                'medium'
            );
        }
        
        return $attempts;
    }

    /**
     * Restablecer los intentos fallidos.
     *
     * @param string $type
     * @param string $identifier
     * @return void
     */
    public function resetFailedAttempts(string $type, string $identifier): void
    {
        $key = "failed_attempts:{$type}:{$identifier}";
        Cache::forget($key);
    }

    /**
     * Verificar si una IP está bloqueada.
     *
     * @param string $ip
     * @return bool
     */
    public function isIpBlocked(string $ip): bool
    {
        // Verificar lista negra estática
        if (in_array($ip, $this->blacklistedIps)) {
            return true;
        }
        
        // Verificar en la base de datos
        return BlockedIp::where('ip_address', $ip)
            ->active()
            ->exists();
    }

    /**
     * Bloquear una IP temporalmente.
     *
     * @param string $ip
     * @param string $reason
     * @param int|null $securityIncidentId
     * @param int|null $minutes
     * @return BlockedIp
     */
    public function blockIp(string $ip, string $reason, ?int $securityIncidentId = null, ?int $minutes = 60): BlockedIp
    {
        $expiresAt = $minutes ? now()->addMinutes($minutes) : null;
        
        // Crear o actualizar el registro de IP bloqueada
        $blockedIp = BlockedIp::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'security_incident_id' => $securityIncidentId,
                'expires_at' => $expiresAt,
            ]
        );
        
        // También almacenar en caché para rendimiento
        if ($expiresAt) {
            Cache::put("blocked_ip:{$ip}", true, $expiresAt);
        } else {
            Cache::forever("blocked_ip:{$ip}", true);
        }
        
        Log::warning("IP bloqueada: {$ip} - Razón: {$reason} - " . ($expiresAt ? "Expira: {$expiresAt}" : "Bloqueo permanente"));
        
        return $blockedIp;
    }

    /**
     * Registrar un incidente de seguridad.
     *
     * @param string $type
     * @param string $description
     * @param array $data
     * @param User|null $user
     * @param string $severity
     * @return SecurityIncident
     */
    public function logSecurityIncident(
        string $type,
        string $description,
        array $data = [],
        ?User $user = null,
        string $severity = 'medium'
    ): SecurityIncident {
        $requestData = null;
        $ipAddress = null;
        $userAgent = null;
        
        // Obtener datos de la solicitud actual si está disponible
        if (request()) {
            $requestData = [
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'headers' => request()->headers->all(),
                'params' => request()->all(),
            ];
            
            $ipAddress = request()->ip();
            $userAgent = request()->userAgent();
        }
        
        // Crear el registro de incidente
        $incident = SecurityIncident::create([
            'type' => $type,
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_id' => $user ? $user->id : null,
            'user_agent' => $userAgent,
            'severity' => $severity,
            'request_data' => $requestData,
            'additional_data' => $data,
        ]);
        
        // Registrar en el log según la severidad
        $logMethod = 'info';
        
        switch ($severity) {
            case 'critical':
                $logMethod = 'critical';
                break;
            case 'high':
                $logMethod = 'alert';
                break;
            case 'medium':
                $logMethod = 'warning';
                break;
            case 'low':
                $logMethod = 'notice';
                break;
        }
        
        Log::$logMethod("Incidente de seguridad: {$type} - {$description}", [
            'incident_id' => $incident->id,
            'ip' => $ipAddress,
            'user_id' => $user ? $user->id : null,
        ]);
        
        return $incident;
    }

    /**
     * Analizar una solicitud en busca de patrones de ataque.
     *
     * @param Request $request
     * @return array|null
     */
    public function analyzeRequest(Request $request): ?array
    {
        $detectedAttacks = [];
        
        // Verificar IP bloqueada
        if ($this->isIpBlocked($request->ip())) {
            return ['blocked_ip' => true];
        }
        
        // Verificar agente de usuario sospechoso
        $userAgent = $request->userAgent();
        foreach ($this->suspiciousUserAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $detectedAttacks['suspicious_user_agent'] = $agent;
                break;
            }
        }
        
        // Analizar parámetros de la solicitud
        $allParams = array_merge(
            $request->all(),
            $request->query(),
            $request->route() ? $request->route()->parameters() : []
        );
        
        // Convertir a JSON para análisis
        $jsonParams = json_encode($allParams, JSON_UNESCAPED_SLASHES);
        
        // Verificar patrones de ataque
        foreach ($this->attackPatterns as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $jsonParams)) {
                    $detectedAttacks[$attackType] = true;
                    break;
                }
            }
        }
        
        // Si se detectaron ataques, registrar incidente
        if (!empty($detectedAttacks)) {
            $this->logSecurityIncident('attack_pattern_detected', 'Se detectó un patrón de ataque en la solicitud', $detectedAttacks, null, 'high');
            
            // Bloquear IP si se detecta un ataque grave
            if (isset($detectedAttacks['sql_injection']) || 
                isset($detectedAttacks['command_injection'])) {
                $this->blockIp($request->ip(), 'Ataque grave detectado', null, 120); // Bloquear por 2 horas
            }
        }
        
        return !empty($detectedAttacks) ? $detectedAttacks : null;
    }

    /**
     * Detectar actividad anómala para un usuario.
     *
     * @param User $user
     * @param Request $request
     * @return array|null
     */
    public function detectAnomalies(User $user, Request $request): ?array
    {
        $anomalies = [];
        
        // Obtener datos de geolocalización actuales
        $currentLocation = $this->getLocationData($request->ip());
        
        // Obtener datos del dispositivo actual
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        $currentDevice = [
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'is_desktop' => $agent->isDesktop(),
        ];
        
        // Obtener últimas sesiones del usuario
        $lastSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->limit(5)
            ->get();
        
        // Verificar cambios de ubicación
        if ($currentLocation && count($lastSessions) > 0) {
            $lastSession = $lastSessions->first();
            
            // Si hay datos de ubicación en la última sesión
            if (isset($lastSession->payload) && $payload = json_decode($lastSession->payload, true)) {
                if (isset($payload['location']) && $lastLocation = $payload['location']) {
                    // Calcular distancia entre ubicaciones
                    $distance = $this->calculateDistance(
                        $currentLocation['lat'] ?? 0, 
                        $currentLocation['lon'] ?? 0,
                        $lastLocation['lat'] ?? 0, 
                        $lastLocation['lon'] ?? 0
                    );
                    
                    // Si la distancia es significativa (más de 500 km) y el tiempo entre sesiones es corto
                    if ($distance > 500 && (time() - $lastSession->last_activity) < 3600) {
                        $anomalies['location_change'] = [
                            'previous' => $lastLocation,
                            'current' => $currentLocation,
                            'distance' => $distance,
                            'time_diff' => time() - $lastSession->last_activity,
                        ];
                    }
                }
            }
        }
        
        // Verificar cambios de dispositivo
        if (count($lastSessions) > 0) {
            $lastSession = $lastSessions->first();
            
            if (isset($lastSession->payload) && $payload = json_decode($lastSession->payload, true)) {
                if (isset($payload['device']) && $lastDevice = $payload['device']) {
                    // Verificar si hay cambios significativos en el dispositivo
                    if ($lastDevice['browser'] != $currentDevice['browser'] || 
                        $lastDevice['platform'] != $currentDevice['platform'] ||
                        $lastDevice['is_mobile'] != $currentDevice['is_mobile']) {
                        
                        $anomalies['device_change'] = [
                            'previous' => $lastDevice,
                            'current' => $currentDevice,
                        ];
                    }
                }
            }
        }
        
        // Verificar patrones de comportamiento anómalos
        $behaviorAnomalies = $this->analyzeUserBehavior($user);
        if ($behaviorAnomalies) {
            $anomalies = array_merge($anomalies, $behaviorAnomalies);
        }
        
        // Si se detectaron anomalías, registrar incidente
        if (!empty($anomalies)) {
            $this->logSecurityIncident('anomalous_activity', 'Se detectó actividad anómala', $anomalies, $user, 'medium');
        }
        
        return !empty($anomalies) ? $anomalies : null;
    }

    /**
     * Obtener datos de geolocalización para una IP.
     *
     * @param string $ip
     * @return array|null
     */
    protected function getLocationData(string $ip): ?array
    {
        // Ignorar IPs locales
        if (in_array($ip, ['127.0.0.1', '::1']) || strpos($ip, '192.168.') === 0) {
            return null;
        }
        
        // Intentar obtener de caché primero
        $cacheKey = "ip_location:{$ip}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            // Usar un servicio gratuito para geolocalización
            $response = file_get_contents("http://ip-api.com/json/{$ip}");
            $data = json_decode($response, true);
            
            if ($data && isset($data['status']) && $data['status'] === 'success') {
                $locationData = [
                    'country' => $data['country'] ?? null,
                    'country_code' => $data['countryCode'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'isp' => $data['isp'] ?? null,
                ];
                
                // Guardar en caché por 1 día
                Cache::put($cacheKey, $locationData, now()->addDay());
                
                return $locationData;
            }
        } catch (\Exception $e) {
            Log::error("Error al obtener datos de geolocalización: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Calcular la distancia entre dos puntos geográficos.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // en kilómetros
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Convertir grados a radianes.
     *
     * @param float $deg
     * @return float
     */
    protected function deg2rad(float $deg): float
    {
        return $deg * pi() / 180;
    }

    /**
     * Analizar patrones de comportamiento de usuario para detectar anomalías.
     *
     * @param User $user
     * @return array|null
     */
    public function analyzeUserBehavior(User $user): ?array
    {
        $anomalies = [];
        
        // Obtener historial reciente de actividad
        $recentActivity = DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        
        if ($recentActivity->isEmpty()) {
            return null;
        }
        
        // Analizar patrones de tiempo
        $timePatterns = $this->analyzeTimePatterns($recentActivity);
        if (!empty($timePatterns)) {
            $anomalies['time_patterns'] = $timePatterns;
        }
        
        // Analizar patrones de acceso a recursos
        $resourcePatterns = $this->analyzeResourcePatterns($recentActivity, $user);
        if (!empty($resourcePatterns)) {
            $anomalies['resource_patterns'] = $resourcePatterns;
        }
        
        // Analizar velocidad de navegación
        $navigationSpeed = $this->analyzeNavigationSpeed($recentActivity);
        if (!empty($navigationSpeed)) {
            $anomalies['navigation_speed'] = $navigationSpeed;
        }
        
        return !empty($anomalies) ? $anomalies : null;
    }

    /**
     * Analizar patrones de tiempo en la actividad del usuario.
     *
     * @param \Illuminate\Support\Collection $activities
     * @return array|null
     */
    protected function analyzeTimePatterns($activities): ?array
    {
        // Implementación simplificada
        return null;
    }

    /**
     * Analizar patrones de acceso a recursos.
     *
     * @param \Illuminate\Support\Collection $activities
     * @param User $user
     * @return array|null
     */
    protected function analyzeResourcePatterns($activities, User $user): ?array
    {
        // Implementación simplificada
        return null;
    }

    /**
     * Analizar velocidad de navegación.
     *
     * @param \Illuminate\Support\Collection $activities
     * @return array|null
     */
    protected function analyzeNavigationSpeed($activities): ?array
    {
        // Implementación simplificada
        return null;
    }

    /**
     * Generar un informe de seguridad.
     *
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @return array
     */
    public function generateSecurityReport(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = SecurityIncident::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $incidents = $query->get();
        
        // Agrupar por tipo de incidente
        $incidentsByType = $incidents->groupBy('type')->map->count();
        
        // Agrupar por severidad
        $incidentsBySeverity = $incidents->groupBy('severity')->map->count();
        
        // Agrupar por IP
        $incidentsByIp = $incidents->groupBy('ip_address')->map->count();
        
        // Agrupar por usuario
        $incidentsByUser = $incidents->whereNotNull('user_id')->groupBy('user_id')->map->count();
        
        // Calcular tendencias por día
        $incidentsByDay = $incidents->groupBy(function ($incident) {
            return $incident->created_at->format('Y-m-d');
        })->map->count();
        
        // Generar array de fechas para el gráfico
        $dates = [];
        $incidentsByDate = [];
        
        if ($startDate && $endDate) {
            $period = new \DatePeriod(
                $startDate,
                new \DateInterval('P1D'),
                $endDate->modify('+1 day')
            );
            
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $dates[] = $date->format('d/m');
                $incidentsByDate[] = $incidentsByDay[$dateStr] ?? 0;
            }
        }
        
        return [
            'total_incidents' => $incidents->count(),
            'unresolved_incidents' => $incidents->whereNull('resolved_at')->count(),
            'incidents_by_type' => $incidentsByType,
            'incidents_by_severity' => $incidentsBySeverity,
            'top_offending_ips' => $incidentsByIp->sortDesc()->take(10),
            'top_affected_users' => $incidentsByUser->sortDesc()->take(10),
            'incidents_by_date' => $incidentsByDate,
            'dates' => $dates,
        ];
    }
}
