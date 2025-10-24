<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            // Xóa khóa ngoại cũ
            $table->dropForeign(['khoa_id']);

            // Tạo lại với CASCADE khi xóa
            $table->foreign('khoa_id')
                  ->references('id')
                  ->on('khoa')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            $table->dropForeign(['khoa_id']);
            $table->foreign('khoa_id')
                  ->references('id')
                  ->on('khoa')
                  ->restrictOnDelete();
        });
    }
};
