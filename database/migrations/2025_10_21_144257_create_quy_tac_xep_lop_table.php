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
        Schema::create('quy_tac_xep_lop', function (Blueprint $table) {
            $table->id();
            $table->string('ten_quy_tac');
            $table->integer('thu_tu_uu_tien')->comment('Thứ tự áp dụng quy tắc');
            $table->text('mo_ta')->nullable();
            $table->boolean('kich_hoat')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quy_tac_xep_lop');
    }
};
