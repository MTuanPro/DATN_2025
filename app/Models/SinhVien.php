<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SinhVien extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sinh_vien';

    protected $fillable = [
        'ma_sinh_vien',
        'ho_ten',
        'email',
        'ngay_sinh',
        'gioi_tinh',
        'so_dien_thoai',
        'so_nha_duong',
        'phuong_xa',
        'quan_huyen',
        'tinh_thanh',
        'can_cuoc_cong_dan',
        'ngay_cap_cccd',
        'noi_cap_cccd',
        'anh_dai_dien',
        'khoa_hoc_id',
        'lop_hanh_chinh_id',
        'nganh_id',
        'chuyen_nganh_id',
        'ky_hien_tai',
        'trang_thai_hoc_tap_id',
        'giang_vien_chu_nhiem_id',
        'user_id',
    ];

    protected $casts = [
        'ngay_sinh' => 'date',
        'ngay_cap_cccd' => 'date',
        'ky_hien_tai' => 'integer',
    ];

    /**
     * Relationship: SinhVien belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: SinhVien belongs to KhoaHoc
     */
    public function khoaHoc()
    {
        return $this->belongsTo(\App\Models\DaoTao\KhoaHoc::class, 'khoa_hoc_id');
    }

    /**
     * Relationship: SinhVien belongs to LopHanhChinh
     */
    public function lopHanhChinh()
    {
        return $this->belongsTo(\App\Models\DaoTao\LopHanhChinh::class, 'lop_hanh_chinh_id');
    }

    /**
     * Relationship: SinhVien belongs to Nganh
     */
    public function nganh()
    {
        return $this->belongsTo(\App\Models\DaoTao\Nganh::class, 'nganh_id');
    }

    /**
     * Relationship: SinhVien belongs to ChuyenNganh (optional)
     */
    public function chuyenNganh()
    {
        return $this->belongsTo(\App\Models\DaoTao\ChuyenNganh::class, 'chuyen_nganh_id');
    }

    /**
     * Relationship: SinhVien belongs to TrangThaiHocTap
     */
    public function trangThaiHocTap()
    {
        return $this->belongsTo(\App\Models\DanhMuc\TrangThaiHocTap::class, 'trang_thai_hoc_tap_id');
    }

    /**
     * Relationship: SinhVien has one academic advisor (GiangVien)
     */
    public function giangVienChuNhiem()
    {
        return $this->belongsTo(GiangVien::class, 'giang_vien_chu_nhiem_id');
    }

    /**
     * Relationship: SinhVien has many LopHocPhanSinhVien (class enrollments)
     */
    public function lopHocPhanSinhVien()
    {
        return $this->hasMany(LopHocPhanSinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: SinhVien has many KetQuaHocTap (academic results)
     */
    public function ketQuaHocTap()
    {
        return $this->hasMany(KetQuaHocTap::class, 'sinh_vien_id');
    }

    /**
     * Relationship: SinhVien has many DangKyMonHocTam (temporary course registrations)
     */
    public function dangKyMonHocTam()
    {
        return $this->hasMany(DangKyMonHocTam::class, 'sinh_vien_id');
    }

    /**
     * Relationship: SinhVien has many HocPhiHocKy (tuition fees per semester)
     */
    public function hocPhiHocKy()
    {
        return $this->hasMany(HocPhiHocKy::class, 'sinh_vien_id');
    }

    /**
     * Relationship: SinhVien has many DiemDanh (attendance records)
     */
    public function diemDanh()
    {
        return $this->hasMany(DiemDanh::class, 'sinh_vien_id');
    }
}
