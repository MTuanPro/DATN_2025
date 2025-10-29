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
        Schema::create('hoc_phi_hoc_ky', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->integer('tong_tin_chi_dang_ky')->default(0)->comment('Tổng số tín chỉ đã đăng ký (tối đa 24 tín chỉ/kỳ)');
            $table->float('tong_hoc_phi_mon_hoc')->default(0)->comment('Tổng tiền học phí các môn (>= 0)');
            $table->float('phi_dich_vu')->default(0)->comment('Phí dịch vụ, bảo hiểm (>= 0)');
            $table->float('tong_so_tien')->default(0)->comment('Tổng = hoc_phi_mon + phi_dv');
            $table->float('so_tien_da_dong')->default(0)->comment('Số tiền đã đóng (0 <= so_tien_da_dong <= tong_so_tien)');
            $table->float('so_tien_con_lai')->default(0)->comment('Số tiền còn lại = tong_so_tien - so_tien_da_dong (>= 0)');
            $table->date('han_dong')->comment('Hạn đóng học phí - bắt buộc');
            $table->timestamp('ngay_dong_lan_cuoi')->nullable();
            $table->enum('trang_thai', ['chua_nop', 'da_nop_mot_phan', 'da_nop_du', 'qua_han'])->default('chua_nop');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->unique(['sinh_vien_id', 'hoc_ky_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_phi_hoc_ky');
    }
};
