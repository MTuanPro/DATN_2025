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
        Schema::create('ca_hoc', function (Blueprint $table) {
            $table->id();
            $table->string('ten_ca', 50)->comment('Tên ca học (VD: Ca 1, Ca 2...)');
            $table->tinyInteger('thu_tu')->unique()->comment('Thứ tự ca học (1-6)');
            $table->time('gio_bat_dau')->comment('Giờ bắt đầu');
            $table->time('gio_ket_thuc')->comment('Giờ kết thúc');
            $table->boolean('trang_thai')->default(true)->comment('Trạng thái: 1-Hoạt động, 0-Không hoạt động');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú');
            $table->timestamps();
            
            // Indexes
            $table->index('trang_thai');
            $table->index('thu_tu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ca_hoc');
    }
};
