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
        Schema::create('khoa_hoc', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khoa_hoc')->unique()->comment('VD: K17, K18, K2021-2025');
            $table->integer('nam_bat_dau');
            $table->integer('nam_ket_thuc');
            $table->integer('so_nam_dao_tao')->default(4);
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khoa_hoc');
    }
};
