<?php

return [
    // الإعدادات العامة للذكاء الاصطناعي
    'general' => [
        'enabled' => true,
        'debug_mode' => env('AI_DEBUG_MODE', false),
        'log_level' => env('AI_LOG_LEVEL', 'info'),
    ],

    // إعدادات روبوت المحادثة
    'chatbot' => [
        'provider' => 'openai',
        'model' => 'gpt-4-turbo',
        'temperature' => 0.7,
        'max_tokens' => 150,
        'context_window' => 4096,
    ],

    // إعدادات الرقابة على المحتوى
    'content_moderation' => [
        'enabled' => true,
        'sensitivity' => 'medium',
        'blocked_categories' => [
            'explicit', 
            'violent', 
            'hate_speech',
            'discrimination'
        ],
        'auto_block_threshold' => 0.8,
    ],

    // إعدادات تحليل المحادثات
    'conversation_analysis' => [
        'sentiment_enabled' => true,
        'entity_extraction' => true,
        'language_detection' => true,
        'context_depth' => 10,
    ],

    // إعدادات توليد الردود
    'reply_generator' => [
        'personalization_enabled' => true,
        'suggestion_count' => 3,
        'context_sensitivity' => 0.6,
    ],

    // إعدادات الترجمة
    'translation' => [
        'default_source_language' => 'auto',
        'default_target_language' => 'ar',
        'supported_languages' => [
            'ar', 'en', 'fr', 'tr', 'ur'
        ],
    ],

    // مزودي خدمات الذكاء الاصطناعي
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY', ''),
            'organization' => env('OPENAI_ORG_ID', ''),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],
        'google' => [
            'api_key' => env('GOOGLE_AI_API_KEY', ''),
        ],
    ],

    // إعدادات الأمان والتحقق
    'security' => [
        'rate_limit' => [
            'requests_per_minute' => 100,
            'requests_per_hour' => 1000,
        ],
        'request_validation' => [
            'enabled' => true,
            'max_input_length' => 5000,
            'block_suspicious_patterns' => true,
        ],
    ],

    // إعدادات التكلفة والفوترة
    'cost_management' => [
        'enabled' => true,
        'input_token_cost' => 0.00003,
        'output_token_cost' => 0.00006,
        'monthly_budget_limit' => 100, // دولار
        'alert_threshold' => 0.8,
    ],
];
