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
        Schema::table('canh_bao_hoc_vu', function (Blueprint $table) {
            // Thêm cột trang_thai: chua_xu_ly, dang_xu_ly, da_xu_ly
            $table->enum('trang_thai', ['chua_xu_ly', 'dang_xu_ly', 'da_xu_ly'])
                  ->default('chua_xu_ly')
                  ->after('nguoi_canh_bao_id');
        });
        
        // Cập nhật dữ liệu từ da_xu_ly sang trang_thai
        DB::statement("UPDATE canh_bao_hoc_vu SET trang_thai = CASE WHEN da_xu_ly = 1 THEN 'da_xu_ly' ELSE 'chua_xu_ly' END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canh_bao_hoc_vu', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
