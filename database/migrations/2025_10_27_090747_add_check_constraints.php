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
        // 1. lop_hoc_phan - Kiểm tra số lượng <= sức chứa
        DB::statement('ALTER TABLE lop_hoc_phan ADD CONSTRAINT chk_lhp_capacity CHECK (so_luong_dang_ky <= suc_chua)');
        DB::statement('ALTER TABLE lop_hoc_phan ADD CONSTRAINT chk_lhp_dates CHECK (ngay_ket_thuc > ngay_bat_dau)');
        DB::statement('ALTER TABLE lop_hoc_phan ADD CONSTRAINT chk_lhp_suc_chua_range CHECK (suc_chua >= 10 AND suc_chua <= 100)');

        // 2. mon_hoc - Kiểm tra tín chỉ
        DB::statement('ALTER TABLE mon_hoc ADD CONSTRAINT chk_mh_tin_chi CHECK (so_tin_chi = so_tin_chi_ly_thuyet + so_tin_chi_thuc_hanh)');
        DB::statement('ALTER TABLE mon_hoc ADD CONSTRAINT chk_mh_tin_chi_range CHECK (so_tin_chi >= 1 AND so_tin_chi <= 10)');

        // 3. cau_hinh_dau_diem - Kiểm tra tỷ lệ
        DB::statement('ALTER TABLE cau_hinh_dau_diem ADD CONSTRAINT chk_chdd_ty_le CHECK (ty_le > 0 AND ty_le <= 100)');

        // 4. lich_hoc_co_dinh - Kiểm tra tiết học
        DB::statement('ALTER TABLE lich_hoc_co_dinh ADD CONSTRAINT chk_lhcd_tiet CHECK (tiet_ket_thuc > tiet_bat_dau)');
        DB::statement('ALTER TABLE lich_hoc_co_dinh ADD CONSTRAINT chk_lhcd_thu CHECK (thu_trong_tuan >= 2 AND thu_trong_tuan <= 8)');

        // 5. ket_qua_hoc_tap - Kiểm tra điểm
        DB::statement('ALTER TABLE ket_qua_hoc_tap ADD CONSTRAINT chk_kqht_diem_10 CHECK (diem_he_10 >= 0 AND diem_he_10 <= 10)');
        DB::statement('ALTER TABLE ket_qua_hoc_tap ADD CONSTRAINT chk_kqht_diem_4 CHECK (diem_he_4 >= 0 AND diem_he_4 <= 4)');

        // 6. nhap_diem - Kiểm tra điểm
        DB::statement('ALTER TABLE nhap_diem ADD CONSTRAINT chk_nd_diem CHECK (diem_so >= 0 AND diem_so <= 10)');

        // 7. hoc_phi_hoc_ky - Kiểm tra số tiền
        DB::statement('ALTER TABLE hoc_phi_hoc_ky ADD CONSTRAINT chk_hphk_tin_chi_max CHECK (tong_tin_chi_dang_ky <= 24)');
        DB::statement('ALTER TABLE hoc_phi_hoc_ky ADD CONSTRAINT chk_hphk_so_tien CHECK (so_tien_da_dong <= tong_so_tien)');
        DB::statement('ALTER TABLE hoc_phi_hoc_ky ADD CONSTRAINT chk_hphk_so_tien_con_lai CHECK (so_tien_con_lai >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE lop_hoc_phan DROP CONSTRAINT IF EXISTS chk_lhp_capacity');
        DB::statement('ALTER TABLE lop_hoc_phan DROP CONSTRAINT IF EXISTS chk_lhp_dates');
        DB::statement('ALTER TABLE lop_hoc_phan DROP CONSTRAINT IF EXISTS chk_lhp_suc_chua_range');

        DB::statement('ALTER TABLE mon_hoc DROP CONSTRAINT IF EXISTS chk_mh_tin_chi');
        DB::statement('ALTER TABLE mon_hoc DROP CONSTRAINT IF EXISTS chk_mh_tin_chi_range');

        DB::statement('ALTER TABLE cau_hinh_dau_diem DROP CONSTRAINT IF EXISTS chk_chdd_ty_le');

        DB::statement('ALTER TABLE lich_hoc_co_dinh DROP CONSTRAINT IF EXISTS chk_lhcd_tiet');
        DB::statement('ALTER TABLE lich_hoc_co_dinh DROP CONSTRAINT IF EXISTS chk_lhcd_thu');

        DB::statement('ALTER TABLE ket_qua_hoc_tap DROP CONSTRAINT IF EXISTS chk_kqht_diem_10');
        DB::statement('ALTER TABLE ket_qua_hoc_tap DROP CONSTRAINT IF EXISTS chk_kqht_diem_4');

        DB::statement('ALTER TABLE nhap_diem DROP CONSTRAINT IF EXISTS chk_nd_diem');

        DB::statement('ALTER TABLE hoc_phi_hoc_ky DROP CONSTRAINT IF EXISTS chk_hphk_tin_chi_max');
        DB::statement('ALTER TABLE hoc_phi_hoc_ky DROP CONSTRAINT IF EXISTS chk_hphk_so_tien');
        DB::statement('ALTER TABLE hoc_phi_hoc_ky DROP CONSTRAINT IF EXISTS chk_hphk_so_tien_con_lai');
    }
};
