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
        Schema::create('nganh', function (Blueprint $table) {
            $table->id();
            $table->string('ma_nganh')->unique();
            $table->string('ten_nganh');
            $table->foreignId('khoa_id')->nullable()->constrained('khoa')->onDelete('set null');
            $table->text('mo_ta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ten_nganh', 'khoa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nganh');
    }
};
