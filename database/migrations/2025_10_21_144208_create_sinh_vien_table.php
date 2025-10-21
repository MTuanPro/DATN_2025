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
        Schema::create('sinh_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ma_sinh_vien')->unique();
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->date('ngay_sinh')->comment('Bắt buộc có ngày sinh để kiểm tra tuổi nhập học');
            $table->enum('gioi_tinh', ['nam', 'nu', 'khac'])->comment('Bắt buộc để thống kê');
            $table->string('so_dien_thoai')->comment('Bắt buộc để liên hệ');
            $table->string('so_nha_duong')->nullable();
            $table->string('phuong_xa')->nullable();
            $table->string('quan_huyen')->nullable();
            $table->string('tinh_thanh')->nullable();
            $table->string('can_cuoc_cong_dan')->unique()->comment('Bắt buộc có CCCD');
            $table->date('ngay_cap_cccd')->nullable();
            $table->string('noi_cap_cccd')->nullable();
            $table->string('anh_dai_dien')->nullable();
            $table->foreignId('khoa_hoc_id')->constrained('khoa_hoc')->comment('Bắt buộc thuộc khóa học');
            $table->foreignId('lop_hanh_chinh_id')->constrained('lop_hanh_chinh')->comment('Bắt buộc có lớp hành chính');
            $table->foreignId('nganh_id')->constrained('nganh')->comment('Bắt buộc có ngành học');
            $table->foreignId('chuyen_nganh_id')->nullable()->constrained('chuyen_nganh')->onDelete('set null')->comment('Chuyên ngành có thể chọn sau (năm 3)');
            $table->integer('ky_hien_tai')->default(1)->comment('Kỳ hiện tại của sinh viên (1-8)');
            $table->foreignId('trang_thai_hoc_tap_id')->constrained('trang_thai_hoc_tap')->comment('Bắt buộc có trạng thái');
            $table->unsignedBigInteger('giang_vien_chu_nhiem_id')->nullable()->comment('Giảng viên chủ nhiệm lớp');
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade')->comment('Mỗi SV có 1 tài khoản duy nhất');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_vien');
    }
};
