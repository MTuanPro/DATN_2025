<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_su_dong_hoc_phi', function (Blueprint $table) {
            $table->json('response_data')->nullable()->after('ghi_chu')->comment('Dữ liệu phản hồi từ cổng thanh toán');
        });
    }

    public function down(): void
    {
        Schema::table('lich_su_dong_hoc_phi', function (Blueprint $table) {
            $table->dropColumn('response_data');
        });
    }
};
