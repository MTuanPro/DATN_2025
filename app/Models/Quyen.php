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
     * Các actor hợp lệ trong hệ thống
     */
    public const ACTORS = [
        'admin' => 'Quản trị viên',
        'dao_tao' => 'Phòng đào tạo',
        'giang_vien' => 'Giảng viên',
        'sinh_vien' => 'Sinh viên',
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

    /**
     * Relationship: Quyền thuộc về nhiều actor qua bảng quyen_actor
     */
    public function actors()
    {
        return $this->hasMany(QuyenActor::class, 'quyen_id');
    }

    /**
     * Lấy danh sách actor keys (mảng string)
     */
    public function getActorKeysAttribute()
    {
        return $this->actors->pluck('actor')->toArray();
    }

    /**
     * Lấy tên các actor
     */
    public function getActorNamesAttribute()
    {
        return $this->actors->map(function ($item) {
            return self::ACTORS[$item->actor] ?? $item->actor;
        })->toArray();
    }

    /**
     * Sync actors cho quyền này
     */
    public function syncActors(array $actorKeys)
    {
        // Xóa các actor cũ
        $this->actors()->delete();

        // Thêm các actor mới
        foreach ($actorKeys as $actorKey) {
            if (array_key_exists($actorKey, self::ACTORS)) {
                $this->actors()->create(['actor' => $actorKey]);
            }
        }
    }

    /**
     * Kiểm tra quyền có thuộc về actor không
     */
    public function belongsToActor($actor)
    {
        return $this->actors()->where('actor', $actor)->exists();
    }

    /**
     * Scope: Lọc quyền theo actor
     */
    public function scopeForActor($query, $actor)
    {
        return $query->whereHas('actors', function ($q) use ($actor) {
            $q->where('actor', $actor);
        });
    }

    /**
     * Scope: Lọc quyền theo nhiều actors
     */
    public function scopeForActors($query, array $actors)
    {
        return $query->whereHas('actors', function ($q) use ($actors) {
            $q->whereIn('actor', $actors);
        });
    }
}
