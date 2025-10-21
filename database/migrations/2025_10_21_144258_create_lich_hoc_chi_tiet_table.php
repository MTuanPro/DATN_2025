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
        Schema::create('lich_hoc_chi_tiet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lich_hoc_co_dinh_id')->nullable()->constrained('lich_hoc_co_dinh')->onDelete('set null');
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->date('ngay_hoc');
            $table->integer('tiet_bat_dau');
            $table->integer('tiet_ket_thuc');
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->foreignId('phong_hoc_id')->nullable()->constrained('phong_hoc')->onDelete('set null');
            $table->foreignId('giang_vien_id')->constrained('giang_vien');
            $table->enum('hinh_thuc', ['offline', 'online', 'hybrid'])->default('offline');
            $table->string('link_online')->nullable();
            $table->text('noi_dung_giang_day')->nullable()->comment('Nội dung giảng dạy trong buổi');
            $table->string('tai_lieu_dinh_kem')->nullable();
            $table->string('trang_thai')->default('chua_day')->comment('chua_day, dang_day, da_day, huy');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->index(['lop_hoc_phan_id', 'ngay_hoc'], 'idx_lop_ngay');
            $table->unique(['ngay_hoc', 'tiet_bat_dau', 'phong_hoc_id'], 'unique_phong_ngay_tiet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hoc_chi_tiet');
    }
};
