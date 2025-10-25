<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhDauDiem extends Model
{
    use HasFactory;

    protected $table = 'cau_hinh_dau_diem';

    protected $fillable = [
        'lop_hoc_phan_id',
        'ten_dau_diem',
        'ty_le',
        'so_cot',
    ];

    protected $casts = [
        'ty_le' => 'float',
        'so_cot' => 'integer',
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
     * Kiểm tra tổng tỷ lệ % của lớp học phần có = 100% không
     */
    public static function kiemTraTongTyLe($lopHocPhanId, $tyLeMoi = 0, $idLoaiTru = null)
    {
        $tongTyLe = self::where('lop_hoc_phan_id', $lopHocPhanId)
            ->when($idLoaiTru, function ($query) use ($idLoaiTru) {
                $query->where('id', '!=', $idLoaiTru);
            })
            ->sum('ty_le');

        return ($tongTyLe + $tyLeMoi) <= 100;
    }

    /**
     * Lấy tổng tỷ lệ % hiện tại của lớp học phần
     */
    public static function getTongTyLe($lopHocPhanId)
    {
        return self::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
    }

    /**
     * Lấy tỷ lệ % còn lại có thể thêm
     */
    public static function getTyLeConLai($lopHocPhanId, $idLoaiTru = null)
    {
        $tongTyLe = self::where('lop_hoc_phan_id', $lopHocPhanId)
            ->when($idLoaiTru, function ($query) use ($idLoaiTru) {
                $query->where('id', '!=', $idLoaiTru);
            })
            ->sum('ty_le');

        return 100 - $tongTyLe;
    }
}
