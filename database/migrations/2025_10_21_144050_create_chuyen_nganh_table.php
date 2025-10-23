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
        Schema::create('chuyen_nganh', function (Blueprint $table) {
            $table->id();
            $table->string('ma_chuyen_nganh')->unique();
            $table->string('ten_chuyen_nganh');
            $table->foreignId('nganh_id')->nullable()->constrained('nganh')->onDelete('set null');
            $table->integer('tong_tin_chi_toi_thieu')->nullable()->comment('Tổng số tín chỉ tối thiểu để tốt nghiệp');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ten_chuyen_nganh', 'nganh_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuyen_nganh');
    }
};
