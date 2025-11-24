<?php

return [
    'xendit' => [
        'api_key' => env('XENDIT_API_KEY'),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN'),
    ],
    'platform_fee' => [
        'percentage' => 5, // 5%
    ],
];
