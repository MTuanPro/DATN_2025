<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiChatbotMessage extends Model
{
    protected $table = 'ai_chatbot_message';

    protected $fillable = [
        'conversation_id',
        'nguoi_gui',
        'noi_dung',
        'knowledge_base_id',
        'do_tuong_dong',
        'thoi_gian_gui',
    ];

    protected $casts = [
        'thoi_gian_gui' => 'datetime',
        'do_tuong_dong' => 'float',
    ];

    /**
     * Cuộc hội thoại chứa tin nhắn này
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiChatbotConversation::class, 'conversation_id');
    }

    /**
     * Knowledge base được sử dụng (nếu là bot reply)
     */
    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(AiChatbotKnowledgeBase::class, 'knowledge_base_id');
    }

    /**
     * Feedback cho tin nhắn này
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(AiChatbotFeedback::class, 'message_id');
    }

    /**
     * Scope: Tin nhắn từ user
     */
    public function scopeFromUser($query)
    {
        return $query->where('nguoi_gui', 'user');
    }

    /**
     * Scope: Tin nhắn từ bot
     */
    public function scopeFromBot($query)
    {
        return $query->where('nguoi_gui', 'bot');
    }

    /**
     * Kiểm tra xem tin nhắn có feedback chưa
     */
    public function daCoFeedback(): bool
    {
        return $this->feedback()->exists();
    }

    /**
     * Kiểm tra feedback có hữu ích không
     */
    public function feedbackHuuIch(): bool
    {
        return $this->feedback && $this->feedback->danh_gia === 'huu_ich';
    }

    /**
     * Lấy phần trăm độ tương đồng
     */
    public function doTuongDongPhanTram(): ?int
    {
        return $this->do_tuong_dong ? (int) round($this->do_tuong_dong * 100) : null;
    }
}
