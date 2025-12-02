<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhanCongGiangDay;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\LopHocPhan;
use App\Models\CauHinhDauDiem;
use App\Models\NhapDiem;
use App\Models\NguoiNhanThongBao;
use App\Models\GiangVien;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $giangVien = $user->giangVien;

        if (!$giangVien) {
            return redirect()->route('profile.show')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // 1. Tổng số lớp giảng dạy
        $totalClasses = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->whereHas('lopHocPhan', function($q) {
                $q->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc']);
            })
            ->count();

        // 2. Tổng số sinh viên trong các lớp giảng dạy
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();
        
        $totalStudents = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('trang_thai', 'dang_hoc')
            ->distinct('sinh_vien_id')
            ->count('sinh_vien_id');

        // 3. Số buổi học tuần này
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weekSessions = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek])
            ->whereIn('trang_thai', ['chua_day', 'dang_day'])
            ->count();

        // 4. Số lớp cần nhập điểm (có cấu hình đầu điểm nhưng chưa nhập đủ)
        $pendingGrades = 0;
        $lopHocPhansCanNhapDiem = LopHocPhan::whereIn('id', $lopHocPhanIds)
            ->whereHas('cauHinhDauDiem')
            ->get();
        
        foreach ($lopHocPhansCanNhapDiem as $lhp) {
            $cauHinhDiem = $lhp->cauHinhDauDiem;
            $soSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lhp->id)
                ->where('trang_thai', 'dang_hoc')
                ->count();
            
            if ($soSinhVien > 0) {
                // Kiểm tra xem đã nhập đủ điểm chưa (sử dụng relationship nhapDiems)
                $soSinhVienDaNhapDiem = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lhp->id)
                    ->where('trang_thai', 'dang_hoc')
                    ->whereHas('nhapDiems')
                    ->distinct('sinh_vien_id')
                    ->count('sinh_vien_id');
                
                if ($soSinhVienDaNhapDiem < $soSinhVien) {
                    $pendingGrades++;
                }
            }
        }

        // 5. Lớp chủ nhiệm (đã xóa chức năng này)
        $homeRoomClassName = null;

        // 6. Lịch dạy tuần này - Lấy tất cả lịch dạy của tất cả giảng viên
        // Lấy danh sách giảng viên cho filter
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        $query = LichHocChiTiet::whereBetween('ngay_hoc', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('trang_thai', '!=', 'huy')
            ->with([
                'lopHocPhan.monHoc',
                'phongHoc',
                'giangVien',
                'caHoc',
                'lichHocCoDinh'
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

        // 7. Điểm danh gần đây (5 buổi gần nhất)
        $diemDanhGanDay = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('trang_thai', 'da_day')
            ->whereHas('diemDanh')
            ->with(['lopHocPhan.monHoc', 'diemDanh'])
            ->orderBy('ngay_hoc', 'desc')
            ->limit(5)
            ->get();

        // 8. Thông báo mới (chưa đọc)
        $thongBaoMoi = NguoiNhanThongBao::where('nguoi_nhan_id', $user->id)
            ->where('da_doc', false)
            ->with('thongBao')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = [
            'totalClasses' => $totalClasses,
            'totalStudents' => $totalStudents,
            'weekSessions' => $weekSessions,
            'pendingGrades' => $pendingGrades,
            'homeRoomClass' => $homeRoomClassName,
            'lichDayTuanNay' => $lichDayTuanNay,
            'diemDanhGanDay' => $diemDanhGanDay,
            'thongBaoMoi' => $thongBaoMoi,
            'giangViens' => $giangViens,
        ];

        return view('giangvien.dashboard', $data);
    }
}
