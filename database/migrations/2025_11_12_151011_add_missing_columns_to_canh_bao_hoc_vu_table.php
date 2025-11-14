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
        Schema::table('canh_bao_hoc_vu', function (Blueprint $table) {
            // Thêm cột ghi_chu
            $table->text('ghi_chu')->nullable()->after('ly_do');
            
            // Thêm cột nguoi_tao_id (có thể là admin hoặc dao_tao)
            $table->unsignedBigInteger('nguoi_tao_id')->nullable()->after('nguoi_canh_bao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canh_bao_hoc_vu', function (Blueprint $table) {
            $table->dropColumn(['ghi_chu', 'nguoi_tao_id']);
        });
    }
};
