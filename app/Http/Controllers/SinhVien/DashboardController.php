<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\DaoTao\SinhVien;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocPhiHocKy;
use App\Models\CanhBaoHocVu;
use App\Models\NguoiNhanThongBao;
use App\Models\LichHocChiTiet;
use App\Models\KetQuaHocTap;
use App\Models\LichThi;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        // Lấy tất cả thông báo mới nhất để hiển thị trong modal (từ bất kỳ actor nào gửi)
        // Sắp xếp theo ngày giờ mới nhất, ưu tiên thông báo chưa đọc
        $userId = Auth::id();
        $now = now();
        $thongBaoMoiNhat = NguoiNhanThongBao::with(['thongBao.nguoiGui'])
            ->where('nguoi_nhan_id', $userId)
            ->whereHas('thongBao', function ($q) use ($now) {
                $q->where('trang_thai', 'cong_khai')
                    ->where(function ($subQ) use ($now) {
                        $subQ->whereNull('hien_thi_tu_ngay')
                            ->orWhere('hien_thi_tu_ngay', '<=', $now);
                    })
                    ->where(function ($subQ) use ($now) {
                        $subQ->whereNull('ngay_het_han')
                            ->orWhere('ngay_het_han', '>=', $now);
                    });
            })
            ->get()
            ->filter(function ($item) {
                return $item->thongBao !== null;
            })
            ->sort(function ($a, $b) {
                // Ưu tiên thông báo chưa đọc trước
                if ($a->da_doc !== $b->da_doc) {
                    return $a->da_doc ? 1 : -1; // Chưa đọc trước
                }
                // Nếu cùng trạng thái đọc, sắp xếp theo ngày giờ mới nhất
                $aTime = $a->thongBao ? $a->thongBao->ngay_gui->timestamp : 0;
                $bTime = $b->thongBao ? $b->thongBao->ngay_gui->timestamp : 0;
                return $bTime - $aTime; // Mới nhất trước
            })
            ->take(10) // Giới hạn 10 thông báo mới nhất
            ->values();
        
        // Log để debug
        Log::info('Dashboard - Thông báo mới nhất', [
            'user_id' => $userId,
            'count' => $thongBaoMoiNhat->count(),
            'thong_bao_ids' => $thongBaoMoiNhat->pluck('thong_bao_id')->toArray()
        ]);

        // Lấy thời khóa biểu tuần này
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        
        $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->pluck('lop_hoc_phan_id');
        
        $weeklyTimetable = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->where('trang_thai', '!=', 'huy')
            ->with([
                'lopHocPhan.monHoc',
                'phongHoc',
                'giangVien',
                'caHoc'
            ])
            ->orderBy('ngay_hoc', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->get();

        // Lấy điểm gần đây (5 điểm mới nhất)
        $recentGrades = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function($q) use ($sinhVien) {
                $q->where('sinh_vien_id', $sinhVien->id);
            })
            ->with([
                'lopHocPhanSinhVien.lopHocPhan.monHoc'
            ])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy lịch thi sắp tới (5 lịch thi gần nhất)
        $upcomingExams = LichThi::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('ngay_thi', '>=', now()->toDateString())
            ->with([
                'lopHocPhan.monHoc',
                'phongHoc'
            ])
            ->orderBy('ngay_thi', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->limit(5)
            ->get();

        $data = [
            'totalCredits' => $totalCredits,
            'gpa' => $gpaFormatted,
            'currentClasses' => $currentClasses,
            'debt' => $debt,
            'studentCode' => $sinhVien->ma_sinh_vien,
            'className' => null, // Lớp hành chính đã được xóa
            'course' => $sinhVien->khoaHoc ? $sinhVien->khoaHoc->ten_khoa_hoc : null,
            'warnings' => $warnings,
            'sinhVien' => $sinhVien,
            'thongBaoMoiNhat' => $thongBaoMoiNhat,
            'weeklyTimetable' => $weeklyTimetable,
            'recentGrades' => $recentGrades,
            'upcomingExams' => $upcomingExams,
        ];

        return view('sinhvien.dashboard', $data);
    }
}
