<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    protected $table = 'quyen';

    protected $fillable = [
        'ma_quyen',
        'ten_quyen',
        'mo_ta',
        'nhom_quyen_id',
    ];

    /**
     * Relationship: Quyền thuộc về một nhóm quyền
     */
    public function nhomQuyen()
    {
        return $this->belongsTo(NhomQuyen::class, 'nhom_quyen_id');
    }

    /**
     * Relationship: Quyền có nhiều vai trò qua bảng vai_tro_quyen
     */
    public function vaiTros()
    {
        return $this->belongsToMany(
            VaiTro::class,
            'vai_tro_quyen',
            'quyen_id',
            'vai_tro_id'
        )->withTimestamps();
    }
}
