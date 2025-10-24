<?php

namespace App\Models\DanhMuc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhongHoc extends Model
{
    use SoftDeletes;

    protected $table = 'phong_hoc';

    protected $fillable = [
        'ma_phong',
        'ten_phong',
        'suc_chua',
        'vi_tri',
        'loai_phong',
        'trang_thai',
        'mo_ta',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'suc_chua' => 'integer',
    ];

    /**
     * Relationships
     */

    // Một phòng có nhiều lịch học cố định
    public function lichHocCoDinhs()
    {
        return $this->hasMany(\App\Models\DaoTao\LichHocCoDinh::class, 'phong_hoc_id');
    }

    // Một phòng có nhiều lịch thi
    public function lichThis()
    {
        return $this->hasMany(\App\Models\DaoTao\LichThi::class, 'phong_thi_id');
    }
}
