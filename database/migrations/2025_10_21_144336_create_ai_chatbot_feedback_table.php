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
        Schema::create('ai_chatbot_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('ai_chatbot_message')->onDelete('cascade');
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->string('danh_gia')->comment('huu_ich, khong_huu_ich');
            $table->text('ly_do')->nullable()->comment('Lý do đánh giá');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chatbot_feedback');
    }
};
