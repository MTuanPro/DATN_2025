<?php

namespace App\Models\Daotao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonHocTienQuyet extends Model
{
    use SoftDeletes;

    protected $table = 'mon_hoc_tien_quyet';

    protected $fillable = [
        'mon_hoc_id',
        'mon_tien_quyet_id',
        'loai_tien_quyet',
        'dieu_kien_qua_mon',
        'ghi_chu',
    ];

    protected $casts = [
        'dieu_kien_qua_mon' => 'boolean',
    ];

    // Môn học chính
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    // Môn tiên quyết
    public function monTienQuyet()
    {
        return $this->belongsTo(MonHoc::class, 'mon_tien_quyet_id');
    }
}
