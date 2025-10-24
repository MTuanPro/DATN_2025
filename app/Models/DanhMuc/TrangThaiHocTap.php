<?php

namespace App\Models\DanhMuc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrangThaiHocTap extends Model
{
    use SoftDeletes;

    protected $table = 'trang_thai_hoc_tap';

    protected $fillable = [
        'ten_trang_thai',
        'mo_ta',
    ];

    /**
     * Relationships
     */

    // Một trạng thái có nhiều sinh viên
    public function sinhViens()
    {
        return $this->hasMany(\App\Models\DaoTao\SinhVien::class, 'trang_thai_hoc_tap_id');
    }
}
