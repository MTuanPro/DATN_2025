<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhanSinhVien;
use App\Models\DiemDanh;
use App\Models\LichHocChiTiet;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class XemDiemController extends Controller
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Xem điểm các môn học theo học kỳ
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // Lấy học kỳ hiện tại hoặc học kỳ được chọn
        $hocKyId = $request->get('hoc_ky_id');
        
        if (!$hocKyId) {
            $hocKyHienTai = HocKy::where('la_hoc_ky_hien_tai', true)->first();
            $hocKyId = $hocKyHienTai ? $hocKyHienTai->id : null;
        }

        // Lấy danh sách học kỳ đã học
        $hocKys = HocKy::whereHas('lopHocPhans.lopHocPhanSinhVien', function ($q) use ($sinhVien) {
            $q->where('sinh_vien_id', $sinhVien->id);
        })
        ->orderBy('ngay_bat_dau', 'desc')
        ->get();

        // Lấy danh sách môn học đã có điểm trong học kỳ
        $monHocs = [];
        
        if ($hocKyId) {
            $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                    $q->where('hoc_ky_id', $hocKyId)
                      ->where('trang_thai_lop', 'da_duyet_diem');
                })
                ->with([
                    'lopHocPhan.monHoc',
                    'lopHocPhan.hocKy',
                    'ketQuaHocTap'
                ])
                ->get();

            // Tính thống kê điểm danh cho từng môn học
            foreach ($monHocs as $monHoc) {
                $lopHocPhanId = $monHoc->lopHocPhan->id;
                
                // Tổng số buổi học đã diễn ra
                $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
                    ->where('ngay_hoc', '<=', Carbon::now())
                    ->count();

                // Thống kê điểm danh
                $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $monHoc->id)
                    ->selectRaw('
                        COUNT(*) as tong_buoi_diem_danh,
                        SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                        SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                        SUM(CASE WHEN trang_thai = "di_tre" THEN 1 ELSE 0 END) as di_tre,
                        SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                    ')
                    ->first();

                $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
                $tyLeCoMat = $tongBuoiHoc > 0 
                    ? round(($coMat / $tongBuoiHoc) * 100, 1) 
                    : 0;

                // Gán thống kê vào môn học
                $monHoc->tong_buoi_hoc = $tongBuoiHoc;
                $monHoc->diem_danh_stats = $diemDanhStats;
                $monHoc->ty_le_co_mat = $tyLeCoMat;
            }
        }

        // Tính GPA học kỳ và tích lũy (chỉ tính điểm đã duyệt)
        $gpaHocKy = $hocKyId ? $this->diemService->tinhGPAHocKy($sinhVien->id, $hocKyId) : 0;
        $gpaTichLuy = $this->diemService->tinhGPATichLuy($sinhVien->id);
        $tongTinChiDat = $this->diemService->tinhTongTinChiDat($sinhVien->id);

        return view('sinhvien.diem.index', compact(
            'monHocs',
            'hocKys',
            'hocKyId',
            'gpaHocKy',
            'gpaTichLuy',
            'tongTinChiDat'
        ));
    }

    /**
     * Xem chi tiết điểm một môn học
     */
    public function show($lopHocPhanId)
    {
        $sinhVien = Auth::user()->sinhVien;

        // Lấy thông tin đăng ký lớp học phần
        $lhpsv = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->where('lop_hoc_phan_id', $lopHocPhanId)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.cauHinhDauDiem',
'ketQuaHocTap'
            ])
            ->first();

        if (!$lhpsv) {
            return redirect()->route('sinh-vien.diem.index')
                ->with('error', 'Bạn chưa đăng ký lớp học phần này');
        }

        // Kiểm tra điểm đã được công bố chưa
        if ($lhpsv->lopHocPhan->trang_thai_lop !== 'da_duyet_diem') {
            return redirect()->route('sinh-vien.diem.index')
                ->with('error', 'Điểm môn học này chưa được công bố');
        }

        // Lấy điểm thành phần
        $diemThanhPhan = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
            ->with('cauHinh')
            ->get()
            ->groupBy('cau_hinh_id');

        return view('sinhvien.diem.show', compact('lhpsv', 'diemThanhPhan'));
    }

    /**
     * Xem bảng điểm tổng hợp
     */
    public function bangDiem()
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // Load relationships
        $sinhVien->load(['user', 'lopHanhChinh', 'lopHanhChinh.khoa']);

        // Lấy tất cả môn đã học (đã duyệt điểm)
        $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) {
                $q->where('trang_thai_lop', 'da_duyet_diem');
            })
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'ketQuaHocTap'
            ])
            ->get()
            ->groupBy('lopHocPhan.hoc_ky_id');

        // Tính toán tổng hợp
        $gpaTichLuy = $this->diemService->tinhGPATichLuy($sinhVien->id);
        $tongTinChiDat = $this->diemService->tinhTongTinChiDat($sinhVien->id);
        
        // Tính tổng tín chỉ đã học (bao gồm cả không đạt)
        $tongTinChiHoc = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) {
                $q->where('trang_thai_lop', 'da_duyet_diem');
            })
            ->with('lopHocPhan.monHoc')
            ->get()
            ->unique(function ($item) {
                return $item->lopHocPhan->mon_hoc_id;
            })
            ->sum(function ($item) {
                return $item->lopHocPhan->monHoc->so_tin_chi;
            });

        return view('sinhvien.diem.bang-diem', compact(
            'monHocs',
            'gpaTichLuy',
            'tongTinChiDat',
            'tongTinChiHoc',
            'sinhVien'
        ));
    }

    /**
     * Export bảng điểm PDF
     */
    public function exportPDF()
    {
        $sinhVien = Auth::user()->sinhVien;

        // TODO: Implement PDF export using DomPDF or similar
        return redirect()->route('sinh-vien.diem.bang-diem')
            ->with('info', 'Chức năng xuất PDF đang được phát triển');
    }
}