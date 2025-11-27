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
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->integer('lan_gui_diem')->nullable()->after('trang_thai_lop')->comment('Lần gửi điểm hiện tại: 1 (giữa kỳ), 2 (cuối kỳ)');
            $table->string('trang_thai_gui_diem_lan_1')->nullable()->after('lan_gui_diem')->comment('Trạng thái gửi điểm lần 1: chua_gui, da_gui, da_duyet, da_tra_ve');
            $table->string('trang_thai_gui_diem_lan_2')->nullable()->after('trang_thai_gui_diem_lan_1')->comment('Trạng thái gửi điểm lần 2: chua_gui, da_gui, da_duyet, da_tra_ve');
            $table->boolean('cho_phep_gui_diem_lan_1')->default(false)->after('trang_thai_gui_diem_lan_2')->comment('Đào tạo mở/đóng gửi điểm lần 1');
            $table->boolean('cho_phep_gui_diem_lan_2')->default(false)->after('cho_phep_gui_diem_lan_1')->comment('Đào tạo mở/đóng gửi điểm lần 2');
            $table->boolean('cho_phep_sua_diem_sau_duyet')->default(false)->after('cho_phep_gui_diem_lan_2')->comment('Cho phép đào tạo sửa điểm sau khi duyệt (phúc khảo)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hoc_phan', function (Blueprint $table) {
            $table->dropColumn([
                'lan_gui_diem',
                'trang_thai_gui_diem_lan_1',
                'trang_thai_gui_diem_lan_2',
                'cho_phep_gui_diem_lan_1',
                'cho_phep_gui_diem_lan_2',
                'cho_phep_sua_diem_sau_duyet',
            ]);
        });
    }
};
