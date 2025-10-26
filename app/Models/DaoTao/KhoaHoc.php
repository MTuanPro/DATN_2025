<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhoaHoc extends Model
{
    use SoftDeletes;

    protected $table = 'khoa_hoc';

    protected $fillable = [
        'ma_khoa_hoc',
        'ten_khoa_hoc',
        'nam_bat_dau',
        'nam_ket_thuc',
        'so_nam_dao_tao',
        'trang_thai',
        'mo_ta',
    ];

    public function lopHanhChinhs()
    {
        return $this->hasMany(LopHanhChinh::class, 'khoa_hoc_id');
    }
}
