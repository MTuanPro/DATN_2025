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
        Schema::create('trang_thai_hoc_tap', function (Blueprint $table) {
            $table->id();
            $table->string('ten_trang_thai')->unique()->comment('Đang học, Bảo lưu, Thôi học, Tốt nghiệp');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trang_thai_hoc_tap');
    }
};
