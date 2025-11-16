<?php

namespace App\Jobs;

use App\Models\LichThi;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckExamReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        Log::info('Bắt đầu kiểm tra nhắc nhở lịch thi');

        $today = Carbon::today();
        $thresholds = [7, 3, 1]; // Nhắc nhở trước 7, 3, 1 ngày

        $totalNotifications = 0;

        foreach ($thresholds as $days) {
            $targetDate = $today->copy()->addDays($days);

            // Lấy các lịch thi sắp diễn ra
            $lichThis = LichThi::with(['lopHocPhan.monHoc', 'lopHocPhan.lopHocPhanSinhViens.sinhVien'])
                ->whereDate('ngay_thi', '=', $targetDate)
                ->get();

            Log::info("Tìm thấy {$lichThis->count()} lịch thi trong {$days} ngày tới");

            foreach ($lichThis as $lichThi) {
                try {
                    $lopHocPhan = $lichThi->lopHocPhan;
                    if (!$lopHocPhan || !$lopHocPhan->monHoc) {
                        continue;
                    }

                    $monHoc = $lopHocPhan->monHoc;
                    
                    // Lấy danh sách sinh viên (chỉ lấy các sinh viên đã được xếp lớp)
                    $sinhVienIds = $lopHocPhan->lopHocPhanSinhViens()
                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                        ->with('sinhVien')
                        ->get()
                        ->pluck('sinhVien.user_id')
                        ->filter()
                        ->toArray();

                    if (!empty($sinhVienIds)) {
                        $tieuDe = "Nhắc nhở: Lịch thi {$monHoc->ten_mon} còn {$days} ngày";
                        $noiDung = "Kính chào các bạn,\n\n"
                            . "Đây là lời nhắc nhở về kỳ thi sắp tới:\n\n"
                            . "📚 Môn học: {$monHoc->ten_mon} ({$monHoc->ma_mon})\n"
                            . "📅 Ngày thi: " . $lichThi->ngay_thi->format('d/m/Y') . " ({$days} ngày nữa)\n"
                            . "⏰ Giờ bắt đầu: {$lichThi->gio_bat_dau}\n"
                            . "📍 Phòng thi: {$lichThi->phong_thi}\n"
                            . "⏱️ Thời gian làm bài: {$lichThi->thoi_gian_lam_bai} phút\n\n";

                        if ($days == 1) {
                            $noiDung .= "⚠️ Lưu ý: Kỳ thi sẽ diễn ra vào NGÀY MAI!\n"
                                . "Vui lòng chuẩn bị:\n"
                                . "- Giấy tờ tùy thân (CMND/CCCD, thẻ sinh viên)\n"
                                . "- Dụng cụ học tập cần thiết\n"
                                . "- Có mặt trước giờ thi 15 phút\n\n";
                        } else {
                            $noiDung .= "Vui lòng ôn tập kỹ lưỡng và chuẩn bị đầy đủ giấy tờ.\n\n";
                        }

                        $noiDung .= "Chúc các bạn thi tốt!\n\nTrân trọng!";

                        $mucDo = $days <= 1 ? 'rat_quan_trong' : 'quan_trong';

                        $notificationService->createAutoNotification(
                            'lich_thi',
                            $tieuDe,
                            $noiDung,
                            $sinhVienIds,
                            [
                                'muc_do_quan_trong' => $mucDo,
                                'lien_ket_id' => $lichThi->id,
                                'lien_ket_loai' => 'lich_thi',
                                'gui_email' => $days <= 3,
                            ]
                        );

                        $totalNotifications++;
                    }
                } catch (\Exception $e) {
                    Log::error("Lỗi gửi nhắc nhở lịch thi", [
                        'lich_thi_id' => $lichThi->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        Log::info("Hoàn thành kiểm tra lịch thi - Đã gửi {$totalNotifications} thông báo");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job kiểm tra lịch thi thất bại: " . $exception->getMessage());
    }
}

