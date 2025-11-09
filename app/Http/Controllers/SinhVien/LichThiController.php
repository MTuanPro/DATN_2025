<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LichThiController extends Controller
{
    /**
     * Xem lịch thi cá nhân
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy các lớp học phần mà sinh viên đã đăng ký
        $lopHocPhanIds = $sinhVien->lopHocPhanSinhVien()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        $query = LichThi::with([
            'lopHocPhan.monHoc', 
            'lopHocPhan.hocKy',
            'phongHoc', 
            'giamThi1', 
            'giamThi2', 
            'hocKy',
            'lichThiSinhViens' => function($q) use ($sinhVien) {
                $q->where('sinh_vien_id', $sinhVien->id)
                  ->with('phongThi');
            }
        ])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds);

        // Lọc theo loại thi
        if ($request->filled('loai_thi')) {
            $query->where('loai_thi', $request->loai_thi);
        }

        // Lọc theo tháng
        if ($request->filled('thang')) {
            $query->whereMonth('ngay_thi', $request->thang);
        }

        // Lọc theo trạng thái (sắp thi / đã thi)
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai == 'sap_thi') {
                $query->where('ngay_thi', '>=', now()->toDateString());
            } elseif ($request->trang_thai == 'da_thi') {
                $query->where('ngay_thi', '<', now()->toDateString());
            }
        }

        $lichThis = $query->orderBy('ngay_thi', 'asc')
                          ->orderBy('gio_bat_dau', 'asc')
                          ->paginate(15);

        return view('sinhvien.lich-thi.index', compact('lichThis'));
    }

    /**
     * Xem chi tiết lịch thi
     */
    public function show(LichThi $lichThi)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Kiểm tra quyền xem (phải đăng ký lớp học phần này)
        $lopHocPhanIds = $sinhVien->lopHocPhanSinhVien()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->route('sinhvien.lich-thi.index')
                ->with('error', 'Bạn không có quyền xem lịch thi này!');
        }

        $lichThi->load([
            'lopHocPhan.monHoc', 
            'lopHocPhan.hocKy',
            'phongThi', 
            'giamThi1', 
            'giamThi2', 
            'hocKy',
            'lichThiSinhViens' => function($q) use ($sinhVien) {
                $q->where('sinh_vien_id', $sinhVien->id)
                  ->with('phongThi');
            }
        ]);

        return view('sinhvien.lich-thi.show', compact('lichThi'));
    }

    /**
     * Xuất lịch thi PDF
     */
    public function exportPdf(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy các lớp học phần mà sinh viên đã đăng ký
        $lopHocPhanIds = $sinhVien->lopHocPhanSinhVien()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        $query = LichThi::with(['lopHocPhan.monHoc', 'phongHoc', 'giamThi1', 'giamThi2', 'hocKy'])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds);

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        } else {
            // Mặc định lấy học kỳ hiện tại
            $query->whereHas('hocKy', function($q) {
                $q->where('trang_thai', 'dang_dien_ra');
            });
        }

        $lichThis = $query->orderBy('ngay_thi', 'asc')
                          ->orderBy('gio_bat_dau', 'asc')
                          ->get();

        $pdf = Pdf::loadView('sinhvien.lich-thi.pdf', [
            'sinhVien' => $sinhVien,
            'lichThis' => $lichThis,
        ]);

        return $pdf->download('lich-thi-' . $sinhVien->ma_sinh_vien . '.pdf');
    }

    /**
     * Lịch thi theo tuần (calendar view)
     */
    public function calendar(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinhvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy các lớp học phần mà sinh viên đã đăng ký
        $lopHocPhanIds = $sinhVien->lopHocPhanSinhVien()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        $lichThis = LichThi::with(['lopHocPhan.monHoc', 'phongHoc'])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('ngay_thi', '>=', now()->toDateString())
            ->orderBy('ngay_thi', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->get();

        // Format dữ liệu cho calendar
        $events = $lichThis->map(function($lichThi) {
            return [
                'id' => $lichThi->id,
                'title' => $lichThi->lopHocPhan->monHoc->ten_mon . ' (' . $lichThi->loai_thi_text . ')',
                'start' => $lichThi->ngay_thi->format('Y-m-d') . 'T' . $lichThi->gio_bat_dau,
                'end' => $lichThi->ngay_thi->format('Y-m-d') . 'T' . $lichThi->gio_ket_thuc,
                'backgroundColor' => $this->getColorByLoaiThi($lichThi->loai_thi),
                'borderColor' => $this->getColorByLoaiThi($lichThi->loai_thi),
                'extendedProps' => [
                    'phong' => $lichThi->phongHoc->ten_phong,
                    'loai_thi' => $lichThi->loai_thi_text,
                    'hinh_thuc' => $lichThi->hinh_thuc_thi_text,
                    'link_online' => $lichThi->link_thi_online,
                ],
            ];
        });

        return view('sinhvien.lich-thi.calendar', compact('events'));
    }

    /**
     * Lấy màu cho loại thi
     */
    private function getColorByLoaiThi($loaiThi)
    {
        $colors = [
            'giua_ky' => '#17a2b8',  // info
            'cuoi_ky' => '#dc3545',  // danger
            'thi_lai' => '#ffc107',  // warning
        ];

        return $colors[$loaiThi] ?? '#6c757d';  // secondary
    }
}
