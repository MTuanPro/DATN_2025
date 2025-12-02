<?php

namespace App\Models\DaoTao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\DaoTao\ChuyenNganh;
use App\Models\DanhMuc\TrangThaiHocTap;
use App\Models\GiangVien;

class SinhVien extends Model
{
    use SoftDeletes;

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
        'nganh_id',
        'chuyen_nganh_id',
        'ky_hien_tai',
        'trang_thai_hoc_tap_id',
        'user_id',
    ];

    protected $casts = [
        'ngay_sinh' => 'date',
        'ngay_cap_cccd' => 'date',
        'ky_hien_tai' => 'integer',
    ];

    // Relationship: User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Khóa học
    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'khoa_hoc_id');
    }

    // Relationship: Ngành
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'nganh_id');
    }

    // Relationship: Chuyên ngành
    public function chuyenNganh()
    {
        return $this->belongsTo(ChuyenNganh::class, 'chuyen_nganh_id');
    }

    // Relationship: Trạng thái học tập
    public function trangThaiHocTap()
    {
        return $this->belongsTo(TrangThaiHocTap::class, 'trang_thai_hoc_tap_id');
    }

    // Relationship: Đăng ký môn học tạm
    public function dangKyMonHocTams()
    {
        return $this->hasMany(\App\Models\DangKyMonHocTam::class, 'sinh_vien_id');
    }

    // Relationship: Lớp học phần đã đăng ký
    public function lopHocPhanSinhViens()
    {
        return $this->hasMany(\App\Models\LopHocPhanSinhVien::class, 'sinh_vien_id');
    }

    // Relationship: Các lớp học phần (through pivot)
    public function lopHocPhans()
    {
        return $this->belongsToMany(
            \App\Models\LopHocPhan::class,
            'lop_hoc_phan_sinh_vien',
            'sinh_vien_id',
            'lop_hoc_phan_id'
        )->withPivot([
            'dang_ky_tam_id',
            'ngay_dang_ky',
            'ngay_xep_lop',
            'nguoi_duyet_id',
            'ngay_duyet',
            'phuong_thuc_xep',
            'trang_thai',
            'ly_do_huy'
        ])->withTimestamps();
    }

    // Relationship: Kết quả học tập (through lop_hoc_phan_sinh_vien)
    public function ketQuaHocTap()
    {
        return $this->hasManyThrough(
            \App\Models\KetQuaHocTap::class,
            \App\Models\LopHocPhanSinhVien::class,
            'sinh_vien_id', // Foreign key on lop_hoc_phan_sinh_vien table
            'lop_hoc_phan_sinh_vien_id', // Foreign key on ket_qua_hoc_tap table
            'id', // Local key on sinh_vien table
            'id' // Local key on lop_hoc_phan_sinh_vien table
        );
    }

    // Alias for ketQuaHocTap (with 's' for consistency)
    public function ketQuaHocTaps()
    {
        return $this->ketQuaHocTap();
    }

    // Relationship: Điểm danh
    public function diemDanh()
    {
        return $this->hasManyThrough(
            \App\Models\DiemDanh::class,
            \App\Models\LopHocPhanSinhVien::class,
            'sinh_vien_id', // Foreign key on lop_hoc_phan_sinh_vien table
            'lop_hoc_phan_sinh_vien_id', // Foreign key on diem_danh table
            'id', // Local key on sinh_vien table
            'id' // Local key on lop_hoc_phan_sinh_vien table
        );
    }

    // Relationship: Học phí học kỳ
    public function hocPhiHocKy()
    {
        return $this->hasMany(\App\Models\HocPhiHocKy::class, 'sinh_vien_id');
    }

    // Relationship: Cảnh báo học vụ
    public function canhBaoHocVu()
    {
        return $this->hasMany(\App\Models\CanhBaoHocVu::class, 'sinh_vien_id');
    }
}
