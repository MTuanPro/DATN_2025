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
        Schema::create('lich_thi_sinh_vien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lich_thi_id')->constrained('lich_thi')->onDelete('cascade');
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('phong_thi_id')->nullable()->constrained('phong_hoc')->onDelete('set null');
            $table->string('so_bao_danh', 20)->nullable()->comment('Số báo danh');
            $table->enum('trang_thai', ['du_thi', 'vang_co_phep', 'vang_khong_phep'])->default('du_thi');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['lich_thi_id', 'sinh_vien_id']);
            $table->index(['lich_thi_id', 'phong_thi_id']);
            
            // Unique constraint: 1 sinh viên chỉ thi 1 lần cho 1 lịch thi
            $table->unique(['lich_thi_id', 'sinh_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_thi_sinh_vien');
    }
};
