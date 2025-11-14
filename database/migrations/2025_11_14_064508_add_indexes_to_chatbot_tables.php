<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: Thêm indexes để tối ưu performance cho AI Chatbot
     */
    public function up(): void
    {
        // Indexes cho bảng ai_chatbot_conversation
        Schema::table('ai_chatbot_conversation', function (Blueprint $table) {
            // Index cho query: tìm conversation theo sinh viên + trạng thái
            $table->index(['sinh_vien_id', 'trang_thai'], 'idx_conv_sv_status');
            
            // Index cho query: sắp xếp theo ngày bắt đầu
            $table->index('ngay_bat_dau', 'idx_conv_ngay_bat_dau');
        });
        
        // Indexes cho bảng ai_chatbot_message
        Schema::table('ai_chatbot_message', function (Blueprint $table) {
            // Index cho query: lấy messages theo conversation + thời gian
            $table->index(['conversation_id', 'thoi_gian_gui'], 'idx_msg_conv_time');
            
            // Index cho query: lọc theo người gửi
            $table->index('nguoi_gui', 'idx_msg_nguoi_gui');
            
            // Index cho query: tìm theo knowledge base
            $table->index('knowledge_base_id', 'idx_msg_kb_id');
        });
        
        // Indexes cho bảng ai_chatbot_knowledge_base
        Schema::table('ai_chatbot_knowledge_base', function (Blueprint $table) {
            // Index cho query: lọc theo kích hoạt + độ ưu tiên
            $table->index(['kich_hoat', 'do_uu_tien'], 'idx_kb_active_priority');
            
            // Index cho query: tìm theo chủ đề
            $table->index(['chu_de', 'kich_hoat'], 'idx_kb_chu_de_active');
            
            // Index cho query: sắp xếp theo lượt truy cập
            $table->index('luot_truy_cap', 'idx_kb_luot_truy_cap');
        });
        
        // Indexes cho bảng ai_chatbot_feedback
        Schema::table('ai_chatbot_feedback', function (Blueprint $table) {
            // Index cho query: lọc feedback theo đánh giá
            $table->index('danh_gia', 'idx_feedback_danh_gia');
            
            // Index cho query: tìm feedback của sinh viên
            $table->index('sinh_vien_id', 'idx_feedback_sv_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes từ ai_chatbot_conversation
        Schema::table('ai_chatbot_conversation', function (Blueprint $table) {
            $table->dropIndex('idx_conv_sv_status');
            $table->dropIndex('idx_conv_ngay_bat_dau');
        });
        
        // Drop indexes từ ai_chatbot_message
        Schema::table('ai_chatbot_message', function (Blueprint $table) {
            $table->dropIndex('idx_msg_conv_time');
            $table->dropIndex('idx_msg_nguoi_gui');
            $table->dropIndex('idx_msg_kb_id');
        });
        
        // Drop indexes từ ai_chatbot_knowledge_base
        Schema::table('ai_chatbot_knowledge_base', function (Blueprint $table) {
            $table->dropIndex('idx_kb_active_priority');
            $table->dropIndex('idx_kb_chu_de_active');
            $table->dropIndex('idx_kb_luot_truy_cap');
        });
        
        // Drop indexes từ ai_chatbot_feedback
        Schema::table('ai_chatbot_feedback', function (Blueprint $table) {
            $table->dropIndex('idx_feedback_danh_gia');
            $table->dropIndex('idx_feedback_sv_id');
        });
    }
};
