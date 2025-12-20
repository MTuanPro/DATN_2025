<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Bảng liên kết quyền với các actor (nhóm người dùng)
     * Mỗi quyền có thể áp dụng cho một hoặc nhiều actor
     * VD: quyền "quản lý thông báo" có thể gán cho cả admin và dao_tao
     * VD: quyền "nhập điểm" chỉ gán cho giang_vien
     */
    public function up(): void
    {
        Schema::create('quyen_actor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quyen_id')->constrained('quyen')->onDelete('cascade');
            $table->string('actor', 50)->comment('admin, dao_tao, giang_vien, sinh_vien');
            $table->timestamps();

            // Đảm bảo không trùng lặp cặp quyen_id + actor
            $table->unique(['quyen_id', 'actor']);

            // Index cho tìm kiếm nhanh
            $table->index('actor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyen_actor');
    }
};
