<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Daotao\Khoa;

class MonHoc extends Model
{
    use SoftDeletes;

    protected $table = 'mon_hoc';

    protected $fillable = [
        'ma_mon',
        'ten_mon',
        'so_tin_chi',
        'so_tin_chi_ly_thuyet',
        'so_tin_chi_thuc_hanh',
        'mo_ta',
        'loai_mon',
        'khoa_id',
        'hinh_thuc_day',
        'thoi_luong_hoc',
        'so_buoi_hoc',
    ];

    protected $casts = [
        'so_tin_chi' => 'integer',
        'so_tin_chi_ly_thuyet' => 'integer',
        'so_tin_chi_thuc_hanh' => 'integer',
        'thoi_luong_hoc' => 'integer',
        'so_buoi_hoc' => 'integer',
    ];

    // Relationship: Môn học thuộc khoa
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }

    // Relationship: Các môn tiên quyết của môn học này
    public function monTienQuyet()
    {
        return $this->belongsToMany(
            MonHoc::class,
            'mon_hoc_tien_quyet',
            'mon_hoc_id',
            'mon_tien_quyet_id'
        )->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu')->withTimestamps();
    }

    // Relationship: Các môn học cần môn này làm tiên quyết
    public function monCanMonNay()
    {
        return $this->belongsToMany(
            MonHoc::class,
            'mon_hoc_tien_quyet',
            'mon_tien_quyet_id',
            'mon_hoc_id'
        )->withPivot('id', 'loai_tien_quyet', 'dieu_kien_qua_mon', 'ghi_chu')->withTimestamps();
    }
}
