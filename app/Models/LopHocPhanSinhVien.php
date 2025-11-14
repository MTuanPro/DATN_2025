<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LopHocPhan;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao;

class LopHocPhanSinhVien extends Model
{
    protected $table = 'lop_hoc_phan_sinh_vien';

    protected $fillable = [
        'lop_hoc_phan_id',
        'sinh_vien_id',
        'dang_ky_tam_id',
        'ngay_dang_ky',
        'ngay_xep_lop',
        'nguoi_duyet_id',
        'ngay_duyet',
        'phuong_thuc_xep',
        'trang_thai',
        'ly_do_huy',
    ];

    protected $casts = [
        'ngay_dang_ky' => 'datetime',
        'ngay_xep_lop' => 'datetime',
        'ngay_duyet' => 'datetime',
    ];

    /**
     * Relationship: Thuộc lớp học phần
     */
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    /**
     * Relationship: Thuộc sinh viên
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: Đăng ký tạm ban đầu
     */
    public function dangKyTam()
    {
        return $this->belongsTo(DangKyMonHocTam::class, 'dang_ky_tam_id');
    }

    /**
     * Relationship: Người duyệt
     */
    public function nguoiDuyet()
    {
        return $this->belongsTo(DaoTao::class, 'nguoi_duyet_id');
    }

    /**
     * Relationship: Kết quả học tập
     */
    public function ketQuaHocTap()
    {
        return $this->hasOne(KetQuaHocTap::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: Các điểm đã nhập
     */
    public function nhapDiems()
    {
        return $this->hasMany(NhapDiem::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Scope: Đã xếp lớp
     */
    public function scopeDaXepLop($query)
    {
        return $query->where('trang_thai', 'da_xep_lop');
    }

    /**
     * Scope: Đang học
     */
    public function scopeDangHoc($query)
    {
        return $query->where('trang_thai', 'dang_hoc');
    }

    /**
     * Scope: Đã hoàn thành
     */
    public function scopeDaHoanThanh($query)
    {
        return $query->where('trang_thai', 'da_hoan_thanh');
    }

    /**
     * Scope: Bỏ học
     */
    public function scopeBoHoc($query)
    {
        return $query->where('trang_thai', 'bo_hoc');
    }

    /**
     * Scope: Hủy đăng ký
     */
    public function scopeHuyDangKy($query)
    {
        return $query->where('trang_thai', 'huy_dang_ky');
    }

    /**
     * Get trạng thái label
     */
    public function getTrangThaiLabelAttribute()
    {
        $labels = [
            'da_xep_lop' => 'Đã xếp lớp',
            'dang_hoc' => 'Đang học',
            'da_hoan_thanh' => 'Đã hoàn thành',
            'bo_hoc' => 'Bỏ học',
            'huy_dang_ky' => 'Hủy đăng ký',
        ];

        return $labels[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get trạng thái badge class
     */
    public function getTrangThaiBadgeAttribute()
    {
        $badges = [
            'da_xep_lop' => 'info',
            'dang_hoc' => 'primary',
            'da_hoan_thanh' => 'success',
            'bo_hoc' => 'danger',
            'huy_dang_ky' => 'secondary',
        ];

        return $badges[$this->trang_thai] ?? 'secondary';
    }

    /**
     * Get phương thức xếp label
     */
    public function getPhuongThucXepLabelAttribute()
    {
        return $this->phuong_thuc_xep === 'tu_dong' ? 'Tự động' : 'Thủ công';
    }
}
