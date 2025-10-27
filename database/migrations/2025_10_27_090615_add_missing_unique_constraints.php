<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. tai_khoan_vai_tro - Tránh gán trùng vai trò cho cùng user
        Schema::table('tai_khoan_vai_tro', function (Blueprint $table) {
            $table->unique(['tai_khoan_id', 'vai_tro_id'], 'unique_user_role');
        });

        // 2. lop_hoc_phan_giang_vien - Thêm unique bao gồm vai_tro
        // Không drop unique cũ vì có thể đang dùng trong foreign key
        Schema::table('lop_hoc_phan_giang_vien', function (Blueprint $table) {
            $table->unique(['lop_hoc_phan_id', 'giang_vien_id', 'vai_tro'], 'unique_lhp_gv_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tai_khoan_vai_tro', function (Blueprint $table) {
            $table->dropUnique('unique_user_role');
        });

        Schema::table('lop_hoc_phan_giang_vien', function (Blueprint $table) {
            $table->dropUnique('unique_lhp_gv_role');
        });
    }
};
