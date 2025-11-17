<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaHoc extends Model
{
    protected $table = 'ca_hoc';

    protected $fillable = [
        'ten_ca',
        'thu_tu',
        'gio_bat_dau',
        'gio_ket_thuc',
        'trang_thai',
        'ghi_chu',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
        'gio_bat_dau' => 'datetime:H:i',
        'gio_ket_thuc' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Lấy tất cả ca học đang hoạt động
     */
    public static function getCaHocHoatDong()
    {
        return self::where('trang_thai', true)
            ->orderBy('thu_tu')
            ->get();
    }

    /**
     * Kiểm tra thời gian ca học có hợp lệ không
     */
    public function isValidTimeRange()
    {
        return $this->gio_bat_dau < $this->gio_ket_thuc;
    }

    /**
     * Format thời gian ca học
     */
    public function getFormattedTimeRange()
    {
        return date('H:i', strtotime($this->gio_bat_dau)) . ' - ' . date('H:i', strtotime($this->gio_ket_thuc));
    }

    /**
     * Kiểm tra xung đột thời gian với ca học khác
     * Hai ca học trùng nhau nếu có khoảng thời gian giao nhau
     * Logic: Trùng nếu end1 > start2 AND start1 < end2
     * 
     * @param string $gioBatDau Giờ bắt đầu (H:i)
     * @param string $gioKetThuc Giờ kết thúc (H:i)
     * @param int|null $excludeId ID ca học cần loại trừ (khi update)
     * @return bool
     */
    public static function kiemTraXungDotThoiGian($gioBatDau, $gioKetThuc, $excludeId = null)
    {
        // Chuyển đổi thời gian sang timestamp để so sánh
        $batDau = strtotime($gioBatDau);
        $ketThuc = strtotime($gioKetThuc);

        $query = self::where(function($q) use ($batDau, $ketThuc) {
            $q->whereRaw('TIME(gio_ket_thuc) > ?', [date('H:i:s', $batDau)])
              ->whereRaw('TIME(gio_bat_dau) < ?', [date('H:i:s', $ketThuc)]);
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Lấy danh sách ca học trùng thời gian
     * 
     * @param string $gioBatDau
     * @param string $gioKetThuc
     * @param int|null $excludeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCaHocTrungThoiGian($gioBatDau, $gioKetThuc, $excludeId = null)
    {
        $batDau = strtotime($gioBatDau);
        $ketThuc = strtotime($gioKetThuc);

        $query = self::where(function($q) use ($batDau, $ketThuc) {
            $q->whereRaw('TIME(gio_ket_thuc) > ?', [date('H:i:s', $batDau)])
              ->whereRaw('TIME(gio_bat_dau) < ?', [date('H:i:s', $ketThuc)]);
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
}
