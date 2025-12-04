<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZaloPayService;
use App\Models\LichSuDongHocPhi;
use App\Models\ChiTietHocPhiMon;
use App\Services\HocPhiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckZaloPayPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zalopay:check-payment {app_trans_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cập nhật trạng thái thanh toán ZaloPay';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appTransId = $this->argument('app_trans_id');
        
        $this->info("Đang kiểm tra trạng thái thanh toán: {$appTransId}");
        
        // Find payment record
        $lichSu = LichSuDongHocPhi::where('ma_giao_dich', $appTransId)->first();
        
        if (!$lichSu) {
            $this->error("Không tìm thấy giao dịch với mã: {$appTransId}");
            return 1;
        }
        
        $this->info("Tìm thấy giao dịch:");
        $this->line("  - Học phí ID: {$lichSu->hoc_phi_hoc_ky_id}");
        $this->line("  - Số tiền: " . number_format($lichSu->so_tien_dong, 0, ',', '.') . "đ");
        $this->line("  - Ghi chú: {$lichSu->ghi_chu}");
        
        // Query ZaloPay
        $zaloPayService = new ZaloPayService();
        $statusResult = $zaloPayService->queryOrder($appTransId);
        
        $this->info("\nKết quả từ ZaloPay:");
        $this->line("  - Return code: " . ($statusResult['returncode'] ?? 'N/A'));
        $this->line("  - Message: " . ($statusResult['returnmessage'] ?? 'N/A'));
        
        if (isset($statusResult['returncode']) && $statusResult['returncode'] == 1) {
            // Payment successful
            $this->info("\n✓ Thanh toán đã thành công!");
            
            if (str_contains($lichSu->ghi_chu ?? '', 'Đang chờ')) {
                $this->info("Đang cập nhật trạng thái...");
                
                try {
                    DB::beginTransaction();
                    
                    $hocPhi = $lichSu->hocPhiHocKy;
                    
                    // Update payment record
                    $lichSu->update([
                        'ngay_dong' => now(),
                        'ghi_chu' => 'Thanh toán thành công qua ZaloPay (đã kiểm tra lại). Mã giao dịch: ' . $appTransId,
                    ]);
                    
                    $this->info("  ✓ Đã cập nhật lịch sử thanh toán");
                    
                    // Update HocPhiHocKy
                    $oldSoTienDaDong = $hocPhi->so_tien_da_dong;
                    $hocPhi->so_tien_da_dong += $lichSu->so_tien_dong;
                    $hocPhi->so_tien_con_lai = $hocPhi->tong_so_tien - $hocPhi->so_tien_da_dong;
                    $hocPhi->ngay_dong_lan_cuoi = now();
                    $hocPhi->save();
                    
                    $this->info("  ✓ Đã cập nhật học phí:");
                    $this->line("    - Số tiền đã đóng: " . number_format($oldSoTienDaDong, 0, ',', '.') . "đ → " . number_format($hocPhi->so_tien_da_dong, 0, ',', '.') . "đ");
                    $this->line("    - Số tiền còn lại: " . number_format($hocPhi->so_tien_con_lai, 0, ',', '.') . "đ");
                    
                    // Update status
                    $hocPhi->updateTrangThai();
                    $this->info("  ✓ Đã cập nhật trạng thái học phí");
                    
                    // Update chi tiết học phí môn thành đã thanh toán (nếu thanh toán đủ)
                    if ($hocPhi->so_tien_con_lai == 0) {
                        ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)
                            ->where('trang_thai', 'chua_thanh_toan')
                            ->update(['trang_thai' => 'da_thanh_toan']);
                        
                        $hocPhiService = new HocPhiService();
                        $hocPhiService->themVaoDanhSachChoXepLop($hocPhi->sinh_vien_id, $hocPhi->hoc_ky_id);
                        
                        $this->info("  ✓ Đã cập nhật chi tiết học phí môn và thêm vào danh sách chờ xếp lớp");
                    }
                    
                    DB::commit();
                    
                    $this->info("\n✓ Hoàn thành! Đã cập nhật trạng thái thanh toán thành công.");
                    return 0;
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("✗ Lỗi khi cập nhật: " . $e->getMessage());
                    Log::error('CheckZaloPayPayment Error: ' . $e->getMessage());
                    return 1;
                }
            } else {
                $this->warn("\n⚠ Giao dịch đã được xử lý trước đó.");
                return 0;
            }
        } else {
            $errorMessage = $statusResult['returnmessage'] ?? 'Thanh toán chưa hoàn tất hoặc đã thất bại.';
            $this->error("\n✗ Thanh toán chưa thành công: {$errorMessage}");
            return 1;
        }
    }
}
