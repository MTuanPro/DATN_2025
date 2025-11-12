<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhanSinhVien;
use App\Models\LichHocChiTiet;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ThoiKhoaBieuController extends Controller
{
    /**
     * Hiển thị thời khóa biểu cá nhân
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Lấy học kỳ hiện tại hoặc chọn
        $hocKy = null;
        if ($request->filled('hoc_ky_id')) {
            $hocKy = HocKy::find($request->hoc_ky_id);
        } else {
            $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        }

        if (!$hocKy) {
            $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
            return view('sinhvien.thoi-khoa-bieu.index', [
                'hocKy' => null,
                'hocKys' => $hocKys,
                'message' => 'Không tìm thấy học kỳ hiện tại.'
            ]);
        }

        // Lấy các lớp học phần sinh viên đã đăng ký trong học kỳ
        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
            ])
            ->get();

        // Tạo ma trận thời khóa biểu (7 ngày x 12 tiết)
        $thoiKhoaBieu = [];
        for ($thu = 2; $thu <= 8; $thu++) {
            for ($tiet = 1; $tiet <= 12; $tiet++) {
                $thoiKhoaBieu[$thu][$tiet] = null;
            }
        }

        // Điền lịch cố định vào ma trận
        foreach ($lopHocPhanSinhViens as $lopSV) {
            $lopHocPhan = $lopSV->lopHocPhan;

            foreach ($lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $thuTrongTuan = $lichCoDinh->thu_trong_tuan;
                $tietBatDau = $lichCoDinh->tiet_bat_dau;
                $soTiet = $lichCoDinh->tiet_ket_thuc - $lichCoDinh->tiet_bat_dau + 1;

                $thoiKhoaBieu[$thuTrongTuan][$tietBatDau] = [
                    'mon_hoc' => $lopHocPhan->monHoc->ten_mon,
                    'ma_mon' => $lopHocPhan->monHoc->ma_mon,
                    'phong' => $lichCoDinh->phongHoc->ten_phong ?? 'TBA',
                    'giang_vien' => $lichCoDinh->giangVien->ho_ten ?? 'TBA',
                    'so_tiet' => $soTiet,
                    'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                    'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                    'loai_lop' => $lopHocPhan->loai_lop,
                ];

                // Đánh dấu các tiết tiếp theo là đã có lịch
                for ($i = 1; $i < $soTiet; $i++) {
                    $thoiKhoaBieu[$thuTrongTuan][$tietBatDau + $i] = 'span';
                }
            }
        }

        // Kiểm tra trùng lịch
        $trungLich = $this->kiemTraTrungLich($lopHocPhanSinhViens);

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('sinhvien.thoi-khoa-bieu.index', compact(
            'hocKy',
            'hocKys',
            'lopHocPhanSinhViens',
            'thoiKhoaBieu',
            'trungLich',
            'sinhVien'
        ));
    }

    /**
     * Kiểm tra trùng lịch
     */
    private function kiemTraTrungLich($lopHocPhanSinhViens)
    {
        $lichHoc = [];
        $trungLich = [];

        foreach ($lopHocPhanSinhViens as $lopSV) {
            foreach ($lopSV->lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $key = $lichCoDinh->thu_trong_tuan . '_' . $lichCoDinh->tiet_bat_dau;

                if (isset($lichHoc[$key])) {
                    $trungLich[] = [
                        'thu' => $lichCoDinh->getTenThuAttribute(),
                        'tiet' => $lichCoDinh->tiet_bat_dau,
                        'mon_1' => $lichHoc[$key],
                        'mon_2' => $lopSV->lopHocPhan->monHoc->ten_mon,
                    ];
                } else {
                    $lichHoc[$key] = $lopSV->lopHocPhan->monHoc->ten_mon;
                }
            }
        }

        return $trungLich;
    }

    /**
     * Xem lịch chi tiết theo tuần
     */
    public function chiTiet(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        // Lấy học kỳ
        $hocKyId = $request->get('hoc_ky_id');
        $selectedHocKy = $hocKyId
            ? HocKy::find($hocKyId)
            : HocKy::where('la_hoc_ky_hien_tai', true)->first();

        if (!$selectedHocKy) {
            $selectedHocKy = HocKy::orderBy('nam_hoc', 'desc')->first();
        }

        // Lấy tuần (mặc định tuần 1)
        $tuan = $request->get('tuan', 1);

        // Lấy các lớp học phần của sinh viên trong học kỳ
        $lopHocPhanIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($selectedHocKy) {
                $query->where('hoc_ky_id', $selectedHocKy->id);
            })
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->pluck('lop_hoc_phan_id');

        // Tính ngày bắt đầu và kết thúc của tuần
        $startOfWeek = now()->startOfWeek()->addWeeks($tuan - 1);
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Lấy lịch học chi tiết cho tuần này
        $lichHoc = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereBetween('ngay_hoc', [$startOfWeek, $endOfWeek])
            ->with([
                'lopHocPhan.monHoc',
                'giangVien',
                'phongHoc',
            ])
            ->get();

        // Danh sách học kỳ cho bộ lọc
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('sinhvien.thoi-khoa-bieu.chi-tiet', compact(
            'lichHoc',
            'selectedHocKy',
            'tuan',
            'hocKys'
        ));
    }

    /**
     * Xuất PDF thời khóa biểu
     */
    public function exportPDF(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        $hocKy = null;
        if ($request->filled('hoc_ky_id')) {
            $hocKy = HocKy::find($request->hoc_ky_id);
        } else {
            $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
        }

        $lopHocPhanSinhViens = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($query) use ($hocKy) {
                $query->where('hoc_ky_id', $hocKy->id);
            })
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.lichHocCoDinhs.phongHoc',
                'lopHocPhan.lichHocCoDinhs.giangVien',
            ])
            ->get();

        // Tạo ma trận thời khóa biểu
        $thoiKhoaBieu = [];
        for ($thu = 2; $thu <= 8; $thu++) {
            for ($tiet = 1; $tiet <= 12; $tiet++) {
                $thoiKhoaBieu[$thu][$tiet] = null;
            }
        }

        foreach ($lopHocPhanSinhViens as $lopSV) {
            foreach ($lopSV->lopHocPhan->lichHocCoDinhs as $lichCoDinh) {
                $thuTrongTuan = $lichCoDinh->thu_trong_tuan;
                $tietBatDau = $lichCoDinh->tiet_bat_dau;
                $soTiet = $lichCoDinh->tiet_ket_thuc - $lichCoDinh->tiet_bat_dau + 1;

                $thoiKhoaBieu[$thuTrongTuan][$tietBatDau] = [
                    'mon_hoc' => $lopSV->lopHocPhan->monHoc->ten_mon,
                    'phong' => $lichCoDinh->phongHoc->ten_phong ?? 'TBA',
                    'giang_vien' => $lichCoDinh->giangVien->ho_ten ?? 'TBA',
                    'so_tiet' => $soTiet,
                ];

                for ($i = 1; $i < $soTiet; $i++) {
                    $thoiKhoaBieu[$thuTrongTuan][$tietBatDau + $i] = 'span';
                }
            }
        }

        $pdf = Pdf::loadView('sinhvien.thoi-khoa-bieu.pdf', compact(
            'sinhVien',
            'hocKy',
            'thoiKhoaBieu'
        ));

        return $pdf->download('thoi-khoa-bieu-' . $sinhVien->ma_sinh_vien . '.pdf');
    }
}
