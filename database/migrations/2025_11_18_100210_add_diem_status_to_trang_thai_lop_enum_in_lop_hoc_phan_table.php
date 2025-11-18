<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm các giá trị mới vào ENUM trang_thai_lop
        DB::statement("ALTER TABLE `lop_hoc_phan` MODIFY COLUMN `trang_thai_lop` ENUM('mo_dang_ky', 'dang_hoc', 'ket_thuc', 'huy', 'da_khoa_diem', 'da_duyet_diem') DEFAULT 'mo_dang_ky'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa các giá trị mới khỏi ENUM (chỉ giữ lại các giá trị ban đầu)
        // Lưu ý: Cần đảm bảo không có dữ liệu nào đang sử dụng các giá trị này
        DB::statement("ALTER TABLE `lop_hoc_phan` MODIFY COLUMN `trang_thai_lop` ENUM('mo_dang_ky', 'dang_hoc', 'ket_thuc', 'huy') DEFAULT 'mo_dang_ky'");
    }
};
