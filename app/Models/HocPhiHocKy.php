<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocPhiHocKy extends Model
{
    use HasFactory;

    protected $table = 'hoc_phi_hoc_ky';

    protected $fillable = [
        'sinh_vien_id',
        'hoc_ky_id',
        'tong_tin_chi_dang_ky',
        'tong_hoc_phi_mon_hoc',
        'phi_dich_vu',
        'tong_so_tien',
        'so_tien_da_dong',
        'so_tien_con_lai',
        'han_dong',
        'ngay_dong_lan_cuoi',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'tong_tin_chi_dang_ky' => 'integer',
        'tong_hoc_phi_mon_hoc' => 'float',
        'phi_dich_vu' => 'float',
        'tong_so_tien' => 'float',
        'so_tien_da_dong' => 'float',
        'so_tien_con_lai' => 'float',
        'han_dong' => 'date',
        'ngay_dong_lan_cuoi' => 'datetime',
    ];

    /**
     * Relationship: HocPhiHocKy belongs to SinhVien
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: HocPhiHocKy belongs to HocKy
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Relationship: HocPhiHocKy has many ChiTietHocPhiMon
     */
    public function chiTietHocPhiMon()
    {
        return $this->hasMany(ChiTietHocPhiMon::class, 'hoc_phi_hoc_ky_id');
    }
}
