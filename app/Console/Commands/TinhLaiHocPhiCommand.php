<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HocPhiService;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Support\Facades\DB;

class TinhLaiHocPhiCommand extends Command
{
    protected $signature = 'hocphi:recalculate';
    protected $description = 'Recalculate tuition for all students who have been assigned to classes';

    protected $hocPhiService;

    public function __construct(HocPhiService $hocPhiService)
    {
        parent::__construct();
        $this->hocPhiService = $hocPhiService;
    }

    public function handle()
    {
        $this->info('Starting tuition recalculation...');

        // Group by sinh_vien_id and hoc_ky_id
        $lopHocPhanSinhViens = LopHocPhanSinhVien::with('lopHocPhan')
            ->get()
            ->groupBy(function ($item) {
                return $item->sinh_vien_id . '_' . $item->lopHocPhan->hoc_ky_id;
            });

        $this->info("Found {$lopHocPhanSinhViens->count()} student-semester combinations");

        $bar = $this->output->createProgressBar($lopHocPhanSinhViens->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($lopHocPhanSinhViens as $key => $group) {
            list($sinhVienId, $hocKyId) = explode('_', $key);
            $lopIds = $group->pluck('id')->toArray();

            try {
                $this->hocPhiService->tinhHocPhiKhiDangKy($sinhVienId, $hocKyId, $lopIds);
                $success++;
            } catch (\Exception $e) {
                $this->error("\nFailed for SV $sinhVienId, HK $hocKyId: " . $e->getMessage());
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Success: $success");
        if ($failed > 0) {
            $this->error("❌ Failed: $failed");
        }
        $this->info('Done!');

        return 0;
    }
}
