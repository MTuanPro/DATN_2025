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
        Schema::create('lich_hoc_co_dinh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->integer('thu_trong_tuan')->comment('Thứ 2=2, 3=3,...7=7, CN=8 (giá trị từ 2-8)');
            $table->integer('tiet_bat_dau')->comment('Tiết bắt đầu (1-10)');
            $table->integer('tiet_ket_thuc')->comment('Tiết kết thúc (phải > tiet_bat_dau, tối đa 10)');
            $table->time('gio_bat_dau')->comment('Phải tương ứng với tiet_bat_dau');
            $table->time('gio_ket_thuc')->comment('Phải > gio_bat_dau');
            $table->foreignId('phong_hoc_id')->constrained('phong_hoc')->comment('Bắt buộc có phòng nếu offline');
            $table->foreignId('giang_vien_id')->constrained('giang_vien')->comment('Bắt buộc có giảng viên dạy buổi này');
            $table->enum('hinh_thuc', ['offline', 'online', 'hybrid'])->default('offline');
            $table->string('link_online')->nullable()->comment('Bắt buộc nếu hinh_thuc = online/hybrid');
            $table->text('ghi_chu')->nullable()->comment('Lịch cố định hàng tuần');
            $table->timestamps();

            $table->unique(['lop_hoc_phan_id', 'thu_trong_tuan', 'tiet_bat_dau'], 'unique_lop_thu_tiet');
            $table->unique(['thu_trong_tuan', 'tiet_bat_dau', 'phong_hoc_id'], 'unique_phong_thu_tiet');
            $table->index(['thu_trong_tuan', 'tiet_bat_dau', 'giang_vien_id'], 'idx_gv_trung_lich');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_hoc_co_dinh');
    }
};
