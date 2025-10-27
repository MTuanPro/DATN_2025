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
        // 1. lop_hoc_phan_sinh_vien - Query theo sinh viên và trạng thái
        Schema::table('lop_hoc_phan_sinh_vien', function (Blueprint $table) {
            $table->index('sinh_vien_id', 'idx_lhpsv_sinh_vien');
            $table->index('trang_thai', 'idx_lhpsv_trang_thai');
            $table->index(['sinh_vien_id', 'trang_thai'], 'idx_lhpsv_sv_tt');
        });

        // 2. ket_qua_hoc_tap - Thống kê qua môn và sắp xếp theo điểm
        Schema::table('ket_qua_hoc_tap', function (Blueprint $table) {
            $table->index('qua_mon', 'idx_kqht_qua_mon');
            $table->index('diem_he_10', 'idx_kqht_diem');
        });

        // 3. dang_ky_mon_hoc_tam - Lọc theo trạng thái và học kỳ
        Schema::table('dang_ky_mon_hoc_tam', function (Blueprint $table) {
            $table->index('trang_thai', 'idx_dkmht_trang_thai');
            $table->index(['hoc_ky_id', 'trang_thai'], 'idx_dkmht_hk_tt');
        });

        // 4. lich_hoc_chi_tiet - Query theo ngày học
        Schema::table('lich_hoc_chi_tiet', function (Blueprint $table) {
            $table->index('ngay_hoc', 'idx_lhct_ngay');
            $table->index(['ngay_hoc', 'tiet_bat_dau'], 'idx_lhct_ngay_tiet');
        });

        // 5. diem_danh - Thống kê điểm danh
        Schema::table('diem_danh', function (Blueprint $table) {
            $table->index('trang_thai', 'idx_dd_trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hoc_phan_sinh_vien', function (Blueprint $table) {
            $table->dropIndex('idx_lhpsv_sinh_vien');
            $table->dropIndex('idx_lhpsv_trang_thai');
            $table->dropIndex('idx_lhpsv_sv_tt');
        });

        Schema::table('ket_qua_hoc_tap', function (Blueprint $table) {
            $table->dropIndex('idx_kqht_qua_mon');
            $table->dropIndex('idx_kqht_diem');
        });

        Schema::table('dang_ky_mon_hoc_tam', function (Blueprint $table) {
            $table->dropIndex('idx_dkmht_trang_thai');
            $table->dropIndex('idx_dkmht_hk_tt');
        });

        Schema::table('lich_hoc_chi_tiet', function (Blueprint $table) {
            $table->dropIndex('idx_lhct_ngay');
            $table->dropIndex('idx_lhct_ngay_tiet');
        });

        Schema::table('diem_danh', function (Blueprint $table) {
            $table->dropIndex('idx_dd_trang_thai');
        });
    }
};
