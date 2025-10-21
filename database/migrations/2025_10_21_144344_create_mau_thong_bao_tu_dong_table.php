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
        Schema::create('mau_thong_bao_tu_dong', function (Blueprint $table) {
            $table->id();
            $table->string('loai_thong_bao')->unique()->comment('lich_hoc_moi, lich_thi_sap_toi, hoc_phi_sap_het_han, diem_da_cap_nhat');
            $table->string('tieu_de_mau')->comment('Tiêu đề có thể chứa biến: {mon_hoc}, {ngay_thi}');
            $table->text('noi_dung_mau')->comment('Nội dung có thể chứa biến');
            $table->string('doi_tuong_mac_dinh')->nullable()->comment('sinh_vien, giang_vien');
            $table->string('muc_do_uu_tien')->default('binh_thuong');
            $table->boolean('gui_email_mac_dinh')->default(false);
            $table->boolean('gui_sms_mac_dinh')->default(false);
            $table->boolean('kich_hoat')->default(true)->comment('Bật/tắt thông báo tự động này');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau_thong_bao_tu_dong');
    }
};
