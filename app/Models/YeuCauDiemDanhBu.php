<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\SinhVien;
use Carbon\Carbon;

class YeuCauDiemDanhBu extends Model
{
    use HasFactory;

    protected $table = 'yeu_cau_diem_danh_bu';

    protected $fillable = [
        'lop_hoc_phan_sinh_vien_id',
        'lich_hoc_chi_tiet_id',
        'ly_do',
        'trang_thai',
        'ly_do_tu_choi',
        'ngay_gui',
        'ngay_duyet',
        'nguoi_duyet_id',
    ];

    protected $casts = [
        'ngay_gui' => 'datetime',
        'ngay_duyet' => 'datetime',
    ];

    /**
     * Relationship: YeuCauDiemDanhBu belongs to LopHocPhanSinhVien
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: YeuCauDiemDanhBu belongs to LichHocChiTiet
     */
    public function lichHocChiTiet()
    {
        return $this->belongsTo(LichHocChiTiet::class, 'lich_hoc_chi_tiet_id');
    }

    /**
     * Relationship: YeuCauDiemDanhBu belongs to User (người duyệt)
     */
    public function nguoiDuyet()
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
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

    /**
     * Get status badge color
     */
    public function getTrangThaiBadgeAttribute()
    {
        $badges = [
            'cho_duyet' => 'warning',
            'da_duyet' => 'success',
            'tu_choi' => 'danger',
        ];

        return $badges[$this->trang_thai] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getTrangThaiTextAttribute()
    {
        $texts = [
            'cho_duyet' => 'Chờ duyệt',
            'da_duyet' => 'Đã duyệt',
            'tu_choi' => 'Từ chối',
        ];

        return $texts[$this->trang_thai] ?? $this->trang_thai;
    }
}
