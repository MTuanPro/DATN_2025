<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SinhVien;
use App\Models\HocKy;

class BoDungDuLieuBaoCaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Bổ sung dữ liệu cho báo cáo...\n";

        // 1. Tạo thêm dữ liệu đăng ký môn học
        $this->createDangKyMonHoc();

        // 2. Tạo thêm dữ liệu nhập điểm (bảng điểm)
        $this->createBangDiem();

        echo "Hoàn thành bổ sung dữ liệu!\n";
    }

    /**
     * Tạo dữ liệu đăng ký môn học
     */
    private function createDangKyMonHoc()
    {
        echo "Tạo dữ liệu đăng ký môn học...\n";

        // Kiểm tra xem đã có dữ liệu chưa
        $count = DB::table('dang_ky_mon_hoc')->count();
        if ($count > 0) {
            echo "Đã có {$count} bản ghi đăng ký môn học\n";
            return;
        }

        $sinhViens = SinhVien::with('lopHanhChinh')->limit(100)->get();
        $hocKys = HocKy::all();

        $dangKyData = [];

        foreach ($sinhViens as $sv) {
            foreach ($hocKys->take(2) as $hocKy) {
                // Lấy các lớp học phần sinh viên đã đăng ký
                $lopHocPhans = DB::table('lop_hoc_phan_sinh_vien')
                    ->where('sinh_vien_id', $sv->id)
                    ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
                    ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
                    ->select('lop_hoc_phan.id as lop_hoc_phan_id', 'mon_hoc.id as mon_hoc_id', 'mon_hoc.so_tin_chi')
                    ->limit(5)
                    ->get();

                foreach ($lopHocPhans as $lhp) {
                    $dangKyData[] = [
                        'sinh_vien_id' => $sv->id,
                        'mon_hoc_id' => $lhp->mon_hoc_id,
                        'lop_hoc_phan_id' => $lhp->lop_hoc_phan_id,
                        'hoc_ky_id' => $hocKy->id,
                        'trang_thai' => 'da_duyet',
                        'ngay_dang_ky' => now()->subDays(rand(30, 90)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($dangKyData)) {
            // Xóa dữ liệu cũ
            DB::table('dang_ky_mon_hoc')->truncate();
            
            foreach (array_chunk($dangKyData, 100) as $chunk) {
                DB::table('dang_ky_mon_hoc')->insert($chunk);
            }
            echo "Đã tạo " . count($dangKyData) . " bản ghi đăng ký môn học\n";
        }
    }

    /**
     * Tạo dữ liệu nhập điểm (bảng điểm)
     */
    private function createBangDiem()
    {
        echo "Tạo dữ liệu bảng điểm...\n";

        // Kiểm tra xem đã có dữ liệu chưa
        $count = DB::table('bang_diem')->count();
        if ($count > 0) {
            echo "Đã có {$count} bản ghi bảng điểm\n";
            return;
        }

        // Lấy các lớp học phần sinh viên
        $lopHocPhanSinhViens = DB::table('lop_hoc_phan_sinh_vien')
            ->join('lop_hoc_phan', 'lop_hoc_phan_sinh_vien.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->select(
                'lop_hoc_phan_sinh_vien.id as lhp_sv_id',
                'lop_hoc_phan_sinh_vien.sinh_vien_id',
                'lop_hoc_phan.id as lop_hoc_phan_id',
                'mon_hoc.id as mon_hoc_id'
            )
            ->limit(500)
            ->get();

        $bangDiemData = [];

        foreach ($lopHocPhanSinhViens as $lhpsv) {
            // Random điểm các thành phần
            $diemChuyenCan = round(rand(60, 100) / 10, 1); // 6.0 - 10.0
            $diemGiuaKy = round(rand(30, 100) / 10, 1); // 3.0 - 10.0
            $diemCuoiKy = round(rand(30, 100) / 10, 1); // 3.0 - 10.0
            $diemThucHanh = round(rand(50, 100) / 10, 1); // 5.0 - 10.0

            $bangDiemData[] = [
                'lop_hoc_phan_sinh_vien_id' => $lhpsv->lhp_sv_id,
                'sinh_vien_id' => $lhpsv->sinh_vien_id,
                'mon_hoc_id' => $lhpsv->mon_hoc_id,
                'lop_hoc_phan_id' => $lhpsv->lop_hoc_phan_id,
                'diem_chuyen_can' => $diemChuyenCan,
                'diem_giua_ky' => $diemGiuaKy,
                'diem_cuoi_ky' => $diemCuoiKy,
                'diem_thuc_hanh' => $diemThucHanh,
                'ghi_chu' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($bangDiemData)) {
            // Xóa dữ liệu cũ
            DB::table('bang_diem')->truncate();
            
            foreach (array_chunk($bangDiemData, 100) as $chunk) {
                DB::table('bang_diem')->insert($chunk);
            }
            echo "Đã tạo " . count($bangDiemData) . " bản ghi bảng điểm\n";
        }
    }
}
