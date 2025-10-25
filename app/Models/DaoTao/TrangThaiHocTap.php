<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrangThaiHocTap extends Model
{
    use SoftDeletes;

    protected $table = 'trang_thai_hoc_tap';

    protected $fillable = [
        'ten_trang_thai',
        'ma_trang_thai',
        'mo_ta',
    ];
}
