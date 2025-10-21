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
        Schema::create('giang_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ma_giang_vien')->unique();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('so_dien_thoai')->comment('Bắt buộc để liên hệ');
            $table->foreignId('trinh_do_id')->constrained('dm_trinh_do')->comment('Bắt buộc có trình độ');
            $table->string('chuyen_mon')->comment('Bắt buộc có chuyên môn');
            $table->foreignId('khoa_id')->constrained('khoa')->comment('Bắt buộc thuộc khoa');
            $table->date('ngay_vao_truong')->comment('Ngày vào làm việc');
            $table->string('anh_dai_dien')->nullable();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->comment('Mỗi GV có 1 tài khoản duy nhất');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giang_vien');
    }
};
