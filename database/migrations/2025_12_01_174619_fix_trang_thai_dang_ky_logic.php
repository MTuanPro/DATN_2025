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
        // Fix logic: 
        // - Nếu sinh viên có trạng thái "da_xep_lop" nhưng CHƯA có trong bảng lop_hoc_phan_sinh_vien
        //   => Chuyển về "cho_xep_lop" (vì chưa thực sự được xếp lớp)
        
        $dangKysDaXepLop = DB::table('dang_ky_mon_hoc_tam')
            ->where('trang_thai', 'da_xep_lop')
            ->get();

        foreach ($dangKysDaXepLop as $dangKy) {
            // Kiểm tra xem có thực sự được xếp vào lớp chưa
            $daXepLop = DB::table('lop_hoc_phan_sinh_vien')
                ->where('dang_ky_tam_id', $dangKy->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->exists();

            if (!$daXepLop) {
                // Chưa được xếp lớp thực sự => Chuyển về "cho_xep_lop"
                DB::table('dang_ky_mon_hoc_tam')
                    ->where('id', $dangKy->id)
                    ->update([
                        'trang_thai' => 'cho_xep_lop',
                        'ly_do_that_bai' => null // Xóa lý do cũ
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần rollback
    }
};
