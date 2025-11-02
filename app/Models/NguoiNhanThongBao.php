<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguoiNhanThongBao extends Model
{
    use HasFactory;

    protected $table = 'nguoi_nhan_thong_bao';

    protected $fillable = [
        'thong_bao_id',
        'nguoi_nhan_id',
        'da_doc',
        'ngay_doc',
        'da_gui_email',
        'ngay_gui_email',
        'da_gui_sms',
        'ngay_gui_sms',
    ];

    protected $casts = [
        'da_doc' => 'boolean',
        'ngay_doc' => 'datetime',
        'da_gui_email' => 'boolean',
        'ngay_gui_email' => 'datetime',
        'da_gui_sms' => 'boolean',
        'ngay_gui_sms' => 'datetime',
    ];

    /**
     * Relationship: Thông báo
     */
    public function thongBao()
    {
        return $this->belongsTo(ThongBao::class, 'thong_bao_id');
    }

    /**
     * Relationship: Người nhận (User)
     */
    public function nguoiNhan()
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id');
    }

    /**
     * Đánh dấu đã đọc
     */
    public function danhDauDaDoc()
    {
        $this->update([
            'da_doc' => true,
            'ngay_doc' => now(),
        ]);
    }

    /**
     * Scope: Chưa đọc
     */
    public function scopeChuaDoc($query)
    {
        return $query->where('da_doc', false);
    }

    /**
     * Scope: Đã đọc
     */
    public function scopeDaDoc($query)
    {
        return $query->where('da_doc', true);
    }
}
