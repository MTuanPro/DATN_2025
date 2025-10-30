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
        Schema::create('dang_ky_mon_hocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->onDelete('cascade');
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phans')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_kys')->onDelete('cascade');
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi', 'da_huy'])->default('cho_duyet');
            $table->text('ghi_chu')->nullable();
            $table->timestamp('thoi_gian_dang_ky');
            $table->timestamp('thoi_gian_duyet')->nullable();
            $table->foreignId('nguoi_duyet_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Một sinh viên không thể đăng ký cùng một lớp học phần trong cùng một học kỳ
            $table->unique(['sinh_vien_id', 'lop_hoc_phan_id', 'hoc_ky_id'], 'unique_dang_ky');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dang_ky_mon_hocs');
    }
};
