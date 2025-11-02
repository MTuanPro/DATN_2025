<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // ========================================
            // PHASE 1: Hệ thống phân quyền
            // ========================================
            VaiTroSeeder::class,           // 1. Vai trò (admin, sinh_vien, giang_vien, dao_tao)
            NhomQuyenSeeder::class,        // 2. Nhóm quyền
            QuyenSeeder::class,            // 3. Quyền
            VaiTroQuyenSeeder::class,      // 4. Map Vai trò - Quyền

            // ========================================
            // PHASE 2: Tài khoản quản trị
            // ========================================
            AdminSeeder::class,            // 5. Tài khoản Admin
            DaoTaoSeeder::class,           // 6. Tài khoản Đào tạo (Trưởng phòng + Nhân viên)

            // ========================================
            // PHASE 3: Danh mục cơ bản
            // ========================================
            KhoaSeeder::class,             // 7. Khoa
            NganhSeeder::class,            // 8. Ngành
            ChuyenNganhSeeder::class,      // 9. Chuyên ngành
            KhoaHocSeeder::class,          // 10. Khóa học
            TrinhDoSeeder::class,          // 11. Trình độ
            TrangThaiHocTapSeeder::class,  // 12. Trạng thái học tập
            PhongHocSeeder::class,         // 13. Phòng học

            // ========================================
            // PHASE 4: Môn học và Chương trình đào tạo
            // ========================================
            MonHocSeeder::class,           // 14. Môn học
            MonHocTienQuyetSeeder::class,  // 15. Môn học tiên quyết
            ChuongTrinhKhungSeeder::class, // 16. Chương trình khung
            HocKySeeder::class,            // 17. Học kỳ

            // ========================================
            // PHASE 5: Giảng viên
            // ========================================
            GiangVienSeeder::class,        // 18. Giảng viên (tạo user + giang_vien)

            // ========================================
            // PHASE 6: Lớp hành chính và Sinh viên
            // ========================================
            LopHanhChinhSeeder::class,     // 19. Lớp hành chính
            SinhVienSeeder::class,         // 20. Sinh viên (tạo user + sinh_vien)

            // ========================================
            // PHASE 7: Lớp học phần
            // ========================================
            LopHocPhanSeeder::class,       // 21. Lớp học phần
            LopHocPhanGiangVienSeeder::class, // 22. Giảng viên dạy lớp học phần
            CauHinhDauDiemSeeder::class,   // 23. Cấu hình đầu điểm

            // ========================================
            // PHASE 8: Lịch học
            // ========================================
            LichHocCoDinhSeeder::class,    // 24. Lịch học cố định
            LichHocChiTietSeeder::class,   // 25. Lịch học chi tiết

            // ========================================
            // PHASE 9: Đăng ký môn học
            // ========================================
            DangKyMonHocTamSeeder::class,  // 26. Đăng ký môn học tạm
            LopHocPhanSinhVienSeeder::class, // 27. Sinh viên đã đăng ký lớp học phần

            // ========================================
            // PHASE 10: Lịch thi
            // ========================================
            LichThiSeeder::class,          // 28. Lịch thi
        ]);
    }
}
