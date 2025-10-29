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
        Schema::create('chi_tiet_hoc_phi_mon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_phi_hoc_ky_id')->constrained('hoc_phi_hoc_ky')->onDelete('cascade');
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->integer('so_tin_chi');
            $table->float('don_gia_tin_chi')->comment('Giá/tín chỉ tại thời điểm đăng ký');
            $table->float('thanh_tien')->comment('so_tin_chi * don_gia_tin_chi');
            $table->timestamp('ngay_tinh')->comment('Ngày tính học phí (khi đăng ký môn)');
            $table->string('trang_thai')->default('chua_thanh_toan')->comment('chua_thanh_toan, da_thanh_toan, huy');
            $table->timestamps();

            $table->unique(['hoc_phi_hoc_ky_id', 'lop_hoc_phan_sinh_vien_id'], 'unique_hp_sv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoc_phi_mon');
    }
};
