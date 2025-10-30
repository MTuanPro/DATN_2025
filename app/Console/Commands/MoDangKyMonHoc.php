<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HocKy;
use App\Models\LopHocPhan;
use Carbon\Carbon;

class MoDangKyMonHoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dangky:open {hoc_ky_id?} {--days=30 : Số ngày mở đăng ký} {--list : Xem danh sách học kỳ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mở đăng ký môn học cho học kỳ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Hiển thị danh sách học kỳ
        if ($this->option('list')) {
            $this->showHocKyList();
            return 0;
        }

        $hocKyId = $this->argument('hoc_ky_id');

        if (!$hocKyId) {
            $this->error('Vui lòng cung cấp ID học kỳ. Dùng --list để xem danh sách.');
            return 1;
        }

        $days = (int) $this->option('days');

        $hocKy = HocKy::find($hocKyId);

        if (!$hocKy) {
            $this->error("❌ Không tìm thấy học kỳ ID: {$hocKyId}");
            return 1;
        }

        // Confirm trước khi mở
        if (!$this->confirm("Bạn có chắc muốn mở đăng ký cho [{$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}]?")) {
            $this->info('Đã hủy.');
            return 0;
        }

        // Đóng tất cả học kỳ khác
        HocKy::query()->update(['la_hoc_ky_hien_tai' => false]);

        // Mở học kỳ được chọn
        $ngayBatDau = Carbon::now();
        $ngayKetThuc = Carbon::now()->addDays($days);
        $hocKy->update([
            'la_hoc_ky_hien_tai' => true,
            'ngay_bat_dau_dang_ky' => $ngayBatDau,
            'ngay_ket_thuc_dang_ky' => $ngayKetThuc,
        ]);

        // Mở tất cả lớp học phần trong học kỳ này
        $soLopMo = LopHocPhan::where('hoc_ky_id', $hocKyId)
            ->update(['trang_thai_lop' => 'mo_dang_ky']);

        // Hiển thị thông tin
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ ĐÃ MỞ ĐĂNG KÝ THÀNH CÔNG!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->info("📚 Học kỳ: {$hocKy->ten_hoc_ky} - {$hocKy->nam_hoc}");
        $this->info("⏰ Thời gian đăng ký:");
        $this->info("   Từ: " . $ngayBatDau->format('d/m/Y H:i'));
        $this->info("   Đến: " . $ngayKetThuc->format('d/m/Y H:i'));
        $this->info("📊 Số lớp học phần đã mở: {$soLopMo}");
        $this->line('');
        $this->info('🎓 Sinh viên có thể đăng ký tại: /sinhvien/dang-ky-mon-hoc');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return 0;
    }

    /**
     * Hiển thị danh sách học kỳ
     */
    private function showHocKyList()
    {
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->get();

        if ($hocKys->isEmpty()) {
            $this->warn('Không có học kỳ nào trong hệ thống.');
            return;
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📋 DANH SÁCH HỌC KỲ');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');

        $headers = ['ID', 'Học kỳ', 'Năm học', 'Bắt đầu', 'Kết thúc', 'Đăng ký từ', 'Đăng ký đến', 'Hiện tại'];
        $rows = [];

        foreach ($hocKys as $hk) {
            $rows[] = [
                $hk->id,
                $hk->ten_hoc_ky,
                $hk->nam_hoc,
                $hk->ngay_bat_dau->format('d/m/Y'),
                $hk->ngay_ket_thuc->format('d/m/Y'),
                $hk->ngay_bat_dau_dang_ky ? $hk->ngay_bat_dau_dang_ky->format('d/m/Y') : '-',
                $hk->ngay_ket_thuc_dang_ky ? $hk->ngay_ket_thuc_dang_ky->format('d/m/Y') : '-',
                $hk->la_hoc_ky_hien_tai ? '✅' : '',
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info('💡 Sử dụng: php artisan dangky:open {ID} --days=30');
    }
}
