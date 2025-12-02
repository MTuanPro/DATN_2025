<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhanCongGiangDay;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\HocKy;
use App\Models\LichHocCoDinh;
use App\Models\KetQuaHocTap;
use App\Models\NhapDiem;
use App\Models\CauHinhDauDiem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeachingClassController extends Controller
{
    /**
     * Hiển thị danh sách lớp học phần được phân công
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Lấy danh sách học kỳ để filter
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Filter theo học kỳ
        $hocKyId = $request->get('hoc_ky_id');
        
        // Lấy danh sách lớp được phân công
        $query = PhanCongGiangDay::with([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'lopHocPhan.giangVienChinh.giangVien',
            'giangVien'
        ])
        ->where('giang_vien_id', $giangVien->id)
        // Chỉ lấy lớp có môn học hợp lệ
        ->whereHas('lopHocPhan', function ($q) {
            $q->whereNotNull('mon_hoc_id')
              ->whereHas('monHoc');
        });

        if ($hocKyId) {
            $query->whereHas('lopHocPhan', function ($q) use ($hocKyId) {
                $q->where('hoc_ky_id', $hocKyId);
            });
        }

        $phanCongs = $query->orderBy('created_at', 'desc')->paginate(15);

        // Thêm thông tin số sinh viên, tiến độ nhập điểm cho mỗi lớp
        foreach ($phanCongs as $phanCong) {
            $lopHocPhanId = $phanCong->lop_hoc_phan_id;
            
            // Số sinh viên
            $tongSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->count();
            
            // Số sinh viên đã có điểm (có ít nhất 1 điểm đã nhập)
            $svCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
                $q->where('lop_hoc_phan_id', $lopHocPhanId)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
            })
                ->whereNotNull('diem_he_10')
                ->count();
            
            // Kiểm tra lớp đã kết thúc chưa
            $daKetThuc = $phanCong->lopHocPhan->daKetThuc();
            $dangDienRa = $phanCong->lopHocPhan->dangDienRa();
            
            $phanCong->lopHocPhan->so_sinh_vien = $tongSV;
            $phanCong->lopHocPhan->sv_co_diem = $svCoDiem;
            $phanCong->lopHocPhan->ty_le_nhap_diem = $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0;
            $phanCong->lopHocPhan->da_ket_thuc = $daKetThuc;
            $phanCong->lopHocPhan->dang_dien_ra = $dangDienRa;
            $phanCong->lopHocPhan->da_khoa_diem = $phanCong->lopHocPhan->trang_thai_lop === 'da_khoa_diem';
        }

        return view('giangvien.lop-giang-day.index', compact('phanCongs', 'hocKys', 'hocKyId'));
    }

    /**
     * Hiển thị chi tiết lớp học phần và danh sách sinh viên
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền: giảng viên phải được phân công dạy lớp này
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        // Load thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with([
            'monHoc',
            'hocKy',
            'lopHocPhanGiangVien.giangVien',
            'cauHinhDauDiem'
        ])->findOrFail($id);

        // Lấy danh sách sinh viên trong lớp
        // Logic: 
        // - Nếu lớp đang diễn ra: chỉ hiển thị sinh viên "Đã xếp lớp" và "Đang học"
        // - Nếu lớp đã kết thúc: hiển thị cả "Đã hoàn thành" để xem lịch sử
        $trangThaiLop = $lopHocPhan->trang_thai_lop;
        $trangThaiSinhVien = ['da_xep_lop', 'dang_hoc'];
        
        // Nếu lớp đã kết thúc hoặc đã duyệt điểm, hiển thị cả sinh viên đã hoàn thành
        if (in_array($trangThaiLop, ['ket_thuc', 'da_duyet_diem', 'da_khoa_diem'])) {
            $trangThaiSinhVien[] = 'da_hoan_thanh';
        }
        
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', $trangThaiSinhVien)
        ->orderBy('trang_thai', 'asc')
        ->get();

        // Lấy thông tin lịch học cố định
        $lichHocCoDinh = LichHocCoDinh::where('lop_hoc_phan_id', $id)
            ->with('phongHoc')
            ->get();

        // Lấy dữ liệu kết quả học tập
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $id)->get();
        
        $danhSachSinhVienKetQua = null;
        $thongKe = null;
        
        if ($cauHinhs->isNotEmpty()) {
            // Lấy danh sách sinh viên với điểm
            $danhSachSinhVienKetQua = LopHocPhanSinhVien::where('lop_hoc_phan_id', $id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->with(['sinhVien', 'ketQuaHocTap'])
                ->get()
                ->map(function ($lhpsv) use ($cauHinhs) {
                    // Lấy tất cả điểm đã nhập
                    $danhSachDiem = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                        ->with('cauHinh')
                        ->get();
                    
                    $lhpsv->danh_sach_diem = $danhSachDiem;
                    
                    // Lấy điểm tổng kết từ ket_qua_hoc_tap
                    $ketQua = $lhpsv->ketQuaHocTap;
                    $lhpsv->diem_tong_ket = $ketQua ? $ketQua->diem_he_10 : null;
                    $lhpsv->diem_chu = $ketQua ? $ketQua->diem_chu : null;
                    $lhpsv->diem_he_4 = $ketQua ? $ketQua->diem_he_4 : null;
                    $lhpsv->xep_loai = $ketQua ? $ketQua->xep_loai : null;
                    
                    return $lhpsv;
                })
                ->sortBy('sinhVien.ma_sinh_vien');

            // Thống kê phân bố điểm
            $thongKe = [
                'tong_sv' => $danhSachSinhVienKetQua->count(),
                'sv_co_diem' => $danhSachSinhVienKetQua->filter(fn($sv) => $sv->diem_tong_ket !== null)->count(),
                'sv_qua_mon' => $danhSachSinhVienKetQua->filter(fn($sv) => $sv->diem_tong_ket !== null && $sv->diem_tong_ket >= 4)->count(),
                'sv_truot' => $danhSachSinhVienKetQua->filter(fn($sv) => $sv->diem_tong_ket !== null && $sv->diem_tong_ket < 4)->count(),
                'diem_cao_nhat' => $danhSachSinhVienKetQua->where('diem_tong_ket', '!=', null)->max('diem_tong_ket') ?? 0,
                'diem_thap_nhat' => $danhSachSinhVienKetQua->where('diem_tong_ket', '!=', null)->min('diem_tong_ket') ?? 0,
                'diem_trung_binh' => $danhSachSinhVienKetQua->where('diem_tong_ket', '!=', null)->avg('diem_tong_ket') ?? 0,
            ];
        }

        return view('giangvien.lop-giang-day.show', compact(
            'lopHocPhan',
            'phanCong',
            'sinhViens',
            'lichHocCoDinh',
            'cauHinhs',
            'danhSachSinhVienKetQua',
            'thongKe'
        ));
    }

    /**
     * Xuất danh sách sinh viên ra Excel
     */
    public function exportStudents(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($id);
        
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
        ->get();

        // Tạo file CSV
        $fileName = 'danh-sach-sinh-vien-' . $lopHocPhan->ma_lop_hp . '-' . Carbon::now()->format('YmdHis') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($sinhViens, $lopHocPhan) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'STT',
                'Mã sinh viên',
                'Họ và tên',
                'Email',
                'Số điện thoại',
                'Lớp hành chính',
                'Trạng thái',
                'Ngày đăng ký',
                'Ngày xếp lớp'
            ]);

            // Data
            $stt = 1;
            foreach ($sinhViens as $lhpsv) {
                fputcsv($file, [
                    $stt++,
                    $lhpsv->sinhVien->ma_sinh_vien ?? '',
                    $lhpsv->sinhVien->ho_ten ?? '',
                    $lhpsv->sinhVien->email ?? '',
                    $lhpsv->sinhVien->so_dien_thoai ?? '',
                    $lhpsv->sinhVien->lopHanhChinh->ma_lop ?? '',
                    $this->getTrangThaiText($lhpsv->trang_thai),
                    $lhpsv->ngay_dang_ky ? $lhpsv->ngay_dang_ky->format('d/m/Y H:i') : '',
                    $lhpsv->ngay_xep_lop ? $lhpsv->ngay_xep_lop->format('d/m/Y H:i') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất danh sách sinh viên ra PDF
     */
    public function exportStudentsPdf(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $id)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($id);
        
        $sinhViens = LopHocPhanSinhVien::with([
            'sinhVien.lopHanhChinh',
            'ketQuaHocTap'
        ])
        ->where('lop_hoc_phan_id', $id)
        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
        ->get();

        // Return view for PDF printing
        return view('giangvien.lop-giang-day.export-pdf', compact('lopHocPhan', 'sinhViens', 'giangVien'));
    }

    /**
     * Xem chi tiết sinh viên và bảng điểm
     */
    public function showStudent($lopHocPhanId, $sinhVienId)
    {
        $user = request()->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Kiểm tra quyền: giảng viên phải được phân công dạy lớp này
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->firstOrFail();

        // Lấy thông tin sinh viên
        $sinhVien = \App\Models\DaoTao\SinhVien::with(['lopHanhChinh', 'user'])
            ->findOrFail($sinhVienId);

        // Lấy tất cả môn học của sinh viên (đã có điểm hoặc đang học)
        $monHocs = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'ketQuaHocTap'
            ])
            ->orderBy('lopHocPhan.hoc_ky_id', 'desc')
            ->orderBy('lopHocPhan.mon_hoc_id')
            ->get()
            ->map(function ($lhpsv) {
                $ketQua = $lhpsv->ketQuaHocTap;
                return [
                    'id' => $lhpsv->id,
                    'ma_lop_hp' => $lhpsv->lopHocPhan->ma_lop_hp,
                    'ten_lop_hp' => $lhpsv->lopHocPhan->ten_lop_hp,
                    'ma_mon' => $lhpsv->lopHocPhan->monHoc->ma_mon,
                    'ten_mon' => $lhpsv->lopHocPhan->monHoc->ten_mon,
                    'so_tin_chi' => $lhpsv->lopHocPhan->monHoc->so_tin_chi,
                    'hoc_ky' => $lhpsv->lopHocPhan->hocKy->ten_hoc_ky,
                    'nam_hoc' => $lhpsv->lopHocPhan->hocKy->nam_hoc,
                    'diem_he_10' => $ketQua ? $ketQua->diem_he_10 : null,
                    'diem_he_4' => $ketQua ? $ketQua->diem_he_4 : null,
                    'diem_chu' => $ketQua ? $ketQua->diem_chu : null,
                    'xep_loai' => $ketQua ? $ketQua->xep_loai : null,
                    'qua_mon' => $ketQua ? ($ketQua->diem_he_10 >= 4) : null,
                    'trang_thai_lop' => $lhpsv->lopHocPhan->trang_thai_lop,
                ];
            });

        // Tính GPA tích lũy
        $diemService = app(\App\Services\DiemService::class);
        $gpaTichLuy = $diemService->tinhGPATichLuy($sinhVienId);
        $tongTinChiDat = $diemService->tinhTongTinChiDat($sinhVienId);
        $tongTinChiHoc = $monHocs->sum('so_tin_chi');

        // Nhóm theo học kỳ
        $monHocsTheoHocKy = $monHocs->groupBy(function ($item) {
            return $item['hoc_ky'] . ' - ' . $item['nam_hoc'];
        });

        return response()->json([
            'success' => true,
            'sinhVien' => [
                'id' => $sinhVien->id,
                'ma_sinh_vien' => $sinhVien->ma_sinh_vien,
                'ho_ten' => $sinhVien->ho_ten,
                'email' => $sinhVien->email,
                'so_dien_thoai' => $sinhVien->so_dien_thoai,
                'lop_hanh_chinh' => $sinhVien->lopHanhChinh->ma_lop ?? 'N/A',
                'ngay_sinh' => $sinhVien->ngay_sinh ? \Carbon\Carbon::parse($sinhVien->ngay_sinh)->format('d/m/Y') : 'N/A',
                'gioi_tinh' => $sinhVien->gioi_tinh,
            ],
            'monHocs' => $monHocs->values(),
            'monHocsTheoHocKy' => $monHocsTheoHocKy->map(function ($group) {
                return $group->values();
            }),
            'thongKe' => [
                'tong_mon' => $monHocs->count(),
                'tong_tin_chi_hoc' => $tongTinChiHoc,
                'tong_tin_chi_dat' => $tongTinChiDat,
                'gpa_tich_luy' => $gpaTichLuy,
                'so_mon_qua' => $monHocs->where('qua_mon', true)->count(),
                'so_mon_truot' => $monHocs->where('qua_mon', false)->count(),
            ]
        ]);
    }

    /**
     * Helper: Convert trạng thái to Vietnamese text
     */
    private function getTrangThaiText($trangThai)
    {
        $mapping = [
            'da_xep_lop' => 'Đã xếp lớp',
            'dang_hoc' => 'Đang học',
            'da_hoan_thanh' => 'Đã hoàn thành',
            'bo_hoc' => 'Bỏ học',
            'huy_dang_ky' => 'Hủy đăng ký'
        ];

        return $mapping[$trangThai] ?? $trangThai;
    }
}
