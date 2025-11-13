<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

/**
 * Seeder tổng hợp cho hệ thống quản lý sinh viên
 * 
 * Seeder này đảm bảo tất cả dữ liệu cần thiết được tạo theo đúng thứ tự
 * để hệ thống quản lý sinh viên hoạt động đầy đủ.
 */
class QuanLySinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu seed dữ liệu cho hệ thống quản lý sinh viên...');
        $this->command->newLine();

        // ========================================
        // PHASE 1: Hệ thống phân quyền
        // ========================================
        $this->command->info('📋 PHASE 1: Hệ thống phân quyền');
        $this->call(VaiTroSeeder::class);
        $this->call(NhomQuyenSeeder::class);
        $this->call(QuyenSeeder::class);
        $this->call(VaiTroQuyenSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 1');
        $this->command->newLine();

        // ========================================
        // PHASE 2: Tài khoản quản trị
        // ========================================
        $this->command->info('👤 PHASE 2: Tài khoản quản trị');
        $this->call(AdminSeeder::class);
        $this->call(DaoTaoSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 2');
        $this->command->newLine();

        // ========================================
        // PHASE 3: Danh mục cơ bản
        // ========================================
        $this->command->info('📚 PHASE 3: Danh mục cơ bản');
        $this->call(KhoaSeeder::class);
        $this->call(NganhSeeder::class);
        $this->call(ChuyenNganhSeeder::class);
        $this->call(KhoaHocSeeder::class);
        $this->call(TrinhDoSeeder::class);
        $this->call(TrangThaiHocTapSeeder::class);
        $this->call(PhongHocSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 3');
        $this->command->newLine();

        // ========================================
        // PHASE 4: Môn học và Chương trình đào tạo
        // ========================================
        $this->command->info('📖 PHASE 4: Môn học và Chương trình đào tạo');
        $this->call(MonHocSeeder::class);
        $this->call(MonHocTienQuyetSeeder::class);
        $this->call(ChuongTrinhKhungSeeder::class);
        $this->call(HocKySeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 4');
        $this->command->newLine();

        // ========================================
        // PHASE 5: Giảng viên
        // ========================================
        $this->command->info('👨‍🏫 PHASE 5: Giảng viên');
        $this->call(GiangVienSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 5');
        $this->command->newLine();

        // ========================================
        // PHASE 6: Lớp hành chính và Sinh viên
        // ========================================
        $this->command->info('👥 PHASE 6: Lớp hành chính và Sinh viên');
        $this->call(LopHanhChinhSeeder::class);
        $this->call(GanGVCNSeeder::class); // Gán giảng viên chủ nhiệm
        $this->call(SinhVienSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 6');
        $this->command->newLine();

        // ========================================
        // PHASE 7: Lớp học phần
        // ========================================
        $this->command->info('📝 PHASE 7: Lớp học phần');
        $this->call(LopHocPhanSeeder::class);
        $this->call(LopHocPhanGiangVienSeeder::class);
        $this->call(CauHinhDauDiemSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 7');
        $this->command->newLine();

        // ========================================
        // PHASE 8: Lịch học
        // ========================================
        $this->command->info('📅 PHASE 8: Lịch học');
        $this->call(LichHocCoDinhSeeder::class);
        $this->call(LichHocChiTietSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 8');
        $this->command->newLine();

        // ========================================
        // PHASE 9: Đăng ký môn học
        // ========================================
        $this->command->info('📋 PHASE 9: Đăng ký môn học');
        $this->call(DangKyMonHocTamSeeder::class);
        $this->call(LopHocPhanSinhVienSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 9');
        $this->command->newLine();

        // ========================================
        // PHASE 10: Lịch thi
        // ========================================
        $this->command->info('📆 PHASE 10: Lịch thi');
        $this->call(LichThiSeeder::class);
        $this->call(LichThiSinhVienSeeder::class);
        $this->command->info('✅ Hoàn thành PHASE 10');
        $this->command->newLine();

        // ========================================
        // PHASE 11: Học phí (nếu có)
        // ========================================
        if (class_exists(CauHinhHocPhiSeeder::class)) {
            $this->command->info('💰 PHASE 11: Cấu hình học phí');
            $this->call(CauHinhHocPhiSeeder::class);
            $this->command->info('✅ Hoàn thành PHASE 11');
            $this->command->newLine();
        }

        // ========================================
        // PHASE 12: Dữ liệu bổ sung (nếu có)
        // ========================================
        if (class_exists(DiemDanhSeeder::class)) {
            $this->command->info('📊 PHASE 12: Dữ liệu bổ sung');
            $this->call(DiemDanhSeeder::class);
            $this->command->info('✅ Hoàn thành PHASE 12');
            $this->command->newLine();
        }

        $this->command->info('🎉 Hoàn thành seed dữ liệu cho hệ thống quản lý sinh viên!');
        $this->command->newLine();
        $this->displaySummary();
    }

    /**
     * Hiển thị tóm tắt dữ liệu đã seed
     */
    private function displaySummary(): void
    {
        $this->command->info('📊 TÓM TẮT DỮ LIỆU:');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Đếm số lượng
        $counts = [
            'Khoa' => DB::table('khoa')->count(),
            'Ngành' => DB::table('nganh')->count(),
            'Chuyên ngành' => DB::table('chuyen_nganh')->count(),
            'Khóa học' => DB::table('khoa_hoc')->count(),
            'Lớp hành chính' => DB::table('lop_hanh_chinh')->count(),
            'Sinh viên' => DB::table('sinh_vien')->count(),
            'Giảng viên' => DB::table('giang_vien')->count(),
            'Môn học' => DB::table('mon_hoc')->count(),
            'Lớp học phần' => DB::table('lop_hoc_phan')->count(),
            'Học kỳ' => DB::table('hoc_ky')->count(),
            'Phòng học' => DB::table('phong_hoc')->count(),
            'Lịch học cố định' => DB::table('lich_hoc_co_dinh')->count(),
            'Lịch học chi tiết' => DB::table('lich_hoc_chi_tiet')->count(),
            'Lịch thi' => DB::table('lich_thi')->count(),
            'Tài khoản' => DB::table('users')->count(),
        ];

        foreach ($counts as $label => $count) {
            $this->command->line(sprintf('  %-25s: %s', $label, number_format($count)));
        }

        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();

        // Thông tin đăng nhập
        $this->command->info('🔐 THÔNG TIN ĐĂNG NHẬP:');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $admin = DB::table('users')
            ->join('tai_khoan_vai_tro', 'users.id', '=', 'tai_khoan_vai_tro.tai_khoan_id')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->where('vai_tro.ma_vai_tro', 'admin')
            ->select('users.email', 'users.name')
            ->first();

        if ($admin) {
            $this->command->line("  👤 Admin: {$admin->email} / password");
        }

        $daoTao = DB::table('users')
            ->join('tai_khoan_vai_tro', 'users.id', '=', 'tai_khoan_vai_tro.tai_khoan_id')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->whereIn('vai_tro.ma_vai_tro', ['truong_phong_dt', 'nhan_vien_dt'])
            ->select('users.email', 'users.name', 'vai_tro.ten_vai_tro')
            ->first();

        if ($daoTao) {
            $this->command->line("  👤 {$daoTao->ten_vai_tro}: {$daoTao->email} / password");
        }

        $sinhVien = DB::table('users')
            ->join('tai_khoan_vai_tro', 'users.id', '=', 'tai_khoan_vai_tro.tai_khoan_id')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->join('sinh_vien', 'users.id', '=', 'sinh_vien.user_id')
            ->where('vai_tro.ma_vai_tro', 'sinh_vien')
            ->select('users.email', 'sinh_vien.ma_sinh_vien', 'sinh_vien.ho_ten')
            ->first();

        if ($sinhVien) {
            $this->command->line("  👤 Sinh viên: {$sinhVien->email} / password");
            $this->command->line("     Mã SV: {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten}");
        }

        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
    }
}

