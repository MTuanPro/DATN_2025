<?php

namespace App\Services;

use App\Models\AiChatbotKnowledgeBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdvancedChatbotMatchingService
{
    /**
     * Multi-stage matching pipeline
     * 
     * @param string $question Câu hỏi của user
     * @param array $context Context từ conversation trước
     * @return array
     */
    public function findBestMatch(string $question, array $context = []): array
    {
        // Stage 1: Intent Detection
        $intent = $this->detectIntent($question);
        
        // Stage 2: Entity Extraction
        $entities = $this->extractEntities($question);
        
        // Stage 3: Keyword Matching (Fast filter)
        $keywordMatches = $this->keywordSearch($question, $intent);
        
        if ($keywordMatches->isEmpty()) {
            Log::warning('AdvancedMatching: No keyword matches found', [
                'question' => $question,
                'intent' => $intent,
                'entities' => $entities,
            ]);
            
            return [
                'knowledge' => null,
                'similarity' => 0,
                'reason' => 'no_candidates',
                'intent' => $intent,
                'entities' => $entities,
            ];
        }
        
        // Stage 4: Semantic Similarity (Accurate scoring)
        $semanticResults = $this->semanticSearch($question, $keywordMatches);
        
        // Stage 5: Context Ranking
        $rankedResults = $this->rankByContext($semanticResults, $context, $entities);
        
        // Stage 6: Apply Confidence Threshold
        return $this->applyThreshold($rankedResults, $intent, $entities);
    }

    /**
     * Detect intent (ý định hỏi)
     * 
     * @param string $question
     * @return string
     */
    protected function detectIntent(string $question): string
    {
        $intents = [
            'dang_ky' => [
                'đăng ký', 'đk', 'đk môn', 'register', 'enroll', 
                'đkhp', 'đăng ký học phần', 'đăng kí'
            ],
            'hoc_phi' => [
                'học phí', 'tiền học', 'nộp tiền', 'thanh toán', 
                'đóng tiền', 'phí', 'chi phí', 'miễn giảm'
            ],
            'diem' => [
                'điểm', 'kết quả', 'xem điểm', 'tra điểm', 
                'tra cứu điểm', 'kqht', 'gpa', 'dtb'
            ],
            'lich_hoc' => [
                'lịch học', 'tkb', 'thời khóa biểu', 'học khi nào', 
                'lịch trình học', 'xem lịch', 'học lúc nào'
            ],
            'lich_thi' => [
                'lịch thi', 'thi khi nào', 'thi ở đâu', 
                'xem lịch thi', 'lịch kiểm tra', 'phòng thi'
            ],
            'hoc_lai' => [
                'học lại', 'rớt môn', 'thi lại', 'học cải thiện', 
                'điểm f', 'không đạt'
            ],
            'chuyen_nganh' => [
                'chuyên ngành', 'chọn cn', 'ngành', 'định hướng', 
                'major', 'specialization'
            ],
            'thoi_hoc' => [
                'thôi học', 'bảo lưu', 'nghỉ học', 'hoãn học', 
                'rút học phần', 'drop out'
            ],
            'tot_nghiep' => [
                'tốt nghiệp', 'tn', 'graduation', 'khóa luận', 
                'điều kiện tn', 'xét tn'
            ],
            'tai_khoan' => [
                'tài khoản', 'đăng nhập', 'login', 'password', 
                'quên mật khẩu', 'reset password', 'đổi mk'
            ],
        ];

        $normalized = $this->normalizeString($question);

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strpos($normalized, $this->normalizeString($keyword)) !== false) {
                    return $intent;
                }
            }
        }

        return 'unknown';
    }

    /**
     * Extract entities (môn học, học kỳ, năm học, ...)
     * 
     * @param string $question
     * @return array
     */
    public function extractEntities(string $question): array
    {
        $entities = [];

        // Tìm môn học (pattern: "môn X", "học X", "đk X")
        if (preg_match('/(?:môn|học|đk|đăng ký)\s+([a-zàáảãạâầấẩẫậăằắẳẵặèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵ\s]+?)(?:\s+(?:như|thế|không|chưa|khi|ở|lúc|là|có)|[?.!,]|$)/ui', $question, $matches)) {
            $entities['mon_hoc'] = trim($matches[1]);
        }

        // Tìm học kỳ (pattern: "học kỳ 1", "hk1", "kỳ 2")
        if (preg_match('/(?:học\s+kỳ|hk|kỳ)\s*(\d+)/ui', $question, $matches)) {
            $entities['hoc_ky'] = (int) $matches[1];
        }

        // Tìm năm học (pattern: "năm 2024", "năm học 2024-2025")
        if (preg_match('/năm(?:\s+học)?\s*(\d{4})(?:-(\d{4}))?/ui', $question, $matches)) {
            $entities['nam_hoc'] = $matches[1];
            if (isset($matches[2])) {
                $entities['nam_hoc'] .= '-' . $matches[2];
            }
        }

        // Tìm số tín chỉ
        if (preg_match('/(\d+)\s*(?:tín\s*chỉ|tc|credit)/ui', $question, $matches)) {
            $entities['so_tin_chi'] = (int) $matches[1];
        }

        // Tìm thời gian (sáng, chiều, tối, thứ)
        if (preg_match('/(?:thứ\s*(\d+)|sáng|chiều|tối)/ui', $question, $matches)) {
            $entities['thoi_gian'] = $matches[0];
        }

        return $entities;
    }

    /**
     * Keyword search (fast filter)
     * 
     * @param string $question
     * @param string $intent
     * @return Collection
     */
    protected function keywordSearch(string $question, string $intent): Collection
    {
        $query = AiChatbotKnowledgeBase::kichHoat();

        // Filter by intent if detected
        if ($intent != 'unknown') {
            $query->where('chu_de', $intent);
        }

        // Extract keywords from question
        $words = $this->extractKeywords($question);
        
        if (!empty($words)) {
            $query->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('tu_khoa', 'LIKE', "%{$word}%")
                      ->orWhere('cau_hoi_mau', 'LIKE', "%{$word}%")
                      ->orWhere('chu_de', 'LIKE', "%{$word}%");
                }
            });
        }

        return $query->orderBy('do_uu_tien', 'desc')
                     ->limit(20)
                     ->get();
    }

    /**
     * Semantic search with multiple similarity metrics
     * 
     * @param string $question
     * @param Collection $candidates
     * @return array
     */
    protected function semanticSearch(string $question, Collection $candidates): array
    {
        $results = [];

        foreach ($candidates as $knowledge) {
            // Similarity với câu hỏi mẫu
            $questionSimilarity = $this->calculateAdvancedSimilarity($question, $knowledge->cau_hoi_mau);

            // Similarity với từ khóa
            $keywordSimilarity = $this->calculateKeywordOverlap($question, $knowledge->tu_khoa);

            // Similarity với câu trả lời (để hiểu context)
            $answerSimilarity = $this->calculateAdvancedSimilarity($question, $knowledge->cau_tra_loi);

            // Weighted score
            $score = ($questionSimilarity * 0.5) +   // Câu hỏi quan trọng nhất
                     ($keywordSimilarity * 0.3) +    // Từ khóa khá quan trọng
                     ($answerSimilarity * 0.2);      // Context từ câu trả lời

            // Bonus cho độ ưu tiên (max +0.15)
            $priorityBonus = min(($knowledge->do_uu_tien / 100) * 0.15, 0.15);
            $score += $priorityBonus;

            $results[] = [
                'knowledge' => $knowledge,
                'score' => $score,
                'question_sim' => $questionSimilarity,
                'keyword_sim' => $keywordSimilarity,
                'answer_sim' => $answerSimilarity,
                'priority_bonus' => $priorityBonus,
            ];
        }

        return $results;
    }

    /**
     * Rank by context and entities
     * 
     * @param array $results
     * @param array $context
     * @param array $entities
     * @return array
     */
    protected function rankByContext(array $results, array $context, array $entities): array
    {
        foreach ($results as &$result) {
            $bonus = 0;

            // Bonus nếu trùng chủ đề với câu hỏi trước
            if (isset($context['previous_topic']) && 
                $result['knowledge']->chu_de == $context['previous_topic']) {
                $bonus += 0.1;
            }

            // Bonus nếu mention entity từ context (follow-up question)
            if (!empty($context['entities'])) {
                foreach ($context['entities'] as $key => $value) {
                    if (isset($entities[$key]) && $entities[$key] == $value) {
                        $bonus += 0.05; // User đang tiếp tục hỏi về cùng entity
                    }
                }
            }

            // Bonus nếu entity trong câu hỏi match với knowledge base content
            if (isset($entities['mon_hoc'])) {
                $monHoc = $this->normalizeString($entities['mon_hoc']);
                $content = $this->normalizeString($result['knowledge']->cau_tra_loi);
                
                if (mb_strpos($content, $monHoc) !== false) {
                    $bonus += 0.12;
                }
            }

            // Bonus nếu hoc_ky được mention
            if (isset($entities['hoc_ky'])) {
                if (mb_strpos($result['knowledge']->cau_tra_loi, (string)$entities['hoc_ky']) !== false) {
                    $bonus += 0.08;
                }
            }

            $result['score'] += $bonus;
            $result['context_bonus'] = $bonus;
        }

        // Sort by score desc
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $results;
    }

    /**
     * Apply confidence threshold and return result
     * 
     * @param array $results
     * @param string $intent
     * @param array $entities
     * @return array
     */
    protected function applyThreshold(array $results, string $intent, array $entities): array
    {
        $threshold = config('chatbot.similarity_threshold', 0.6); // Tăng từ 0.3 lên 0.6

        if (empty($results) || $results[0]['score'] < $threshold) {
            Log::info('AdvancedMatching: Below threshold', [
                'best_score' => $results[0]['score'] ?? 0,
                'threshold' => $threshold,
                'intent' => $intent,
                'entities' => $entities,
            ]);

            return [
                'knowledge' => null,
                'similarity' => $results[0]['score'] ?? 0,
                'reason' => 'below_threshold',
                'intent' => $intent,
                'entities' => $entities,
                'alternatives' => [], // Không có alternatives vì score quá thấp
            ];
        }

        // Collect alternatives (top 3 candidates)
        $alternatives = [];
        for ($i = 1; $i < min(3, count($results)); $i++) {
            if ($results[$i]['score'] >= $threshold * 0.8) { // At least 80% of threshold
                $alternatives[] = [
                    'knowledge' => $results[$i]['knowledge'],
                    'score' => $results[$i]['score'],
                ];
            }
        }

        return [
            'knowledge' => $results[0]['knowledge'],
            'similarity' => $results[0]['score'],
            'intent' => $intent,
            'entities' => $entities,
            'alternatives' => $alternatives,
            'debug' => config('app.debug') ? [
                'question_sim' => $results[0]['question_sim'],
                'keyword_sim' => $results[0]['keyword_sim'],
                'answer_sim' => $results[0]['answer_sim'],
                'priority_bonus' => $results[0]['priority_bonus'],
                'context_bonus' => $results[0]['context_bonus'] ?? 0,
            ] : null,
        ];
    }

    /**
     * Calculate advanced similarity (combination of methods)
     * 
     * @param string $str1
     * @param string $str2
     * @return float
     */
    protected function calculateAdvancedSimilarity(string $str1, string $str2): float
    {
        $normalized1 = $this->normalizeString($str1);
        $normalized2 = $this->normalizeString($str2);

        // Method 1: Similar Text (0-1)
        similar_text($normalized1, $normalized2, $percent);
        $similarTextScore = $percent / 100;

        // Method 2: Levenshtein Distance (normalized to 0-1)
        $maxLen = max(mb_strlen($normalized1), mb_strlen($normalized2));
        if ($maxLen > 0) {
            $lev = levenshtein(
                mb_substr($normalized1, 0, 255), 
                mb_substr($normalized2, 0, 255)
            );
            $levenshteinScore = 1 - ($lev / $maxLen);
        } else {
            $levenshteinScore = 0;
        }

        // Method 3: Word Overlap
        $wordOverlapScore = $this->calculateWordOverlap($normalized1, $normalized2);

        // Weighted combination
        $finalScore = ($similarTextScore * 0.4) + 
                      ($levenshteinScore * 0.3) + 
                      ($wordOverlapScore * 0.3);

        return $finalScore;
    }

    /**
     * Calculate word overlap score
     * 
     * @param string $str1
     * @param string $str2
     * @return float
     */
    protected function calculateWordOverlap(string $str1, string $str2): float
    {
        $words1 = $this->extractKeywords($str1);
        $words2 = $this->extractKeywords($str2);

        if (empty($words1) || empty($words2)) {
            return 0;
        }

        $intersection = count(array_intersect($words1, $words2));
        $union = count(array_unique(array_merge($words1, $words2)));

        return $union > 0 ? $intersection / $union : 0;
    }

    /**
     * Calculate keyword overlap (cho từ khóa cách nhau bởi dấu phẩy)
     * 
     * @param string $question
     * @param string $keywords
     * @return float
     */
    protected function calculateKeywordOverlap(string $question, string $keywords): float
    {
        $questionWords = $this->extractKeywords($question);
        
        // Keywords từ DB thường là: "đăng ký, học phần, môn học"
        $keywordList = array_map(function($k) {
            return $this->normalizeString(trim($k));
        }, explode(',', $keywords));

        if (empty($questionWords) || empty($keywordList)) {
            return 0;
        }

        $matches = 0;
        foreach ($keywordList as $keyword) {
            foreach ($questionWords as $word) {
                if (mb_strpos($word, $keyword) !== false || mb_strpos($keyword, $word) !== false) {
                    $matches++;
                    break;
                }
            }
        }

        return $matches / count($keywordList);
    }

    /**
     * Extract meaningful keywords from text
     * 
     * @param string $text
     * @return array
     */
    protected function extractKeywords(string $text): array
    {
        $normalized = $this->normalizeString($text);

        // Remove stopwords
        $stopwords = [
            'toi', 'la', 'cua', 'va', 'co', 'khong', 'duoc', 'nhu', 'the', 'nao',
            'gi', 'ra', 'vao', 'tren', 'duoi', 'trong', 'ngoai', 'sau', 'truoc',
            'khi', 'ma', 'neu', 'thi', 'hay', 'hoac', 'nhung', 'ban', 'minh',
            'em', 'anh', 'chi', 'cho', 'cac', 'mot', 'hai', 'ba', 'tu', 'den',
            'di', 'lai', 'voi', 'se', 'da', 'dang', 'bi', 'con', 'ho', 'ca',
        ];

        // Split by whitespace
        $words = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out stopwords and short words
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return mb_strlen($word) >= 2 && !in_array($word, $stopwords);
        });

        return array_values($keywords);
    }

    /**
     * Normalize string (lowercase, remove accents, trim)
     * 
     * @param string $str
     * @return string
     */
    protected function normalizeString(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        
        // Remove Vietnamese accents
        $accents = [
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'đ' => 'd',
        ];

        $str = strtr($str, $accents);
        
        return trim($str);
    }
}

