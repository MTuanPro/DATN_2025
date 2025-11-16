<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm indexes để tối ưu performance cho các query thường dùng
     */
    public function up(): void
    {
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        // Helper function để check index exist
        $indexExists = function($table, $indexName) use ($connection, $dbName) {
            $result = $connection->select("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = ? 
                AND table_name = ? 
                AND index_name = ?
            ", [$dbName, $table, $indexName]);
            
            return $result[0]->count > 0;
        };
        
        // Indexes cho bảng dang_ky_mon_hoc_tam
        Schema::table('dang_ky_mon_hoc_tam', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('dang_ky_mon_hoc_tam', 'idx_dkmt_sv_hk_tt')) {
                $table->index(['sinh_vien_id', 'hoc_ky_id', 'trang_thai'], 'idx_dkmt_sv_hk_tt');
            }
            if (!$indexExists('dang_ky_mon_hoc_tam', 'idx_dkmt_date_priority')) {
                $table->index(['ngay_dang_ky', 'uu_tien'], 'idx_dkmt_date_priority');
            }
            if (!$indexExists('dang_ky_mon_hoc_tam', 'idx_dkmt_mh_hk')) {
                $table->index(['mon_hoc_id', 'hoc_ky_id'], 'idx_dkmt_mh_hk');
            }
        });

        // Indexes cho bảng dang_ky_mon_hocs
        Schema::table('dang_ky_mon_hocs', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('dang_ky_mon_hocs', 'idx_dkmh_tt_tg')) {
                $table->index(['trang_thai', 'thoi_gian_dang_ky'], 'idx_dkmh_tt_tg');
            }
            if (!$indexExists('dang_ky_mon_hocs', 'idx_dkmh_nguoi_duyet')) {
                $table->index('nguoi_duyet_id', 'idx_dkmh_nguoi_duyet');
            }
            if (!$indexExists('dang_ky_mon_hocs', 'idx_dkmh_lhp_tt')) {
                $table->index(['lop_hoc_phan_id', 'trang_thai'], 'idx_dkmh_lhp_tt');
            }
        });

        // Indexes cho bảng lop_hoc_phan_sinh_vien
        Schema::table('lop_hoc_phan_sinh_vien', function (Blueprint $table) use ($indexExists) {
            // Note: idx_lhpsv_sv_tt already exists from migration 2025_10_27_090834
            if (!$indexExists('lop_hoc_phan_sinh_vien', 'idx_lhpsv_ngay_dk')) {
                $table->index('ngay_dang_ky', 'idx_lhpsv_ngay_dk');
            }
            if (!$indexExists('lop_hoc_phan_sinh_vien', 'idx_lhpsv_ptx_nxl')) {
                $table->index(['phuong_thuc_xep', 'ngay_xep_lop'], 'idx_lhpsv_ptx_nxl');
            }
            if (!$indexExists('lop_hoc_phan_sinh_vien', 'idx_lhpsv_dkt_id')) {
                $table->index('dang_ky_tam_id', 'idx_lhpsv_dkt_id');
            }
        });

        // Indexes cho bảng lop_hoc_phan
        Schema::table('lop_hoc_phan', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('lop_hoc_phan', 'idx_lhp_mh_hk_tt')) {
                $table->index(['mon_hoc_id', 'hoc_ky_id', 'trang_thai_lop'], 'idx_lhp_mh_hk_tt');
            }
            if (!$indexExists('lop_hoc_phan', 'idx_lhp_slots')) {
                $table->index(['so_luong_dang_ky', 'suc_chua'], 'idx_lhp_slots');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes từ dang_ky_mon_hoc_tam
        Schema::table('dang_ky_mon_hoc_tam', function (Blueprint $table) {
            $table->dropIndex('idx_dkmt_sv_hk_tt');
            $table->dropIndex('idx_dkmt_date_priority');
            $table->dropIndex('idx_dkmt_mh_hk');
        });

        // Drop indexes từ dang_ky_mon_hocs
        Schema::table('dang_ky_mon_hocs', function (Blueprint $table) {
            $table->dropIndex('idx_dkmh_tt_tg');
            $table->dropIndex('idx_dkmh_nguoi_duyet');
            $table->dropIndex('idx_dkmh_lhp_tt');
        });

        // Drop indexes từ lop_hoc_phan_sinh_vien
        Schema::table('lop_hoc_phan_sinh_vien', function (Blueprint $table) {
            // Note: idx_lhpsv_sv_tt không drop vì thuộc migration khác
            $table->dropIndex('idx_lhpsv_ngay_dk');
            $table->dropIndex('idx_lhpsv_ptx_nxl');
            $table->dropIndex('idx_lhpsv_dkt_id');
        });

        // Drop indexes từ lop_hoc_phan
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->dropIndex('idx_lhp_mh_hk_tt');
            $table->dropIndex('idx_lhp_slots');
        });
    }
};
