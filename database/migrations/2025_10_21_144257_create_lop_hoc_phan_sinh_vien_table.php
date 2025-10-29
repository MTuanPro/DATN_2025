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
        Schema::create('lop_hoc_phan_sinh_vien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('dang_ky_tam_id')->nullable()->constrained('dang_ky_mon_hoc_tam')->onDelete('set null')->comment('ID đăng ký tạm ban đầu');
            $table->timestamp('ngay_dang_ky');
            $table->timestamp('ngay_xep_lop')->nullable()->comment('Thời gian hệ thống xếp lớp tự động');
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('dao_tao')->onDelete('set null')->comment('Người duyệt (nếu cần duyệt thủ công)');
            $table->timestamp('ngay_duyet')->nullable();
            $table->string('phuong_thuc_xep')->default('tu_dong')->comment('tu_dong, thu_cong');
            $table->string('trang_thai')->default('da_xep_lop')->comment('da_xep_lop, dang_hoc, da_hoan_thanh, bo_hoc, huy_dang_ky');
            $table->text('ly_do_huy')->nullable()->comment('Lý do hủy/bỏ học nếu có');
            $table->timestamps();

            $table->unique(['lop_hoc_phan_id', 'sinh_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc_phan_sinh_vien');
    }
};
