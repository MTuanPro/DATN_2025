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
        // Xóa foreign key và cột giang_vien_chu_nhiem_id từ bảng sinh_vien
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->dropForeign(['giang_vien_chu_nhiem_id']);
            $table->dropColumn('giang_vien_chu_nhiem_id');
        });

        // Xóa foreign key và cột lop_hanh_chinh_id từ bảng sinh_vien
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->dropForeign(['lop_hanh_chinh_id']);
            $table->dropColumn('lop_hanh_chinh_id');
        });

        // Xóa bảng lop_hanh_chinh
        Schema::dropIfExists('lop_hanh_chinh');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tạo lại bảng lop_hanh_chinh
        Schema::create('lop_hanh_chinh', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lop')->unique();
            $table->string('ten_lop');
            $table->foreignId('khoa_hoc_id')->nullable()->constrained('khoa_hoc')->onDelete('set null');
            $table->foreignId('nganh_id')->nullable()->constrained('nganh')->onDelete('set null');
            $table->unsignedBigInteger('giang_vien_chu_nhiem_id')->nullable();
            $table->integer('si_so')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Thêm lại cột lop_hanh_chinh_id vào bảng sinh_vien
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->foreignId('lop_hanh_chinh_id')->nullable()->constrained('lop_hanh_chinh')->onDelete('set null');
        });

        // Thêm lại cột giang_vien_chu_nhiem_id vào bảng sinh_vien
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->unsignedBigInteger('giang_vien_chu_nhiem_id')->nullable();
            $table->foreign('giang_vien_chu_nhiem_id')
                ->references('id')
                ->on('giang_vien')
                ->onDelete('set null');
        });
    }
};
