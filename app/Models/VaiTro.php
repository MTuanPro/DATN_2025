<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vai_tro';

    protected $fillable = [
        'ma_vai_tro',
        'ten_vai_tro',
        'mo_ta',
        'muc_do_uu_tien',
        'actor',
    ];

    /**
     * Các actor hợp lệ trong hệ thống
     */
    public const ACTORS = [
        'admin' => 'Quản trị viên',
        'dao_tao' => 'Phòng đào tạo',
        'giang_vien' => 'Giảng viên',
        'sinh_vien' => 'Sinh viên',
    ];

    /**
     * Relationship: Vai trò có nhiều user qua bảng tai_khoan_vai_tro
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'tai_khoan_vai_tro',
            'vai_tro_id',
            'tai_khoan_id'
        )->withTimestamps();
    }

    /**
     * Relationship: Vai trò có nhiều quyền qua bảng vai_tro_quyen
     */
    public function quyens()
    {
        return $this->belongsToMany(
            Quyen::class,
            'vai_tro_quyen',
            'vai_tro_id',
            'quyen_id'
        )->withTimestamps();
    }

    /**
     * Alias for quyens() - để tương thích với code cũ
     */
    public function quyen()
    {
        return $this->quyens();
    }
}
