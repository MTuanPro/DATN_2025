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
        Schema::create('nhat_ky_hoat_dong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Người thực hiện hành động');
            $table->string('hanh_dong')->comment('CREATE, UPDATE, DELETE, LOGIN, LOGOUT, EXPORT, IMPORT');
            $table->string('bang_du_lieu')->comment('Tên bảng bị tác động');
            $table->unsignedBigInteger('ban_ghi_id')->nullable()->comment('ID của bản ghi bị tác động');
            $table->text('du_lieu_cu')->nullable()->comment('JSON - Dữ liệu trước khi thay đổi');
            $table->text('du_lieu_moi')->nullable()->comment('JSON - Dữ liệu sau khi thay đổi');
            $table->string('ip_address')->nullable()->comment('IP của người thực hiện');
            $table->text('user_agent')->nullable()->comment('Thông tin trình duyệt');
            $table->text('mo_ta')->nullable()->comment('Mô tả chi tiết hành động');
            $table->timestamp('thoi_gian');
            $table->timestamps();

            $table->index(['user_id', 'thoi_gian'], 'idx_user_time');
            $table->index(['bang_du_lieu', 'ban_ghi_id'], 'idx_table_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhat_ky_hoat_dong');
    }
};
