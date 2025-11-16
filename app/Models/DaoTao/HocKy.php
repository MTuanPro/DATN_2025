<?php

namespace App\Models\DaoTao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HocKy extends Model
{
    protected $table = 'hoc_ky';

    protected $fillable = [
        'ten_hoc_ky',
        'nam_hoc',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ngay_bat_dau_dang_ky',
        'ngay_ket_thuc_dang_ky',
        'la_hoc_ky_hien_tai',
        'mo_ta'
    ];

    protected $casts = [
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngay_bat_dau_dang_ky' => 'datetime',
        'ngay_ket_thuc_dang_ky' => 'datetime',
        'la_hoc_ky_hien_tai' => 'boolean'
    ];

    public function lopHocPhan(): HasMany
    {
        return $this->hasMany(\App\Models\LopHocPhan::class, 'hoc_ky_id');
    }
}
