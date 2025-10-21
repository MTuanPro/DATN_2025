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
        Schema::create('lop_hoc_phan_giang_vien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->foreignId('giang_vien_id')->constrained('giang_vien')->onDelete('cascade');
            $table->string('vai_tro')->default('giang_vien_chinh')->comment('giang_vien_chinh, giang_vien_phu, tro_giang');
            $table->text('phan_cong_giang_day')->nullable()->comment('Phần nội dung giảng viên này phụ trách');
            $table->date('ngay_phan_cong')->nullable();
            $table->foreignId('nguoi_phan_cong_id')->nullable()->constrained('dao_tao')->onDelete('set null');
            $table->timestamps();

            $table->unique(['lop_hoc_phan_id', 'giang_vien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc_phan_giang_vien');
    }
};
