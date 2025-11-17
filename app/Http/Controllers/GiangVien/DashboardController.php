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
use App\Models\DaoTao\LopHanhChinh;
use App\Models\NguoiNhanThongBao;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
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

        // 5. Lớp chủ nhiệm
        $homeRoomClass = LopHanhChinh::where('giang_vien_chu_nhiem_id', $giangVien->id)
            ->with(['khoaHoc', 'nganh'])
            ->first();
        
        $homeRoomClassName = $homeRoomClass 
            ? $homeRoomClass->ten_lop . ' (' . $homeRoomClass->khoaHoc->ten_khoa_hoc . ')' 
            : null;

        // 6. Lịch dạy tuần này
        $lichDayTuanNay = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek])
            ->with(['lopHocPhan.monHoc', 'phongHoc', 'lichHocCoDinh'])
            ->orderBy('ngay_hoc', 'asc')
            ->orderBy('tiet_bat_dau', 'asc')
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
        ];

        return view('giangvien.dashboard', $data);
    }
}
