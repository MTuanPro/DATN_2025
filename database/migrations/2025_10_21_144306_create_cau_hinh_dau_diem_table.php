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
        Schema::create('cau_hinh_dau_diem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->string('ten_dau_diem')->comment('Chuyên cần, Giữa kỳ, Cuối kỳ, Thực hành, Tiểu luận');
            $table->float('ty_le')->comment('Tỷ lệ % (0-100, tổng của tất cả đầu điểm = 100)');
            $table->integer('so_cot')->default(1)->comment('Số cột điểm (VD: 3 cột điểm giữa kỳ)');
            $table->timestamps();

            $table->unique(['lop_hoc_phan_id', 'ten_dau_diem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hinh_dau_diem');
    }
};
