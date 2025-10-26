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
        Schema::create('chuong_trinh_khung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuyen_nganh_id')->constrained('chuyen_nganh')->onDelete('cascade');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->integer('hoc_ky_goi_y')->comment('Học kỳ gợi ý (1-8)');
            $table->enum('loai_mon_hoc', ['dai_cuong', 'co_so_nganh', 'chuyen_nganh_bat_buoc', 'chuyen_nganh_tu_chon', 'thuc_tap', 'do_an_tot_nghiep']);
            $table->boolean('bat_buoc')->default(true)->comment('true: bắt buộc, false: tự chọn');
            $table->integer('so_tin_chi_toi_thieu')->nullable()->comment('Số tín chỉ tối thiểu nếu là nhóm tự chọn');
            $table->integer('thu_tu_hoc')->nullable()->comment('Thứ tự ưu tiên học trong học kỳ');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['chuyen_nganh_id', 'mon_hoc_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuong_trinh_khung');
    }
};
