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
        Schema::create('nhap_diem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade')->comment('Tham chiếu đến bảng lop_hoc_phan_sinh_vien');
            $table->foreignId('cau_hinh_id')->constrained('cau_hinh_dau_diem')->onDelete('cascade');
            $table->integer('cot_diem')->default(1)->comment('Cột điểm thứ mấy (nếu có nhiều cột)');
            $table->float('diem_so')->nullable()->comment('Điểm số (0-10)');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->unique(['lop_hoc_phan_sinh_vien_id', 'cau_hinh_id', 'cot_diem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhap_diem');
    }
};
