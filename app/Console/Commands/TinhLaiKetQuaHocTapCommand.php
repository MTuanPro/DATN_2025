<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DiemService;
use App\Models\KetQuaHocTap;
use Illuminate\Support\Facades\DB;

class TinhLaiKetQuaHocTapCommand extends Command
{
    protected $signature = 'ket-qua:tinh-lai';
    protected $description = 'Tính lại kết quả học tập cho tất cả sinh viên dựa trên điểm số và điểm danh mới';

    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        parent::__construct();
        $this->diemService = $diemService;
    }

    public function handle()
    {
        $this->info('🔧 Bắt đầu tính lại kết quả học tập...');
        $this->newLine();

        // Lấy tất cả kết quả học tập đã có
        $ketQuas = KetQuaHocTap::with('lopHocPhanSinhVien.lopHocPhan')
            ->get();

        $this->info("Tìm thấy {$ketQuas->count()} kết quả học tập cần tính lại");
        $this->newLine();

        $bar = $this->output->createProgressBar($ketQuas->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($ketQuas as $ketQua) {
            try {
                $lopHocPhanSinhVienId = $ketQua->lop_hoc_phan_sinh_vien_id;
                
                // Tính lại điểm tổng kết (sẽ kiểm tra cả điểm F và tỷ lệ vắng > 20%)
                $result = $this->diemService->tinhDiemTong($lopHocPhanSinhVienId);
                
                if ($result) {
                    $success++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("\nLỗi khi tính lại kết quả ID {$ketQua->id}: " . $e->getMessage());
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Thành công: $success");
        if ($failed > 0) {
            $this->warn("⚠️  Thất bại: $failed");
        }
        $this->info('Hoàn thành!');

        return 0;
    }
}

