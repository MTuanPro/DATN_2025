<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DaoTao\MonHoc;

class LopHocPhan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lop_hoc_phan';

    protected $fillable = [
        'ma_lop_hp',
        'ten_lop_hp',
        'mon_hoc_id',
        'hoc_ky_id',
        'nhom_lop',
        'suc_chua',
        'so_luong_dang_ky',
        'so_luong_toi_thieu',
        'hinh_thuc',
        'link_online',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ghi_chu',
        'trang_thai_lop',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Quan hệ với MonHoc
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Quan hệ với HocKy
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Quan hệ với LopHocPhanGiangVien (Phân công giảng dạy)
     */
    public function lopHocPhanGiangVien()
    {
        return $this->hasMany(PhanCongGiangDay::class, 'lop_hoc_phan_id');
    }

    /**
     * Lấy giảng viên chính
     */
    public function giangVienChinh()
    {
        return $this->hasOne(PhanCongGiangDay::class, 'lop_hoc_phan_id')
            ->where('vai_tro', 'giang_vien_chinh');
    }

    /**
     * Quan hệ với CauHinhDauDiem
     */
    public function cauHinhDauDiem()
    {
        return $this->hasMany(CauHinhDauDiem::class, 'lop_hoc_phan_id')
            ->orderBy('id');
    }
    public function lopHocPhanSinhVien()
    {
        return $this->hasMany(LopHocPhanSinhVien::class, 'lop_hoc_phan_id');
    }

    /**
     * Kiểm tra còn chỗ trống không
     */
    public function conChoTrong()
    {
        return $this->so_luong_dang_ky < $this->suc_chua;
    }

    /**
     * Kiểm tra đủ số lượng tối thiểu chưa
     */
    public function duSoLuongToiThieu()
    {
        return $this->so_luong_dang_ky >= $this->so_luong_toi_thieu;
    }

    /**
     * Lấy tên đầy đủ (Mã + Tên môn + Tên lớp)
     */
    public function getTenDayDuAttribute()
    {
        return $this->ma_lop_hp . ' - ' . $this->monHoc->ten_mon . ' (' . $this->ten_lop_hp . ')';
    }

    /**
     * Lấy tên trạng thái tiếng Việt
     */
    public function getTenTrangThaiAttribute()
    {
        $trangThai = [
            'mo_dang_ky' => 'Mở đăng ký',
            'dang_hoc' => 'Đang học',
            'ket_thuc' => 'Kết thúc',
            'huy' => 'Hủy',
        ];

        return $trangThai[$this->trang_thai_lop] ?? $this->trang_thai_lop;
    }
}
