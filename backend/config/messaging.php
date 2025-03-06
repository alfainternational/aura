<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messaging Core Configuration
    |--------------------------------------------------------------------------
    |
    | Define core settings for text and media messaging in the Aura application.
    |
    */
    'core' => [
        'max_message_length' => 5000,
        'max_media_size' => 20 * 1024 * 1024,  // 20MB
        'supported_media_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'document' => ['pdf', 'doc', 'docx']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversation Settings
    |--------------------------------------------------------------------------
    |
    | Define settings for conversations and group messaging.
    |
    */
    'conversations' => [
        'max_participants' => 50,
        'max_group_name_length' => 50,
        'group_creation_restrictions' => [
            'min_participants' => 2,
            'max_participants' => 50
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Status and Delivery
    |--------------------------------------------------------------------------
    |
    | Define settings for message delivery and read receipts.
    |
    */
    'delivery' => [
        'read_receipts_enabled' => true,
        'delivery_receipts_enabled' => true,
        'message_expiration' => [
            'default' => 90,  // Days
            'ephemeral_messages' => 24  // Hours
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Filtering and Moderation
    |--------------------------------------------------------------------------
    |
    | Define settings for content filtering and moderation.
    |
    */
    'moderation' => [
        'profanity_filter_enabled' => true,
        'spam_detection' => [
            'enabled' => true,
            'max_messages_per_minute' => 10,
            'block_duration' => 60  // Minutes
        ],
        'blocked_content_types' => [
            'external_links' => true,
            'executable_files' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Search and Archiving
    |--------------------------------------------------------------------------
    |
    | Define settings for message search and archiving.
    |
    */
    'search' => [
        'message_indexing_enabled' => true,
        'search_history_limit' => 1000,
        'search_result_limit' => 100
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy Settings
    |--------------------------------------------------------------------------
    |
    | Define privacy-related messaging configurations.
    |
    */
    'privacy' => [
        'message_deletion' => [
            'for_self_enabled' => true,
            'for_everyone_enabled' => true,
            'time_limit_for_deletion' => 24  // Hours
        ],
        'blocking' => [
            'block_message_sending' => true,
            'block_call_receiving' => true
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance and Optimization
    |--------------------------------------------------------------------------
    |
    | Define performance-related messaging configurations.
    |
    */
    'performance' => [
        'message_batch_size' => 50,
        'typing_indicator_timeout' => 5,  // Seconds
        'connection_retry_interval' => 3,  // Seconds
        'message_sync_interval' => 30  // Seconds
    ]
];
