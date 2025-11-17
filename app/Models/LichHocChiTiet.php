<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Daotao\PhongHoc;

class LichHocChiTiet extends Model
{
    use HasFactory;

    protected $table = 'lich_hoc_chi_tiet';

    protected $fillable = [
        'lich_hoc_co_dinh_id',
        'lop_hoc_phan_id',
        'ca_hoc_id',
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
        'gio_bat_dau' => 'datetime',
        'gio_ket_thuc' => 'datetime',
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
     * Quan hệ với CaHoc
     */
    public function caHoc()
    {
        return $this->belongsTo(CaHoc::class, 'ca_hoc_id');
    }

    /**
     * Quan hệ với DiemDanh
     */
    public function diemDanh()
    {
        return $this->hasMany(DiemDanh::class, 'lich_hoc_chi_tiet_id');
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
     * Kiểm tra xung đột với lịch học cố định
     * Kiểm tra xem có lịch học cố định nào trùng phòng và trùng ca (thứ) không
     * 
     * @param int|null $excludeLichCoDinhId ID lịch học cố định cần loại trừ (thường là lịch học cố định mà lịch học chi tiết này được tạo từ đó)
     */
    public function kiemTraXungDotVoiLichCoDinh($excludeLichCoDinhId = null)
    {
        if (!$this->phong_hoc_id || !$this->ngay_hoc) {
            return false;
        }

        // Lấy thứ trong tuần từ ngày học (2-8: Thứ 2 = 2, CN = 8)
        $thu = \Carbon\Carbon::parse($this->ngay_hoc)->dayOfWeek;
        $thuTrongTuan = $thu == 0 ? 8 : $thu + 1;

        // Kiểm tra xung đột với lịch học cố định
        // Trùng nếu: cùng phòng + cùng thứ + trùng ca (tiết giao nhau)
        $query = LichHocCoDinh::where('phong_hoc_id', $this->phong_hoc_id)
            ->where('thu_trong_tuan', $thuTrongTuan)
            ->where(function ($q) {
                // Kiểm tra trùng ca: tiết giao nhau
                // Trùng nếu: tiet_ket_thuc >= tiet_bat_dau_moi AND tiet_bat_dau <= tiet_ket_thuc_moi
                $q->where('tiet_ket_thuc', '>=', $this->tiet_bat_dau)
                  ->where('tiet_bat_dau', '<=', $this->tiet_ket_thuc);
            });

        // Loại trừ lịch học cố định mà lịch học chi tiết này được tạo từ đó (nếu có)
        if ($excludeLichCoDinhId) {
            $query->where('id', '!=', $excludeLichCoDinhId);
        }

        return $query->exists();
    }

    /**
     * Kiểm tra xung đột phòng học (bao gồm cả lịch học cố định và lịch học chi tiết)
     * 
     * @param int|null $excludeId ID lịch học chi tiết cần loại trừ
     * @param int|null $excludeLichCoDinhId ID lịch học cố định cần loại trừ
     */
    public function kiemTraXungDotPhongDayDu($excludeId = null, $excludeLichCoDinhId = null)
    {
        // Kiểm tra xung đột với lịch học chi tiết khác
        $xungDotChiTiet = $this->kiemTraXungDotPhongTheoNgay($excludeId);
        
        // Kiểm tra xung đột với lịch học cố định
        $xungDotCoDinh = $this->kiemTraXungDotVoiLichCoDinh($excludeLichCoDinhId);

        return $xungDotChiTiet || $xungDotCoDinh;
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
