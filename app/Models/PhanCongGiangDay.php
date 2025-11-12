<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanCongGiangDay extends Model
{
    use HasFactory;

    protected $table = 'lop_hoc_phan_giang_vien';

    protected $fillable = [
        'lop_hoc_phan_id',
        'giang_vien_id',
        'vai_tro',
        'phan_cong_giang_day',
        'nguoi_phan_cong_id',
        'ngay_phan_cong',
    ];

    protected $casts = [
        'ngay_phan_cong' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với LopHocPhan
     */
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    /**
     * Quan hệ với GiangVien
     */
    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'giang_vien_id');
    }

    /**
     * Người phân công (nhân viên đào tạo)
     */
    public function nguoiPhanCong()
    {
        return $this->belongsTo(\App\Models\DaoTao::class, 'nguoi_phan_cong_id');
    }

    /**
     * Kiểm tra giảng viên có bị trùng lịch không
     * (cần check với lịch học cố định)
     */
    public static function kiemTraTrungLich($giangVienId, $lopHocPhanId)
    {
        // TODO: Implement logic kiểm tra trùng lịch
        // Cần kết hợp với bảng lich_hoc_co_dinh để kiểm tra
        // Hiện tại return false (không trùng)
        return false;
    }

    /**
     * Lấy tên vai trò tiếng Việt
     */
    public function getTenVaiTroAttribute()
    {
        $vaiTro = [
            'giang_vien_chinh' => 'Giảng viên chính',
            'giang_vien_phu' => 'Giảng viên phụ',
            'tro_giang' => 'Trợ giảng',
        ];

        return $vaiTro[$this->vai_tro] ?? $this->vai_tro;
    }
}
