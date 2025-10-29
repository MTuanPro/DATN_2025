<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;

class DangKyMonHocTam extends Model
{
    protected $table = 'dang_ky_mon_hoc_tam';

    protected $fillable = [
        'sinh_vien_id',
        'mon_hoc_id',
        'hoc_ky_id',
        'ngay_dang_ky',
        'uu_tien',
        'trang_thai',
        'ly_do_that_bai',
    ];

    protected $casts = [
        'ngay_dang_ky' => 'datetime',
        'uu_tien' => 'integer',
    ];

    /**
     * Relationship: Thuộc sinh viên
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: Môn học đăng ký
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Relationship: Thuộc học kỳ
     */
    public function hocKy()
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    /**
     * Relationship: Kết quả xếp lớp
     */
    public function lopHocPhanSinhVien()
    {
        return $this->hasOne(LopHocPhanSinhVien::class, 'dang_ky_tam_id');
    }

    /**
     * Scope: Chờ xếp lớp
     */
    public function scopeChoXepLop($query)
    {
        return $query->where('trang_thai', 'cho_xep_lop');
    }

    /**
     * Scope: Đã xếp lớp
     */
    public function scopeDaXepLop($query)
    {
        return $query->where('trang_thai', 'da_xep_lop');
    }

    /**
     * Scope: Thất bại
     */
    public function scopeThatBai($query)
    {
        return $query->where('trang_thai', 'that_bai');
    }

    /**
     * Get trạng thái label
     */
    public function getTrangThaiLabelAttribute()
    {
        $labels = [
            'cho_xep_lop' => 'Chờ xếp lớp',
            'da_xep_lop' => 'Đã xếp lớp',
            'that_bai' => 'Thất bại',
        ];

        return $labels[$this->trang_thai] ?? $this->trang_thai;
    }

    /**
     * Get trạng thái badge class
     */
    public function getTrangThaiBadgeAttribute()
    {
        $badges = [
            'cho_xep_lop' => 'warning',
            'da_xep_lop' => 'success',
            'that_bai' => 'danger',
        ];

        return $badges[$this->trang_thai] ?? 'secondary';
    }
}
