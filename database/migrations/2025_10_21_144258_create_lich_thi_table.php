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
        Schema::create('lich_thi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->string('loai_thi')->comment('Giữa kỳ, Cuối kỳ, Thi lại');
            $table->date('ngay_thi');
            $table->time('gio_bat_dau');
            $table->time('gio_ket_thuc');
            $table->foreignId('phong_thi_id')->nullable()->constrained('phong_hoc')->onDelete('set null');
            $table->foreignId('giam_thi_1_id')->nullable()->constrained('giang_vien')->onDelete('set null');
            $table->foreignId('giam_thi_2_id')->nullable()->constrained('giang_vien')->onDelete('set null');
            $table->integer('so_sinh_vien_du_thi')->nullable();
            $table->enum('hinh_thuc', ['offline', 'online', 'hybrid'])->default('offline');
            $table->string('link_online')->nullable();
            $table->string('de_thi_file')->nullable()->comment('File đề thi');
            $table->string('dap_an_file')->nullable()->comment('File đáp án');
            $table->timestamp('ngay_gui_lich')->nullable();
            $table->foreignId('nguoi_gui_id')->nullable()->constrained('dao_tao')->onDelete('set null');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->unique(['ngay_thi', 'gio_bat_dau', 'phong_thi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_thi');
    }
};
