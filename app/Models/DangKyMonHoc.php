<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangKyMonHoc extends Model
{
    use HasFactory;

    protected $table = 'dang_ky_mon_hocs';

    protected $fillable = [
        'sinh_vien_id',
        'lop_hoc_phan_id',
        'hoc_ky_id',
        'trang_thai',
        'ghi_chu',
        'thoi_gian_dang_ky',
        'thoi_gian_duyet',
        'nguoi_duyet_id'
    ];

    protected $casts = [
        'thoi_gian_dang_ky' => 'datetime',
        'thoi_gian_duyet' => 'datetime'
    ];

    // Relationships
    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function lopHocPhan(): BelongsTo
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    public function hocKy(): BelongsTo
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    public function nguoiDuyet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }
}
