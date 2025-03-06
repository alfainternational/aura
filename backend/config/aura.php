<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de la plataforma Aura
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene configuraciones específicas de la plataforma Aura,
    | incluyendo límites de transacciones, configuración de OTP y más.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuración regional predeterminada
    |--------------------------------------------------------------------------
    */
    'region' => [
        'default_country' => 'SD', // Sudán como país predeterminado
        'default_currency' => 'SDG', // Libra sudanesa como moneda predeterminada
        'default_language' => 'ar', // Árabe como idioma predeterminado
        'default_timezone' => 'Africa/Khartoum', // Zona horaria predeterminada
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de OTP
    |--------------------------------------------------------------------------
    */
    'otp' => [
        'length' => 6, // Longitud del código OTP
        'expiry' => 15, // Tiempo de expiración en minutos
        'max_attempts' => 3, // Número máximo de intentos permitidos
        'purposes' => [
            'transaction', // Para verificar transacciones
            'login', // Para login de doble factor
            'password_reset', // Para restablecer contraseña
            'account_verification', // Para verificar cuenta
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de transacciones
    |--------------------------------------------------------------------------
    */
    'transactions' => [
        'otp_threshold' => env('TRANSACTION_OTP_THRESHOLD', 1000), // Monto a partir del cual se requiere OTP
        'daily_limit' => env('TRANSACTION_DAILY_LIMIT', 5000), // Límite diario de transacciones
        'transaction_fee' => env('TRANSACTION_FEE', 0.01), // Tarifa de transacción (1%)
        'min_amount' => env('TRANSACTION_MIN_AMOUNT', 10), // Monto mínimo de transacción
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de pagos a plazos
    |--------------------------------------------------------------------------
    */
    'installments' => [
        'enabled' => true, // Habilitar sistema de pagos a plazos
        'options' => [4, 6, 8, 12], // Opciones de cuotas disponibles
        'interest_rates' => [
            4 => 0, // 0% de interés para 4 cuotas
            6 => 0, // 0% de interés para 6 cuotas
            8 => 0, // 0% de interés para 8 cuotas
            12 => 0.05, // 5% de interés para 12 cuotas
        ],
        'min_amount' => env('INSTALLMENT_MIN_AMOUNT', 200), // Monto mínimo para pagos a plazos
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de integración bancaria
    |--------------------------------------------------------------------------
    */
    'banking' => [
        'enabled' => true, // Habilitar integración bancaria
        'webhook_secret' => env('BANKING_WEBHOOK_SECRET'), // Secreto para verificar webhooks
        'auto_verify_threshold' => env('BANKING_AUTO_VERIFY_THRESHOLD', 500), // Monto hasta el cual no se requiere OTP
    ],
];
