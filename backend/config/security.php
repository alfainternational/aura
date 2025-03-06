<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Seguridad
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene la configuración relacionada con la seguridad
    | de la aplicación, incluyendo la detección de intrusiones y la
    | gestión de incidentes de seguridad.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    |
    | Define security settings for user authentication and access control.
    |
    */
    'auth' => [
        'password' => [
            'min_length' => 8,
            'max_length' => 64,
            'require_complexity' => true,
            'complexity_rules' => [
                'uppercase' => 1,
                'lowercase' => 1,
                'numbers' => 1,
                'special_chars' => 1
            ],
            'password_history' => 5,  // Prevent reusing last 5 passwords
            'reset_token_expiry' => 60  // Minutes
        ],
        'login' => [
            'max_attempts' => 5,
            'lockout_duration' => 15,  // Minutes
            'two_factor_enabled' => true,
            'two_factor_methods' => ['email', 'sms', 'authenticator_app']
        ],
        'session' => [
            'max_concurrent_sessions' => 3,
            'idle_timeout' => 30,  // Minutes
            'absolute_timeout' => 480  // 8 hours
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Identity Verification
    |--------------------------------------------------------------------------
    |
    | Define security settings for user identity verification.
    |
    */
    'verification' => [
        'kyc' => [
            'enabled' => true,
            'required_documents' => ['national_id', 'passport'],
            'selfie_required' => true,
            'max_verification_attempts' => 3
        ],
        'location' => [
            'geofencing_enabled' => true,
            'allowed_countries' => ['السودان', 'السعودية'],
            'ip_validation' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Protection
    |--------------------------------------------------------------------------
    |
    | Define data protection and privacy settings.
    |
    */
    'data_protection' => [
        'encryption' => [
            'personal_data' => true,
            'message_content' => true,
            'call_logs' => true
        ],
        'data_retention' => [
            'user_data_lifetime' => 365,  // Days
            'message_lifetime' => 90,  // Days
            'call_logs_lifetime' => 180  // Days
        ],
        'anonymization' => [
            'enabled' => true,
            'fields' => ['phone', 'email']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Define rate limiting rules for different application endpoints.
    |
    */
    'rate_limiting' => [
        'login' => [
            'limit' => 5,
            'decay_minutes' => 1
        ],
        'registration' => [
            'limit' => 3,
            'decay_minutes' => 15
        ],
        'message_send' => [
            'limit' => 50,
            'decay_minutes' => 1
        ],
        'call_initiate' => [
            'limit' => 10,
            'decay_minutes' => 1
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Threat Detection
    |--------------------------------------------------------------------------
    |
    | Define settings for detecting and preventing potential security threats.
    |
    */
    'threat_detection' => [
        'suspicious_login_detection' => true,
        'unusual_activity_threshold' => 3,
        'block_disposable_emails' => true,
        'block_vpn_ips' => true,
        'block_tor_exit_nodes' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Lista Negra de IPs
    |--------------------------------------------------------------------------
    |
    | Esta lista contiene direcciones IP que serán bloqueadas permanentemente.
    | Las IPs en esta lista no podrán acceder a la aplicación.
    |
    */
    'blacklisted_ips' => [
        // Ejemplos:
        // '192.168.1.1',
        // '10.0.0.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Umbrales de Detección
    |--------------------------------------------------------------------------
    |
    | Estos umbrales determinan cuándo se debe considerar que una actividad
    | es sospechosa y potencialmente maliciosa.
    |
    */
    'thresholds' => [
        'login_attempts' => env('SECURITY_THRESHOLD_LOGIN', 5),
        'password_reset_attempts' => env('SECURITY_THRESHOLD_PASSWORD_RESET', 3),
        'api_requests' => env('SECURITY_THRESHOLD_API_REQUESTS', 100),
        'admin_access_attempts' => env('SECURITY_THRESHOLD_ADMIN_ACCESS', 3),
        'suspicious_activity' => env('SECURITY_THRESHOLD_SUSPICIOUS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Duración de Bloqueos
    |--------------------------------------------------------------------------
    |
    | Duración (en minutos) de los bloqueos temporales para diferentes
    | tipos de incidentes de seguridad.
    |
    */
    'block_duration' => [
        'login_attempts' => env('SECURITY_BLOCK_LOGIN', 30),
        'password_reset_attempts' => env('SECURITY_BLOCK_PASSWORD_RESET', 60),
        'api_abuse' => env('SECURITY_BLOCK_API_ABUSE', 120),
        'attack_detected' => env('SECURITY_BLOCK_ATTACK', 1440), // 24 horas
    ],

    /*
    |--------------------------------------------------------------------------
    | Notificaciones
    |--------------------------------------------------------------------------
    |
    | Configuración para las notificaciones de incidentes de seguridad.
    |
    */
    'notifications' => [
        'email' => [
            'enabled' => env('SECURITY_NOTIFY_EMAIL', true),
            'recipients' => explode(',', env('SECURITY_EMAIL_RECIPIENTS', 'admin@example.com')),
            'severity_level' => env('SECURITY_EMAIL_SEVERITY', 'high'), // 'low', 'medium', 'high', 'critical'
        ],
        'slack' => [
            'enabled' => env('SECURITY_NOTIFY_SLACK', false),
            'webhook' => env('SECURITY_SLACK_WEBHOOK'),
            'channel' => env('SECURITY_SLACK_CHANNEL', '#security-alerts'),
            'severity_level' => env('SECURITY_SLACK_SEVERITY', 'medium'), // 'low', 'medium', 'high', 'critical'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Registro de Actividad
    |--------------------------------------------------------------------------
    |
    | Configuración para el registro de actividad de seguridad.
    |
    */
    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'channels' => explode(',', env('SECURITY_LOG_CHANNELS', 'security,daily')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Geolocalización
    |--------------------------------------------------------------------------
    |
    | Configuración para el servicio de geolocalización.
    |
    */
    'geolocation' => [
        'enabled' => env('SECURITY_GEOLOCATION_ENABLED', true),
        'service' => env('SECURITY_GEOLOCATION_SERVICE', 'ip-api'), // 'ip-api', 'maxmind', 'ipinfo'
        'api_key' => env('SECURITY_GEOLOCATION_API_KEY'),
        'cache_duration' => env('SECURITY_GEOLOCATION_CACHE', 1440), // 24 horas
    ],

    /*
    |--------------------------------------------------------------------------
    | Verificación Adicional
    |--------------------------------------------------------------------------
    |
    | Configuración para la verificación adicional cuando se detecta
    | actividad anómala.
    |
    */
    'additional_verification' => [
        'enabled' => env('SECURITY_ADDITIONAL_VERIFICATION', true),
        'methods' => explode(',', env('SECURITY_VERIFICATION_METHODS', 'email,sms,totp')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Protección contra Ataques
    |--------------------------------------------------------------------------
    |
    | Configuración para la protección contra diferentes tipos de ataques.
    |
    */
    'attack_protection' => [
        'sql_injection' => env('SECURITY_PROTECT_SQL_INJECTION', true),
        'xss' => env('SECURITY_PROTECT_XSS', true),
        'csrf' => env('SECURITY_PROTECT_CSRF', true),
        'path_traversal' => env('SECURITY_PROTECT_PATH_TRAVERSAL', true),
        'command_injection' => env('SECURITY_PROTECT_COMMAND_INJECTION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Análisis de Comportamiento
    |--------------------------------------------------------------------------
    |
    | Configuración para el análisis de comportamiento de usuarios.
    |
    */
    'behavior_analysis' => [
        'enabled' => env('SECURITY_BEHAVIOR_ANALYSIS', true),
        'sensitivity' => env('SECURITY_BEHAVIOR_SENSITIVITY', 'medium'), // 'low', 'medium', 'high'
        'learning_period' => env('SECURITY_BEHAVIOR_LEARNING', 14), // días
    ],
];
