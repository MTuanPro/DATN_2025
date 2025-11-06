<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dang_ky_mon_hoc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinh_vien_id');
            $table->unsignedBigInteger('mon_hoc_id');
            $table->unsignedBigInteger('hoc_ky_id');
            $table->timestamp('ngay_dang_ky')->nullable();
            $table->string('trang_thai')->default('cho_xep_lop');
            $table->text('ly_do')->nullable();
            $table->timestamps();

            // foreign keys are optional here; uncomment if the referenced tables/columns exist
            // $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('cascade');
            // $table->foreign('mon_hoc_id')->references('id')->on('mon_hoc')->onDelete('cascade');
            // $table->foreign('hoc_ky_id')->references('id')->on('hoc_ky')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dang_ky_mon_hoc');
    }
};
