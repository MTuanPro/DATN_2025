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
        Schema::create('diem_danh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade');
            $table->foreignId('lich_hoc_chi_tiet_id')->constrained('lich_hoc_chi_tiet')->onDelete('cascade');
            $table->enum('trang_thai', ['co_mat', 'vang', 'di_tre', 'nghi_phep'])->default('vang');
            $table->timestamp('thoi_gian_diem_danh')->nullable()->comment('Thời gian giảng viên điểm danh');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->unique(['lop_hoc_phan_sinh_vien_id', 'lich_hoc_chi_tiet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_danh');
    }
};
