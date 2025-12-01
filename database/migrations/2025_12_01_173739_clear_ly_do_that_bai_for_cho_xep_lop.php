<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Xóa lý do thất bại cho các đăng ký có trạng thái chờ xếp lớp
        // Lý do: Sinh viên đã đóng học phí và đang chờ xếp lớp, không nên hiển thị lý do thất bại
        DB::table('dang_ky_mon_hoc_tam')
            ->where('trang_thai', 'cho_xep_lop')
            ->whereNotNull('ly_do_that_bai')
            ->update(['ly_do_that_bai' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback vì đây là data cleanup
    }
};
