<?php

namespace App\Observers;

use App\Models\LichThi;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class LichThiObserver
{
    /**
     * Handle the LichThi "created" event.
     */
    public function created(LichThi $lichThi): void
    {
        // Gửi thông báo lịch thi mới
        $this->sendNewExamScheduleNotification($lichThi);
    }

    /**
     * Handle the LichThi "updated" event.
     */
    public function updated(LichThi $lichThi): void
    {
        // Gửi thông báo khi lịch thi thay đổi
        if ($lichThi->wasChanged(['ngay_thi', 'gio_bat_dau', 'phong_thi'])) {
            $this->sendExamScheduleChangeNotification($lichThi);
        }
    }

    /**
     * Gửi thông báo lịch thi mới
     */
    private function sendNewExamScheduleNotification(LichThi $lichThi): void
    {
        try {
            if ($lichThi->lopHocPhan) {
                $notificationService = app(NotificationService::class);
                $lopHocPhan = $lichThi->lopHocPhan;
                $monHoc = $lopHocPhan->monHoc;

                // Lấy danh sách sinh viên trong lớp học phần
                $sinhVienIds = $lopHocPhan->sinhViens()
                    ->pluck('sinh_vien.user_id')
                    ->filter()
                    ->toArray();

                if (!empty($sinhVienIds)) {
                    $tieuDe = "Thông báo lịch thi: {$monHoc->ten_mon}";
                    $noiDung = "Kính chào các bạn,\n\n"
                        . "Lịch thi cho môn học {$monHoc->ten_mon} đã được công bố:\n\n"
                        . "📅 Ngày thi: " . $lichThi->ngay_thi->format('d/m/Y') . "\n"
                        . "⏰ Giờ bắt đầu: {$lichThi->gio_bat_dau}\n"
                        . "📍 Phòng thi: {$lichThi->phong_thi}\n"
                        . "⏱️ Thời gian làm bài: {$lichThi->thoi_gian_lam_bai} phút\n\n"
                        . "Vui lòng chuẩn bị đầy đủ giấy tờ và có mặt trước giờ thi 15 phút.\n\n"
                        . "Chúc các bạn thi tốt!\n\nTrân trọng!";

                    $notificationService->createAutoNotification(
                        'lich_thi',
                        $tieuDe,
                        $noiDung,
                        $sinhVienIds,
                        [
                            'muc_do_quan_trong' => 'rat_quan_trong',
                            'lien_ket_id' => $lichThi->id,
                            'lien_ket_loai' => 'lich_thi',
                            'gui_email' => true,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo lịch thi mới: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo khi lịch thi thay đổi
     */
    private function sendExamScheduleChangeNotification(LichThi $lichThi): void
    {
        try {
            if ($lichThi->lopHocPhan) {
                $notificationService = app(NotificationService::class);
                $lopHocPhan = $lichThi->lopHocPhan;
                $monHoc = $lopHocPhan->monHoc;

                $sinhVienIds = $lopHocPhan->sinhViens()
                    ->pluck('sinh_vien.user_id')
                    ->filter()
                    ->toArray();

                if (!empty($sinhVienIds)) {
                    $tieuDe = "⚠️ THAY ĐỔI LỊCH THI: {$monHoc->ten_mon}";
                    $noiDung = "Kính chào các bạn,\n\n"
                        . "⚠️ LƯU Ý: Lịch thi môn {$monHoc->ten_mon} đã có thay đổi!\n\n"
                        . "📅 Ngày thi: " . $lichThi->ngay_thi->format('d/m/Y') . "\n"
                        . "⏰ Giờ bắt đầu: {$lichThi->gio_bat_dau}\n"
                        . "📍 Phòng thi: {$lichThi->phong_thi}\n"
                        . "⏱️ Thời gian làm bài: {$lichThi->thoi_gian_lam_bai} phút\n\n"
                        . "Vui lòng kiểm tra lại lịch thi và sắp xếp thời gian phù hợp.\n\n"
                        . "Trân trọng!";

                    $notificationService->createAutoNotification(
                        'lich_thi',
                        $tieuDe,
                        $noiDung,
                        $sinhVienIds,
                        [
                            'muc_do_quan_trong' => 'rat_quan_trong',
                            'lien_ket_id' => $lichThi->id,
                            'lien_ket_loai' => 'lich_thi',
                            'gui_email' => true,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi thông báo thay đổi lịch thi: ' . $e->getMessage());
        }
    }
}

