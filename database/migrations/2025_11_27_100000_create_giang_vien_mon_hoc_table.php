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
        Schema::create('giang_vien_mon_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giang_vien_id')->constrained('giang_vien')->onDelete('cascade');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade');
            $table->timestamps();

            // Đảm bảo một giảng viên không bị trùng môn học
            $table->unique(['giang_vien_id', 'mon_hoc_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giang_vien_mon_hoc');
    }
};
