<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiemDanh extends Model
{
    use HasFactory;

    protected $table = 'diem_danh';

    protected $fillable = [
        'lop_hoc_phan_sinh_vien_id',
        'lich_hoc_chi_tiet_id',
        'trang_thai',
        'thoi_gian_diem_danh',
        'ghi_chu',
    ];

    protected $casts = [
        'thoi_gian_diem_danh' => 'datetime',
    ];

    /**
     * Relationship: DiemDanh belongs to LopHocPhanSinhVien
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: DiemDanh belongs to LichHocChiTiet
     */
    public function lichHocChiTiet()
    {
        return $this->belongsTo(LichHocChiTiet::class, 'lich_hoc_chi_tiet_id');
    }

    /**
     * Get SinhVien through LopHocPhanSinhVien
     */
    public function sinhVien()
    {
        return $this->hasOneThrough(
            SinhVien::class,
            LopHocPhanSinhVien::class,
            'id',
            'id',
            'lop_hoc_phan_sinh_vien_id',
            'sinh_vien_id'
        );
    }
}
