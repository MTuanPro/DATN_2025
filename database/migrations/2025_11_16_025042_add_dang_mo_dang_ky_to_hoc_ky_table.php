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
        Schema::table('hoc_ky', function (Blueprint $table) {
            $table->boolean('dang_mo_dang_ky')->default(false)->after('la_hoc_ky_hien_tai')->comment('Trạng thái mở/đóng đăng ký môn học');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoc_ky', function (Blueprint $table) {
            $table->dropColumn('dang_mo_dang_ky');
        });
    }
};
