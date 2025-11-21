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
        $now = now();
        $nowDate = $now->toDateString();
        
        // Lấy tất cả cấu hình có thể áp dụng
        $cauHinhs = self::where('ap_dung_tu_ngay', '<=', $nowDate)
            ->where(function ($query) use ($nowDate) {
                $query->whereNull('ap_dung_den_ngay')
                    ->orWhere('ap_dung_den_ngay', '>=', $nowDate);
            })
            ->orderBy('ap_dung_tu_ngay', 'desc')
            ->get();
        
        // Kiểm tra từng cấu hình với Carbon để đảm bảo chính xác
        foreach ($cauHinhs as $cauHinh) {
            if ($cauHinh->isActive()) {
                return $cauHinh;
            }
        }
        
        return null;
    }

    /**
     * Check if this config is currently active
     */
    public function isActive()
    {
        $now = now();
        
        // Parse dates - đảm bảo là Carbon instance để so sánh chính xác
        $tuNgay = \Carbon\Carbon::parse($this->ap_dung_tu_ngay)->startOfDay();
        
        // Nếu chưa đến ngày bắt đầu áp dụng
        if ($tuNgay->isAfter($now)) {
            return false;
        }
        
        // Nếu có ngày kết thúc
        if ($this->ap_dung_den_ngay) {
            $denNgay = \Carbon\Carbon::parse($this->ap_dung_den_ngay)->endOfDay();
            // Nếu đã qua ngày kết thúc
            if ($denNgay->isBefore($now)) {
                return false;
            }
        }
        
        // Đang trong khoảng thời gian áp dụng
        return true;
    }
}
