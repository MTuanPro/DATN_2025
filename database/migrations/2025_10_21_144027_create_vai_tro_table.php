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
        Schema::create('vai_tro', function (Blueprint $table) {
            $table->id();
            $table->string('ma_vai_tro')->unique()->comment('admin, truong_phong_dt, nhan_vien_dt, giang_vien, sinh_vien');
            $table->string('ten_vai_tro')->unique();
            $table->text('mo_ta')->nullable();
            $table->integer('muc_do_uu_tien')->nullable()->comment('Mức độ ưu tiên: 1=cao nhất (admin), 5=thấp nhất');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vai_tro');
    }
};
