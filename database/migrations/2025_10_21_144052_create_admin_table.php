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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ma_admin', 20)->unique()->comment('Mã admin');
            $table->string('ho_ten', 100)->comment('Họ và tên');
            $table->date('ngay_sinh')->nullable()->comment('Ngày sinh');
            $table->enum('gioi_tinh', ['Nam', 'Nữ', 'Khác'])->default('Nam')->comment('Giới tính');
            $table->string('email', 100)->unique()->comment('Email');
            $table->string('so_dien_thoai', 15)->nullable()->comment('Số điện thoại');
            $table->text('dia_chi')->nullable()->comment('Địa chỉ');
            $table->string('anh_dai_dien')->nullable()->comment('Ảnh đại diện');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú');
            $table->softDeletes()->comment('Xóa mềm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
