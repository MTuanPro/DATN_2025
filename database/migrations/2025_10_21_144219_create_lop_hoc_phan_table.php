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
        Schema::create('lop_hoc_phan', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lop_hp')->unique()->comment('VD: CNTT101.01');
            $table->string('ten_lop_hp')->comment('Tên lớp học phần');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->integer('nhom_lop')->default(1)->comment('Nhóm lớp (nếu có nhiều lớp cùng môn)');
            $table->integer('suc_chua')->default(50)->comment('Số lượng sinh viên tối đa (10-100)');
            $table->integer('so_luong_dang_ky')->default(0)->comment('Số sinh viên đã đăng ký (0 <= so_luong_dang_ky <= suc_chua)');
            $table->integer('so_luong_toi_thieu')->default(10)->comment('Số SV tối thiểu để mở lớp (5-30)');
            $table->enum('hinh_thuc', ['offline', 'online', 'hybrid'])->default('offline');
            $table->string('link_online')->nullable()->comment('Bắt buộc nếu hinh_thuc = online hoặc hybrid');
            $table->date('ngay_bat_dau')->comment('Ngày bắt đầu học - phải trong khoảng học kỳ');
            $table->date('ngay_ket_thuc')->comment('Ngày kết thúc học - phải > ngay_bat_dau');
            $table->text('ghi_chu')->nullable();
            $table->enum('trang_thai_lop', ['mo_dang_ky', 'dang_hoc', 'ket_thuc', 'huy'])->default('mo_dang_ky');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mon_hoc_id', 'hoc_ky_id', 'nhom_lop']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc_phan');
    }
};
