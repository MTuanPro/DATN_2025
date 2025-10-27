<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetQuaHocTap extends Model
{
    protected $table = 'ket_qua_hoc_tap';

    protected $fillable = [
        'lop_hoc_phan_sinh_vien_id',
        'diem_he_10',
        'diem_he_4',
        'diem_chu',
        'qua_mon',
        'ghi_chu',
    ];

    protected $casts = [
        'diem_he_10' => 'float',
        'diem_he_4' => 'float',
        'qua_mon' => 'boolean',
    ];

    /**
     * Relationship: Thuộc lớp học phần sinh viên
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Scope: Qua môn
     */
    public function scopeQuaMon($query)
    {
        return $query->where('qua_mon', true);
    }

    /**
     * Scope: Không qua môn
     */
    public function scopeKhongQuaMon($query)
    {
        return $query->where('qua_mon', false);
    }

    /**
     * Get điểm chữ badge class
     */
    public function getDiemChuBadgeAttribute()
    {
        $badges = [
            'A' => 'success',
            'B+' => 'success',
            'B' => 'info',
            'C+' => 'info',
            'C' => 'warning',
            'D+' => 'warning',
            'D' => 'danger',
            'F' => 'danger',
        ];

        return $badges[$this->diem_chu] ?? 'secondary';
    }

    /**
     * Tính điểm chữ từ điểm hệ 10
     */
    public static function tinhDiemChu($diemHe10)
    {
        if ($diemHe10 >= 8.5) return 'A';
        if ($diemHe10 >= 8.0) return 'B+';
        if ($diemHe10 >= 7.0) return 'B';
        if ($diemHe10 >= 6.5) return 'C+';
        if ($diemHe10 >= 5.5) return 'C';
        if ($diemHe10 >= 5.0) return 'D+';
        if ($diemHe10 >= 4.0) return 'D';
        return 'F';
    }

    /**
     * Tính điểm hệ 4 từ điểm hệ 10
     */
    public static function tinhDiemHe4($diemHe10)
    {
        if ($diemHe10 >= 8.5) return 4.0;
        if ($diemHe10 >= 8.0) return 3.5;
        if ($diemHe10 >= 7.0) return 3.0;
        if ($diemHe10 >= 6.5) return 2.5;
        if ($diemHe10 >= 5.5) return 2.0;
        if ($diemHe10 >= 5.0) return 1.5;
        if ($diemHe10 >= 4.0) return 1.0;
        return 0.0;
    }

    /**
     * Kiểm tra qua môn
     */
    public static function kiemTraQuaMon($diemHe10)
    {
        return $diemHe10 >= 4.0;
    }

    /**
     * Boot method để tự động tính toán
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ketQua) {
            if ($ketQua->diem_he_10 !== null) {
                $ketQua->diem_he_4 = self::tinhDiemHe4($ketQua->diem_he_10);
                $ketQua->diem_chu = self::tinhDiemChu($ketQua->diem_he_10);
                $ketQua->qua_mon = self::kiemTraQuaMon($ketQua->diem_he_10);
            }
        });
    }
}
