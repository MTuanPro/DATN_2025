<?php

namespace App\Models\DaoTao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DaoTao\Nganh;

class ChuyenNganh extends Model
{
    use SoftDeletes;

    protected $table = 'chuyen_nganh';

    protected $fillable = [
        'ma_chuyen_nganh',
        'ten_chuyen_nganh',
        'nganh_id',
        'tong_tin_chi_toi_thieu',
        'mo_ta',
    ];

    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'nganh_id');
    }

    public function chuongTrinhKhung()
    {
        return $this->hasMany(ChuongTrinhKhung::class, 'chuyen_nganh_id');
    }
}
