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
            $table->boolean('da_xem')->default(false)->after('trang_thai')->comment('Đánh dấu sinh viên đã xem cảnh báo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canh_bao_hoc_vu', function (Blueprint $table) {
            $table->dropColumn('da_xem');
        });
    }
};
