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

    /**
     * Relationship: User has one Admin
     */
    public function admin()
    {
        return $this->hasOne(\App\Models\Admin::class, 'user_id');
    }

    /**
     * Relationship: User has one DaoTao
     */
    public function daoTao()
    {
        return $this->hasOne(\App\Models\DaoTao::class, 'user_id');
    }

    /**
     * Relationship: User has one SinhVien
     */
    public function sinhVien()
    {
        return $this->hasOne(\App\Models\DaoTao\SinhVien::class, 'user_id');
    }

    /**
     * Relationship: User has one GiangVien
     */
    public function giangVien()
    {
        return $this->hasOne(\App\Models\GiangVien::class, 'user_id');
    }

    /**
     * Lấy ảnh đại diện từ bảng tương ứng với vai trò
     * Lưu ý: Accessor này sẽ tự động load relationships nếu chưa được load
     */
    public function getAnhDaiDienAttribute()
    {
        // Lấy roles (có thể cache để tối ưu)
        if (!$this->relationLoaded('vaiTro')) {
            $this->load('vaiTro');
        }
        $roles = $this->vaiTro->pluck('ma_vai_tro')->toArray();
        
        if (in_array('giang_vien', $roles)) {
            if (!$this->relationLoaded('giangVien')) {
                $this->load('giangVien');
            }
            if ($this->giangVien && $this->giangVien->anh_dai_dien) {
                return $this->giangVien->anh_dai_dien;
            }
        }
        
        if (in_array('sinh_vien', $roles)) {
            if (!$this->relationLoaded('sinhVien')) {
                $this->load('sinhVien');
            }
            if ($this->sinhVien && $this->sinhVien->anh_dai_dien) {
                return $this->sinhVien->anh_dai_dien;
            }
        }
        
        if (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)) {
            if (!$this->relationLoaded('daoTao')) {
                $this->load('daoTao');
            }
            if ($this->daoTao && $this->daoTao->anh_dai_dien) {
                return $this->daoTao->anh_dai_dien;
            }
        }
        
        if (in_array('admin', $roles)) {
            if (!$this->relationLoaded('admin')) {
                $this->load('admin');
            }
            if ($this->admin && $this->admin->anh_dai_dien) {
                return $this->admin->anh_dai_dien;
            }
        }
        
        return null;
    }
}
