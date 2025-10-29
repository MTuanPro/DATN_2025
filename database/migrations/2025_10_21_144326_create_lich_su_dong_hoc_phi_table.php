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
        Schema::create('lich_su_dong_hoc_phi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoc_phi_hoc_ky_id')->constrained('hoc_phi_hoc_ky')->onDelete('cascade');
            $table->float('so_tien_dong');
            $table->timestamp('ngay_dong');
            $table->string('phuong_thuc_thanh_toan')->nullable()->comment('Tiền mặt, Chuyển khoản, Ví điện tử, QR Code');
            $table->string('ma_giao_dich')->unique()->nullable();
            $table->string('ngan_hang')->nullable();
            $table->foreignId('nguoi_thu_id')->nullable()->constrained('dao_tao')->onDelete('set null');
            $table->string('bien_lai_file')->nullable()->comment('File biên lai thanh toán');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_dong_hoc_phi');
    }
};
