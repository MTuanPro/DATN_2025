<?php

namespace App\Models\Daotao;

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

    protected $casts = [
        'suc_chua' => 'integer',
    ];
}
