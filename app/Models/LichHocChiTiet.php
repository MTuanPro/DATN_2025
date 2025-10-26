<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\PhongHoc;

class LichHocChiTiet extends Model
{
    use HasFactory;

    protected $table = 'lich_hoc_chi_tiet';

    protected $fillable = [
        'lich_hoc_co_dinh_id',
        'lop_hoc_phan_id',
        'ngay_hoc',
        'tiet_bat_dau',
        'tiet_ket_thuc',
        'gio_bat_dau',
        'gio_ket_thuc',
        'phong_hoc_id',
        'giang_vien_id',
        'hinh_thuc',
        'link_online',
        'noi_dung_giang_day',
        'tai_lieu_dinh_kem',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_hoc' => 'date',
        'gio_bat_dau' => 'datetime:H:i',
        'gio_ket_thuc' => 'datetime:H:i',
    ];

    /**
     * Quan hệ với LichHocCoDinh
     */
    public function lichHocCoDinh()
    {
        return $this->belongsTo(LichHocCoDinh::class, 'lich_hoc_co_dinh_id');
    }

    /**
     * Quan hệ với LopHocPhan
     */
    public function lopHocPhan()
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    /**
     * Quan hệ với PhongHoc
     */
    public function phongHoc()
    {
        return $this->belongsTo(PhongHoc::class, 'phong_hoc_id');
    }

    /**
     * Quan hệ với GiangVien
     */
    public function giangVien()
    {
        return $this->belongsTo(GiangVien::class, 'giang_vien_id');
    }

    /**
     * Kiểm tra xung đột phòng học theo ngày
     */
    public function kiemTraXungDotPhongTheoNgay($excludeId = null)
    {
        $query = self::where('phong_hoc_id', $this->phong_hoc_id)
            ->where('ngay_hoc', $this->ngay_hoc)
            ->where('trang_thai', '!=', 'huy')
            ->where(function ($q) {
                $q->whereBetween('tiet_bat_dau', [$this->tiet_bat_dau, $this->tiet_ket_thuc])
                    ->orWhereBetween('tiet_ket_thuc', [$this->tiet_bat_dau, $this->tiet_ket_thuc])
                    ->orWhere(function ($q2) {
                        $q2->where('tiet_bat_dau', '<=', $this->tiet_bat_dau)
                            ->where('tiet_ket_thuc', '>=', $this->tiet_ket_thuc);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Kiểm tra xung đột giảng viên theo ngày
     */
    public function kiemTraXungDotGiangVienTheoNgay($excludeId = null)
    {
        $query = self::where('giang_vien_id', $this->giang_vien_id)
            ->where('ngay_hoc', $this->ngay_hoc)
            ->where('trang_thai', '!=', 'huy')
            ->where(function ($q) {
                $q->whereBetween('tiet_bat_dau', [$this->tiet_bat_dau, $this->tiet_ket_thuc])
                    ->orWhereBetween('tiet_ket_thuc', [$this->tiet_bat_dau, $this->tiet_ket_thuc])
                    ->orWhere(function ($q2) {
                        $q2->where('tiet_bat_dau', '<=', $this->tiet_bat_dau)
                            ->where('tiet_ket_thuc', '>=', $this->tiet_ket_thuc);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Lấy tên trạng thái
     */
    public function getTenTrangThaiAttribute()
    {
        $trangThai = [
            'chua_day' => 'Chưa dạy',
            'dang_day' => 'Đang dạy',
            'da_day' => 'Đã dạy',
            'huy' => 'Hủy',
        ];

        return $trangThai[$this->trang_thai] ?? '';
    }
}
