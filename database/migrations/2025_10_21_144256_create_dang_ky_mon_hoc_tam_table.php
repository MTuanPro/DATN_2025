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
        Schema::create('dang_ky_mon_hoc_tam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->timestamp('ngay_dang_ky');
            $table->integer('uu_tien')->default(0)->comment('Độ ưu tiên (sinh viên năm cuối, học lại có ưu tiên cao hơn)');
            $table->string('trang_thai')->default('cho_xep_lop')->comment('cho_xep_lop, da_xep_lop, that_bai');
            $table->text('ly_do_that_bai')->nullable()->comment('Lý do không xếp được lớp: hết chỗ, thiếu môn tiên quyết, v.v.');
            $table->timestamps();

            $table->unique(['sinh_vien_id', 'mon_hoc_id', 'hoc_ky_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dang_ky_mon_hoc_tam');
    }
};
