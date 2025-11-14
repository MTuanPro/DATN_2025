<?php

namespace App\Services;

use App\Models\AiChatbotFeedback;
use App\Models\AiChatbotMessage;
use App\Models\AiChatbotKnowledgeBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatbotLearningService
{
    /**
     * Phân tích feedback để cải thiện matching
     * Chạy scheduled job hàng ngày
     * 
     * @return array Statistics
     */
    public function analyzeFeedback(): array
    {
        $stats = [
            'processed_positive' => 0,
            'processed_negative' => 0,
            'adjusted_knowledge' => 0,
            'errors' => 0,
        ];

        try {
            DB::beginTransaction();

            // 1. Xử lý feedback KHÔNG HỮU ÍCH
            $badFeedbacks = AiChatbotFeedback::where('danh_gia', 'khong_huu_ich')
                ->whereDate('created_at', '>=', now()->subDays(7)) // Feedback trong 7 ngày qua
                ->with(['message.knowledgeBase'])
                ->get();

            foreach ($badFeedbacks as $feedback) {
                $message = $feedback->message;
                $knowledge = $message->knowledgeBase;

                if (!$knowledge) continue;

                // Giảm độ ưu tiên nếu:
                // - Similarity cao (>= 60%) nhưng user vẫn không hài lòng → Knowledge base sai
                // - Giảm nhiều hơn nếu similarity rất cao
                if ($message->do_tuong_dong >= 0.6) {
                    $decreaseAmount = $message->do_tuong_dong >= 0.8 ? 10 : 5;
                    
                    if ($knowledge->do_uu_tien > 0) {
                        $oldPriority = $knowledge->do_uu_tien;
                        $knowledge->decrement('do_uu_tien', $decreaseAmount);
                        $knowledge->refresh();

                        Log::info('Learning: Decreased priority (negative feedback)', [
                            'knowledge_id' => $knowledge->id,
                            'chu_de' => $knowledge->chu_de,
                            'old_priority' => $oldPriority,
                            'new_priority' => $knowledge->do_uu_tien,
                            'similarity' => $message->do_tuong_dong,
                            'decrease' => $decreaseAmount,
                        ]);

                        $stats['adjusted_knowledge']++;
                    }
                }

                $stats['processed_negative']++;
            }

            // 2. Xử lý feedback HỮU ÍCH
            $goodFeedbacks = AiChatbotFeedback::where('danh_gia', 'huu_ich')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->with(['message.knowledgeBase'])
                ->get();

            foreach ($goodFeedbacks as $feedback) {
                $message = $feedback->message;
                $knowledge = $message->knowledgeBase;

                if (!$knowledge) continue;

                // Tăng độ ưu tiên nếu:
                // - Similarity trung bình (40-70%) nhưng user hài lòng → Match tốt
                // - Tăng nhiều hơn nếu similarity vừa phải nhưng vẫn đúng
                if ($message->do_tuong_dong >= 0.4 && $message->do_tuong_dong <= 0.7) {
                    $increaseAmount = 3;
                } elseif ($message->do_tuong_dong > 0.7) {
                    $increaseAmount = 2; // Tăng ít hơn vì đã match tốt rồi
                } else {
                    $increaseAmount = 1;
                }

                if ($knowledge->do_uu_tien < 100) {
                    $oldPriority = $knowledge->do_uu_tien;
                    $knowledge->increment('do_uu_tien', $increaseAmount);
                    $knowledge->refresh();

                    // Ensure not exceed 100
                    if ($knowledge->do_uu_tien > 100) {
                        $knowledge->do_uu_tien = 100;
                        $knowledge->save();
                    }

                    Log::info('Learning: Increased priority (positive feedback)', [
                        'knowledge_id' => $knowledge->id,
                        'chu_de' => $knowledge->chu_de,
                        'old_priority' => $oldPriority,
                        'new_priority' => $knowledge->do_uu_tien,
                        'similarity' => $message->do_tuong_dong,
                        'increase' => $increaseAmount,
                    ]);

                    $stats['adjusted_knowledge']++;
                }

                $stats['processed_positive']++;
            }

            DB::commit();

            Log::info('Chatbot Learning: Feedback analysis completed', $stats);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Chatbot Learning: Failed to analyze feedback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $stats['errors']++;
        }

        return $stats;
    }

    /**
     * Suggest new knowledge base từ câu hỏi không match
     * Gửi report cho admin hàng tuần
     * 
     * @param int $minOccurrences Số lần xuất hiện tối thiểu
     * @return Collection
     */
    public function suggestNewKnowledge(int $minOccurrences = 3): Collection
    {
        // Tìm các tin nhắn user mà bot không trả lời được (similarity < threshold)
        $threshold = config('chatbot.similarity_threshold', 0.6);
        
        $unansweredQuestions = AiChatbotMessage::where('nguoi_gui', 'user')
            ->whereHas('conversation', function($q) {
                // Trong 30 ngày qua
                $q->where('ngay_bat_dau', '>=', now()->subDays(30));
            })
            ->whereDoesntHave('conversation.messages', function($q) use ($threshold) {
                // Không có response từ bot với similarity >= threshold
                $q->where('nguoi_gui', 'bot')
                  ->where('knowledge_base_id', '!=', null)
                  ->where('do_tuong_dong', '>=', $threshold);
            })
            ->select('noi_dung', DB::raw('COUNT(*) as occurrences'))
            ->groupBy('noi_dung')
            ->havingRaw('COUNT(*) >= ?', [$minOccurrences])
            ->orderByDesc('occurrences')
            ->limit(50)
            ->get();

        Log::info('Chatbot Learning: Suggested new knowledge', [
            'total_suggestions' => $unansweredQuestions->count(),
            'threshold' => $threshold,
            'min_occurrences' => $minOccurrences,
        ]);

        return $unansweredQuestions;
    }

    /**
     * Phân tích accuracy dựa trên feedback
     * 
     * @param int $days Số ngày phân tích
     * @return array
     */
    public function calculateAccuracy(int $days = 7): array
    {
        $messages = AiChatbotMessage::where('nguoi_gui', 'bot')
            ->whereNotNull('knowledge_base_id')
            ->whereHas('conversation', function($q) use ($days) {
                $q->where('ngay_bat_dau', '>=', now()->subDays($days));
            })
            ->with('feedback')
            ->get();

        $total = $messages->count();
        $withFeedback = $messages->filter(fn($m) => $m->feedback !== null)->count();
        $positive = $messages->filter(fn($m) => $m->feedback && $m->feedback->danh_gia == 'huu_ich')->count();
        $negative = $messages->filter(fn($m) => $m->feedback && $m->feedback->danh_gia == 'khong_huu_ich')->count();

        return [
            'total_responses' => $total,
            'with_feedback' => $withFeedback,
            'feedback_rate' => $total > 0 ? round(($withFeedback / $total) * 100, 2) : 0,
            'positive_feedback' => $positive,
            'negative_feedback' => $negative,
            'accuracy_rate' => $withFeedback > 0 ? round(($positive / $withFeedback) * 100, 2) : 0,
            'period_days' => $days,
        ];
    }

    /**
     * Tìm knowledge base cần review (low performance)
     * 
     * @return Collection
     */
    public function findLowPerformanceKnowledge(): Collection
    {
        // Tìm knowledge base có:
        // - Nhiều feedback không hữu ích (>= 5)
        // - Tỷ lệ hữu ích thấp (<= 30%)
        
        $knowledgeList = AiChatbotKnowledgeBase::kichHoat()
            ->whereHas('messages', function($q) {
                $q->whereHas('feedback', function($fq) {
                    $fq->where('danh_gia', 'khong_huu_ich');
                });
            })
            ->withCount([
                'messages as total_usage' => function($q) {
                    $q->whereDate('thoi_gian_gui', '>=', now()->subDays(30));
                },
                'messages as negative_feedback_count' => function($q) {
                    $q->whereDate('thoi_gian_gui', '>=', now()->subDays(30))
                      ->whereHas('feedback', function($fq) {
                          $fq->where('danh_gia', 'khong_huu_ich');
                      });
                },
            ])
            ->get()
            ->filter(function($knowledge) {
                // Filter: Có ít nhất 5 negative feedback
                if ($knowledge->negative_feedback_count < 5) {
                    return false;
                }

                // Calculate negative rate
                $negativeRate = ($knowledge->negative_feedback_count / $knowledge->total_usage) * 100;
                
                // Filter: Tỷ lệ negative >= 70% (tức positive <= 30%)
                return $negativeRate >= 70;
            })
            ->sortByDesc('negative_feedback_count')
            ->values();

        Log::info('Chatbot Learning: Found low performance knowledge', [
            'count' => $knowledgeList->count(),
        ]);

        return $knowledgeList;
    }

    /**
     * Auto-disable knowledge base có performance quá thấp
     * 
     * @return int Number of disabled knowledge
     */
    public function autoDisableLowPerformance(): int
    {
        $disabled = 0;

        try {
            $lowPerformance = $this->findLowPerformanceKnowledge();

            foreach ($lowPerformance as $knowledge) {
                // Chỉ disable nếu:
                // - Có >= 10 negative feedback
                // - Đã được sử dụng >= 15 lần
                if ($knowledge->negative_feedback_count >= 10 && $knowledge->total_usage >= 15) {
                    $knowledge->kich_hoat = false;
                    $knowledge->save();

                    Log::warning('Chatbot Learning: Auto-disabled low performance knowledge', [
                        'knowledge_id' => $knowledge->id,
                        'chu_de' => $knowledge->chu_de,
                        'cau_hoi_mau' => $knowledge->cau_hoi_mau,
                        'total_usage' => $knowledge->total_usage,
                        'negative_feedback' => $knowledge->negative_feedback_count,
                    ]);

                    $disabled++;
                }
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Learning: Failed to auto-disable knowledge', [
                'error' => $e->getMessage(),
            ]);
        }

        return $disabled;
    }

    /**
     * Generate weekly report
     * 
     * @return array
     */
    public function generateWeeklyReport(): array
    {
        return [
            'accuracy' => $this->calculateAccuracy(7),
            'suggestions' => $this->suggestNewKnowledge(3),
            'low_performance' => $this->findLowPerformanceKnowledge(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}

