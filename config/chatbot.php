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

    /*
    |--------------------------------------------------------------------------
    | GPT Integration
    |--------------------------------------------------------------------------
    |
    | Cấu hình tích hợp GPT (OpenAI)
    |
    */
    'gpt' => [
        'enabled' => env('CHATBOT_GPT_ENABLED', false),
        'api_key' => env('OPENAI_API_KEY', ''),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'model' => env('CHATBOT_GPT_MODEL', 'gpt-3.5-turbo'),
        'max_tokens' => env('CHATBOT_GPT_MAX_TOKENS', 500),
        'temperature' => env('CHATBOT_GPT_TEMPERATURE', 0.7),
        'use_when_no_match' => env('CHATBOT_GPT_USE_WHEN_NO_MATCH', true), // Dùng GPT khi không tìm thấy trong KB
        'use_as_fallback' => env('CHATBOT_GPT_USE_AS_FALLBACK', true), // Dùng GPT làm fallback
        'min_similarity_for_gpt' => env('CHATBOT_GPT_MIN_SIMILARITY', 0.3), // Chỉ dùng GPT nếu similarity < 0.3
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini Integration
    |--------------------------------------------------------------------------
    |
    | Cấu hình tích hợp Google Gemini (Free tier available)
    |
    */
    'gemini' => [
        'enabled' => env('CHATBOT_GEMINI_ENABLED', false),
        'api_key' => env('GOOGLE_GEMINI_API_KEY', ''),
        'api_url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent'),
        'model' => env('CHATBOT_GEMINI_MODEL', 'gemini-2.5-flash'),
        'max_tokens' => env('CHATBOT_GEMINI_MAX_TOKENS', 1000),
        'temperature' => env('CHATBOT_GEMINI_TEMPERATURE', 0.7),
        'use_when_no_match' => env('CHATBOT_GEMINI_USE_WHEN_NO_MATCH', true), // Dùng Gemini khi không tìm thấy trong KB
        'use_as_fallback' => env('CHATBOT_GEMINI_USE_AS_FALLBACK', true), // Dùng Gemini làm fallback
        'min_similarity_for_gemini' => env('CHATBOT_GEMINI_MIN_SIMILARITY', 0.3), // Chỉ dùng Gemini nếu similarity < 0.3
    ],
];

