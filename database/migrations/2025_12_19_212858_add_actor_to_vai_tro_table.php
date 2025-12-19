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
        Schema::table('vai_tro', function (Blueprint $table) {
            // Thêm cột actor để xác định vai trò thuộc nhóm actor nào
            // Actor: admin, dao_tao, giang_vien, sinh_vien
            $table->string('actor')->nullable()->after('muc_do_uu_tien')
                ->comment('Actor: admin, dao_tao, giang_vien, sinh_vien');
        });

        // Cập nhật actor cho các vai trò mặc định
        \DB::table('vai_tro')->where('ma_vai_tro', 'admin')->update(['actor' => 'admin']);
        \DB::table('vai_tro')->where('ma_vai_tro', 'truong_phong_dt')->update(['actor' => 'dao_tao']);
        \DB::table('vai_tro')->where('ma_vai_tro', 'nhan_vien_dt')->update(['actor' => 'dao_tao']);
        \DB::table('vai_tro')->where('ma_vai_tro', 'giang_vien')->update(['actor' => 'giang_vien']);
        \DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->update(['actor' => 'sinh_vien']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vai_tro', function (Blueprint $table) {
            $table->dropColumn('actor');
        });
    }
};
