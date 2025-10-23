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
        Schema::create('mon_hoc_tien_quyet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade')->comment('Môn học chính');
            $table->foreignId('mon_tien_quyet_id')->constrained('mon_hoc')->onDelete('cascade')->comment('Môn học phải học trước');
            $table->string('loai_tien_quyet')->default('bat_buoc')->comment('bat_buoc, khuyen_nghi');
            $table->boolean('dieu_kien_qua_mon')->default(true)->comment('Phải qua môn tiên quyết mới được đăng ký');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mon_hoc_id', 'mon_tien_quyet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_hoc_tien_quyet');
    }
};
