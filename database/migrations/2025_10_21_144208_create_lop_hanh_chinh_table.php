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
        Schema::create('lop_hanh_chinh', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lop')->unique();
            $table->string('ten_lop');
            $table->foreignId('khoa_hoc_id')->nullable()->constrained('khoa_hoc')->onDelete('set null');
            $table->foreignId('nganh_id')->nullable()->constrained('nganh')->onDelete('set null');
            $table->unsignedBigInteger('giang_vien_chu_nhiem_id')->nullable();
            $table->integer('si_so')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hanh_chinh');
    }
};
