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
        Schema::create('phong_hoc', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong')->unique();
            $table->string('ten_phong');
            $table->integer('suc_chua')->nullable();
            $table->string('vi_tri')->nullable();
            $table->string('loai_phong')->nullable()->comment('Lý thuyết, Thực hành, Hội trường, Phòng máy');
            $table->string('trang_thai')->default('Hoạt động')->comment('Hoạt động, Bảo trì, Không sử dụng');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong_hoc');
    }
};
