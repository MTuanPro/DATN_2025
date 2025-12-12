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
            $table->foreignId('nhap_diem_id')->nullable()->constrained('nhap_diem')->onDelete('cascade');
            $table->foreignId('lop_hoc_phan_sinh_vien_id')->constrained('lop_hoc_phan_sinh_vien')->onDelete('cascade');
            $table->foreignId('cau_hinh_id')->constrained('cau_hinh_dau_diem')->onDelete('cascade');
            $table->string('cot_diem'); // chuyen_can, giua_ky, cuoi_ky, v.v.
            $table->decimal('diem_cu', 5, 2)->nullable();
            $table->decimal('diem_moi', 5, 2)->nullable();
            $table->foreignId('nguoi_sua_id')->constrained('giang_vien')->onDelete('cascade');
            $table->enum('loai_thao_tac', ['them', 'sua', 'xoa'])->default('sua');
            $table->text('ly_do')->nullable();
            $table->timestamps();
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
