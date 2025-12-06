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
        Schema::create('lich_su_sua_diem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhap_diem_id')->constrained('nhap_diem')->onDelete('cascade')->comment('ID điểm bị sửa');
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade');
            $table->foreignId('cau_hinh_id')->constrained('cau_hinh_dau_diem')->onDelete('cascade');
            $table->integer('cot_diem')->comment('Cột điểm');
            $table->float('diem_cu')->nullable()->comment('Điểm cũ trước khi sửa');
            $table->float('diem_moi')->nullable()->comment('Điểm mới sau khi sửa');
            $table->foreignId('nguoi_sua_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID giảng viên sửa điểm');
            $table->enum('loai_thao_tac', ['them', 'sua', 'xoa'])->default('sua')->comment('Loại thao tác: thêm/sửa/xóa điểm');
            $table->text('ly_do')->nullable()->comment('Lý do sửa điểm');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->index(['lop_hoc_phan_sinh_vien_id', 'cau_hinh_id']);
            $table->index('nguoi_sua_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_sua_diem');
    }
};
