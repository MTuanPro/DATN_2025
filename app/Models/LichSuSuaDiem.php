<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuSuaDiem extends Model
{
    protected $table = 'lich_su_sua_diem';

    protected $fillable = [
        'nhap_diem_id',
        'lop_hoc_phan_sinh_vien_id',
        'cau_hinh_id',
        'cot_diem',
        'diem_cu',
        'diem_moi',
        'nguoi_sua_id',
        'loai_thao_tac',
        'ly_do',
        'ghi_chu',
    ];

    protected $casts = [
        'cot_diem' => 'integer',
        'diem_cu' => 'float',
        'diem_moi' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Điểm đã nhập
     */
    public function nhapDiem()
    {
        return $this->belongsTo(NhapDiem::class, 'nhap_diem_id');
    }

    /**
     * Relationship: Sinh viên trong lớp học phần
     */
    public function lopHocPhanSinhVien()
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    /**
     * Relationship: Cấu hình đầu điểm
     */
    public function cauHinh()
    {
        return $this->belongsTo(CauHinhDauDiem::class, 'cau_hinh_id');
    }

    /**
     * Relationship: Người sửa điểm (Giảng viên)
     */
    public function nguoiSua()
    {
        return $this->belongsTo(User::class, 'nguoi_sua_id');
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
     * Lấy lớp học phần
     */
    public function lopHocPhan()
    {
        return $this->hasOneThrough(
            LopHocPhan::class,
            LopHocPhanSinhVien::class,
            'id',
            'id',
            'lop_hoc_phan_sinh_vien_id',
            'lop_hoc_phan_id'
        );
    }
}
