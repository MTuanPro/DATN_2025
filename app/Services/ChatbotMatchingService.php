<?php

namespace App\Services;

use App\Models\AiChatbotKnowledgeBase;

class ChatbotMatchingService
{
    /**
     * Tìm kiếm câu trả lời phù hợp nhất với câu hỏi
     * 
     * @param string $question Câu hỏi từ user
     * @param string|null $chuDe Chủ đề (nếu có)
     * @return array ['knowledge' => Model|null, 'similarity' => float]
     */
    public function findBestMatch(string $question, ?string $chuDe = null): array
    {
        // Lấy tất cả knowledge base đã kích hoạt
        $query = AiChatbotKnowledgeBase::kichHoat();
        
        if ($chuDe) {
            $query->where('chu_de', $chuDe);
        }
        
        $knowledgeList = $query->get();
        
        if ($knowledgeList->isEmpty()) {
            return ['knowledge' => null, 'similarity' => 0];
        }
        
        $bestMatch = null;
        $highestSimilarity = 0;
        
        foreach ($knowledgeList as $knowledge) {
            // Tính độ tương đồng với câu hỏi mẫu
            $similarity = $this->calculateSimilarity($question, $knowledge->cau_hoi_mau);
            
            // Tính độ tương đồng với từ khóa (nếu có)
            if ($knowledge->tu_khoa) {
                $keywordSimilarity = $this->calculateKeywordSimilarity($question, $knowledge->tu_khoa);
                // Ưu tiên từ khóa hơn
                $similarity = max($similarity, $keywordSimilarity * 1.2);
            }
            
            // Bonus cho độ ưu tiên cao
            $similarity += ($knowledge->do_uu_tien * 0.01);
            
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $knowledge;
            }
        }
        
        // Chỉ trả về nếu độ tương đồng >= 30%
        if ($highestSimilarity < 0.3) {
            return ['knowledge' => null, 'similarity' => $highestSimilarity];
        }
        
        return [
            'knowledge' => $bestMatch,
            'similarity' => min($highestSimilarity, 1.0) // Cap tối đa 100%
        ];
    }
    
    /**
     * Tính độ tương đồng giữa 2 chuỗi (Simple algorithm)
     * Sử dụng Similar Text và Levenshtein Distance
     * 
     * @param string $str1
     * @param string $str2
     * @return float Giá trị từ 0-1
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        // Chuẩn hóa chuỗi
        $str1 = $this->normalizeString($str1);
        $str2 = $this->normalizeString($str2);
        
        // Tính similar_text percentage
        similar_text($str1, $str2, $percent);
        $similarity1 = $percent / 100;
        
        // Tính Levenshtein similarity
        $maxLen = max(mb_strlen($str1), mb_strlen($str2));
        if ($maxLen > 0) {
            $levDistance = levenshtein(
                mb_substr($str1, 0, 255),  // Limit for levenshtein
                mb_substr($str2, 0, 255)
            );
            $similarity2 = 1 - ($levDistance / $maxLen);
        } else {
            $similarity2 = 0;
        }
        
        // Tính word overlap
        $words1 = $this->extractWords($str1);
        $words2 = $this->extractWords($str2);
        $commonWords = array_intersect($words1, $words2);
        $totalWords = array_unique(array_merge($words1, $words2));
        $wordOverlap = count($totalWords) > 0 ? count($commonWords) / count($totalWords) : 0;
        
        // Kết hợp các phương pháp (trọng số)
        return ($similarity1 * 0.3) + ($similarity2 * 0.3) + ($wordOverlap * 0.4);
    }
    
    /**
     * Tính độ tương đồng với từ khóa
     * 
     * @param string $question
     * @param string $keywords Chuỗi từ khóa cách nhau bởi dấu phẩy
     * @return float
     */
    protected function calculateKeywordSimilarity(string $question, string $keywords): float
    {
        $question = $this->normalizeString($question);
        $keywordArray = array_map('trim', explode(',', $keywords));
        
        $matchCount = 0;
        $totalKeywords = count($keywordArray);
        
        foreach ($keywordArray as $keyword) {
            $keyword = $this->normalizeString($keyword);
            if (mb_strpos($question, $keyword) !== false) {
                $matchCount++;
            }
        }
        
        return $totalKeywords > 0 ? $matchCount / $totalKeywords : 0;
    }
    
    /**
     * Chuẩn hóa chuỗi: lowercase, remove accents, trim
     * 
     * @param string $str
     * @return string
     */
    protected function normalizeString(string $str): string
    {
        // Lowercase
        $str = mb_strtolower($str, 'UTF-8');
        
        // Remove Vietnamese accents
        $str = $this->removeVietnameseAccents($str);
        
        // Remove extra spaces
        $str = preg_replace('/\s+/', ' ', $str);
        
        return trim($str);
    }
    
    /**
     * Bỏ dấu tiếng Việt
     * 
     * @param string $str
     * @return string
     */
    protected function removeVietnameseAccents(string $str): string
    {
        $marTViet = [
            "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ",
            "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ"
        ];
        
        $khongDau = [
            "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D"
        ];
        
        return str_replace($marTViet, $khongDau, $str);
    }
    
    /**
     * Tách từ từ chuỗi
     * 
     * @param string $str
     * @return array
     */
    protected function extractWords(string $str): array
    {
        // Tách theo space và loại bỏ từ rỗng
        $words = preg_split('/\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);
        
        // Loại bỏ stopwords tiếng Việt
        $stopwords = ['là', 'thì', 'của', 'và', 'có', 'được', 'cho', 'với', 'bởi', 'từ', 'đến', 'trong', 'ngoài', 'về', 'như', 'để'];
        $words = array_diff($words, $stopwords);
        
        return array_values($words);
    }
    
    /**
     * Tìm kiếm nhiều câu trả lời liên quan
     * 
     * @param string $question
     * @param int $limit
     * @return array
     */
    public function findRelatedAnswers(string $question, int $limit = 5): array
    {
        $knowledgeList = AiChatbotKnowledgeBase::kichHoat()->get();
        $results = [];
        
        foreach ($knowledgeList as $knowledge) {
            $similarity = $this->calculateSimilarity($question, $knowledge->cau_hoi_mau);
            
            if ($knowledge->tu_khoa) {
                $keywordSimilarity = $this->calculateKeywordSimilarity($question, $knowledge->tu_khoa);
                $similarity = max($similarity, $keywordSimilarity * 1.2);
            }
            
            $similarity += ($knowledge->do_uu_tien * 0.01);
            
            if ($similarity >= 0.2) {
                $results[] = [
                    'knowledge' => $knowledge,
                    'similarity' => min($similarity, 1.0)
                ];
            }
        }
        
        // Sắp xếp theo độ tương đồng giảm dần
        usort($results, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });
        
        return array_slice($results, 0, $limit);
    }
}
