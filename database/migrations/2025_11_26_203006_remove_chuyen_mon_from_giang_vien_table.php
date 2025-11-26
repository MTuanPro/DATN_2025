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
        Schema::table('giang_vien', function (Blueprint $table) {
            $table->dropColumn('chuyen_mon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giang_vien', function (Blueprint $table) {
            $table->string('chuyen_mon')->after('trinh_do_id')->comment('Bắt buộc có chuyên môn');
        });
    }
};
