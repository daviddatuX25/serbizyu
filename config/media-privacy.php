<?php

use App\Domains\Users\Models\UserVerification;
use App\Domains\Listings\Models\OpenOffer;

return [
    /*
    |--------------------------------------------------------------------------
    | Media Privacy Settings
    |--------------------------------------------------------------------------
    |
    | This array maps models to their default media privacy.
    | Supported values: 'public', 'private'.
    |
    | - 'public': Media can be viewed by anyone. No authorization checks.
    | - 'private': Media can only be viewed by authorized users.
    |              This requires a policy to be defined for the Media model.
    |
    */

    'default' => 'private',

    'models' => [
        UserVerification::class => 'private',
        OpenOffer::class => 'public',
        // Example of a public model
        // \App\Domains\Listings\Models\Service::class => 'public',
    ],
];
