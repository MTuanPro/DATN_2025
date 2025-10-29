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
        Schema::create('ai_chatbot_message', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_chatbot_conversation')->onDelete('cascade');
            $table->string('nguoi_gui')->comment('user, bot');
            $table->text('noi_dung');
            $table->foreignId('knowledge_base_id')->nullable()->constrained('ai_chatbot_knowledge_base')->onDelete('set null')->comment('Câu trả lời từ KB nào');
            $table->float('do_tuong_dong')->nullable()->comment('Độ tương đồng câu hỏi (0-1)');
            $table->timestamp('thoi_gian_gui');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chatbot_message');
    }
};
