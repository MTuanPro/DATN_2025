<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\HocKy;
use App\Models\DaoTao\ChuongTrinhKhung;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChuyenKySinhVien extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinh-vien:chuyen-ky
                          {--hoc-ky-id= : ID học kỳ cần xử lý (nếu không có sẽ lấy học kỳ vừa kết thúc)}
                          {--force : Bỏ qua kiểm tra ngày kết thúc}
                          {--dry-run : Chỉ hiển thị mô phỏng, không thực sự cập nhật}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển kỳ cho sinh viên sau khi kết thúc học kỳ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎓 BẮT ĐẦU CHUYỂN KỲ CHO SINH VIÊN');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Lấy học kỳ cần xử lý
        $hocKyId = $this->option('hoc-ky-id');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        if ($hocKyId) {
            $hocKy = HocKy::find($hocKyId);
        } else {
            // Lấy học kỳ vừa kết thúc gần nhất
            $hocKy = HocKy::where('ngay_ket_thuc', '<', now())
                ->orderBy('ngay_ket_thuc', 'desc')
                ->first();
        }

        if (!$hocKy) {
            $this->error('❌ Không tìm thấy học kỳ cần xử lý!');
            return 1;
        }

        // Kiểm tra học kỳ đã kết thúc chưa
        if (!$force && $hocKy->ngay_ket_thuc->isFuture()) {
            $this->warn("⚠️  Học kỳ {$hocKy->ten_hoc_ky} chưa kết thúc!");
            $this->warn("   Ngày kết thúc: {$hocKy->ngay_ket_thuc->format('d/m/Y')}");
            $this->warn("   Sử dụng --force để bỏ qua kiểm tra này");
            return 1;
        }

        $this->info("📚 Học kỳ: {$hocKy->ten_hoc_ky} ({$hocKy->nam_hoc})");
        $this->info("📅 Kết thúc: {$hocKy->ngay_ket_thuc->format('d/m/Y')}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  CHẾ ĐỘ MÔ PHỎNG (DRY RUN) - Không thực sự cập nhật dữ liệu');
            $this->newLine();
        }

        // Lấy tất cả sinh viên đang học
        $sinhViens = SinhVien::whereHas('trangThaiHocTap', function($query) {
            $query->where('ten_trang_thai', 'Đang học');
        })->get();

        $this->info("👥 Tổng số sinh viên đang học: " . $sinhViens->count());
        $this->newLine();

        $bar = $this->output->createProgressBar($sinhViens->count());
        $bar->start();

        $stats = [
            'total' => $sinhViens->count(),
            'upgraded' => 0,
            'already_max' => 0,
            'not_complete' => 0,
            'errors' => 0,
        ];

        foreach ($sinhViens as $sinhVien) {
            try {
                $result = $this->xuLyChuyenKySinhVien($sinhVien, $hocKy, $dryRun);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Lỗi chuyển kỳ cho SV {$sinhVien->ma_sinh_vien}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Hiển thị thống kê
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 KẾT QUẢ CHUYỂN KỲ:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->table(
            ['Trạng thái', 'Số lượng'],
            [
                ['Tổng số sinh viên', $stats['total']],
                ['✅ Đã chuyển kỳ', '<fg=green>' . $stats['upgraded'] . '</>'],
                ['⏭️  Đã đạt kỳ tối đa', '<fg=yellow>' . $stats['already_max'] . '</>'],
                ['⏸️  Chưa hoàn thành kỳ hiện tại', '<fg=blue>' . $stats['not_complete'] . '</>'],
                ['❌ Lỗi', '<fg=red>' . $stats['errors'] . '</>'],
            ]
        );
        $this->newLine();

        if ($dryRun) {
            $this->warn('💡 Đây chỉ là kết quả mô phỏng. Chạy lại không có --dry-run để thực sự cập nhật.');
        } else {
            $this->info('✅ Hoàn thành!');
        }

        return 0;
    }

    /**
     * Xử lý chuyển kỳ cho một sinh viên
     */
    private function xuLyChuyenKySinhVien(SinhVien $sinhVien, HocKy $hocKy, bool $dryRun): string
    {
        $kyHienTai = $sinhVien->ky_hien_tai;

        // Kiểm tra đã đạt kỳ tối đa (8 kỳ)
        if ($kyHienTai >= 8) {
            return 'already_max';
        }

        // Kiểm tra sinh viên có đăng ký môn trong học kỳ này không
        $daHocTrongKyNay = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->exists();

        // Nếu sinh viên không học gì trong kỳ này, không chuyển kỳ
        if (!$daHocTrongKyNay) {
            return 'not_complete';
        }

        // Lấy danh sách môn trong chương trình khung của kỳ hiện tại
        if (!$sinhVien->chuyen_nganh_id) {
            return 'not_complete'; // Chưa có chuyên ngành thì không chuyển kỳ
        }

        $monTrongChuongTrinh = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
            ->where('hoc_ky_goi_y', $kyHienTai)
            ->pluck('mon_hoc_id')
            ->toArray();

        // Nếu không có môn nào trong chương trình khung kỳ này, chuyển kỳ luôn
        if (empty($monTrongChuongTrinh)) {
            if (!$dryRun) {
                $sinhVien->ky_hien_tai = $kyHienTai + 1;
                $sinhVien->save();
                Log::info("Chuyển kỳ {$sinhVien->ma_sinh_vien}: {$kyHienTai} -> " . ($kyHienTai + 1));
            }
            return 'upgraded';
        }

        // Kiểm tra sinh viên đã đăng ký và học hết các môn trong chương trình khung chưa
        $daHocCacMon = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function($query) use ($monTrongChuongTrinh) {
                $query->whereIn('mon_hoc_id', $monTrongChuongTrinh);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->pluck('lop_hoc_phan_id')
            ->count();

        // Lấy số môn bắt buộc trong chương trình khung
        $soMonBatBuoc = ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
            ->where('hoc_ky_goi_y', $kyHienTai)
            ->where('bat_buoc', true)
            ->count();

        // Chỉ chuyển kỳ nếu sinh viên đã đăng ký ít nhất các môn bắt buộc
        if ($daHocCacMon >= $soMonBatBuoc) {
            if (!$dryRun) {
                $sinhVien->ky_hien_tai = $kyHienTai + 1;
                $sinhVien->save();
                Log::info("Chuyển kỳ {$sinhVien->ma_sinh_vien}: {$kyHienTai} -> " . ($kyHienTai + 1) . " (đã hoàn thành {$daHocCacMon}/{$soMonBatBuoc} môn bắt buộc)");
            }
            return 'upgraded';
        }

        return 'not_complete';
    }
}

