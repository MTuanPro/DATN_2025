<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatbot Default Response
    |--------------------------------------------------------------------------
    |
    | Câu trả lời mặc định khi bot không tìm thấy câu trả lời phù hợp
    |
    */
    'default_response' => env('CHATBOT_DEFAULT_RESPONSE', 
        'Xin lỗi, tôi chưa có thông tin về câu hỏi này. Bạn có thể liên hệ phòng Đào tạo để được hỗ trợ chi tiết hơn.'),

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Thông tin liên hệ hiển thị khi bot không trả lời được
    |
    */
    'hotline' => env('CHATBOT_HOTLINE', '024.xxxx.xxxx'),
    'email' => env('CHATBOT_EMAIL', 'daotao@smis.edu.vn'),

    /*
    |--------------------------------------------------------------------------
    | Matching Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình thuật toán matching
    |
    */
    // UPGRADE: Tăng từ 0.3 lên 0.6 để giảm câu trả lời sai
    'similarity_threshold' => env('CHATBOT_SIMILARITY_THRESHOLD', 0.6), // 60%
    'max_message_length' => env('CHATBOT_MAX_MESSAGE_LENGTH', 1000),
    'max_alternatives' => 3, // Số câu trả lời thay thế

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Giới hạn số lần gửi tin nhắn
    |
    */
    'rate_limit' => [
        'max_attempts' => 30, // Số request tối đa
        'decay_minutes' => 1, // Trong 1 phút
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Management
    |--------------------------------------------------------------------------
    |
    | Cấu hình quản lý context
    |
    */
    'context' => [
        'enabled' => true, // UPGRADE: Bật context management
        'ttl' => 3600, // Cache context trong 1 giờ
        'max_topics' => 5, // Lưu tối đa 5 topics gần nhất
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Cấu hình logging
    |
    */
    'logging' => [
        'enabled' => env('CHATBOT_LOGGING_ENABLED', true),
        'log_no_match' => true, // Log câu hỏi không match
        'log_errors' => true, // Log errors
    ],
];

