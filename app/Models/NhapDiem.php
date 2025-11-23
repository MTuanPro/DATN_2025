<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhapDiem extends Model
{
    use HasFactory;

    protected $table = 'nhap_diem';

    protected $fillable = [
        'lop_hoc_phan_sinh_vien_id',
        'cau_hinh_id',
        'cot_diem',
        'diem_so',
        'ghi_chu',
    ];

    protected $casts = [
        'cot_diem' => 'integer',
        'diem_so' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: NhapDiem belongs to LopHocPhanSinhVien
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: NhapDiem belongs to CauHinhDauDiem
     */
    public function cauHinh()
    {
        return $this->belongsTo(CauHinhDauDiem::class, 'cau_hinh_id');
    }

    /**
     * Lấy sinh viên
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
     * Scope: Lấy điểm theo lớp học phần
     */
    public function scopeTheoLopHocPhan($query, $lopHocPhanId)
    {
        return $query->whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId);
        });
    }

    /**
     * Scope: Lấy điểm theo sinh viên
     */
    public function scopeTheoSinhVien($query, $sinhVienId)
    {
        return $query->whereHas('lopHocPhanSinhVien', function ($q) use ($sinhVienId) {
            $q->where('sinh_vien_id', $sinhVienId);
        });
    }

    /**
     * Kiểm tra điểm có hợp lệ không (0-10)
     */
    public function laDiemHopLe()
    {
        return $this->diem_so >= 0 && $this->diem_so <= 10;
    }
}
