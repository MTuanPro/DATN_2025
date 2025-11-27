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
        Schema::create('yeu_cau_diem_danh_bu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade');
            $table->foreignId('lich_hoc_chi_tiet_id')->constrained('lich_hoc_chi_tiet')->onDelete('cascade');
            $table->text('ly_do')->comment('Lý do xin điểm danh bù');
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->text('ly_do_tu_choi')->nullable()->comment('Lý do từ chối (nếu bị từ chối)');
            $table->timestamp('ngay_gui')->useCurrent()->comment('Ngày sinh viên gửi yêu cầu');
            $table->timestamp('ngay_duyet')->nullable()->comment('Ngày giảng viên duyệt/từ chối');
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('users')->onDelete('set null')->comment('Giảng viên duyệt');
            $table->timestamps();

            $table->index(['lop_hoc_phan_sinh_vien_id', 'lich_hoc_chi_tiet_id'], 'yc_ddb_lhpsv_lhct_idx');
            $table->index('trang_thai', 'yc_ddb_trang_thai_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_diem_danh_bu');
    }
};
