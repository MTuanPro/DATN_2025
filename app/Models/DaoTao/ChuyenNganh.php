<?php

namespace App\Models\Daotao;

use App\Models\DaoTao\Nganh;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChuyenNganh extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chuyen_nganh';

    protected $fillable = [
        'ma_chuyen_nganh',
        'ten_chuyen_nganh',
        'nganh_id',
        'tong_tin_chi_toi_thieu',
        'mo_ta',
    ];

    // Chuyên ngành thuộc về 1 ngành
    public function nganh()
    {
        return $this->belongsTo(Nganh::class, 'nganh_id');
    }
}
