<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình tích hợp cổng thanh toán VNPay
    | Đăng ký tài khoản tại: https://sandbox.vnpayment.vn/
    |
    */

    'vnpay' => [
        // Terminal ID (Mã website) - Lấy từ VNPay sandbox/production
        'tmn_code' => env('VNPAY_TMN_CODE', 'YOUR_TMN_CODE'),
        
        // Secret Key để mã hóa dữ liệu - Lấy từ VNPay
        'hash_secret' => env('VNPAY_HASH_SECRET', 'YOUR_HASH_SECRET'),
        
        // URL thanh toán VNPay
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        
        // URL return sau khi thanh toán (sinh viên sẽ quay lại đây)
        'return_url' => env('VNPAY_RETURN_URL', 'http://localhost:8000/sinh-vien/hoc-phi/payment-return'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MoMo Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình tích hợp cổng thanh toán MoMo
    | Đăng ký tài khoản tại: https://business.momo.vn/
    |
    */

    'momo' => [
        // Partner Code - Mã đối tác (lấy từ MoMo)
        'partner_code' => env('MOMO_PARTNER_CODE', 'YOUR_PARTNER_CODE'),
        
        // Access Key - Khóa truy cập
        'access_key' => env('MOMO_ACCESS_KEY', 'YOUR_ACCESS_KEY'),
        
        // Secret Key - Khóa bí mật để mã hóa
        'secret_key' => env('MOMO_SECRET_KEY', 'YOUR_SECRET_KEY'),
        
        // API Endpoint
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        
        // Return URL - Sinh viên quay lại sau khi thanh toán
        'return_url' => env('MOMO_RETURN_URL', 'http://localhost:8000/sinh-vien/hoc-phi/payment-return'),
        
        // Notify URL - MoMo gửi IPN (server-to-server)
        'notify_url' => env('MOMO_NOTIFY_URL', 'http://localhost:8000/sinh-vien/hoc-phi/payment-callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        // Số tiền tối thiểu cho mỗi giao dịch (VNĐ)
        'min_amount' => 10000,
        
        // Số tiền tối đa cho mỗi giao dịch (VNĐ)
        'max_amount' => 500000000,
        
        // Timeout cho transaction (phút)
        'transaction_timeout' => 15,
        
        // Cho phép thanh toán online
        'enabled' => env('PAYMENT_ENABLED', true),
        
        // Các gateway đang active
        'active_gateways' => ['vnpay', 'momo'],
    ],

];
