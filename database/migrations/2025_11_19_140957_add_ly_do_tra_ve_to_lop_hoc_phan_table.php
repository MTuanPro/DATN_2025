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
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->text('ly_do_tra_ve')->nullable()->after('trang_thai_lop')->comment('Lý do đào tạo trả về điểm để giảng viên chỉnh sửa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->dropColumn('ly_do_tra_ve');
        });
    }
};
