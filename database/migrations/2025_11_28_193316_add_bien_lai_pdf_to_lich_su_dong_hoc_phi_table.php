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
        Schema::table('lich_su_dong_hoc_phi', function (Blueprint $table) {
            $table->string('bien_lai_pdf')->nullable()->after('bien_lai_file')->comment('Đường dẫn file PDF biên lai tự động tạo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_su_dong_hoc_phi', function (Blueprint $table) {
            $table->dropColumn('bien_lai_pdf');
        });
    }
};
