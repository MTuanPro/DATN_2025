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
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id();
            $table->string('ma_mon')->unique();
            $table->string('ten_mon');
            $table->integer('so_tin_chi')->comment('Tổng tín chỉ (1-5), phải = ly_thuyet + thuc_hanh');
            $table->integer('so_tin_chi_ly_thuyet')->default(0)->comment('Số tín chỉ lý thuyết (0-5)');
            $table->integer('so_tin_chi_thuc_hanh')->default(0)->comment('Số tín chỉ thực hành (0-5)');
            $table->text('mo_ta')->nullable();
            $table->enum('loai_mon', ['dai_cuong', 'co_so_nganh', 'chuyen_nganh_bat_buoc', 'chuyen_nganh_tu_chon', 'thuc_tap', 'do_an_tot_nghiep'])->comment('Bắt buộc phân loại môn học');
            $table->foreignId('khoa_id')->constrained('khoa')->comment('Bắt buộc - Khoa quản lý môn học');
            $table->enum('hinh_thuc_day', ['offline', 'online', 'hybrid'])->comment('Bắt buộc chọn hình thức');
            $table->integer('thoi_luong_hoc')->nullable()->comment('Số giờ học (45 phút/giờ), tối thiểu = so_tin_chi * 15');
            $table->integer('so_buoi_hoc')->nullable()->comment('Số buổi học dự kiến (tối thiểu 10 buổi)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_hoc');
    }
};
