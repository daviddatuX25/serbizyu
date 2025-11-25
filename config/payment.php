<?php

return [
    'mode' => env('PAYMENT_MODE', 'test'),
    'pay_first' => env('PAY_FIRST_ENABLED', true),
    'cash_enabled' => env('CASH_PAYMENT_ENABLED', true),

    'xendit' => [
        'api_key' => env('XENDIT_API_KEY'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    ],
    'platform_fee' => [
        'percentage' => 5, // 5%
    ],
];
