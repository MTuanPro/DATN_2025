<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietHocPhiMon extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_hoc_phi_mon';

    protected $fillable = [
        'hoc_phi_hoc_ky_id',
        'lop_hoc_phan_sinh_vien_id',
        'mon_hoc_id',
        'so_tin_chi',
        'don_gia_tin_chi',
        'thanh_tien',
    ];

    protected $casts = [
        'so_tin_chi' => 'integer',
        'don_gia_tin_chi' => 'float',
        'thanh_tien' => 'float',
    ];

    /**
     * Relationship: ChiTietHocPhiMon belongs to HocPhiHocKy
     */
    public function hocPhiHocKy()
    {
        return $this->belongsTo(HocPhiHocKy::class, 'hoc_phi_hoc_ky_id');
    }

    /**
     * Relationship: ChiTietHocPhiMon belongs to LopHocPhanSinhVien
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: ChiTietHocPhiMon belongs to MonHoc
     */
    public function monHoc()
    {
        return $this->belongsTo(\App\Models\DaoTao\MonHoc::class, 'mon_hoc_id');
    }
}
