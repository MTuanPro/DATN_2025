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
        // Thêm ca_hoc_id vào bảng lich_hoc_co_dinh
        Schema::table('lich_hoc_co_dinh', function (Blueprint $table) {
            $table->foreignId('ca_hoc_id')->nullable()->after('lop_hoc_phan_id')
                ->constrained('ca_hoc')->onDelete('set null')
                ->comment('Ca học - liên kết đến bảng ca_hoc');
        });

        // Thêm ca_hoc_id vào bảng lich_hoc_chi_tiet
        Schema::table('lich_hoc_chi_tiet', function (Blueprint $table) {
            $table->foreignId('ca_hoc_id')->nullable()->after('lop_hoc_phan_id')
                ->constrained('ca_hoc')->onDelete('set null')
                ->comment('Ca học - liên kết đến bảng ca_hoc');
        });

        // Thêm ca_hoc_id vào bảng lich_thi
        Schema::table('lich_thi', function (Blueprint $table) {
            $table->foreignId('ca_hoc_id')->nullable()->after('ngay_thi')
                ->constrained('ca_hoc')->onDelete('set null')
                ->comment('Ca thi - liên kết đến bảng ca_hoc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa ca_hoc_id từ bảng lich_hoc_co_dinh
        Schema::table('lich_hoc_co_dinh', function (Blueprint $table) {
            $table->dropForeign(['ca_hoc_id']);
            $table->dropColumn('ca_hoc_id');
        });

        // Xóa ca_hoc_id từ bảng lich_hoc_chi_tiet
        Schema::table('lich_hoc_chi_tiet', function (Blueprint $table) {
            $table->dropForeign(['ca_hoc_id']);
            $table->dropColumn('ca_hoc_id');
        });

        // Xóa ca_hoc_id từ bảng lich_thi
        Schema::table('lich_thi', function (Blueprint $table) {
            $table->dropForeign(['ca_hoc_id']);
            $table->dropColumn('ca_hoc_id');
        });
    }
};
