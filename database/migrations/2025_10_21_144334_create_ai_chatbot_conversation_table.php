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
        Schema::create('ai_chatbot_conversation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->string('session_id')->comment('ID phiên chat');
            $table->string('tieu_de_chat')->nullable()->comment('Tiêu đề tóm tắt cuộc hội thoại');
            $table->timestamp('ngay_bat_dau');
            $table->timestamp('ngay_ket_thuc')->nullable();
            $table->string('trang_thai')->default('dang_mo')->comment('dang_mo, da_dong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chatbot_conversation');
    }
};
