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
        Schema::create('nguoi_nhan_thong_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thong_bao_id')->constrained('thong_bao')->onDelete('cascade');
            $table->foreignId('nguoi_nhan_id')->constrained('users')->onDelete('cascade');
            $table->boolean('da_doc')->default(false);
            $table->timestamp('ngay_doc')->nullable();
            $table->boolean('da_gui_email')->default(false);
            $table->timestamp('ngay_gui_email')->nullable();
            $table->boolean('da_gui_sms')->default(false);
            $table->timestamp('ngay_gui_sms')->nullable();
            $table->timestamps();

            $table->unique(['thong_bao_id', 'nguoi_nhan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_nhan_thong_bao');
    }
};
