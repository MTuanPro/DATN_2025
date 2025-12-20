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
        Schema::create('cau_hinh_he_thong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_cau_hinh')->unique()->comment('Mã cấu hình (ví dụ: cho_phep_diem_danh_tuong_lai)');
            $table->string('ten_cau_hinh')->comment('Tên cấu hình');
            $table->text('gia_tri')->nullable()->comment('Giá trị cấu hình (JSON hoặc text)');
            $table->boolean('trang_thai')->default(true)->comment('Trạng thái: true = bật, false = tắt');
            $table->text('mo_ta')->nullable()->comment('Mô tả cấu hình');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_he_thong');
    }
};
