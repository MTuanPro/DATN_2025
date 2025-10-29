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
        Schema::create('canh_bao_hoc_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->string('loai_canh_bao')->comment('diem_thap, vang_nhieu, no_hoc_phi, hoc_ky_lien_tiep');
            $table->string('muc_do')->comment('canh_cao, dinh_chi, buoc_thoi_hoc');
            $table->text('ly_do')->comment('GPA < 1.0, vắng > 20%, nợ học phí quá hạn');
            $table->date('ngay_canh_bao');
            $table->foreignId('nguoi_canh_bao_id')->nullable()->constrained('dao_tao')->onDelete('set null');
            $table->boolean('da_xu_ly')->default(false);
            $table->date('ngay_xu_ly')->nullable();
            $table->text('ket_qua_xu_ly')->nullable();
            $table->timestamps();

            $table->index(['sinh_vien_id', 'hoc_ky_id', 'loai_canh_bao'], 'idx_sv_hk_loai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canh_bao_hoc_vu');
    }
};
