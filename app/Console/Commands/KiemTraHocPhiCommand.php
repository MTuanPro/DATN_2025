<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocPhiHocKy;
use App\Models\CauHinhHocPhi;
use Illuminate\Support\Facades\DB;

class KiemTraHocPhiCommand extends Command
{
    protected $signature = 'hocphi:check {sinh_vien_id?}';
    protected $description = 'Check tuition status for a student or all students';

    public function handle()
    {
        $sinhVienId = $this->argument('sinh_vien_id');

        $this->info('=== TUITION SYSTEM STATUS ===');
        $this->newLine();

        // Check config
        $config = CauHinhHocPhi::getCauHinhHienTai();
        if ($config) {
            $this->info("✅ Tuition Config: {$config->nam_hoc}");
            $this->line("   - Price: " . number_format($config->don_gia_tren_tin_chi) . " VND/credit");
            $this->line("   - Service fee: " . number_format($config->phi_dich_vu) . " VND");
        } else {
            $this->error("❌ No active tuition config found!");
            return 1;
        }
        $this->newLine();

        // Check registered students
        $query = LopHocPhanSinhVien::with(['sinhVien', 'lopHocPhan.hocKy', 'lopHocPhan.monHoc']);
        if ($sinhVienId) {
            $query->where('sinh_vien_id', $sinhVienId);
        }
        $registrations = $query->get();

        $this->info("📚 Total registrations: {$registrations->count()}");
        $this->newLine();

        // Group by student
        $byStudent = $registrations->groupBy('sinh_vien_id');
        
        foreach ($byStudent as $svId => $regs) {
            $sinhVien = $regs->first()->sinhVien;
            $this->line("Student: {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten}");
            
            // Check tuition records
            $hocPhis = HocPhiHocKy::where('sinh_vien_id', $svId)->get();
            
            if ($hocPhis->isEmpty()) {
                $this->error("  ❌ No tuition records found!");
                $this->line("  📝 Registered courses: {$regs->count()}");
                foreach ($regs as $reg) {
                    $this->line("     - {$reg->lopHocPhan->monHoc->ma_mon}: {$reg->lopHocPhan->monHoc->ten_mon} (HK: {$reg->lopHocPhan->hocKy->ten_hoc_ky})");
                }
            } else {
                $this->info("  ✅ Tuition records: {$hocPhis->count()}");
                foreach ($hocPhis as $hp) {
                    $this->line("     HK {$hp->hocKy->ten_hoc_ky}: " . number_format($hp->tong_so_tien) . " VND (Paid: " . number_format($hp->so_tien_da_dong) . " VND)");
                }
            }
            $this->newLine();
        }

        return 0;
    }
}
