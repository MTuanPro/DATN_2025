<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use App\Models\LopHocPhan;
use App\Models\DaoTao\MonHoc;
use App\Models\CanhBaoHocVu;
use App\Models\LichHocChiTiet;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Tổng số sinh viên
        $totalStudents = SinhVien::count();

        // Tổng số giảng viên
        $totalTeachers = GiangVien::count();

        // Tổng số lớp học phần
        $totalClasses = LopHocPhan::count();

        // Tổng số môn học
        $totalSubjects = MonHoc::count();

        // Cảnh báo học vụ
        $warningsAcademic = CanhBaoHocVu::where('loai_canh_bao', 'hoc_vu')
            ->where('trang_thai', 'chua_xu_ly')
            ->count();

        // Cảnh báo học phí
        $warningsTuition = CanhBaoHocVu::where('loai_canh_bao', 'hoc_phi')
            ->where('trang_thai', 'chua_xu_ly')
            ->count();

        // Lấy danh sách giảng viên cho filter
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        // Lịch dạy tuần này - Lấy tất cả lịch học chi tiết của tất cả giảng viên trong tuần
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $query = LichHocChiTiet::whereBetween('ngay_hoc', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('trang_thai', '!=', 'huy')
            ->with([
                'lopHocPhan.monHoc',
                'phongHoc',
                'giangVien',
                'caHoc'
            ]);

        // Lọc theo giảng viên nếu có
        if ($request->has('giang_vien_id') && $request->giang_vien_id) {
            $query->where('giang_vien_id', $request->giang_vien_id);
        }

        // Lọc theo tên hoặc mã giảng viên nếu có
        if ($request->has('tim_kiem_giang_vien') && $request->tim_kiem_giang_vien) {
            $searchTerm = $request->tim_kiem_giang_vien;
            $query->whereHas('giangVien', function($q) use ($searchTerm) {
                $q->where('ho_ten', 'like', '%' . $searchTerm . '%')
                  ->orWhere('ma_giang_vien', 'like', '%' . $searchTerm . '%');
            });
        }

        $lichDayTuanNay = $query->orderBy('ngay_hoc', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->get();

        $data = [
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'warningsAcademic' => $warningsAcademic,
            'warningsTuition' => $warningsTuition,
            'lichDayTuanNay' => $lichDayTuanNay,
            'giangViens' => $giangViens,
        ];

        return view('daotao.dashboard', $data);
    }
}
