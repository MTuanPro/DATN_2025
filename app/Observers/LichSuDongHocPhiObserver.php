<?php

namespace App\Observers;

use App\Models\LichSuDongHocPhi;
use App\Models\DangKyMonHocTam;
use App\Models\HocPhiHocKy;
use Illuminate\Support\Facades\Log;

class LichSuDongHocPhiObserver
{
    /**
     * Handle the LichSuDongHocPhi "created" event.
     * Khi sinh viên đóng học phí, tự động cập nhật trạng thái đăng ký môn học
     */
    public function created(LichSuDongHocPhi $lichSuDongHocPhi): void
    {
        try {
            // Lấy thông tin học phí học kỳ
            $hocPhiHocKy = $lichSuDongHocPhi->hocPhiHocKy;
            
            if (!$hocPhiHocKy) {
                return;
            }

            // Kiểm tra xem học phí đã đủ chưa (so_tien_con_lai <= 0)
            // Hoặc có thể cho phép xếp lớp ngay khi có đóng tiền (tùy chính sách)
            if ($hocPhiHocKy->so_tien_con_lai <= 0) {
                // Cập nhật tất cả đăng ký môn học của sinh viên trong học kỳ này
                // từ "cho_dong_hoc_phi" sang "cho_xep_lop"
                DangKyMonHocTam::where('sinh_vien_id', $hocPhiHocKy->sinh_vien_id)
                    ->where('hoc_ky_id', $hocPhiHocKy->hoc_ky_id)
                    ->where('trang_thai', 'cho_dong_hoc_phi')
                    ->update([
                        'trang_thai' => 'cho_xep_lop',
                    ]);

                Log::info('Đã cập nhật trạng thái đăng ký môn học sau khi đóng học phí đủ', [
                    'sinh_vien_id' => $hocPhiHocKy->sinh_vien_id,
                    'hoc_ky_id' => $hocPhiHocKy->hoc_ky_id,
                    'so_tien_dong' => $lichSuDongHocPhi->so_tien_dong,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi trong LichSuDongHocPhiObserver: ' . $e->getMessage());
        }
    }
}
