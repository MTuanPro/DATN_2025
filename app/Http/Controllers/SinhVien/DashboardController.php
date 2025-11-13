<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\SinhVien;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocPhiHocKy;
use App\Models\CanhBaoHocVu;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    public function index()
    {
        $user = Auth::user();
        $sinhVien = $user->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('profile.show')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Load relationships
        $sinhVien->load([
            'lopHanhChinh',
            'khoaHoc',
            'nganh',
            'chuyenNganh',
            'trangThaiHocTap'
        ]);

        // Tính tổng tín chỉ đã đạt
        $totalCredits = $this->diemService->tinhTongTinChiDat($sinhVien->id);

        // Tính GPA tích lũy
        $gpa = $this->diemService->tinhGPATichLuy($sinhVien->id);
        $gpaFormatted = number_format($gpa, 2, '.', '');

        // Đếm số lớp học phần đang học (trạng thái dang_hoc)
        $currentClasses = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->where('trang_thai', 'dang_hoc')
            ->count();

        // Tính công nợ học phí (tổng số tiền còn lại)
        $debt = HocPhiHocKy::where('sinh_vien_id', $sinhVien->id)
            ->sum('so_tien_con_lai');

        // Lấy cảnh báo học vụ gần đây (5 cảnh báo mới nhất)
        $warnings = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
            ->with(['hocKy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = [
            'totalCredits' => $totalCredits,
            'gpa' => $gpaFormatted,
            'currentClasses' => $currentClasses,
            'debt' => $debt,
            'studentCode' => $sinhVien->ma_sinh_vien,
            'className' => $sinhVien->lopHanhChinh ? $sinhVien->lopHanhChinh->ten_lop : null,
            'course' => $sinhVien->khoaHoc ? $sinhVien->khoaHoc->ten_khoa_hoc : null,
            'warnings' => $warnings,
            'sinhVien' => $sinhVien,
        ];

        return view('sinhvien.dashboard', $data);
    }
}
