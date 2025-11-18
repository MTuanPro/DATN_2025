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
        Schema::table('bang_diem', function (Blueprint $table) {
            $table->integer('tong_tin_chi_dang_ky')->nullable()->after('hoc_ky_id');
            $table->float('diem_trung_binh_he_10')->nullable()->after('tong_tin_chi_dat');
            $table->float('diem_trung_binh_he_4')->nullable()->after('diem_trung_binh_he_10');
            $table->string('xep_loai_hoc_tap')->nullable()->after('diem_trung_binh_he_4');
            $table->boolean('da_cong_bo')->default(false)->after('xep_loai_hoc_tap');
            $table->timestamp('ngay_cong_bo')->nullable()->after('da_cong_bo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bang_diem', function (Blueprint $table) {
            $table->dropColumn([
                'tong_tin_chi_dang_ky',
                'diem_trung_binh_he_10',
                'diem_trung_binh_he_4',
                'xep_loai_hoc_tap',
                'da_cong_bo',
                'ngay_cong_bo',
            ]);
        });
    }
};
