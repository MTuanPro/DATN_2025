<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HocPhiHocKy;
use App\Models\LopHocPhanSinhVien;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckTKBAccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * 
     * Kiểm tra các sinh viên:
     * - Đã được xếp lớp cách đây 1 tuần (đúng 7 ngày)
     * - Gửi thông báo về việc TKB sẽ xuất hiện hoặc cần đóng học phí
     */
    public function handle(): void
    {
        try {
            Log::info('CheckTKBAccessJob: Bắt đầu kiểm tra quyền truy cập TKB');

            // Lấy ngày 1 tuần trước (7 ngày)
            $motTuanTruoc = Carbon::now()->subWeek()->startOfDay();
            $ngayKetThuc = Carbon::now()->subWeek()->endOfDay();

            // Lấy các sinh viên được xếp lớp đúng 1 tuần trước
            $danhSachXepLop = LopHocPhanSinhVien::whereBetween('ngay_xep_lop', [$motTuanTruoc, $ngayKetThuc])
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->with(['sinhVien.user', 'lopHocPhan.hocKy', 'lopHocPhan.monHoc'])
                ->get()
                ->groupBy('sinh_vien_id');

            $tongSinhVien = $danhSachXepLop->count();
            $daDongHocPhi = 0;
            $chuaDongHocPhi = 0;

            Log::info("CheckTKBAccessJob: Tìm thấy {$tongSinhVien} sinh viên được xếp lớp 1 tuần trước");

            foreach ($danhSachXepLop as $sinhVienId => $lopList) {
                // Lấy học kỳ từ lớp đầu tiên
                $hocKyId = $lopList->first()->lopHocPhan->hoc_ky_id;

                // Kiểm tra học phí
                $hocPhi = HocPhiHocKy::where('sinh_vien_id', $sinhVienId)
                    ->where('hoc_ky_id', $hocKyId)
                    ->first();

                if (!$hocPhi) {
                    continue; // Bỏ qua nếu chưa có thông tin học phí
                }

                $sinhVien = $lopList->first()->sinhVien;

                if ($hocPhi->trang_thai == 'da_nop_du') {
                    // ✅ ĐÃ ĐÓNG HỌC PHÍ → Thông báo TKB đã sẵn sàng
                    $this->guiThongBaoTKBSanSang($sinhVien, $hocKyId);
                    $daDongHocPhi++;
                } else {
                    // ❌ CHƯA ĐÓNG HỌC PHÍ → Nhắc nhở đóng học phí để xem TKB
                    $this->guiThongBaoNhacDongHocPhi($sinhVien, $hocPhi);
                    $chuaDongHocPhi++;
                }
            }

            Log::info("CheckTKBAccessJob: Hoàn thành. Đã đóng: {$daDongHocPhi}, Chưa đóng: {$chuaDongHocPhi}");

        } catch (\Exception $e) {
            Log::error('CheckTKBAccessJob: Lỗi - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gửi thông báo TKB đã sẵn sàng
     */
    private function guiThongBaoTKBSanSang($sinhVien, $hocKyId)
    {
        $thongBao = ThongBao::create([
            'tieu_de' => '🎓 Thời khóa biểu của bạn đã sẵn sàng!',
            'noi_dung' => "Chúc mừng bạn! Bạn đã hoàn tất thủ tục đóng học phí và đã đủ 1 tuần kể từ khi xếp lớp. Thời khóa biểu của bạn đã được kích hoạt.\n\nBạn có thể xem và tải xuống thời khóa biểu tại mục 'Thời khóa biểu' trong hệ thống.\n\nChúc bạn học tập tốt! 📚",
            'loai_thong_bao' => 'tin_tuc',
            'muc_do_quan_trong' => 'cao',
            'nguoi_tao_id' => 1, // System
            'doi_tuong_nhan' => 'sinh_vien',
            'ngay_bat_dau' => now(),
            'trang_thai' => 'da_gui',
        ]);

        NguoiNhanThongBao::create([
            'thong_bao_id' => $thongBao->id,
            'nguoi_nhan_id' => $sinhVien->user_id,
            'da_xem' => false,
        ]);

        Log::info("Gửi thông báo TKB sẵn sàng cho sinh viên ID: {$sinhVien->id}");
    }

    /**
     * Gửi thông báo nhắc đóng học phí
     */
    private function guiThongBaoNhacDongHocPhi($sinhVien, $hocPhi)
    {
        $hanDong = Carbon::parse($hocPhi->han_dong)->format('d/m/Y');
        $soTienConLai = number_format($hocPhi->so_tien_con_lai, 0, ',', '.');

        $thongBao = ThongBao::create([
            'tieu_de' => '⚠️ Nhắc nhở: Đóng học phí để xem thời khóa biểu',
            'noi_dung' => "Thân gửi {$sinhVien->ho_ten},\n\nĐã đủ 1 tuần kể từ khi bạn được xếp lớp. Tuy nhiên, bạn chưa hoàn tất thủ tục đóng học phí.\n\n📌 Số tiền còn lại: {$soTienConLai} VNĐ\n📅 Hạn đóng: {$hanDong}\n\n⚠️ Lưu ý: Bạn cần đóng học phí để có thể xem thời khóa biểu và tham gia học tập.\n\nVui lòng hoàn tất thủ tục đóng học phí sớm nhất có thể.",
            'loai_thong_bao' => 'hoc_phi',
            'muc_do_quan_trong' => 'cao',
            'nguoi_tao_id' => 1, // System
            'doi_tuong_nhan' => 'sinh_vien',
            'ngay_bat_dau' => now(),
            'trang_thai' => 'da_gui',
        ]);

        NguoiNhanThongBao::create([
            'thong_bao_id' => $thongBao->id,
            'nguoi_nhan_id' => $sinhVien->user_id,
            'da_xem' => false,
        ]);

        Log::info("Gửi thông báo nhắc đóng học phí cho sinh viên ID: {$sinhVien->id}");
    }
}

