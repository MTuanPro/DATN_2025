<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhHeThong extends Model
{
    use HasFactory;

    protected $table = 'cau_hinh_he_thong';

    protected $fillable = [
        'ma_cau_hinh',
        'ten_cau_hinh',
        'gia_tri',
        'trang_thai',
        'mo_ta',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];

    /**
     * Lấy giá trị cấu hình theo mã
     * 
     * @param string $maCauHinh
     * @param mixed $defaultValue Giá trị mặc định nếu không tìm thấy
     * @return mixed
     */
    public static function getGiaTri($maCauHinh, $defaultValue = false)
    {
        $cauHinh = self::where('ma_cau_hinh', $maCauHinh)->first();
        
        if (!$cauHinh) {
            return $defaultValue;
        }

        // Nếu là boolean config, trả về trang_thai
        if (in_array($maCauHinh, ['cho_phep_diem_danh_tuong_lai'])) {
            return $cauHinh->trang_thai;
        }

        // Nếu có giá trị, trả về giá trị
        if ($cauHinh->gia_tri) {
            $decoded = json_decode($cauHinh->gia_tri, true);
            return $decoded !== null ? $decoded : $cauHinh->gia_tri;
        }

        return $defaultValue;
    }

    /**
     * Cập nhật hoặc tạo cấu hình
     * 
     * @param string $maCauHinh
     * @param mixed $giaTri
     * @param string|null $tenCauHinh
     * @param string|null $moTa
     * @return CauHinhHeThong
     */
    public static function setGiaTri($maCauHinh, $giaTri, $tenCauHinh = null, $moTa = null)
    {
        $cauHinh = self::firstOrNew(['ma_cau_hinh' => $maCauHinh]);

        // Nếu là boolean config, lưu vào trang_thai
        if (in_array($maCauHinh, ['cho_phep_diem_danh_tuong_lai'])) {
            $cauHinh->trang_thai = (bool) $giaTri;
        } else {
            // Lưu giá trị dạng JSON nếu là array/object, ngược lại lưu string
            if (is_array($giaTri) || is_object($giaTri)) {
                $cauHinh->gia_tri = json_encode($giaTri);
            } else {
                $cauHinh->gia_tri = $giaTri;
            }
        }

        if ($tenCauHinh) {
            $cauHinh->ten_cau_hinh = $tenCauHinh;
        }

        if ($moTa) {
            $cauHinh->mo_ta = $moTa;
        }

        $cauHinh->save();

        return $cauHinh;
    }

    /**
     * Kiểm tra xem có cho phép điểm danh tương lai không
     * 
     * @return bool
     */
    public static function choPhepDiemDanhTuongLai()
    {
        return self::getGiaTri('cho_phep_diem_danh_tuong_lai', false);
    }
}
