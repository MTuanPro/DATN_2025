<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Chuẩn hóa enum gioi_tinh trong bảng giang_vien
     * Chuyển từ ['Nam', 'Nữ', 'Khác'] sang ['nam', 'nu', 'khac'] để thống nhất với sinh_vien
     */
    public function up(): void
    {
        // Bước 1: Update dữ liệu hiện có
        DB::statement("UPDATE giang_vien SET gioi_tinh = LOWER(gioi_tinh) WHERE gioi_tinh IS NOT NULL");
        
        // Bước 2: Sửa giá trị 'nữ' thành 'nu' (do lowercase chuyển Nữ -> nữ)
        DB::statement("UPDATE giang_vien SET gioi_tinh = 'nu' WHERE gioi_tinh = 'nữ'");
        
        // Bước 3: Modify column enum
        DB::statement("ALTER TABLE giang_vien MODIFY COLUMN gioi_tinh ENUM('nam', 'nu', 'khac') COMMENT 'Giới tính'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Chuyển về giá trị cũ
        DB::statement("UPDATE giang_vien SET gioi_tinh = 'Nam' WHERE gioi_tinh = 'nam'");
        DB::statement("UPDATE giang_vien SET gioi_tinh = 'Nữ' WHERE gioi_tinh = 'nu'");
        DB::statement("UPDATE giang_vien SET gioi_tinh = 'Khác' WHERE gioi_tinh = 'khac'");
        
        DB::statement("ALTER TABLE giang_vien MODIFY COLUMN gioi_tinh ENUM('Nam', 'Nữ', 'Khác') COMMENT 'Giới tính'");
    }
};
