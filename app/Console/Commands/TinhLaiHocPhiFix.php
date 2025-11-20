<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HocPhiService;
use App\Models\HocPhiHocKy;
use Illuminate\Support\Facades\DB;

class TinhLaiHocPhiFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hocphi:fix {--force : Force recalculation even if data seems correct}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix tuition calculation: Only calculate service fee when there are registered courses';

    protected $hocPhiService;

    public function __construct(HocPhiService $hocPhiService)
    {
        parent::__construct();
        $this->hocPhiService = $hocPhiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Bắt đầu sửa lỗi tính học phí...');
        $this->newLine();

        // Lấy tất cả học phí học kỳ
        $hocPhis = HocPhiHocKy::with('chiTietHocPhiMon')->get();
        
        $this->info("Tìm thấy {$hocPhis->count()} học phí học kỳ");
        $this->newLine();

        $bar = $this->output->createProgressBar($hocPhis->count());
        $bar->start();

        $fixed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($hocPhis as $hocPhi) {
            try {
                // Tính lại tổng tín chỉ và học phí môn học (chỉ tính các môn không bị hủy)
                $tongTinChi = $hocPhi->chiTietHocPhiMon
                    ->where('trang_thai', '!=', 'huy')
                    ->sum('so_tin_chi');

                $tongHocPhiMon = $hocPhi->chiTietHocPhiMon
                    ->where('trang_thai', '!=', 'huy')
                    ->sum('thanh_tien');

                // Chỉ tính phí dịch vụ khi có ít nhất 1 môn học
                $phiDichVu = ($tongTinChi > 0) ? $hocPhi->phi_dich_vu : 0;
                $tongSoTien = $tongHocPhiMon + $phiDichVu;
                
                // ✅ XỬ LÝ TRƯỜNG HỢP ĐẶC BIỆT:
                // Nếu tong_so_tien = 0 (không có môn học) nhưng đã đóng tiền, 
                // thì điều chỉnh so_tien_da_dong về 0 (hoặc giữ nguyên nếu không muốn mất dữ liệu)
                // Nhưng để đảm bảo tính nhất quán, nếu không có môn học thì không nên có tiền đã đóng
                if ($tongSoTien == 0 && $hocPhi->so_tien_da_dong > 0) {
                    // Nếu không có môn học, reset số tiền đã đóng về 0
                    $hocPhi->so_tien_da_dong = 0;
                }
                
                $soTienConLai = $tongSoTien - $hocPhi->so_tien_da_dong;
                
                // Đảm bảo so_tien_con_lai không âm
                if ($soTienConLai < 0) {
                    $soTienConLai = 0;
                    // Điều chỉnh so_tien_da_dong để không vượt quá tong_so_tien
                    $hocPhi->so_tien_da_dong = $tongSoTien;
                }

                // Kiểm tra xem có cần sửa không
                $needsFix = false;
                if ($hocPhi->tong_tin_chi_dang_ky != $tongTinChi) {
                    $needsFix = true;
                }
                if ($hocPhi->tong_hoc_phi_mon_hoc != $tongHocPhiMon) {
                    $needsFix = true;
                }
                if ($hocPhi->tong_so_tien != $tongSoTien) {
                    $needsFix = true;
                }
                if ($hocPhi->so_tien_con_lai != $soTienConLai) {
                    $needsFix = true;
                }

                if ($needsFix || $this->option('force')) {
                    DB::beginTransaction();
                    
                    $hocPhi->tong_tin_chi_dang_ky = $tongTinChi;
                    $hocPhi->tong_hoc_phi_mon_hoc = $tongHocPhiMon;
                    $hocPhi->tong_so_tien = $tongSoTien;
                    $hocPhi->so_tien_con_lai = $soTienConLai;
                    $hocPhi->save();

                    // Cập nhật trạng thái
                    $hocPhi->updateTrangThai();

                    DB::commit();
                    $fixed++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("\n❌ Lỗi khi sửa học phí ID {$hocPhi->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Đã sửa: {$fixed} học phí");
        $this->info("⏭️  Bỏ qua: {$skipped} học phí (đã đúng)");
        if ($errors > 0) {
            $this->error("❌ Lỗi: {$errors} học phí");
        }
        
        $this->newLine();
        $this->info('🎉 Hoàn thành!');

        return 0;
    }
}
