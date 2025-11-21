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
        Schema::table('chi_tiet_hoc_phi_mon', function (Blueprint $table) {
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_hoc_phi_mon', function (Blueprint $table) {
            // Lưu ý: Không thể revert về NOT NULL nếu đã có dữ liệu NULL
            // Nếu cần revert, phải xóa dữ liệu NULL trước
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->nullable(false)->change();
        });
    }
};
