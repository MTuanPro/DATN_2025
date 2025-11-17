<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DaoTao\PhongHoc;

class LichHocCoDinh extends Model
{
    use HasFactory;

    protected $table = 'lich_hoc_co_dinh';

    protected $fillable = [
        'lop_hoc_phan_id',
        'thu_trong_tuan',
        'tiet_bat_dau',
        'tiet_ket_thuc',
        'gio_bat_dau',
        'gio_ket_thuc',
        'phong_hoc_id',
        'giang_vien_id',
        'hinh_thuc',
        'link_online',
        'ghi_chu',
    ];

    protected $casts = [
        'gio_bat_dau' => 'datetime',
        'gio_ket_thuc' => 'datetime',
    ];

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
     * Quan hệ với LichHocChiTiet
     */
    public function lichHocChiTiet()
    {
        return $this->hasMany(LichHocChiTiet::class, 'lich_hoc_co_dinh_id');
    }

    /**
     * Lấy tên thứ
     */
    public function getTenThuAttribute()
    {
        $thu = [
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
            8 => 'Chủ nhật',
        ];

        return $thu[$this->thu_trong_tuan] ?? '';
    }

    /**
     * Kiểm tra xung đột phòng học
     * Logic: Hai lịch trùng nhau nếu:
     * - Cùng phòng học
     * - Cùng thứ trong tuần
     * - Có khoảng thời gian giao nhau (không phải end1 < start2 hoặc start1 > end2)
     */
    public function kiemTraXungDotPhong($excludeId = null)
    {
        $query = self::where('phong_hoc_id', $this->phong_hoc_id)
            ->where('thu_trong_tuan', $this->thu_trong_tuan)
            ->where(function ($q) {
                // Logic tương tự kiemTraTrungTiet: trùng nếu !(end1 < start2 || start1 > end2)
                // Tức là: trùng nếu end1 >= start2 AND start1 <= end2
                $q->where(function ($q2) {
                    // Lịch cũ kết thúc sau hoặc bằng lịch mới bắt đầu
                    // VÀ lịch cũ bắt đầu trước hoặc bằng lịch mới kết thúc
                    $q2->where('tiet_ket_thuc', '>=', $this->tiet_bat_dau)
                       ->where('tiet_bat_dau', '<=', $this->tiet_ket_thuc);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Kiểm tra xung đột giảng viên
     * Logic: Hai lịch trùng nhau nếu:
     * - Cùng thứ trong tuần
     * - Có khoảng thời gian giao nhau (không phải end1 < start2 hoặc start1 > end2)
     */
    public function kiemTraXungDotGiangVien($excludeId = null)
    {
        $query = self::where('giang_vien_id', $this->giang_vien_id)
            ->where('thu_trong_tuan', $this->thu_trong_tuan)
            ->where(function ($q) {
                // Logic tương tự kiemTraTrungTiet: trùng nếu !(end1 < start2 || start1 > end2)
                // Tức là: trùng nếu end1 >= start2 AND start1 <= end2
                $q->where(function ($q2) {
                    // Lịch cũ kết thúc sau hoặc bằng lịch mới bắt đầu
                    // VÀ lịch cũ bắt đầu trước hoặc bằng lịch mới kết thúc
                    $q2->where('tiet_ket_thuc', '>=', $this->tiet_bat_dau)
                       ->where('tiet_bat_dau', '<=', $this->tiet_ket_thuc);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
