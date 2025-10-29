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
        Schema::create('quyen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nhom_quyen_id')->constrained('nhom_quyen')->onDelete('cascade');
            $table->string('ma_quyen')->unique()->comment('sinh_vien.xem, sinh_vien.them, sinh_vien.sua, sinh_vien.xoa');
            $table->string('ten_quyen');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyen');
    }
};
