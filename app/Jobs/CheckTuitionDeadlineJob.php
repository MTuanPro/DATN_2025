<?php

namespace App\Jobs;

use App\Models\HocPhiHocKy;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckTuitionDeadlineJob implements ShouldQueue
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
        Log::info('Bắt đầu kiểm tra học phí sắp hết hạn');

        $today = Carbon::today();
        $thresholds = [7, 3, 1]; // Nhắc nhở trước 7, 3, 1 ngày

        $totalNotifications = 0;

        foreach ($thresholds as $days) {
            $targetDate = $today->copy()->addDays($days);

            // Lấy các học phí sắp đến hạn
            $hocPhis = HocPhiHocKy::with('sinhVien')
                ->where('trang_thai', '!=', 'da_nop_du')
                ->whereDate('han_dong', '=', $targetDate)
                ->where('so_tien_con_lai', '>', 0)
                ->get();

            Log::info("Tìm thấy {$hocPhis->count()} học phí sẽ đến hạn trong {$days} ngày");

            foreach ($hocPhis as $hocPhi) {
                try {
                    // Kiểm tra xem đã gửi thông báo cho mốc này chưa
                    $daGuiThongBao = $hocPhi->nguoiNhanThongBao()
                        ->whereHas('thongBao', function ($query) use ($days) {
                            $query->where('loai_thong_bao', 'hoc_phi')
                                ->where('noi_dung', 'like', "%{$days} ngày%")
                                ->whereDate('ngay_gui', '>=', Carbon::today());
                        })
                        ->exists();

                    if (!$daGuiThongBao) {
                        $notificationService->sendTuitionDeadlineNotification(
                            $hocPhi->sinh_vien_id,
                            $hocPhi->id,
                            $days
                        );
                        $totalNotifications++;
                    }
                } catch (\Exception $e) {
                    Log::error("Lỗi gửi thông báo học phí", [
                        'hoc_phi_id' => $hocPhi->id,
                        'sinh_vien_id' => $hocPhi->sinh_vien_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Kiểm tra học phí đã quá hạn
        $hocPhisQuaHan = HocPhiHocKy::with('sinhVien')
            ->where('trang_thai', 'qua_han')
            ->whereDate('han_dong', '<', $today)
            ->where('so_tien_con_lai', '>', 0)
            ->get();

        Log::info("Tìm thấy {$hocPhisQuaHan->count()} học phí đã quá hạn");

        foreach ($hocPhisQuaHan as $hocPhi) {
            try {
                // Kiểm tra đã gửi thông báo quá hạn trong vòng 7 ngày gần nhất chưa
                $daGuiThongBao = $hocPhi->nguoiNhanThongBao()
                    ->whereHas('thongBao', function ($query) {
                        $query->where('loai_thong_bao', 'hoc_phi')
                            ->where('noi_dung', 'like', '%quá hạn%')
                            ->whereDate('ngay_gui', '>=', Carbon::today()->subDays(7));
                    })
                    ->exists();

                if (!$daGuiThongBao && $hocPhi->sinhVien) {
                    $soNgayQuaHan = $today->diffInDays($hocPhi->han_dong);
                    $tieuDe = "KHẨN: Học phí đã quá hạn {$soNgayQuaHan} ngày";
                    $noiDung = "Kính chào {$hocPhi->sinhVien->ho_ten},\n\n"
                        . "Học phí của bạn đã quá hạn {$soNgayQuaHan} ngày. "
                        . "Số tiền còn lại: " . number_format($hocPhi->so_tien_con_lai) . " VNĐ\n\n"
                        . "Vui lòng liên hệ phòng đào tạo hoặc hoàn thành thanh toán ngay để tránh bị ảnh hưởng đến quá trình học tập.\n\n"
                        . "Trân trọng!";

                    $notificationService->createAutoNotification(
                        'hoc_phi',
                        $tieuDe,
                        $noiDung,
                        [$hocPhi->sinhVien->user_id],
                        [
                            'muc_do_quan_trong' => 'rat_quan_trong',
                            'lien_ket_id' => $hocPhi->id,
                            'lien_ket_loai' => 'hoc_phi',
                            'gui_email' => true,
                        ]
                    );
                    $totalNotifications++;
                }
            } catch (\Exception $e) {
                Log::error("Lỗi gửi thông báo học phí quá hạn", [
                    'hoc_phi_id' => $hocPhi->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("Hoàn thành kiểm tra học phí - Đã gửi {$totalNotifications} thông báo");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job kiểm tra học phí thất bại: " . $exception->getMessage());
    }
}

