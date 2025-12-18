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
        // Callback URLs
        'return_url' => env('ZALOPAY_RETURN_URL', env('APP_URL') . '/payment/zalopay/callback'),
        'ipn_url' => env('ZALOPAY_IPN_URL', env('APP_URL') . '/payment/zalopay/callback'),
        // API v1 endpoints:
        // Sandbox: https://sandbox.zalopay.com.vn/v001/tpe
        // Production: https://zalopay.com.vn/v001/tpe
    ],

    'payos' => [
        'client_id' => env('PAYOS_CLIENT_ID', ''),
        'api_key' => env('PAYOS_API_KEY', ''),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY', ''),
        // Callback URLs
        'return_url' => env('PAYOS_RETURN_URL', env('APP_URL') . '/payment/payos/callback'),
        'cancel_url' => env('PAYOS_CANCEL_URL', env('APP_URL') . '/payment/payos/cancel'),
        'webhook_url' => env('PAYOS_WEBHOOK_URL', env('APP_URL') . '/api/payment/payos/webhook'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Casso Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Casso payment gateway integration
    | Casso là hệ thống nhận thông báo khi có tiền chuyển vào tài khoản ngân hàng
    | Get your credentials from: https://developer.casso.vn/
    |
    */

    'casso' => [
        'api_key' => env('CASSO_API_KEY', ''),
        // Webhook URL mà Casso sẽ gửi thông báo về
        'webhook_url' => env('CASSO_WEBHOOK_URL', env('APP_URL') . '/payment/casso/webhook'),
        // API endpoint
        'api_endpoint' => env('CASSO_API_ENDPOINT', 'https://oauth.casso.vn/v2'),
        // Tiền tố mã đơn hàng trong nội dung chuyển khoản (ví dụ: HP123)
        'memo_prefix' => env('CASSO_MEMO_PREFIX', 'HP'),
        // Số tiền chênh lệch tối đa được chấp nhận (VND)
        'acceptable_difference' => env('CASSO_ACCEPTABLE_DIFFERENCE', 10000),
    ],

];
