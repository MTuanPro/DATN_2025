<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhoaHoc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'khoa_hoc';

    protected $fillable = [
        'ten_khoa_hoc',
        'nam_bat_dau',
        'nam_ket_thuc',
        'so_nam_dao_tao',
        'trang_thai',
        'mo_ta',
    ];

    protected $casts = [
        'nam_bat_dau' => 'integer',
        'nam_ket_thuc' => 'integer',
        'so_nam_dao_tao' => 'integer',
    ];
}
