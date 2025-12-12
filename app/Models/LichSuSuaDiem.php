<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'diem_cu' => 'decimal:2',
        'diem_moi' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function nhapDiem(): BelongsTo
    {
        return $this->belongsTo(NhapDiem::class, 'nhap_diem_id');
    }

    public function lopHocPhanSinhVien(): BelongsTo
    {
        return $this->belongsTo(LopHocPhanSinhVien::class, 'lop_hoc_phan_sinh_vien_id');
    }

    public function cauHinh(): BelongsTo
    {
        return $this->belongsTo(CauHinhDauDiem::class, 'cau_hinh_id');
    }

    public function nguoiSua(): BelongsTo
    {
        return $this->belongsTo(GiangVien::class, 'nguoi_sua_id');
    }
}
