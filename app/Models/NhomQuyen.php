<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhomQuyen extends Model
{
    protected $table = 'nhom_quyen';

    protected $fillable = [
        'ma_nhom',
        'ten_nhom',
        'mo_ta',
    ];

    /**
     * Relationship: Nhóm quyền có nhiều quyền
     */
    public function quyens()
    {
        return $this->hasMany(Quyen::class, 'nhom_quyen_id');
    }
}
