<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Chức năng - đại diện cho các route/action trong hệ thống
 * Mỗi chức năng có thể được gắn với một quyền
 */
class ChucNang extends Model
{
    protected $table = 'chuc_nang';

    protected $fillable = [
        'route_name',
        'ten_chuc_nang',
        'nhom',
        'actor',
        'method',
        'uri',
        'quyen_id',
        'yeu_cau_quyen',
    ];

    protected $casts = [
        'yeu_cau_quyen' => 'boolean',
    ];

    /**
     * Các actor hợp lệ
     */
    public const ACTORS = [
        'admin' => 'Quản trị viên',
        'dao_tao' => 'Phòng đào tạo',
        'giang_vien' => 'Giảng viên',
        'sinh_vien' => 'Sinh viên',
    ];

    /**
     * Relationship: Chức năng thuộc về một quyền
     */
    public function quyen()
    {
        return $this->belongsTo(Quyen::class, 'quyen_id');
    }

    /**
     * Scope: Lọc theo actor
     */
    public function scopeForActor($query, $actor)
    {
        return $query->where('actor', $actor);
    }

    /**
     * Scope: Lọc theo nhóm
     */
    public function scopeInGroup($query, $nhom)
    {
        return $query->where('nhom', $nhom);
    }

    /**
     * Scope: Chức năng yêu cầu quyền
     */
    public function scopeRequiresPermission($query)
    {
        return $query->where('yeu_cau_quyen', true);
    }

    /**
     * Scope: Chức năng chưa gắn quyền
     */
    public function scopeWithoutPermission($query)
    {
        return $query->whereNull('quyen_id');
    }

    /**
     * Lấy mã quyền (nếu có)
     */
    public function getMaQuyenAttribute()
    {
        return $this->quyen?->ma_quyen;
    }
}
