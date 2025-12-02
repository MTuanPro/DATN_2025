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
        // ========================================
        // 1. SEEDER QUẢN LÝ USERS, VAI TRÒ, QUYỀN (Admin)
        // ========================================
        $this->call(VaiTroSeeder::class);
        $this->call(NhomQuyenSeeder::class);
        $this->call(QuyenSeeder::class);
        $this->call(VaiTroQuyenSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(AdminDaoTaoSeeder::class);

        // ========================================
        // 2. SEEDER DANH MỤC & CTĐT
        // ========================================
        // Cơ bản
        $this->call(KhoaSeeder::class);
        $this->call(TrinhDoSeeder::class);
        $this->call(TrangThaiHocTapSeeder::class);
        $this->call(PhongHocSeeder::class);
        $this->call(CaHocSeeder::class);

        // Phụ thuộc Khoa, TrinhDo
        $this->call(NganhSeeder::class);

        // Phụ thuộc Nganh
        $this->call(ChuyenNganhSeeder::class);

        // Phụ thuộc Khoa
        $this->call(MonHocSeeder::class);

        // Phụ thuộc Khoa, TrinhDo, MonHoc (giảng viên được gán môn học luôn)
        $this->call(GiangVienSeeder::class);

        // Phụ thuộc GiangVien, MonHoc - Phân bổ giảng viên cho môn học
        $this->call(PhanBoGiangVienMonHocSeeder::class);

        // Phụ thuộc ChuyenNganh, MonHoc
        $this->call(ChuongTrinhKhungSeeder::class);
        $this->call(ChuongTrinhDaoTaoTestSeeder::class);

        // ========================================
        // 3. SEEDER NIÊN KHÓA & HỌC KỲ
        // ========================================
        $this->call(KhoaHocSeeder::class);
        $this->call(HocKySeeder::class);
        $this->call(CauHinhHocPhiSeeder::class);

        // Phụ thuộc HocKy, MonHoc, GiangVien
        $this->call(LopHocPhanSeeder::class);
        
        // Copy cấu hình đầu điểm từ môn học sang lớp học phần
        $this->call(CopyCauHinhDauDiemToLopHocPhanSeeder::class);

        // Phụ thuộc KhoaHoc, Nganh, TrangThaiHocTap
        $this->call(SinhVienSeeder::class);

        // ========================================
        // 4. SEEDER THÔNG BÁO HỆ THỐNG
        // ========================================
        $this->call(ThongBaoSeeder::class);
        $this->call(MauThongBaoTuDongSeeder::class);

        // ========================================
        // 5. SEEDER AI CHATBOT
        // ========================================
        $this->call(AiChatbotKnowledgeBaseSeeder::class);
    }
}
