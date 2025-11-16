<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotGPTService
{
    protected $apiKey;
    protected $apiUrl;
    protected $model;
    protected $maxTokens;
    protected $temperature;

    public function __construct()
    {
        $this->apiKey = config('chatbot.gpt.api_key');
        $this->apiUrl = config('chatbot.gpt.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->model = config('chatbot.gpt.model', 'gpt-3.5-turbo');
        $this->maxTokens = config('chatbot.gpt.max_tokens', 500);
        $this->temperature = config('chatbot.gpt.temperature', 0.7);
    }

    /**
     * Gửi câu hỏi đến GPT và nhận câu trả lời
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
            Log::warning('GPT API key not configured');
            return [
                'response' => null,
                'error' => 'GPT service not configured',
                'tokens_used' => 0,
            ];
        }

        try {
            // Xây dựng system prompt với context từ knowledge base
            $systemPrompt = $this->buildSystemPrompt($knowledgeBaseContext);
            
            // Xây dựng conversation messages
            $messages = $this->buildMessages($question, $context, $systemPrompt);

            // Gọi API OpenAI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $this->maxTokens,
                    'temperature' => $this->temperature,
                    'top_p' => 1,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $botResponse = $data['choices'][0]['message']['content'] ?? null;
                $tokensUsed = $data['usage']['total_tokens'] ?? 0;

                if ($botResponse) {
                    Log::info('GPT response received', [
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
            Log::error('GPT API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'response' => null,
                'error' => 'Failed to get response from GPT',
                'tokens_used' => 0,
            ];

        } catch (\Exception $e) {
            Log::error('GPT service exception', [
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
     * Xây dựng system prompt với context từ knowledge base
     * 
     * @param array $knowledgeBaseContext
     * @return string
     */
    protected function buildSystemPrompt(array $knowledgeBaseContext = []): string
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

        $basePrompt .= "Thông tin liên hệ:\n" .
            "- Hotline: " . config('chatbot.hotline', '024.xxxx.xxxx') . "\n" .
            "- Email: " . config('chatbot.email', 'daotao@smis.edu.vn') . "\n";

        return $basePrompt;
    }

    /**
     * Xây dựng messages array cho API
     * 
     * @param string $question
     * @param array $context
     * @param string $systemPrompt
     * @return array
     */
    protected function buildMessages(string $question, array $context = [], string $systemPrompt = ''): array
    {
        $messages = [];

        // System message
        if (!empty($systemPrompt)) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt,
            ];
        }

        // Thêm context từ conversation trước (tối đa 5 cặp Q&A gần nhất)
        if (!empty($context['messages'])) {
            $recentMessages = array_slice($context['messages'], -10); // Lấy 10 tin nhắn gần nhất (5 cặp)
            
            foreach ($recentMessages as $msg) {
                $messages[] = [
                    'role' => $msg['nguoi_gui'] === 'user' ? 'user' : 'assistant',
                    'content' => $msg['noi_dung'],
                ];
            }
        }

        // Câu hỏi hiện tại
        $messages[] = [
            'role' => 'user',
            'content' => $question,
        ];

        return $messages;
    }

    /**
     * Kiểm tra kết nối với GPT API
     * 
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            // Kiểm tra API key
            if (empty($this->apiKey)) {
                Log::warning('GPT API key is empty');
                return false;
            }
            
            // Test với một câu hỏi đơn giản
            $response = $this->getResponse('Xin chào', [], []);
            
            if (!empty($response['response'])) {
                Log::info('GPT connection test successful');
                return true;
            }
            
            if (!empty($response['error'])) {
                Log::error('GPT connection test failed', [
                    'error' => $response['error'],
                ]);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('GPT connection test exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách models có sẵn (nếu API hỗ trợ)
     * 
     * @return array
     */
    public function getAvailableModels(): array
    {
        // Các model phổ biến của OpenAI
        return [
            'gpt-4' => 'GPT-4 (Most capable)',
            'gpt-4-turbo' => 'GPT-4 Turbo (Faster)',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Fastest, cheapest)',
            'gpt-3.5-turbo-16k' => 'GPT-3.5 Turbo 16k (Longer context)',
        ];
    }
}

