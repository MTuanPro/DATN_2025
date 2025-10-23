<?php

namespace App\Models\DanhMuc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrinhDo extends Model
{
    use SoftDeletes;

    protected $table = 'dm_trinh_do';

    protected $fillable = [
        'ten_trinh_do',
    ];

    /**
     * Relationships
     */

    // Một trình độ có nhiều sinh viên
    public function sinhViens()
    {
        return $this->hasMany(\App\Models\DaoTao\SinhVien::class, 'trinh_do_id');
    }
}
