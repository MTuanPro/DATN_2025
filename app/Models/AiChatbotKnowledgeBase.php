<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChatbotKnowledgeBase extends Model
{
    protected $table = 'ai_chatbot_knowledge_base';

    protected $fillable = [
        'chu_de',
        'danh_muc',
        'cau_hoi_mau',
        'cau_tra_loi',
        'tu_khoa',
        'do_uu_tien',
        'luot_truy_cap',
        'huu_ich',
        'nguoi_tao_id',
        'ngay_cap_nhat',
        'kich_hoat',
    ];

    protected $casts = [
        'ngay_cap_nhat' => 'datetime',
        'kich_hoat' => 'boolean',
        'do_uu_tien' => 'integer',
        'luot_truy_cap' => 'integer',
        'huu_ich' => 'integer',
    ];

    /**
     * Người tạo knowledge base
     */
    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Các tin nhắn sử dụng knowledge base này
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiChatbotMessage::class, 'knowledge_base_id');
    }

    /**
     * Scope: Chỉ lấy knowledge base đã kích hoạt
     */
    public function scopeKichHoat($query)
    {
        return $query->where('kich_hoat', true);
    }

    /**
     * Scope: Tìm kiếm theo chủ đề
     */
    public function scopeChuDe($query, $chuDe)
    {
        return $query->where('chu_de', $chuDe);
    }

    /**
     * Scope: Sắp xếp theo độ ưu tiên
     */
    public function scopeUuTien($query)
    {
        return $query->orderBy('do_uu_tien', 'desc');
    }

    /**
     * Tăng lượt truy cập
     */
    public function tangLuotTruyCap(): void
    {
        $this->increment('luot_truy_cap');
    }

    /**
     * Tăng đánh giá hữu ích
     */
    public function tangHuuIch(): void
    {
        $this->increment('huu_ich');
    }

    /**
     * Giảm đánh giá hữu ích
     */
    public function giamHuuIch(): void
    {
        $this->decrement('huu_ich');
    }

    /**
     * Tính tỷ lệ hữu ích (%)
     */
    public function tyLeHuuIch(): float
    {
        if ($this->luot_truy_cap == 0) {
            return 0;
        }
        return round(($this->huu_ich / $this->luot_truy_cap) * 100, 2);
    }
}
