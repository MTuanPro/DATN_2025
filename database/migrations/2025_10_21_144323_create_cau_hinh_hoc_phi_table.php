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
        Schema::create('cau_hinh_hoc_phi', function (Blueprint $table) {
            $table->id();
            $table->string('nam_hoc')->comment('2024-2025');
            $table->float('don_gia_tren_tin_chi')->comment('Giá tiền/1 tín chỉ');
            $table->float('phi_dich_vu')->default(0)->comment('Phí dịch vụ, bảo hiểm');
            $table->date('ap_dung_tu_ngay');
            $table->date('ap_dung_den_ngay')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_hoc_phi');
    }
};
