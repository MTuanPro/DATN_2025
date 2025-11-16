<?php

namespace App\Observers;

use App\Models\DangKyMonHoc;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class DangKyMonHocObserver
{
    /**
     * Handle the DangKyMonHoc "created" event.
     */
    public function created(DangKyMonHoc $dangKyMonHoc): void
    {
        // Gửi thông báo khi đăng ký môn thành công
        $this->sendRegistrationNotification($dangKyMonHoc);
    }

    /**
     * Handle the DangKyMonHoc "updated" event.
     */
    public function updated(DangKyMonHoc $dangKyMonHoc): void
    {
        // Gửi thông báo khi trạng thái đăng ký thay đổi
        if ($dangKyMonHoc->wasChanged('trang_thai')) {
            $this->sendRegistrationStatusChangeNotification($dangKyMonHoc);
        }
    }

    /**
     * Gửi thông báo đăng ký môn
     */
    private function sendRegistrationNotification(DangKyMonHoc $dangKyMonHoc): void
    {
        try {
            if ($dangKyMonHoc->sinhVien && $dangKyMonHoc->monHoc) {
                $notificationService = app(NotificationService::class);
                
                $thanhCong = in_array($dangKyMonHoc->trang_thai, ['thanh_cong', 'da_dang_ky']);
                $lyDo = $dangKyMonHoc->ghi_chu ?? null;

                $notificationService->sendCourseRegistrationNotification(
                    $dangKyMonHoc->sinh_vien_id,
                    $dangKyMonHoc->monHoc->ten_mon,
                    $thanhCong,
                    $lyDo
                );
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo đăng ký môn: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo khi trạng thái đăng ký thay đổi
     */
    private function sendRegistrationStatusChangeNotification(DangKyMonHoc $dangKyMonHoc): void
    {
        try {
            if ($dangKyMonHoc->sinhVien && $dangKyMonHoc->monHoc) {
                $notificationService = app(NotificationService::class);
                $sinhVien = $dangKyMonHoc->sinhVien;
                $monHoc = $dangKyMonHoc->monHoc;

                $trangThaiMoi = $dangKyMonHoc->trang_thai;
                $tieuDe = "Thay đổi trạng thái đăng ký môn: {$monHoc->ten_mon}";
                
                $noiDung = "Kính chào {$sinhVien->ho_ten},\n\n"
                    . "Trạng thái đăng ký môn học của bạn đã được cập nhật:\n"
                    . "Môn học: {$monHoc->ten_mon} ({$monHoc->ma_mon})\n"
                    . "Trạng thái mới: " . $this->getTrangThaiLabel($trangThaiMoi) . "\n\n";

                if ($dangKyMonHoc->ghi_chu) {
                    $noiDung .= "Ghi chú: {$dangKyMonHoc->ghi_chu}\n\n";
                }

                $noiDung .= "Vui lòng truy cập hệ thống để xem chi tiết.\n\nTrân trọng!";

                $mucDo = in_array($trangThaiMoi, ['huy', 'that_bai']) ? 'quan_trong' : 'binh_thuong';

                $notificationService->createAutoNotification(
                    'dang_ky_mon',
                    $tieuDe,
                    $noiDung,
                    [$sinhVien->user_id],
                    [
                        'muc_do_quan_trong' => $mucDo,
                        'gui_email' => false,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo thay đổi trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Get label cho trạng thái
     */
    private function getTrangThaiLabel(string $trangThai): string
    {
        $labels = [
            'dang_ky' => 'Đang đăng ký',
            'thanh_cong' => 'Thành công',
            'da_dang_ky' => 'Đã đăng ký',
            'huy' => 'Đã hủy',
            'that_bai' => 'Thất bại',
        ];

        return $labels[$trangThai] ?? $trangThai;
    }
}

