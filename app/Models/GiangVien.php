<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiangVien extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'giang_vien';

    protected $fillable = [
        'ma_giang_vien',
        'ho_ten',
        'email',
        'so_dien_thoai',
        'trinh_do_id',
        'chuyen_mon',
        'khoa_id',
        'ngay_vao_truong',
        'anh_dai_dien',
        'user_id',
    ];

    protected $casts = [
        'ngay_vao_truong' => 'date',
    ];

    /**
     * Relationship: Giảng viên thuộc về một Khoa
     */
    public function khoa()
    {
        return $this->belongsTo(\App\Models\DaoTao\Khoa::class, 'khoa_id');
    }

    /**
     * Relationship: Giảng viên có một Trình độ
     */
    public function trinhDo()
    {
        return $this->belongsTo(\App\Models\DanhMuc\TrinhDo::class, 'trinh_do_id');
    }

    /**
     * Relationship: Giảng viên có một User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Giảng viên chủ nhiệm nhiều lớp hành chính
     */
    public function lopHanhChinhChuNhiem()
    {
        return $this->hasMany(\App\Models\LopHanhChinh::class, 'giang_vien_chu_nhiem_id');
    }

    /**
     * Relationship: Giảng viên dạy nhiều lớp học phần
     */
    public function lopHocPhans()
    {
        return $this->belongsToMany(
            \App\Models\LopHocPhan::class,
            'lop_hoc_phan_giang_vien',
            'giang_vien_id',
            'lop_hoc_phan_id'
        )->withPivot('vai_tro', 'phan_cong_giang_day', 'ngay_phan_cong', 'nguoi_phan_cong_id')
            ->withTimestamps();
    }

    /**
     * Relationship: Giảng viên là trưởng khoa
     */
    public function khoaTruongKhoa()
    {
        return $this->hasOne(\App\Models\DaoTao\Khoa::class, 'truong_khoa_id');
    }
}
