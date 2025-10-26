<?php

namespace App\Models\DaoTao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\LopHanhChinh;
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

    // Relationship: Lớp hành chính
    public function lopHanhChinh()
    {
        return $this->belongsTo(LopHanhChinh::class, 'lop_hanh_chinh_id');
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

    // Relationship: Giảng viên chủ nhiệm
    public function giangVienChuNhiem()
    {
        return $this->belongsTo(GiangVien::class, 'giang_vien_chu_nhiem_id');
    }
}
