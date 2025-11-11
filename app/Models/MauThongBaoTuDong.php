<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauThongBaoTuDong extends Model
{
    protected $table = 'mau_thong_bao_tu_dong';

    protected $fillable = [
        'loai_thong_bao',
        'tieu_de_mau',
        'noi_dung_mau',
        'doi_tuong_mac_dinh',
        'muc_do_uu_tien',
        'gui_email_mac_dinh',
        'gui_sms_mac_dinh',
        'kich_hoat',
        'ghi_chu',
    ];

    protected $casts = [
        'gui_email_mac_dinh' => 'boolean',
        'gui_sms_mac_dinh' => 'boolean',
        'kich_hoat' => 'boolean',
    ];

    /**
     * Các loại thông báo tự động
     */
    public static function getLoaiThongBaoOptions()
    {
        return [
            'lich_hoc_moi' => 'Lịch học mới',
            'lich_thi_sap_toi' => 'Lịch thi sắp tới',
            'hoc_phi_sap_het_han' => 'Học phí sắp hết hạn',
            'diem_da_cap_nhat' => 'Điểm đã cập nhật',
            'dang_ky_mon_thanh_cong' => 'Đăng ký môn thành công',
            'dang_ky_mon_that_bai' => 'Đăng ký môn thất bại',
            'canh_bao_hoc_vu' => 'Cảnh báo học vụ',
        ];
    }

    /**
     * Thay thế biến trong template
     */
    public function replaceVariables($data)
    {
        $tieuDe = $this->tieu_de_mau;
        $noiDung = $this->noi_dung_mau;

        foreach ($data as $key => $value) {
            $tieuDe = str_replace('{' . $key . '}', $value, $tieuDe);
            $noiDung = str_replace('{' . $key . '}', $value, $noiDung);
        }

        return [
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
        ];
    }
}
