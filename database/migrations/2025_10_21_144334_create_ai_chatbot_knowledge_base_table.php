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
        Schema::create('ai_chatbot_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('chu_de')->comment('Đăng ký môn, Lịch học, Học phí, Quy chế đào tạo, Thủ tục hành chính');
            $table->string('danh_muc')->nullable()->comment('FAQ, Hướng dẫn, Quy định');
            $table->text('cau_hoi_mau');
            $table->text('cau_tra_loi');
            $table->string('tu_khoa')->nullable()->comment('Từ khóa để tìm kiếm');
            $table->integer('do_uu_tien')->default(0)->comment('Độ ưu tiên hiển thị câu trả lời');
            $table->integer('luot_truy_cap')->default(0);
            $table->integer('huu_ich')->default(0)->comment('Số lượt đánh giá hữu ích');
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ngay_cap_nhat')->nullable();
            $table->boolean('kich_hoat')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chatbot_knowledge_base');
    }
};
