<?php

return [
    'mode' => env('PAYMENT_MODE', 'test'),
    
    'is_test_mode' => env('APP_ENV') === 'local' || env('PAYMENT_MODE') === 'test',
    
    'pay_first' => env('PAY_FIRST_ENABLED', false),
    
    'cash_enabled' => env('CASH_PAYMENT_ENABLED', true),

    'xendit' => [
        'api_key' => env('XENDIT_API_KEY'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
        'secret_key' => env('XENDIT_SECRET_KEY'),
    ],
    
    'platform_fee' => [
        'percentage' => env('PLATFORM_FEE_PERCENTAGE', 5),
    ],
    
    'handshake_ttl' => 3600, // 1 hour
];

