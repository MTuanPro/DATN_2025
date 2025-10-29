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

            // 1. Vai trò (bắt buộc chạy đầu tiên)
            VaiTroSeeder::class,

            // 2. Nhóm quyền và Quyền
            NhomQuyenSeeder::class,
            QuyenSeeder::class,

            // 3. Users (tài khoản test)
            UserSeeder::class,

            // 4. Map Vai trò - Quyền (sau khi có VaiTro và Quyen)
            VaiTroQuyenSeeder::class,

            // 5. Admin và Đào tạo
            AdminDaoTaoSeeder::class,

            // 6. Danh mục cơ bản
            KhoaSeeder::class,
            NganhSeeder::class,
            ChuyenNganhSeeder::class,
            KhoaHocSeeder::class,
            TrinhDoSeeder::class,
            TrangThaiHocTapSeeder::class,
            PhongHocSeeder::class,

            // 7. Môn học và CTĐT
            MonHocSeeder::class,
            MonHocTienQuyetSeeder::class,
            ChuongTrinhKhungSeeder::class,

            // 8. Học kỳ
            HocKySeeder::class,

            // 9. Giảng viên (phải sau Khoa và TrinhDo)
            GiangVienSeeder::class,

            // 10. Lớp hành chính và Sinh viên (phải sau KhoaHoc, ChuyenNganh)
            LopHanhChinhSeeder::class,
            SinhVienSeeder::class,

            // 11. PHASE 4 - Member 1: Lớp học phần (phải sau MonHoc, HocKy, GiangVien)
            LopHocPhanSeeder::class,
            LopHocPhanGiangVienSeeder::class,
            CauHinhDauDiemSeeder::class,

            // 11. PHASE 4 - Lịch học (phải sau LopHocPhan, PhongHoc, GiangVien)
            LichHocCoDinhSeeder::class,
            LichHocChiTietSeeder::class,

            // 12. PHASE 5 - Đăng ký môn học (phải sau SinhVien, LopHocPhan, HocKy)
            DangKyMonHocTamSeeder::class,
            LopHocPhanSinhVienSeeder::class,

        ]);
    }
}
