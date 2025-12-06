<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\DiemDanh;
use App\Models\CauHinhDauDiem;
use App\Models\NhapDiem;
use App\Models\LichHocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LichThiController extends Controller
{
    /**
     * Xem danh sách lịch thi của lớp giảng dạy
     */
    public function index(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy các lớp học phần mà giảng viên phụ trách
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        $query = LichThi::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongHoc',
            'giamThi1',
            'giamThi2',
            'hocKy'
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

        // Tìm kiếm theo tên môn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lopHocPhan.monHoc', function ($q) use ($search) {
                $q->where('ten_mon', 'like', "%{$search}%")
                    ->orWhere('ma_mon', 'like', "%{$search}%");
            });
        }

        $lichThis = $query->orderBy('ngay_thi', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->paginate(15);

        return view('giangvien.lich-thi.index', compact('lichThis'));
    }

    /**
     * Xem lịch coi thi (nếu được phân công giám thị)
     */
    public function lichCoiThi(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        $query = LichThi::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongHoc',
            'giamThi1',
            'giamThi2',
            'hocKy'
        ])
            ->where(function ($q) use ($giangVien) {
                $q->where('giam_thi_1_id', $giangVien->id)
                    ->orWhere('giam_thi_2_id', $giangVien->id);
            });

        // Lọc theo tháng
        if ($request->filled('thang')) {
            $query->whereMonth('ngay_thi', $request->thang);
        }

        // Lọc theo trạng thái
        if ($request->filled('da_coi')) {
            if ($request->da_coi == '1') {
                $query->where('ngay_thi', '<', now()->toDateString());
            } else {
                $query->where('ngay_thi', '>=', now()->toDateString());
            }
        }

        $lichCoiThis = $query->orderBy('ngay_thi', 'asc')
            ->orderBy('gio_bat_dau', 'asc')
            ->paginate(15);

        return view('giangvien.lich-thi.lich-coi-thi', compact('lichCoiThis'));
    }

    /**
     * Xem chi tiết lịch thi
     */
    public function show(LichThi $lichThi)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền xem (phải là GV phụ trách lớp hoặc giám thị)
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        $isGiamThi = ($lichThi->giam_thi_1_id == $giangVien->id ||
            $lichThi->giam_thi_2_id == $giangVien->id);

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id) && !$isGiamThi) {
            return redirect()->route('giangvien.lich-thi.index')
                ->with('error', 'Bạn không có quyền xem lịch thi này!');
        }

        $lichThi->load([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongThi',
            'giamThi1',
            'giamThi2',
            'hocKy',
            'lichThiSinhViens.phongThi'
        ]);

        // Kiểm tra điều kiện dự thi cho từng sinh viên
        // Chỉ tính các buổi học đã được giảng viên này điểm danh (trang_thai = 'da_day' và giang_vien_id = giảng viên này)
        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->where('ngay_hoc', '<=', now()->toDateString())
            ->where('trang_thai', 'da_day')
            ->where('giang_vien_id', $giangVien->id)
            ->count();

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        $danhSachSinhVienDiThi = [];

        foreach ($lichThi->lichThiSinhViens as $item) {
            // Lấy LopHocPhanSinhVien để kiểm tra điều kiện
            $lopHocPhanSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
                ->where('sinh_vien_id', $item->sinh_vien_id)
                ->first();

            if (!$lopHocPhanSinhVien) {
                continue;
            }

            // 1. Kiểm tra chuyên cần (vắng quá 20% = có mặt < 80%)
            // Chỉ lấy điểm danh của các buổi học đã được giảng viên này điểm danh (trang_thai = 'da_day' và giang_vien_id = giảng viên này)
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                ->whereHas('lichHocChiTiet', function ($query) use ($giangVien) {
                    $query->where('ngay_hoc', '<=', now()->toDateString())
                        ->where('trang_thai', 'da_day')
                        ->where('giang_vien_id', $giangVien->id);
                })
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
            $vang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
            $nghiPhep = $diemDanhStats ? ($diemDanhStats->nghi_phep ?? 0) : 0;

            $tyLeCoMat = $tongBuoiHoc > 0
                ? round(($coMat / $tongBuoiHoc) * 100, 1)
                : 0;

            $khongDatChuyenCan = $tyLeCoMat < 80;

            // 2. Kiểm tra điểm trung bình các đầu điểm < 5
            $diemTrungBinh = null;
            $khongDatDiem = false;

            if ($cauHinhs->isNotEmpty()) {
                $tongDiem = 0;
                $tongTyLe = 0;
                $coDiem = false;

                foreach ($cauHinhs as $cauHinh) {
                    $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVien->id)
                        ->where('cau_hinh_id', $cauHinh->id)
                        ->get();

                    if ($diems->isEmpty()) {
                        continue;
                    }

                    $coDiem = true;
                    $diemTrungBinhDauDiem = $diems->avg('diem_so');

                    if ($diemTrungBinhDauDiem !== null) {
                        $tongDiem += $diemTrungBinhDauDiem * ($cauHinh->ty_le / 100);
                        $tongTyLe += $cauHinh->ty_le;
                    }
                }

                if ($coDiem && $tongTyLe > 0) {
                    if ($tongTyLe < 100) {
                        $diemTrungBinh = round(($tongDiem / $tongTyLe) * 100, 2);
                    } else {
                        $diemTrungBinh = round($tongDiem, 2);
                    }

                    $khongDatDiem = $diemTrungBinh < 5;
                }
            }

            // Không được đi thi nếu: vắng quá 20% HOẶC điểm < 5
            $khongDuocDiThi = $khongDatChuyenCan || $khongDatDiem;

            $danhSachSinhVienDiThi[] = [
                'lich_thi_sinh_vien' => $item,
                'ty_le_co_mat' => $tyLeCoMat,
                'khong_dat_chuyen_can' => $khongDatChuyenCan,
                'diem_trung_binh' => $diemTrungBinh,
                'khong_dat_diem' => $khongDatDiem,
                'khong_duoc_di_thi' => $khongDuocDiThi,
                'ly_do' => $this->taoLyDoKhongDuocDiThi($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh),
            ];
        }

        return view('giangvien.lich-thi.show', compact('lichThi', 'isGiamThi', 'danhSachSinhVienDiThi', 'tongBuoiHoc'));
    }

    /**
     * Upload đề thi (chỉ GV phụ trách lớp)
     */
    public function uploadDeThi(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'de_thi' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'de_thi.required' => 'Vui lòng chọn file đề thi.',
            'de_thi.file' => 'Đề thi phải là file.',
            'de_thi.mimes' => 'Đề thi phải là file PDF, DOC hoặc DOCX.',
            'de_thi.max' => 'Đề thi không được vượt quá 10MB.',
        ]);

        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV phụ trách lớp)
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền upload đề thi cho lớp này!');
        }

        try {
            // Xóa file cũ nếu có
            if ($lichThi->de_thi_file) {
                Storage::disk('public')->delete($lichThi->de_thi_file);
            }

            // Upload file mới (timestamp + tên gốc)
            $file = $request->file('de_thi');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
            $path = $file->storeAs('de-thi', $fileName, 'public');
            $lichThi->update(['de_thi_file' => $path]);

            return redirect()->back()
                ->with('success', 'Upload đề thi thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Upload đáp án (chỉ GV phụ trách lớp)
     */
    public function uploadDapAn(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'dap_an' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'dap_an.required' => 'Vui lòng chọn file đáp án.',
            'dap_an.file' => 'Đáp án phải là file.',
            'dap_an.mimes' => 'Đáp án phải là file PDF, DOC hoặc DOCX.',
            'dap_an.max' => 'Đáp án không được vượt quá 10MB.',
        ]);

        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV phụ trách lớp)
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền upload đáp án cho lớp này!');
        }

        try {
            // Xóa file cũ nếu có
            if ($lichThi->dap_an_file) {
                Storage::disk('public')->delete($lichThi->dap_an_file);
            }

            // Upload file mới (timestamp + tên gốc)
            $file = $request->file('dap_an');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
            $path = $file->storeAs('dap-an', $fileName, 'public');
            $lichThi->update(['dap_an_file' => $path]);

            return redirect()->back()
                ->with('success', 'Upload đáp án thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận đã coi thi (cho giám thị)
     */
    public function xacNhanCoiThi(LichThi $lichThi)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là giám thị)
        if (
            $lichThi->giam_thi_1_id != $giangVien->id &&
            $lichThi->giam_thi_2_id != $giangVien->id
        ) {
            return redirect()->back()
                ->with('error', 'Bạn không phải giám thị của ca thi này!');
        }

        // TODO: Implement xác nhận coi thi (có thể thêm trường trong DB)

        return redirect()->back()
            ->with('success', 'Đã xác nhận coi thi!');
    }

    /**
     * Tải đề thi
     */
    public function downloadDeThi(LichThi $lichThi)
    {
        if (!$lichThi->de_thi_file) {
            return redirect()->back()
                ->with('error', 'Chưa có đề thi!');
        }

        $path = storage_path('app/public/' . $lichThi->de_thi_file);

        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'File không tồn tại!');
        }

        return response()->download($path);
    }

    /**
     * Tải đáp án
     */
    public function downloadDapAn(LichThi $lichThi)
    {
        if (!$lichThi->dap_an_file) {
            return redirect()->back()
                ->with('error', 'Chưa có đáp án!');
        }

        $path = storage_path('app/public/' . $lichThi->dap_an_file);

        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'File không tồn tại!');
        }

        return response()->download($path);
    }

    /**
     * Danh sách lịch thi để xuất danh sách sinh viên đi thi
     */
    public function indexXuatDanhSachThi(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Lấy các lớp học phần mà giảng viên phụ trách
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        $query = LichThi::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongHoc',
            'caHoc'
        ])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds);

        // Lọc theo loại thi
        if ($request->filled('loai_thi')) {
            $query->where('loai_thi', $request->loai_thi);
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhan', function ($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Tìm kiếm theo tên môn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lopHocPhan.monHoc', function ($q) use ($search) {
                $q->where('ten_mon', 'like', "%{$search}%")
                    ->orWhere('ma_mon', 'like', "%{$search}%");
            });
        }

        $lichThis = $query->orderBy('ngay_thi', 'desc')
            ->orderBy('gio_bat_dau', 'asc')
            ->paginate(15);

        $hocKys = \App\Models\HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        return view('giangvien.xuat-danh-sach-thi.index', compact('lichThis', 'hocKys'));
    }

    /**
     * Xuất danh sách sinh viên đi thi (kiểm tra điều kiện)
     */
    public function xuatDanhSachSinhVienDiThi(LichThi $lichThi)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên!');
        }

        // Kiểm tra quyền (phải là GV phụ trách lớp)
        $lopHocPhanIds = $giangVien->lopHocPhans()->pluck('lop_hoc_phan.id')->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->route('giangvien.lich-thi.index')
                ->with('error', 'Bạn không có quyền xem danh sách sinh viên đi thi của lớp này!');
        }

        $lichThi->load(['lopHocPhan.monHoc', 'lopHocPhan.hocKy']);

        // Lấy tất cả sinh viên trong lớp học phần
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien'])
            ->get();

        $sinhVienIds = $sinhViens->pluck('id')->toArray();
        $tongSoSinhVien = count($sinhVienIds);

        // Kiểm tra: Phải điểm danh hết tất cả các buổi học đã được giảng viên này điểm danh (trang_thai = 'da_day' và giang_vien_id = giảng viên này)
        $cacBuoiHocDaDienRa = LichHocChiTiet::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->where('ngay_hoc', '<=', now()->toDateString())
            ->where('trang_thai', 'da_day')
            ->where('giang_vien_id', $giangVien->id)
            ->with('caHoc')
            ->get();

        $buoiHocChuaDiemDanhHet = [];
        foreach ($cacBuoiHocDaDienRa as $buoiHoc) {
            // Đếm số sinh viên đã được điểm danh trong buổi học này
            $soSinhVienDaDiemDanh = DiemDanh::where('lich_hoc_chi_tiet_id', $buoiHoc->id)
                ->whereIn('lop_hoc_phan_sinh_vien_id', $sinhVienIds)
                ->count();

            // Nếu số sinh viên đã điểm danh < tổng số sinh viên thì buổi học chưa điểm danh hết
            if ($soSinhVienDaDiemDanh < $tongSoSinhVien) {
                $buoiHocChuaDiemDanhHet[] = [
                    'buoi_hoc' => $buoiHoc,
                    'so_sinh_vien_da_diem_danh' => $soSinhVienDaDiemDanh,
                    'tong_so_sinh_vien' => $tongSoSinhVien,
                    'con_thieu' => $tongSoSinhVien - $soSinhVienDaDiemDanh
                ];
            }
        }

        // Nếu có buổi học chưa điểm danh hết, không cho phép xuất danh sách thi
        if (!empty($buoiHocChuaDiemDanhHet)) {
            $thongBaoLoi = "Không thể xuất danh sách thi. Vui lòng điểm danh hết tất cả các buổi học đã diễn ra.\n\n";
            $thongBaoLoi .= "Các buổi học chưa điểm danh đầy đủ:\n";
            foreach ($buoiHocChuaDiemDanhHet as $buoi) {
                $thongBaoLoi .= "- Buổi học ngày " . Carbon::parse($buoi['buoi_hoc']->ngay_hoc)->format('d/m/Y') .
                    " (Ca " . ($buoi['buoi_hoc']->caHoc->ten_ca ?? 'N/A') . "): " .
                    "Đã điểm danh {$buoi['so_sinh_vien_da_diem_danh']}/{$buoi['tong_so_sinh_vien']} sinh viên. " .
                    "Còn thiếu {$buoi['con_thieu']} sinh viên.\n";
            }

            return redirect()->route('giangvien.lich-thi.show', $lichThi->id)
                ->with('error', $thongBaoLoi);
        }

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        $danhSachSinhVien = [];

        foreach ($sinhViens as $sv) {
            // 1. Kiểm tra chuyên cần (vắng quá 20% = có mặt < 80%)
            // Đếm tổng số buổi đã có điểm danh (unique theo lich_hoc_chi_tiet_id) của TẤT CẢ sinh viên trong lớp
            $tongBuoi = DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) use ($lichThi) {
                $q->where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id);
            })
                ->distinct('lich_hoc_chi_tiet_id')
                ->count('lich_hoc_chi_tiet_id');

            // Đếm số buổi có mặt của sinh viên cụ thể
            $buoiCoMat = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                ->where('trang_thai', 'co_mat')
                ->count();

            // Tính tỷ lệ có mặt
            $tyLeCoMat = $tongBuoi > 0
                ? round(($buoiCoMat / $tongBuoi) * 100, 1)
                : 0;

            // Vắng quá 20% = có mặt < 80%
            $khongDatChuyenCan = $tyLeCoMat < 80;

            // 2. Kiểm tra điểm trung bình các đầu điểm < 5
            $diemTrungBinh = null;
            $khongDatDiem = false;

            if ($cauHinhs->isNotEmpty()) {
                $tongDiem = 0;
                $tongTyLe = 0;
                $coDiem = false;

                foreach ($cauHinhs as $cauHinh) {
                    // Lấy điểm đã nhập
                    $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $sv->id)
                        ->where('cau_hinh_id', $cauHinh->id)
                        ->get();

                    if ($diems->isEmpty()) {
                        continue;
                    }

                    $coDiem = true;

                    // Tính điểm trung bình của đầu điểm
                    $diemTrungBinhDauDiem = $diems->avg('diem_so');

                    if ($diemTrungBinhDauDiem !== null) {
                        $tongDiem += $diemTrungBinhDauDiem * ($cauHinh->ty_le / 100);
                        $tongTyLe += $cauHinh->ty_le;
                    }
                }

                // Tính điểm trung bình (chuẩn hóa về thang 10 nếu tổng tỷ lệ < 100%)
                if ($coDiem && $tongTyLe > 0) {
                    if ($tongTyLe < 100) {
                        $diemTrungBinh = round(($tongDiem / $tongTyLe) * 100, 2);
                    } else {
                        $diemTrungBinh = round($tongDiem, 2);
                    }

                    // Không đạt nếu điểm < 5
                    $khongDatDiem = $diemTrungBinh < 5;
                }
            }

            // Không được đi thi nếu: vắng quá 20% HOẶC điểm < 5
            $khongDuocDiThi = $khongDatChuyenCan || $khongDatDiem;

            $danhSachSinhVien[] = [
                'sinh_vien' => $sv->sinhVien,
                'lop_hanh_chinh' => null, // TODO: Model LopHanhChinh chưa được tạo
                'tong_buoi_hoc' => $tongBuoi,
                'co_mat' => $buoiCoMat,
                'vang' => 0,
                'nghi_phep' => 0,
                'ty_le_co_mat' => $tyLeCoMat,
                'khong_dat_chuyen_can' => $khongDatChuyenCan,
                'diem_trung_binh' => $diemTrungBinh,
                'khong_dat_diem' => $khongDatDiem,
                'khong_duoc_di_thi' => $khongDuocDiThi,
                'ly_do' => $this->taoLyDoKhongDuocDiThi($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh),
            ];
        }

        // Lọc bỏ sinh viên bị cấm thi (chỉ lấy những sinh viên được đi thi)
        $danhSachSinhVienDiThi = array_filter($danhSachSinhVien, function ($sv) {
            return !$sv['khong_duoc_di_thi'];
        });

        // Sắp xếp theo tên
        usort($danhSachSinhVienDiThi, function ($a, $b) {
            return strcmp($a['sinh_vien']->ho_ten, $b['sinh_vien']->ho_ten);
        });

        return view('giangvien.lich-thi.xuat-danh-sach-di-thi', compact(
            'lichThi',
            'danhSachSinhVien',
            'danhSachSinhVienDiThi',
            'tongBuoi'
        ));
    }

    /**
     * Tạo lý do không được đi thi
     */
    private function taoLyDoKhongDuocDiThi($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh)
    {
        $lyDo = [];

        if ($khongDatChuyenCan) {
            $lyDo[] = "Vắng quá 20% số buổi học (Tỷ lệ có mặt: {$tyLeCoMat}%)";
        }

        if ($khongDatDiem && $diemTrungBinh !== null) {
            $lyDo[] = "Điểm trung bình các đầu điểm không đạt 5 điểm (Điểm: {$diemTrungBinh})";
        }

        return implode('; ', $lyDo);
    }
}
