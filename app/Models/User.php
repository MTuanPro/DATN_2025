<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
        'trang_thai',
        'lan_dang_nhap_cuoi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'lan_dang_nhap_cuoi' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User có nhiều vai trò qua bảng tai_khoan_vai_tro
     */
    public function vaiTro()
    {
        return $this->belongsToMany(
            \App\Models\VaiTro::class,
            'tai_khoan_vai_tro',
            'tai_khoan_id',
            'vai_tro_id'
        )->withTimestamps();
    }

    /**
     * Kiểm tra user có vai trò cụ thể không
     */
    public function hasRole($role)
    {
        return $this->vaiTro()->where('ma_vai_tro', $role)->exists();
    }

    /**
     * Kiểm tra user có một trong các vai trò không
     */
    public function hasAnyRole(array $roles)
    {
        return $this->vaiTro()->whereIn('ma_vai_tro', $roles)->exists();
    }

    /**
     * Kiểm tra user có quyền cụ thể không
     */
    public function hasPermission($permission)
    {
        return $this->vaiTro()
            ->whereHas('quyens', function ($query) use ($permission) {
                $query->where('ma_quyen', $permission);
            })
            ->exists();
    }

    /**
     * Kiểm tra user có một trong các quyền không
     */
    public function hasAnyPermission(array $permissions)
    {
        return $this->vaiTro()
            ->whereHas('quyens', function ($query) use ($permissions) {
                $query->whereIn('ma_quyen', $permissions);
            })
            ->exists();
    }

    /**
     * Lấy tất cả quyền của user
     */
    public function getAllPermissions()
    {
        return $this->vaiTro()
            ->with('quyens')
            ->get()
            ->pluck('quyens')
            ->flatten()
            ->unique('id');
    }
}
