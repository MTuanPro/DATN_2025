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
        Schema::create('ket_qua_hoc_tap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->unique()->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade')->comment('Tham chiếu đến bảng lop_hoc_phan_sinh_vien');
            $table->float('diem_he_10')->nullable()->comment('Điểm hệ 10 (0-10)');
            $table->float('diem_he_4')->nullable()->comment('Điểm hệ 4 (0-4)');
            $table->string('diem_chu')->nullable()->comment('A, B+, B, C+, C, D+, D, F');
            $table->boolean('qua_mon')->default(false)->comment('Qua môn nếu diem_he_10 >= 4.0');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_hoc_tap');
    }
};
