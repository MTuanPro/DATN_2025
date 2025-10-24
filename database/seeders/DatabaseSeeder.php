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
        ]);
    }
}
