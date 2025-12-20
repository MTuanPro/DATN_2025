<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model cho bảng trung gian quyen_actor
 * Liên kết quyền với các actor (nhóm người dùng)
 */
class QuyenActor extends Model
{
    protected $table = 'quyen_actor';

    protected $fillable = [
        'quyen_id',
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
     * Relationship: QuyenActor thuộc về một Quyen
     */
    public function quyen()
    {
        return $this->belongsTo(Quyen::class, 'quyen_id');
    }
}
