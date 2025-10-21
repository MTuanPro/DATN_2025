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
        Schema::create('bang_diem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->foreignId('hoc_ky_id')->constrained('hoc_ky')->onDelete('cascade');
            $table->float('diem_trung_binh_hoc_ky')->nullable();
            $table->float('diem_trung_binh_tich_luy')->nullable();
            $table->integer('tong_tin_chi_dat')->nullable();
            $table->timestamps();

            $table->unique(['sinh_vien_id', 'hoc_ky_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bang_diem');
    }
};
