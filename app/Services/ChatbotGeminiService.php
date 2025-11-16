<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotGeminiService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;
    protected $maxTokens;
    protected $temperature;

    public function __construct()
    {
        $this->apiKey = config('chatbot.gemini.api_key');
        $this->apiUrl = config('chatbot.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent');
        $this->model = config('chatbot.gemini.model', 'gemini-2.5-flash');
        $this->maxTokens = config('chatbot.gemini.max_tokens', 1000);
        $this->temperature = config('chatbot.gemini.temperature', 0.7);
    }

    /**
     * Gửi câu hỏi đến Gemini và nhận câu trả lời
     * 
     * @param string $question Câu hỏi của user
     * @param array $context Context từ conversation (các câu hỏi/trả lời trước)
     * @param array $knowledgeBaseContext Thông tin từ knowledge base (nếu có)
     * @return array ['response' => string, 'tokens_used' => int, 'model' => string]
     */
    public function getResponse(string $question, array $context = [], array $knowledgeBaseContext = []): array
    {
        // Kiểm tra API key
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key not configured');
            return [
                'response' => null,
                'error' => 'Gemini service not configured',
                'tokens_used' => 0,
            ];
        }

        try {
            // Xây dựng prompt với context từ knowledge base
            $prompt = $this->buildPrompt($question, $context, $knowledgeBaseContext);
            
            // Build API URL với model - Sử dụng v1beta API
            $apiUrl = str_replace('{model}', $this->model, $this->apiUrl);
            $apiUrl .= '?key=' . urlencode($this->apiKey);

            // Gọi API Gemini
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $this->temperature,
                        'maxOutputTokens' => $this->maxTokens,
                        'topP' => 0.95,
                        'topK' => 40,
                    ],
                ]);
            
            // Log request để debug
            Log::info('Gemini API request', [
                'url' => str_replace($this->apiKey, 'HIDDEN', $apiUrl),
                'status' => $response->status(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $botResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                $tokensUsed = $data['usageMetadata']['totalTokenCount'] ?? 0;

                if ($botResponse) {
                    Log::info('Gemini response received', [
                        'tokens_used' => $tokensUsed,
                        'model' => $this->model,
                    ]);

                    return [
                        'response' => trim($botResponse),
                        'tokens_used' => $tokensUsed,
                        'model' => $this->model,
                    ];
                }
            }

            // Log lỗi từ API
            $errorBody = $response->body();
            $errorData = json_decode($errorBody, true);
            
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $errorBody,
                'error_message' => $errorData['error']['message'] ?? 'Unknown error',
            ]);

            return [
                'response' => null,
                'error' => $errorData['error']['message'] ?? 'Failed to get response from Gemini',
                'tokens_used' => 0,
            ];

        } catch (\Exception $e) {
            Log::error('Gemini service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'response' => null,
                'error' => $e->getMessage(),
                'tokens_used' => 0,
            ];
        }
    }

    /**
     * Xây dựng prompt với context từ knowledge base
     * 
     * @param string $question
     * @param array $context
     * @param array $knowledgeBaseContext
     * @return string
     */
    protected function buildPrompt(string $question, array $context = [], array $knowledgeBaseContext = []): string
    {
        $basePrompt = "Bạn là trợ lý AI thông minh của hệ thống quản lý sinh viên (S-MIS). " .
            "Nhiệm vụ của bạn là trả lời các câu hỏi của sinh viên về:\n" .
            "- Đăng ký môn học và học phần\n" .
            "- Học phí và thanh toán\n" .
            "- Điểm số và kết quả học tập\n" .
            "- Lịch học và thời khóa biểu\n" .
            "- Lịch thi và phòng thi\n" .
            "- Các quy định và quy trình học tập\n\n" .
            "Hãy trả lời một cách thân thiện, chính xác và ngắn gọn. " .
            "Nếu không chắc chắn, hãy hướng dẫn sinh viên liên hệ phòng Đào tạo.\n\n";

        // Thêm context từ knowledge base nếu có
        if (!empty($knowledgeBaseContext)) {
            $basePrompt .= "Thông tin tham khảo từ hệ thống:\n";
            foreach ($knowledgeBaseContext as $kb) {
                $basePrompt .= "- " . $kb['cau_hoi'] . ": " . $kb['cau_tra_loi'] . "\n";
            }
            $basePrompt .= "\n";
        }

        // Thêm context từ conversation trước
        if (!empty($context['messages'])) {
            $basePrompt .= "Lịch sử hội thoại trước:\n";
            $recentMessages = array_slice($context['messages'], -6); // Lấy 6 tin nhắn gần nhất (3 cặp Q&A)
            foreach ($recentMessages as $msg) {
                $role = $msg['nguoi_gui'] === 'user' ? 'Sinh viên' : 'Trợ lý';
                $basePrompt .= "{$role}: {$msg['noi_dung']}\n";
            }
            $basePrompt .= "\n";
        }

        $basePrompt .= "Thông tin liên hệ:\n" .
            "- Hotline: " . config('chatbot.hotline', '024.xxxx.xxxx') . "\n" .
            "- Email: " . config('chatbot.email', 'daotao@smis.edu.vn') . "\n\n";

        // Câu hỏi hiện tại
        $basePrompt .= "Câu hỏi của sinh viên: {$question}\n\n";
        $basePrompt .= "Hãy trả lời câu hỏi trên một cách ngắn gọn và hữu ích:";

        return $basePrompt;
    }

    /**
     * Kiểm tra kết nối với Gemini API
     * 
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            // Kiểm tra API key
            if (empty($this->apiKey)) {
                Log::warning('Gemini API key is empty');
                return false;
            }
            
            // Test với một câu hỏi đơn giản
            $response = $this->getResponse('Xin chào', [], []);
            
            if (!empty($response['response'])) {
                Log::info('Gemini connection test successful');
                return true;
            }
            
            if (!empty($response['error'])) {
                Log::error('Gemini connection test failed', [
                    'error' => $response['error'],
                ]);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Gemini connection test exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách models có sẵn
     * 
     * @return array
     */
    public function getAvailableModels(): array
    {
        return [
            'gemini-1.5-pro' => 'Gemini 1.5 Pro (Recommended)',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash (Faster)',
            'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash Experimental',
            'gemini-pro' => 'Gemini Pro (Legacy - may not work)',
        ];
    }
}

