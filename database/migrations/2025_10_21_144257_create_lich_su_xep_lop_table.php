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
        Schema::create('lich_su_xep_lop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->timestamp('ngay_chay_xep_lop')->comment('Ngày chạy thuật toán xếp lớp');
            $table->integer('so_sinh_vien_can_xep')->nullable()->comment('Tổng số SV đăng ký chờ xếp');
            $table->integer('so_sinh_vien_xep_thanh_cong')->nullable();
            $table->integer('so_sinh_vien_xep_that_bai')->nullable();
            $table->integer('thoi_gian_xu_ly')->nullable()->comment('Thời gian xử lý (giây)');
            $table->foreignId('nguoi_chay_id')->nullable()->constrained('dao_tao')->onDelete('set null');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_xep_lop');
    }
};
