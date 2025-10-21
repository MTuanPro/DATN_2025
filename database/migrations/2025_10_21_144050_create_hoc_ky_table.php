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
        Schema::create('hoc_ky', function (Blueprint $table) {
            $table->id();
            $table->string('ten_hoc_ky')->comment('HK1, HK2, HK3 (Hè)');
            $table->string('nam_hoc')->comment('2024-2025');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->date('ngay_bat_dau_dang_ky')->nullable();
            $table->date('ngay_ket_thuc_dang_ky')->nullable();
            $table->boolean('la_hoc_ky_hien_tai')->default(false);
            $table->text('mo_ta')->nullable();
            $table->timestamps();

            $table->unique(['ten_hoc_ky', 'nam_hoc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_ky');
    }
};
