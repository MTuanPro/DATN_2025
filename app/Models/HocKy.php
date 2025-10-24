<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocKy extends Model
{
    use HasFactory;

    protected $table = 'hoc_ky';

    protected $fillable = [
        'ten_hoc_ky',
        'nam_hoc',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ngay_bat_dau_dang_ky',
        'ngay_ket_thuc_dang_ky',
        'la_hoc_ky_hien_tai',
        'mo_ta',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngay_bat_dau_dang_ky' => 'date',
        'ngay_ket_thuc_dang_ky' => 'date',
        'la_hoc_ky_hien_tai' => 'boolean',
    ];

    /**
     * Relationship: Học kỳ có nhiều lớp học phần
     */
    public function lopHocPhans()
    {
        return $this->hasMany(\App\Models\LopHocPhan::class, 'hoc_ky_id');
    }

    /**
     * Relationship: Học kỳ có nhiều đăng ký môn học tạm
     */
    public function dangKyMonHocTams()
    {
        return $this->hasMany(\App\Models\DangKyMonHocTam::class, 'hoc_ky_id');
    }

    /**
     * Relationship: Học kỳ có nhiều bảng điểm
     */
    public function bangDiems()
    {
        return $this->hasMany(\App\Models\BangDiem::class, 'hoc_ky_id');
    }

    /**
     * Relationship: Học kỳ có nhiều học phí học kỳ
     */
    public function hocPhiHocKys()
    {
        return $this->hasMany(\App\Models\HocPhiHocKy::class, 'hoc_ky_id');
    }

    /**
     * Scope: Lấy học kỳ hiện tại
     */
    public function scopeHienTai($query)
    {
        return $query->where('la_hoc_ky_hien_tai', true);
    }

    /**
     * Helper: Lấy tên đầy đủ học kỳ
     */
    public function getTenDayDuAttribute()
    {
        return $this->ten_hoc_ky . ' - ' . $this->nam_hoc;
    }
}
