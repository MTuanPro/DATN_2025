<?php

namespace App\Models\DaoTao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DaoTao\KhoaHoc;
use App\Models\DaoTao\Nganh;
use App\Models\GiangVien;
use App\Models\DaoTao\SinhVien;

class LopHanhChinh extends Model
{
    use SoftDeletes;

    protected $table = 'lop_hanh_chinh';

    protected $fillable = [
        'ma_lop',
        'ten_lop',
        'khoa_hoc_id',
        'nganh_id',
        'giang_vien_chu_nhiem_id',
        'si_so',
    ];

    protected $casts = [
        'si_so' => 'integer',
    ];

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

    // Relationship: Giảng viên chủ nhiệm
    public function giangVienChuNhiem()
    {
        return $this->belongsTo(GiangVien::class, 'giang_vien_chu_nhiem_id');
    }

    // Relationship: Sinh viên trong lớp
    public function sinhVien()
    {
        return $this->hasMany(SinhVien::class, 'lop_hanh_chinh_id');
    }
}
