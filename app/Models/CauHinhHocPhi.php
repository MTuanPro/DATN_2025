<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhHocPhi extends Model
{
    use HasFactory;

    protected $table = 'cau_hinh_hoc_phi';

    protected $fillable = [
        'nam_hoc',
        'don_gia_tren_tin_chi',
        'phi_dich_vu',
        'ap_dung_tu_ngay',
        'ap_dung_den_ngay',
        'ghi_chu',
    ];

    protected $casts = [
        'don_gia_tren_tin_chi' => 'float',
        'phi_dich_vu' => 'float',
        'ap_dung_tu_ngay' => 'date',
        'ap_dung_den_ngay' => 'date',
    ];

    /**
     * Get cấu hình học phí hiện tại (đang áp dụng)
     */
    public static function getCauHinhHienTai()
    {
        $now = now()->toDateString();
        return self::where('ap_dung_tu_ngay', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('ap_dung_den_ngay')
                    ->orWhere('ap_dung_den_ngay', '>=', $now);
            })
            ->orderBy('ap_dung_tu_ngay', 'desc')
            ->first();
    }

    /**
     * Check if this config is currently active
     */
    public function isActive()
    {
        $now = now()->toDateString();
        return $this->ap_dung_tu_ngay <= $now &&
            (is_null($this->ap_dung_den_ngay) || $this->ap_dung_den_ngay >= $now);
    }
}
