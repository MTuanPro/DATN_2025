<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichThiSinhVien extends Model
{
    use SoftDeletes;

    protected $table = 'lich_thi_sinh_vien';

    protected $fillable = [
        'lich_thi_id',
        'sinh_vien_id',
        'phong_thi_id',
        'so_bao_danh',
        'trang_thai',
        'ghi_chu',
    ];

    /**
     * Relationship: Thuộc về một lịch thi
     */
    public function lichThi()
    {
        return $this->belongsTo(LichThi::class, 'lich_thi_id');
    }

    /**
     * Relationship: Thuộc về một sinh viên
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    /**
     * Relationship: Thi tại một phòng
     */
    public function phongThi()
    {
        return $this->belongsTo(\App\Models\DanhMuc\PhongHoc::class, 'phong_thi_id');
    }
}
