<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HocPhiHocKy;
use App\Models\CauHinhHocPhi;
use App\Services\HocPhiService;
use Illuminate\Support\Facades\DB;

class DongBoPhiDichVuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hocphi:dong-bo-phi-dich-vu {--force : Force update even if already paid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ phí dịch vụ từ cấu hình hiện tại cho tất cả học phí chưa thanh toán';

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
        $this->info('🔄 Bắt đầu đồng bộ phí dịch vụ...');
        $this->newLine();

        // Lấy cấu hình hiện tại
        $cauHinh = CauHinhHocPhi::getCauHinhHienTai();
        if (!$cauHinh) {
            $this->error('❌ Không tìm thấy cấu hình học phí hiện tại!');
            return 1;
        }

        $this->info("📋 Cấu hình hiện tại:");
        $this->line("   - Năm học: {$cauHinh->nam_hoc}");
        $this->line("   - Đơn giá/tín chỉ: " . number_format($cauHinh->don_gia_tren_tin_chi, 0, ',', '.') . " VNĐ");
        $this->line("   - Phí dịch vụ: " . number_format($cauHinh->phi_dich_vu, 0, ',', '.') . " VNĐ");
        $this->newLine();

        // Lấy tất cả học phí chưa thanh toán đủ
        $query = HocPhiHocKy::where('so_tien_con_lai', '>', 0);
        
        if (!$this->option('force')) {
            // Chỉ cập nhật những học phí chưa thanh toán (so_tien_da_dong = 0)
            $query->where('so_tien_da_dong', 0);
        }

        $hocPhis = $query->get();
        
        if ($hocPhis->isEmpty()) {
            $this->info('✅ Không có học phí nào cần đồng bộ.');
            return 0;
        }

        $this->info("📊 Tìm thấy {$hocPhis->count()} học phí cần đồng bộ.");
        $this->newLine();

        $bar = $this->output->createProgressBar($hocPhis->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($hocPhis as $hocPhi) {
                try {
                    // Kiểm tra xem phí dịch vụ có khác không
                    if ($hocPhi->phi_dich_vu == $cauHinh->phi_dich_vu) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    $oldPhiDichVu = $hocPhi->phi_dich_vu;
                    
                    // Cập nhật phí dịch vụ
                    $hocPhi->phi_dich_vu = $cauHinh->phi_dich_vu;
                    
                    // Tính lại tổng số tiền
                    $hocPhi->tong_so_tien = $hocPhi->tong_hoc_phi_mon_hoc + $hocPhi->phi_dich_vu;
                    $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
                    
                    // Cập nhật trạng thái
                    $hocPhi->updateTrangThai();
                    
                    $hocPhi->save();
                    
                    $updated++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("\n❌ Lỗi khi cập nhật học phí ID {$hocPhi->id}: " . $e->getMessage());
                }
                
                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ Lỗi: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Đã cập nhật: {$updated} học phí");
        if ($skipped > 0) {
            $this->line("⏭️  Đã bỏ qua (đã đúng): {$skipped} học phí");
        }
        if ($errors > 0) {
            $this->error("❌ Lỗi: {$errors} học phí");
        }

        $this->newLine();
        $this->info('✅ Hoàn thành đồng bộ phí dịch vụ!');

        return 0;
    }
}

