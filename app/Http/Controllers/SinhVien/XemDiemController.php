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

    /**
     * Khởi tạo XemDiemController với DiemService dependency
     *
     * Service DiemService cung cấp:
     * - Tính toán điểm tổng kết theo công thức của môn
     * - Tính điểm chữ (A, B+, B, C+, C, D+, D, F)
     * - Xếp loại học tập (Xuất sắc, Giỏi, Khá, Trung bình)
     *
     * @param DiemService $diemService Service xử lý logic điểm
     * @return void
     */
    /**
     * Khởi tạo XemDiemController với DiemService dependency
     *
     * DiemService cung cấp các phương thức:
     * - Tính điểm tổng kết theo công thức từ CauHinhDauDiem
     * - Chuyển đổi điểm hệ 10 sang hệ 4 và chữ (A, B+, B, C+, C, D+, D, F)
     * - Tính điểm trung bình tích lũy (GPA)
     * - Xử lý logic điểm đặc biệt (miễn học, chuyển điểm...)
     *
     * @param DiemService $diemService Service xử lý logic tính điểm
     * @return void
     */
    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Hiển thị bảng điểm các môn học của sinh viên theo học kỳ
     *
     * Quy trình hiển thị:
     * 1. Lấy học kỳ cần xem:
     *    - Nếu có param hoc_ky_id: Dùng học kỳ đó
     *    - Nếu không: Dùng học kỳ hiện tại (la_hoc_ky_hien_tai = true)
     * 2. Lấy danh sách tất cả học kỳ mà sinh viên đã học (có môn đăng ký)
     * 3. Lấy danh sách môn học trong học kỳ đã chọn:
     *    - Chỉ lấy môn có trạng thái lớp: da_duyet_diem (đã công bố điểm)
     *    - Eager load: lopHocPhan, monHoc, hocKy, ketQuaHocTap
     * 4. Tính thống kê điểm danh cho từng môn:
     *    - Tổng số buổi học đã diễn ra (LichHocChiTiet)
     *    - Số buổi có mặt, vắng, đi trễ, nghỉ phép (DiemDanh)
     *    - Tỷ lệ có mặt = (co_mat / tong_buoi_hoc) * 100
     *    - Cảnh báo nếu vắng quá 20% (không được thi)
     * 5. Hiển thị thông tin điểm:
     *    - Điểm thành phần (chuyên cần, giữa kỳ, thực hành, cuối kỳ...)
     *    - Điểm hệ 10, điểm hệ 4, điểm chữ
     *    - Kết quả: Đạt/Không đạt/Rớt/Cải thiện
     * 6. Tính GPA học kỳ và GPA tích lũy
     * 7. Hiển thị xếp loại học tập
     *
     * @param Request $request Có thể chứa hoc_ky_id để xem học kỳ khác
     * @return \Illuminate\View\View Bảng điểm với điểm danh và GPA
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy sinh viên
     */
    public function index(Request $request)
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // Lấy kỳ học được chọn (1-13) thay vì học kỳ thực tế
        $hocKyGoiY = $request->get('hoc_ky_goi_y');
        
        // Lấy chương trình khung để xác định kỳ học gợi ý
        $chuongTrinhKhung = [];
        if ($sinhVien->chuyen_nganh_id) {
            $chuongTrinhKhung = \App\Models\DaoTao\ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
                ->pluck('hoc_ky_goi_y', 'mon_hoc_id')
                ->toArray();
        }

        // Lấy tất cả môn học đã có điểm
        $monHocsAll = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) {
                $q->where('trang_thai_lop', 'da_duyet_diem');
            })
            ->with(['lopHocPhan.monHoc', 'lopHocPhan.hocKy'])
            ->get();

        // Lấy danh sách học kỳ thực tế đã học (sắp xếp theo thời gian)
        $hocKyIds = $monHocsAll->pluck('lopHocPhan.hoc_ky_id')->filter()->unique();
        
        // Nếu sinh viên ở kỳ >= 2, đảm bảo có học kỳ kỳ 1
        // Tìm học kỳ kỳ 1 dựa trên khóa học của sinh viên
        $kyHienTai = $sinhVien->ky_hien_tai ?? 1;
        if ($kyHienTai >= 2 && $sinhVien->khoa_hoc_id) {
            $khoaHoc = \App\Models\DaoTao\KhoaHoc::find($sinhVien->khoa_hoc_id);
            if ($khoaHoc) {
                $namBatDau = $khoaHoc->nam_bat_dau;
                $namHocString = $namBatDau . '-' . ($namBatDau + 1);
                
                // Tìm học kỳ 1 của năm học đầu tiên
                $hocKyKy1 = HocKy::where('ten_hoc_ky', 'Học kỳ 1')
                    ->where('nam_hoc', $namHocString)
                    ->first();
                
                if ($hocKyKy1 && !$hocKyIds->contains($hocKyKy1->id)) {
                    // Thêm học kỳ kỳ 1 vào danh sách nếu chưa có
                    $hocKyIds->push($hocKyKy1->id);
                }
            }
        }
        
        $hocKysThucTe = HocKy::whereIn('id', $hocKyIds)
            ->orderBy('ngay_bat_dau', 'asc')
            ->get();

        // Tạo mapping: hoc_ky_id -> kỳ học hiển thị (1-13)
        // Tương tự như trong bangDiem()
        $hocKyMapping = [];
        $daDungKy = [];
        $monHocsByHocKy = $monHocsAll->groupBy('lopHocPhan.hoc_ky_id');
        
        foreach ($hocKysThucTe as $hocKy) {
            $hocKyId = $hocKy->id;
            $monHocsTrongKy = $monHocsByHocKy->get($hocKyId, collect());
            
            // Tìm kỳ học gợi ý từ CTK
            $kyHocGoiY = null;
            foreach ($monHocsTrongKy as $item) {
                $monHocId = $item->lopHocPhan->mon_hoc_id;
                if (isset($chuongTrinhKhung[$monHocId])) {
                    $kyHocGoiY = $chuongTrinhKhung[$monHocId];
                    break;
                }
            }
            
            if ($kyHocGoiY && $kyHocGoiY <= 8 && !in_array($kyHocGoiY, $daDungKy)) {
                $hocKyMapping[$hocKyId] = $kyHocGoiY;
                $daDungKy[] = $kyHocGoiY;
            } else {
                // Kỳ trả nợ (kỳ 9-13)
                $kyHocHienThi = 9;
                while ($kyHocHienThi <= 13 && in_array($kyHocHienThi, $hocKyMapping)) {
                    $kyHocHienThi++;
                }
                $hocKyMapping[$hocKyId] = $kyHocHienThi <= 13 ? $kyHocHienThi : 13;
            }
        }

        // Lấy danh sách kỳ học (1-13) mà sinh viên đã có môn học
        $kyHocs = array_unique(array_values($hocKyMapping));
        
        // Nếu sinh viên ở kỳ >= 2, đảm bảo có kỳ 1 trong danh sách
        $kyHienTai = $sinhVien->ky_hien_tai ?? 1;
        if ($kyHienTai >= 2 && !in_array(1, $kyHocs)) {
            $kyHocs[] = 1; // Thêm kỳ 1 vào danh sách
        }
        
        sort($kyHocs);

        // Lấy danh sách môn học đã có điểm trong kỳ học được chọn
        $monHocs = [];
        
        if ($hocKyGoiY) {
            // Tìm các học kỳ thực tế tương ứng với kỳ học được chọn
            $hocKyIds = [];
            foreach ($hocKyMapping as $hkId => $kyHoc) {
                if ($kyHoc == $hocKyGoiY) {
                    $hocKyIds[] = $hkId;
                }
            }
            
            // Nếu chọn kỳ 1 và không tìm thấy học kỳ trong mapping (chưa có điểm),
            // tìm học kỳ kỳ 1 dựa trên khóa học của sinh viên
            if ($hocKyGoiY == 1 && empty($hocKyIds) && $sinhVien->khoa_hoc_id) {
                $khoaHoc = \App\Models\DaoTao\KhoaHoc::find($sinhVien->khoa_hoc_id);
                if ($khoaHoc) {
                    $namBatDau = $khoaHoc->nam_bat_dau;
                    $namHocString = $namBatDau . '-' . ($namBatDau + 1);
                    
                    // Tìm học kỳ 1 của năm học đầu tiên
                    $hocKyKy1 = HocKy::where('ten_hoc_ky', 'Học kỳ 1')
                        ->where('nam_hoc', $namHocString)
                        ->first();
                    
                    if ($hocKyKy1) {
                        $hocKyIds[] = $hocKyKy1->id;
                    }
                }
            }
            
            if (!empty($hocKyIds)) {
                $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
                    ->whereHas('lopHocPhan', function ($q) use ($hocKyIds) {
                        $q->where('trang_thai_lop', 'da_duyet_diem')
                          ->whereIn('hoc_ky_id', $hocKyIds);
                    })
                    ->with([
                        'lopHocPhan.monHoc',
                        'lopHocPhan.hocKy',
                        'ketQuaHocTap'
                    ])
                    ->get();
            }

            // Tính thống kê điểm danh cho từng môn học
            foreach ($monHocs as $monHoc) {
                $lopHocPhanId = $monHoc->lopHocPhan->id;
                
                // Tổng số buổi học từ lịch học chi tiết (tất cả buổi, không phân biệt đã diễn ra hay chưa)
                $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
                    ->where('trang_thai', '!=', 'huy') // Không tính các buổi đã hủy
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

        // Tính GPA học kỳ (tính từ các môn trong kỳ học được chọn)
        $gpaHocKy = 0;
        if ($hocKyGoiY && $monHocs->isNotEmpty()) {
            $tongDiem = 0;
            $tongHeSo = 0;
            foreach ($monHocs as $item) {
                if ($item->ketQuaHocTap && $item->ketQuaHocTap->qua_mon) {
                    $tinChi = $item->lopHocPhan->monHoc->so_tin_chi ?? 0;
                    $tongDiem += $item->ketQuaHocTap->diem_he_4 * $tinChi;
                    $tongHeSo += $tinChi;
                }
            }
            $gpaHocKy = $tongHeSo > 0 ? $tongDiem / $tongHeSo : 0;
        }
        
        $gpaTichLuy = $this->diemService->tinhGPATichLuy($sinhVien->id);
        $tongTinChiDat = $this->diemService->tinhTongTinChiDat($sinhVien->id);

        return view('sinhvien.diem.index', compact(
            'monHocs',
            'kyHocs',
            'hocKyGoiY',
            'gpaHocKy',
            'gpaTichLuy',
            'tongTinChiDat'
        ));
    }

    /**
     * Hiển thị chi tiết điểm của một môn học cụ thể
     *
     * Thông tin chi tiết bao gồm:
     * 1. Thông tin môn học:
     *    - Tên môn, mã môn, số tín chỉ
     *    - Lớp học phần, giảng viên
     *    - Học kỳ, năm học
     * 2. Cấu hình đầu điểm (từ CauHinhDauDiem):
     *    - Các đầu điểm: Chuyên cần, Giữa kỳ, Thực hành, Cuối kỳ...
     *    - Tỷ lệ % của từng đầu điểm
     *    - Số cột điểm của mỗi đầu
     * 3. Điểm thành phần đã nhập (từ NhapDiem):
     *    - Điểm từng cột của mỗi đầu điểm
     *    - Điểm trung bình mỗi đầu điểm
     * 4. Công thức tính điểm tổng kết:
     *    - Hiển thị công thức: (CC * 10% + GK * 20% + CK * 70%)
     *    - Điểm tổng kết hệ 10, hệ 4, điểm chữ
     *    - Kết quả: Đạt/Không đạt
     * 5. Thống kê điểm danh:
     *    - Tổng số buổi học, số buổi có mặt, vắng, đi trễ
     *    - Tỷ lệ % tham gia
     *    - Cảnh báo nếu vắng nhiều
     *
     * @param int $lopHocPhanId ID của lớp học phần cần xem điểm chi tiết
     * @return \Illuminate\View\View Chi tiết điểm môn học
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền hoặc chưa có điểm
     */
    /**
     * Xem chi tiết điểm của một môn học cụ thể
     *
     * Hiển thị:
     * - Thông tin lớp học phần (mã, tên, giảng viên, lịch học)
     * - Chi tiết điểm từng thành phần:
     *   + Cấu hình đầu điểm (tên, tỷ lệ, số cột)
     *   + Điểm từng cột (nếu có nhiều cột)
     *   + Điểm trung bình thành phần
     *   + Ghi chú của giảng viên (nếu có)
     * - Công thức tính điểm tổng kết
     * - Điểm tổng kết (hệ 10, hệ 4, chữ)
     * - Lịch sử điểm danh chi tiết (từng buổi học)
     *
     * @param int $lopHocPhanId ID lớp học phần cần xem điểm
     * @return \Illuminate\View\View Chi tiết điểm môn học
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
     */
    /**
     * Hiển thị chi tiết điểm của một môn học cụ thể
     *
     * Thông tin chi tiết bao gồm:
     * 1. Thông tin môn học:
     *    - Tên môn, mã môn, số tín chỉ
     *    - Lớp học phần, giảng viên
     *    - Học kỳ, năm học
     * 2. Cấu hình đầu điểm (từ CauHinhDauDiem):
     *    - Các đầu điểm: Chuyên cần, Giữa kỳ, Thực hành, Cuối kỳ...
     *    - Tỷ lệ % của từng đầu điểm
     *    - Số cột điểm của mỗi đầu
     * 3. Điểm thành phần đã nhập (từ NhapDiem):
     *    - Điểm từng cột của mỗi đầu điểm
     *    - Điểm trung bình mỗi đầu điểm
     * 4. Công thức tính điểm tổng kết:
     *    - Hiển thị công thức: (CC * 10% + GK * 20% + CK * 70%)
     *    - Điểm tổng kết hệ 10, hệ 4, điểm chữ
     *    - Kết quả: Đạt/Không đạt
     * 5. Thống kê điểm danh:
     *    - Tổng số buổi học, số buổi có mặt, vắng, đi trễ
     *    - Tỷ lệ % tham gia
     *    - Cảnh báo nếu vắng nhiều
     *
     * @param int $lopHocPhanId ID của lớp học phần cần xem điểm chi tiết
     * @return \Illuminate\View\View Chi tiết điểm môn học
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền hoặc chưa có điểm
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
    /**
     * Hiển thị bảng điểm tổng hợp tất cả các học kỳ của sinh viên
     *
     * Bảng điểm bao gồm:
     * - Tất cả môn học đã học từ đầu khóa đến hiện tại
     * - Nhóm theo học kỳ
     * - Mỗi môn hiển thị: Mã môn, Tên môn, Số tín chỉ, Điểm (10/4/chữ), Kết quả
     * - Tính GPA từng học kỳ và GPA tích lũy
     * - Tổng số tín chỉ đã tích lũy
     * - Số tín chỉ đạt và không đạt
     * - Xếp loại học tập hiện tại (Xuất sắc/Giỏi/Khá/Trung bình/Yếu/Kém)
     * - Cảnh báo học vụ (nếu có): GPA < 1.0, Rớt nhiều môn...
     *
     * @return \Illuminate\View\View Bảng điểm tổng hợp với GPA và xếp loại
     */
    /**
     * Hiển thị bảng điểm tổng hợp tất cả các học kỳ (transcript)
     *
     * Bảng điểm tổng hợp bao gồm:
     * 1. Thông tin sinh viên:
     *    - MSSV, họ tên, lớp hành chính
     *    - Khoa, ngành, chuyên ngành
     *    - Khóa học, hệ đào tạo
     * 2. Điểm từng môn học theo học kỳ:
     *    - Nhóm theo học kỳ (HK1, HK2, HK3...)
     *    - Mã môn, tên môn, số tín chỉ
     *    - Điểm hệ 10, điểm chữ, kết quả
     * 3. Tổng kết từng học kỳ:
     *    - Số tín chỉ đăng ký
     *    - Số tín chỉ tích lũy
     *    - Điểm trung bình học kỳ (GPA học kỳ)
     *    - Điểm trung bình tích lũy (GPA tích lũy)
     *    - Xếp loại học kỳ
     * 4. Tổng kết toàn khóa:
     *    - Tổng số tín chỉ tích lũy
     *    - GPA tích lũy toàn khóa (4.0 scale)
     *    - Xếp loại tốt nghiệp dự kiến
     *    - Cảnh báo học vụ (nếu có)
     * 5. Biểu đồ phân tích:
     *    - Biểu đồ điểm trung bình qua các học kỳ
     *    - Tỷ lệ các loại điểm chữ (A, B, C, D, F)
     *    - Số tín chỉ tích lũy qua từng học kỳ
     *
     * @return \Illuminate\View\View Bảng điểm tổng hợp với biểu đồ phân tích
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy sinh viên
     */
    public function bangDiem()
    {
        $sinhVien = Auth::user()->sinhVien;

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên');
        }

        // Load relationships
        $sinhVien->load(['user', 'nganh.khoa', 'chuyenNganh.nganh.khoa']);

        // Lấy chương trình khung để xác định kỳ học gợi ý cho mỗi môn
        $chuongTrinhKhung = [];
        if ($sinhVien->chuyen_nganh_id) {
            $chuongTrinhKhung = \App\Models\DaoTao\ChuongTrinhKhung::where('chuyen_nganh_id', $sinhVien->chuyen_nganh_id)
                ->pluck('hoc_ky_goi_y', 'mon_hoc_id')
                ->toArray();
        }
        
        // Lấy tất cả môn đã học (đã duyệt điểm) - chỉ lấy môn đã đăng ký (có LopHocPhanSinhVien)
        $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVien->id)
            ->whereHas('lopHocPhan', function ($q) {
                $q->where('trang_thai_lop', 'da_duyet_diem');
            })
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'ketQuaHocTap'
            ])
            ->get();
        
        // Debug: Kiểm tra môn học kỳ 1
        $monHocKy1 = $monHocs->filter(function ($item) use ($chuongTrinhKhung) {
            $monHocId = $item->lopHocPhan->mon_hoc_id ?? null;
            return isset($chuongTrinhKhung[$monHocId]) && $chuongTrinhKhung[$monHocId] == 1;
        });
        
        \Log::info('BangDiem - Debug', [
            'sinh_vien_id' => $sinhVien->id,
            'chuyen_nganh_id' => $sinhVien->chuyen_nganh_id,
            'ctk_count' => count($chuongTrinhKhung),
            'tong_mon_hoc' => $monHocs->count(),
            'mon_hoc_ky_1_count' => $monHocKy1->count(),
            'mon_hoc_ky_1_ids' => $monHocKy1->pluck('lopHocPhan.mon_hoc_id')->toArray(),
            'ctk_ky_1_mon_ids' => array_keys(array_filter($chuongTrinhKhung, fn($ky) => $ky == 1)),
        ]);

        // Nhóm môn học trực tiếp theo hoc_ky_goi_y từ CTK (1-13)
        // Điều này đảm bảo môn học kỳ 1 luôn hiển thị trong "Học kỳ 1", bất kể học ở học kỳ thực tế nào
        $monHocsGrouped = [];
        $kyHocTrongCTK = []; // Lưu các kỳ học có trong CTK (1-8)
        $kyHocTraNo = 9; // Bắt đầu từ kỳ 9 cho môn trả nợ
        
        foreach ($monHocs as $item) {
            $monHocId = $item->lopHocPhan->mon_hoc_id;
            $kyHocHienThi = null;
            
            // Ưu tiên: Lấy hoc_ky_goi_y từ CTK
            if (isset($chuongTrinhKhung[$monHocId])) {
                $kyHocHienThi = $chuongTrinhKhung[$monHocId];
                if ($kyHocHienThi <= 8) {
                    $kyHocTrongCTK[] = $kyHocHienThi;
                }
            } else {
                // Môn không có trong CTK (môn tự chọn hoặc trả nợ) -> gán vào kỳ trả nợ (9-13)
                // Tìm kỳ trả nợ tiếp theo chưa dùng
                while ($kyHocTraNo <= 13 && isset($monHocsGrouped[$kyHocTraNo])) {
                    $kyHocTraNo++;
                }
                $kyHocHienThi = $kyHocTraNo <= 13 ? $kyHocTraNo : 13;
                $kyHocTraNo++;
            }
            
            // Nếu vẫn chưa có kỳ học hiển thị, mặc định là kỳ 1
            if (!$kyHocHienThi) {
                $kyHocHienThi = 1;
            }
            
            if (!isset($monHocsGrouped[$kyHocHienThi])) {
                $monHocsGrouped[$kyHocHienThi] = collect();
            }
            $monHocsGrouped[$kyHocHienThi]->push($item);
        }

        // Sắp xếp theo kỳ học (1-13) và chuyển thành collection
        ksort($monHocsGrouped);
        
        // Debug: Log để kiểm tra
        \Log::info('BangDiem - Sinh viên: ' . $sinhVien->id, [
            'tong_mon_hoc' => $monHocs->count(),
            'mon_hocs_grouped_keys' => array_keys($monHocsGrouped),
            'chuong_trinh_khung_count' => count($chuongTrinhKhung),
        ]);
        
        $monHocs = collect($monHocsGrouped);

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
    /**
     * Xuất bảng điểm tổng hợp ra file PDF chính thức
     *
     * File PDF bao gồm:
     * - Header: Logo trường, tên trường, khoa quản lý
     * - Thông tin sinh viên: MSSV, Họ tên, Ngày sinh, Lớp, Ngành, Khoa học
     * - Bảng điểm tất cả các học kỳ (tương tự bangDiem())
     * - Thống kê: Tổng tín chỉ, GPA học kỳ, GPA tích lũy, Xếp loại
     * - Chữ ký xác nhận của Phòng Đào tạo (chữ ký số)
     * - Ngày xuất và mã xác thực (QR code)
     * - Định dạng chuẩn theo mẫu Bộ GD&ĐT
     *
     * Tên file: BangDiem_MSSV_HoTen_NgayXuat.pdf
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File PDF download
     */
    /**
     * Xuất bảng điểm tổng hợp ra file PDF để tải về hoặc in
     *
     * Nội dung PDF bao gồm:
     * 1. Header chính thức:
     *    - Logo trường
     *    - Tên trường, khoa
     *    - Tiêu đề: 'BẢNG ĐIỂM TỔNG HỢP'
     * 2. Thông tin sinh viên (đầy đủ):
     *    - MSSV, họ tên, ngày sinh, giới tính
     *    - Lớp hành chính, khóa học
     *    - Ngành, chuyên ngành, khoa
     *    - Hệ đào tạo (Chính quy/Liên thông...)
     * 3. Bảng điểm từng học kỳ:
     *    - Table format chuẩn với border
     *    - Columns: STT, Mã môn, Tên môn, Số TC, Điểm hệ 10, Điểm chữ, Kết quả
     *    - Phân trang tự động nếu quá nhiều môn
     * 4. Tổng kết học kỳ sau mỗi học kỳ:
     *    - Số TC đăng ký, TC tích lũy
     *    - GPA học kỳ, GPA tích lũy
     *    - Xếp loại
     * 5. Tổng kết toàn khóa (cuối file):
     *    - Tổng TC tích lũy
     *    - GPA tích lũy toàn khóa
     *    - Xếp loại tốt nghiệp dự kiến
     * 6. Footer:
     *    - Ngày xuất bảng điểm
     *    - Chữ ký xác nhận (nếu cần)
     *    - Watermark (nếu chưa chính thức)
     *
     * Tên file: BangDiem_[MSSV]_[Ngày].pdf
     * Font: Times New Roman, hỗ trợ tiếng Việt
     * Page size: A4
     * Orientation: Portrait
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File PDF download
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy sinh viên
     */
    public function exportPDF()
    {
        $sinhVien = Auth::user()->sinhVien;

        // TODO: Implement PDF export using DomPDF or similar
        return redirect()->route('sinh-vien.diem.bang-diem')
            ->with('info', 'Chức năng xuất PDF đang được phát triển');
    }
}