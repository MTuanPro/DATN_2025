<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangDiem extends Model
{
    use HasFactory;

    protected $table = 'bang_diem';

    protected $fillable = [
        'sinh_vien_id',
        'hoc_ky_id',
        'tong_tin_chi_dang_ky',
        'tong_tin_chi_dat',
        'diem_trung_binh_he_10',
        'diem_trung_binh_he_4',
        'xep_loai_hoc_tap',
        'da_cong_bo',
        'ngay_cong_bo',
    ];

    protected $casts = [
        'tong_tin_chi_dang_ky' => 'integer',
        'tong_tin_chi_dat' => 'integer',
        'diem_trung_binh_he_10' => 'float',
        'diem_trung_binh_he_4' => 'float',
        'da_cong_bo' => 'boolean',
        'ngay_cong_bo' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: BangDiem belongs to SinhVien
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: BangDiem belongs to HocKy
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Lấy xếp loại học tập badge
     */
    public function getXepLoaiBadgeAttribute()
    {
        $badges = [
            'xuat_sac' => 'success',
            'gioi' => 'info',
            'kha' => 'primary',
            'trung_binh' => 'warning',
            'yeu' => 'danger',
            'kem' => 'danger',
        ];

        return $badges[$this->xep_loai_hoc_tap] ?? 'secondary';
    }

    /**
     * Tính xếp loại học tập từ điểm TB
     */
    public static function tinhXepLoai($diemTB, $tinChiDat, $tinChiDangKy)
    {
        // Phải đạt ít nhất 80% số tín chỉ đăng ký
        if ($tinChiDangKy > 0 && ($tinChiDat / $tinChiDangKy) < 0.8) {
            return 'yeu';
        }

        if ($diemTB >= 3.6) return 'xuat_sac';
        if ($diemTB >= 3.2) return 'gioi';
        if ($diemTB >= 2.5) return 'kha';
        if ($diemTB >= 2.0) return 'trung_binh';
        if ($diemTB >= 1.0) return 'yeu';
        return 'kem';
    }

    /**
     * Lấy label xếp loại
     */
    public function getXepLoaiLabelAttribute()
    {
        $labels = [
            'xuat_sac' => 'Xuất sắc',
            'gioi' => 'Giỏi',
            'kha' => 'Khá',
            'trung_binh' => 'Trung bình',
            'yeu' => 'Yếu',
            'kem' => 'Kém',
        ];

        return $labels[$this->xep_loai_hoc_tap] ?? 'Chưa xếp loại';
    }
}