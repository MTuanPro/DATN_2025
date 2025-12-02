<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\MonHoc;

class CauHinhDauDiemMacDinh extends Model
{
    use HasFactory;

    protected $table = 'cau_hinh_dau_diem_mac_dinh';

    protected $fillable = [
        'mon_hoc_id',
        'ten_dau_diem',
        'ty_le',
        'so_cot',
    ];

    protected $casts = [
        'ty_le' => 'float',
        'so_cot' => 'integer',
    ];

    /**
     * Quan hệ với MonHoc
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }
}
