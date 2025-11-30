<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ZaloPay Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for ZaloPay payment gateway integration
    | Get your credentials from: https://docs.zalopay.vn/
    |
    */

    'zalopay' => [
        'app_id' => env('ZALOPAY_APP_ID', ''),
        'key1' => env('ZALOPAY_KEY1', ''),
        'key2' => env('ZALOPAY_KEY2', ''),
        'endpoint' => env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2'), // Sandbox
        // Production: https://openapi.zalopay.vn/v2
    ],

];
