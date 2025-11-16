<?php

namespace App\Models;

use App\Models\DaoTao\SinhVien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChatbotConversation extends Model
{
    protected $table = 'ai_chatbot_conversation';

    protected $fillable = [
        'sinh_vien_id',
        'session_id',
        'tieu_de_chat',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
    ];

    /**
     * Sinh viên sở hữu cuộc hội thoại
     */
    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Các tin nhắn trong cuộc hội thoại
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiChatbotMessage::class, 'conversation_id')->orderBy('thoi_gian_gui');
    }

    /**
     * Scope: Cuộc hội thoại đang mở
     */
    public function scopeDangMo($query)
    {
        return $query->where('trang_thai', 'dang_mo');
    }

    /**
     * Scope: Cuộc hội thoại đã đóng
     */
    public function scopeDaDong($query)
    {
        return $query->where('trang_thai', 'da_dong');
    }

    /**
     * Đóng cuộc hội thoại
     */
    public function dongCuocHoiThoai(): void
    {
        $this->update([
            'trang_thai' => 'da_dong',
            'ngay_ket_thuc' => now(),
        ]);
    }

    /**
     * Mở lại cuộc hội thoại
     */
    public function moLaiCuocHoiThoai(): void
    {
        $this->update([
            'trang_thai' => 'dang_mo',
            'ngay_ket_thuc' => null,
        ]);
    }

    /**
     * Tự động tạo tiêu đề từ câu hỏi đầu tiên
     */
    public function taoTieuDeTuDong(): void
    {
        if (!$this->tieu_de_chat) {
            $firstUserMessage = $this->messages()
                ->where('nguoi_gui', 'user')
                ->first();
            
            if ($firstUserMessage) {
                $tieuDe = mb_substr($firstUserMessage->noi_dung, 0, 50);
                if (mb_strlen($firstUserMessage->noi_dung) > 50) {
                    $tieuDe .= '...';
                }
                $this->update(['tieu_de_chat' => $tieuDe]);
            }
        }
    }

    /**
     * Đếm số tin nhắn trong cuộc hội thoại
     */
    public function soLuongTinNhan(): int
    {
        return $this->messages()->count();
    }
}
