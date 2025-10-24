<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Daotao\ChuyenNganh;
use App\Models\Daotao\MonHoc;

class ChuongTrinhKhung extends Model
{
    use SoftDeletes;

    protected $table = 'chuong_trinh_khung';

    protected $fillable = [
        'chuyen_nganh_id',
        'mon_hoc_id',
        'hoc_ky_goi_y',
        'loai_mon_hoc',
        'bat_buoc',
        'so_tin_chi_toi_thieu',
        'thu_tu_hoc',
        'ghi_chu',
    ];

    protected $casts = [
        'hoc_ky_goi_y' => 'integer',
        'bat_buoc' => 'boolean',
        'so_tin_chi_toi_thieu' => 'integer',
        'thu_tu_hoc' => 'integer',
    ];

    // Relationship: Thuộc chuyên ngành
    public function chuyenNganh()
    {
        return $this->belongsTo(ChuyenNganh::class, 'chuyen_nganh_id');
    }

    // Relationship: Môn học trong CTĐT
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }
}
