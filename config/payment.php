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
        'sandbox' => env('ZALOPAY_SANDBOX', true),
        // API v1 endpoints:
        // Sandbox: https://sandbox.zalopay.com.vn/v001/tpe
        // Production: https://zalopay.com.vn/v001/tpe
    ],

];
