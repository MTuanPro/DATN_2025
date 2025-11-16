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
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->foreign('giang_vien_chu_nhiem_id')
                ->references('id')
                ->on('giang_vien')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->dropForeign(['giang_vien_chu_nhiem_id']);
        });
    }
};
