<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Countries and Regions
    |--------------------------------------------------------------------------
    |
    | Define the countries and regions supported by the Aura application.
    | This configuration helps in geolocation, registration, and user management.
    |
    */
    'supported_countries' => [
        'السودان' => [
            'code' => 'SD',
            'phone_code' => '+249',
            'cities' => [
                'الخرطوم',
                'أم درمان', 
                'بحري', 
                'الجزيرة', 
                'كسلا'
            ]
        ],
        'السعودية' => [
            'code' => 'SA',
            'phone_code' => '+966',
            'cities' => [
                'الرياض', 
                'جدة', 
                'مكة المكرمة', 
                'المدينة المنورة', 
                'الدمام'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Language Configuration
    |--------------------------------------------------------------------------
    |
    | Define the primary and fallback languages for the application.
    |
    */
    'languages' => [
        'primary' => 'ar',
        'fallback' => 'ar',
        'available' => [
            'ar' => 'العربية',
            'en' => 'English'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Rules
    |--------------------------------------------------------------------------
    |
    | Define specific localization rules and restrictions.
    |
    */
    'rules' => [
        'default_timezone' => 'Africa/Khartoum',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'currency' => [
            'default' => 'SDG',
            'supported' => ['SDG', 'SAR', 'USD']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | User Registration Restrictions
    |--------------------------------------------------------------------------
    |
    | Define geographical restrictions for user registration.
    |
    */
    'registration' => [
        'allowed_countries' => ['السودان', 'السعودية'],
        'minimum_age' => 18,
        'require_location_verification' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Messaging and Communication Localization
    |--------------------------------------------------------------------------
    |
    | Define localization settings for messaging and communication features.
    |
    */
    'messaging' => [
        'default_language' => 'ar',
        'rtl_languages' => ['ar'],
        'text_direction' => 'rtl'
    ]
];
