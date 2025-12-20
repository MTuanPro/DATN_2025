<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuDongHocPhi extends Model
{
    use HasFactory;

    protected $table = 'lich_su_dong_hoc_phi';

    protected $fillable = [
        'hoc_phi_hoc_ky_id',
        'so_tien_dong',
        'ngay_dong',
        'phuong_thuc_thanh_toan',
        'ma_giao_dich',
        'ngan_hang',
        'nguoi_thu_id',
        'bien_lai_file',
        'bien_lai_pdf',
        'ghi_chu',
    ];

    protected $casts = [
        'so_tien_dong' => 'float',
        'ngay_dong' => 'datetime',
    ];

    protected $dates = [
        'ngay_dong',
    ];

    /**
     * Relationship: LichSuDongHocPhi belongs to HocPhiHocKy
     */
    public function hocPhiHocKy()
    {
        return $this->belongsTo(HocPhiHocKy::class, 'hoc_phi_hoc_ky_id');
    }

    /**
     * Relationship: LichSuDongHocPhi belongs to DaoTao (người thu)
     */
    public function nguoiThu()
    {
        return $this->belongsTo(DaoTao::class, 'nguoi_thu_id');
    }

    /**
     * Generate unique transaction code
     */
    public static function generateMaGiaoDich()
    {
        do {
            $maGiaoDich = 'HP' . date('Ymd') . rand(1000, 9999);
        } while (self::where('ma_giao_dich', $maGiaoDich)->exists());

        return $maGiaoDich;
    }
}
