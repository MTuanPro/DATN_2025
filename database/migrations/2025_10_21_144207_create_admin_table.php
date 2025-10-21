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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('so_dien_thoai')->nullable();
            $table->string('anh_dai_dien')->nullable()->comment('Ảnh đại diện của admin');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->comment('Foreign key tới bảng users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
