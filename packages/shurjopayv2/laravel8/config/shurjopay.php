<?php

return [
    'apiCredentials' => [
        'username' => env('MERCHANT_USERNAME'),
        'password' => env('MERCHANT_PASSWORD'),
        'prefix' => env('MERCHANT_PREFIX'),
        'return_url' => env('MERCHANT_RETURN_URL'),
        'cancel_url' => env('MERCHANT_CANCEL_URL'),
        'base_url' => env('ENGINE_URL'),
    ],
];
