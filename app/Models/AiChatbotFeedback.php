<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DaoTao\SinhVien;

class AiChatbotFeedback extends Model
{
    protected $table = 'ai_chatbot_feedback';

    protected $fillable = [
        'message_id',
        'sinh_vien_id',
        'danh_gia',
        'ly_do',
    ];

    /**
     * Tin nhắn được đánh giá
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(AiChatbotMessage::class, 'message_id');
    }

    /**
     * Sinh viên đánh giá
     */
    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Scope: Feedback hữu ích
     */
    public function scopeHuuIch($query)
    {
        return $query->where('danh_gia', 'huu_ich');
    }

    /**
     * Scope: Feedback không hữu ích
     */
    public function scopeKhongHuuIch($query)
    {
        return $query->where('danh_gia', 'khong_huu_ich');
    }

    /**
     * Kiểm tra feedback có hữu ích không
     */
    public function laHuuIch(): bool
    {
        return $this->danh_gia === 'huu_ich';
    }
}
