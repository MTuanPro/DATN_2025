<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\GiangVien;
use App\Models\KetQuaHocTap;
use App\Models\LichSuSuaDiem;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\PhanCongGiangDay;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NhapDiemController extends Controller
{
    protected $diemService;

    /**
     * Khởi tạo controller với DiemService dependency injection
     *
     * @param DiemService $diemService Service xử lý logic tính điểm tổng kết
     * @return void
     */
    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Hiển thị danh sách lớp học phần mà giảng viên đang được phân công giảng dạy
     *
     * Lấy tất cả các lớp học phần của giảng viên hiện tại, kèm theo:
     * - Thông tin môn học, học kỳ
     * - Số lượng sinh viên đã/chưa có điểm
     * - Trạng thái lớp (đang học, đã khóa điểm, đã kết thúc...)
     * - Vai trò của giảng viên (chính/phụ)
     *
     * @return \Illuminate\View\View View danh sách lớp học phần với thông tin chi tiết
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy thông tin giảng viên
     */
    public function index()
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp học phần đang dạy
        $lopHocPhans = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->with([
                'lopHocPhan.monHoc',
                'lopHocPhan.hocKy',
                'lopHocPhan.cauHinhDauDiem'
            ])
            ->get()
            ->map(function ($phanCong) {
                $lhp = $phanCong->lopHocPhan;

                // Đếm số sinh viên
                $tongSV = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lhp->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                    ->count();

                // Đếm số sinh viên đã có điểm
                $svCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhp) {
                    $q->where('lop_hoc_phan_id', $lhp->id)
                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
                })
                    ->whereNotNull('diem_he_10')
                    ->count();

                // Kiểm tra lớp đã kết thúc chưa
                $daKetThuc = $lhp->daKetThuc();
                $dangDienRa = $lhp->dangDienRa();

                // Xác định trạng thái hiển thị
                $trangThaiHienThi = $lhp->trang_thai_lop;
                if ($daKetThuc) {
                    $trangThaiHienThi = 'ket_thuc';
                }

                return [
                    'id' => $lhp->id,
                    'ma_lop_hp' => $lhp->ma_lop_hp,
                    'ten_lop_hp' => $lhp->ten_lop_hp,
                    'mon_hoc' => $lhp->monHoc->ten_mon,
                    'hoc_ky' => $lhp->hocKy->ten_hoc_ky,
                    'vai_tro' => $phanCong->vai_tro,
                    'tong_sv' => $tongSV,
                    'sv_co_diem' => $svCoDiem,
                    'ty_le' => $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0,
                    'trang_thai' => $trangThaiHienThi,
                    'trang_thai_lop' => $lhp->trang_thai_lop, // Giữ nguyên để kiểm tra logic khác
                    'da_khoa_diem' => $lhp->trang_thai_lop === 'da_khoa_diem',
                    'da_ket_thuc' => $daKetThuc,
                    'dang_dien_ra' => $dangDienRa,
                ];
            });

        return view('giangvien.nhap-diem.index', compact('lopHocPhans'));
    }

    /**
     * Hiển thị trang nhập điểm chi tiết cho một lớp học phần cụ thể
     *
     * Kiểm tra quyền truy cập của giảng viên, sau đó load:
     * - Thông tin lớp học phần (môn học, học kỳ)
     * - Cấu hình đầu điểm (các loại điểm: chuyên cần, giữa kỳ, cuối kỳ...)
     * - Danh sách sinh viên và điểm đã nhập
     * - Trạng thái khóa/duyệt điểm
     * - Trạng thái gửi điểm lần 1 và lần 2
     *
     * @param int $lopHocPhanId ID của lớp học phần cần nhập điểm
     * @return \Illuminate\View\View View form nhập điểm với đầy đủ thông tin
     * @return \Illuminate\Http\RedirectResponse Redirect về index nếu không có quyền
     */
    public function show($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.nhap-diem.index')
                ->with('error', 'Bạn không có quyền truy cập lớp này');
        }

        // Lấy thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        // Lấy danh sách sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Lấy tất cả điểm đã nhập
        $nhapDiems = NhapDiem::whereIn('lop_hoc_phan_sinh_vien_id', $sinhViens->pluck('id'))
            ->get()
            ->groupBy('lop_hoc_phan_sinh_vien_id');

        // Kiểm tra trạng thái
        $daKhoaDiem = $lopHocPhan->trang_thai_lop === 'da_khoa_diem';
        $daDuyetDiem = $lopHocPhan->trang_thai_lop === 'da_duyet_diem';
        $laGiangVienChinh = $duocPhanCong->vai_tro === 'giang_vien_chinh';

        // Kiểm tra lớp đã kết thúc chưa (dựa vào ngày kết thúc)
        $daKetThuc = $lopHocPhan->daKetThuc();
        $dangDienRa = $lopHocPhan->dangDienRa();

        // Trạng thái 2 lần gửi điểm
        $trangThaiLan1 = $lopHocPhan->trang_thai_gui_diem_lan_1 ?? 'chua_gui';
        $trangThaiLan2 = $lopHocPhan->trang_thai_gui_diem_lan_2 ?? 'chua_gui';
        $choPhepGuiLan1 = $lopHocPhan->cho_phep_gui_diem_lan_1 ?? false;
        $choPhepGuiLan2 = $lopHocPhan->cho_phep_gui_diem_lan_2 ?? false;
        $daGuiLan2 = $trangThaiLan2 === 'da_gui' || $trangThaiLan2 === 'da_duyet';

        return view('giangvien.nhap-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'sinhViens',
            'nhapDiems',
            'daKhoaDiem',
            'daDuyetDiem',
            'laGiangVienChinh',
            'daKetThuc',
            'dangDienRa',
            'trangThaiLan1',
            'trangThaiLan2',
            'choPhepGuiLan1',
            'choPhepGuiLan2',
            'daGuiLan2'
        ));
    }

    /**
     * Nhập hoặc cập nhật điểm thành phần cho sinh viên qua AJAX request
     *
     * Xử lý việc nhập điểm cho một sinh viên ở một đầu điểm cụ thể.
     * Các bước xử lý:
     * 1. Validate dữ liệu đầu vào (lop_hoc_phan_sinh_vien_id, cau_hinh_id, cot_diem, diem_so)
     * 2. Kiểm tra quyền giảng viên được phân công
     * 3. Kiểm tra trạng thái lớp (đã kết thúc, đã khóa điểm, đã duyệt)
     * 4. Kiểm tra cột điểm hợp lệ
     * 5. Insert/Update điểm hoặc xóa điểm nếu giá trị null
     * 6. Tự động tính lại điểm tổng kết
     *
     * @param Request $request Chứa lop_hoc_phan_sinh_vien_id, cau_hinh_id, cot_diem, diem_so, ghi_chu
     * @return \Illuminate\Http\JsonResponse JSON {success, message, data/deleted}
     * @throws \Exception Khi có lỗi trong quá trình xử lý database
     */
    public function nhapDiem(Request $request)
    {
        // Validate - cho phép diem_so null để xóa điểm
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
            'cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
            'cot_diem' => 'required|integer|min:1',
            'diem_so' => 'nullable|numeric|min:0|max:10', // Cho phép null để xóa điểm
            'ghi_chu' => 'nullable|string|max:500',
        ]);

        // Kiểm tra quyền
        $lhpsv = LopHocPhanSinhVien::find($validated['lop_hoc_phan_sinh_vien_id']);
        $giangVien = Auth::user()->giangVien;

        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lhpsv->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền nhập điểm lớp này'
            ], 403);
        }

        // Kiểm tra lớp đã khóa điểm chưa
        $lopHocPhan = LopHocPhan::find($lhpsv->lop_hoc_phan_id);

        // Kiểm tra lớp đã kết thúc chưa - không cho phép sửa điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể sửa điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Kiểm tra quyền: đào tạo có thể sửa điểm sau khi duyệt (phúc khảo)
        $user = Auth::user();
        $laDaoTao = $user->hasAnyRole(['truong_phong_dt', 'nhan_vien_dt']);
        $choPhepSuaDiemSauDuyet = $lopHocPhan->cho_phep_sua_diem_sau_duyet;

        // Không cho phép sửa điểm khi đã gửi cho đào tạo (đang chờ duyệt)
        // Trừ khi là đào tạo và có quyền sửa sau khi duyệt
        if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem') {
            if (!$laDaoTao || !$choPhepSuaDiemSauDuyet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Điểm đã được gửi cho đào tạo. Bạn không thể sửa điểm khi đang chờ duyệt. Nếu cần sửa, vui lòng đợi đào tạo trả về.'
                ], 403);
            }
        }

        // Không cho phép giảng viên sửa điểm khi đã được duyệt và công bố
        // Đào tạo có thể sửa nếu có quyền (phúc khảo)
        if ($lopHocPhan->trang_thai_lop === 'da_duyet_diem') {
            if (!$laDaoTao || !$choPhepSuaDiemSauDuyet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Điểm đã được duyệt và công bố. Bạn không thể sửa điểm sau khi đã duyệt.'
                ], 403);
            }
        }

        // Kiểm tra cột điểm hợp lệ
        $cauHinh = CauHinhDauDiem::find($validated['cau_hinh_id']);

        if ($validated['cot_diem'] > $cauHinh->so_cot) {
            return response()->json([
                'success' => false,
                'message' => "Cột điểm không hợp lệ (max: {$cauHinh->so_cot})"
            ], 400);
        }

        // Kiểm tra nếu là đầu điểm cuối kỳ - chỉ cho phép nhập khi đã mở gửi điểm lần 2
        // (Trừ khi là đào tạo - đào tạo có thể nhập bất cứ lúc nào)
        // CHỈ KIỂM TRA KHI ĐANG NHẬP ĐIỂM (không phải xóa/để trống)
        if (!$laDaoTao && $validated['diem_so'] !== null && $validated['diem_so'] !== '') {
            $tenDauDiem = mb_strtolower($cauHinh->ten_dau_diem ?? '');
            $laDauDiemCuoiKy = str_contains($tenDauDiem, 'cuối kỳ') ||
                str_contains($tenDauDiem, 'cuoi ky') ||
                str_contains($tenDauDiem, 'cuối kì') ||
                str_contains($tenDauDiem, 'cuoi ki');

            if ($laDauDiemCuoiKy && !$lopHocPhan->cho_phep_gui_diem_lan_2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đào tạo chưa mở gửi điểm lần 2 (cuối kỳ). Bạn chỉ có thể nhập điểm cuối kỳ sau khi đào tạo mở gửi điểm lần 2.'
                ], 403);
            }
        }

        try {
            DB::beginTransaction();

            // Nếu diem_so là null hoặc rỗng, xóa điểm
            if ($validated['diem_so'] === null || $validated['diem_so'] === '') {
                $diemCu = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $validated['lop_hoc_phan_sinh_vien_id'])
                    ->where('cau_hinh_id', $validated['cau_hinh_id'])
                    ->where('cot_diem', $validated['cot_diem'])
                    ->first();

                if ($diemCu) {
                    // Lưu lịch sử xóa điểm
                    LichSuSuaDiem::create([
                        'nhap_diem_id' => $diemCu->id,
                        'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                        'cau_hinh_id' => $validated['cau_hinh_id'],
                        'cot_diem' => $validated['cot_diem'],
                        'diem_cu' => $diemCu->diem_so,
                        'diem_moi' => null,
                        'nguoi_sua_id' => Auth::id(),
                        'loai_thao_tac' => 'xoa',
                        'ly_do' => $validated['ghi_chu'] ?? null,
                    ]);
                }

                $deleted = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $validated['lop_hoc_phan_sinh_vien_id'])
                    ->where('cau_hinh_id', $validated['cau_hinh_id'])
                    ->where('cot_diem', $validated['cot_diem'])
                    ->delete();

                // Tự động tính lại điểm tổng
                $this->diemService->tinhDiemTong($validated['lop_hoc_phan_sinh_vien_id']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa điểm thành công',
                    'deleted' => $deleted > 0
                ]);
            }

            // Insert hoặc Update điểm
            $diemCu = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $validated['lop_hoc_phan_sinh_vien_id'])
                ->where('cau_hinh_id', $validated['cau_hinh_id'])
                ->where('cot_diem', $validated['cot_diem'])
                ->first();

            $nhapDiem = NhapDiem::updateOrCreate(
                [
                    'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                    'cau_hinh_id' => $validated['cau_hinh_id'],
                    'cot_diem' => $validated['cot_diem'],
                ],
                [
                    'diem_so' => $validated['diem_so'],
                    'ghi_chu' => $validated['ghi_chu'] ?? null,
                ]
            );

            // Lưu lịch sử sửa điểm
            LichSuSuaDiem::create([
                'nhap_diem_id' => $nhapDiem->id,
                'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                'cau_hinh_id' => $validated['cau_hinh_id'],
                'cot_diem' => $validated['cot_diem'],
                'diem_cu' => $diemCu ? $diemCu->diem_so : null,
                'diem_moi' => $validated['diem_so'],
                'nguoi_sua_id' => Auth::id(),
                'loai_thao_tac' => $diemCu ? 'sua' : 'them',
                'ly_do' => $validated['ghi_chu'] ?? null,
            ]);

            // Tự động tính điểm tổng
            $this->diemService->tinhDiemTong($validated['lop_hoc_phan_sinh_vien_id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã nhập điểm thành công',
                'data' => $nhapDiem
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy điểm tổng kết (TK) của sinh viên qua AJAX, bao gồm cả điểm tạm thời
     *
     * Nếu đã có điểm chính thức (diem_he_10), trả về điểm đó.
     * Nếu chưa có, tính điểm tạm thời dựa trên các điểm thành phần đã nhập.
     *
     * @param Request $request Chứa lop_hoc_phan_sinh_vien_id
     * @return \Illuminate\Http\JsonResponse JSON {success, diem_tk, is_tam_thoi}
     */
    public function getDiemTK(Request $request)
    {
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
        ]);

        // Reload lại để lấy dữ liệu mới nhất
        $lhpsv = LopHocPhanSinhVien::with('ketQuaHocTap')->find($validated['lop_hoc_phan_sinh_vien_id']);
        $ketQua = $lhpsv->ketQuaHocTap;

        // Nếu có điểm chính thức thì trả về
        if ($ketQua && $ketQua->diem_he_10 !== null) {
            return response()->json([
                'success' => true,
                'diem_tk' => number_format($ketQua->diem_he_10, 2),
                'is_tam_thoi' => false
            ]);
        }

        // Nếu chưa có điểm chính thức, tính điểm tạm thời
        $diemTamThoi = $this->diemService->tinhDiemTKTamThoi($validated['lop_hoc_phan_sinh_vien_id']);

        return response()->json([
            'success' => true,
            'diem_tk' => $diemTamThoi !== null ? number_format($diemTamThoi, 2) : '-',
            'is_tam_thoi' => $diemTamThoi !== null
        ]);
    }

    /**
     * Khóa điểm lớp học phần (chỉ giảng viên chính mới được thực hiện)
     *
     * Sau khi khóa điểm, giảng viên không thể sửa điểm nữa.
     * Điểm sẽ được gửi cho đào tạo để duyệt.
     * Các kiểm tra:
     * - Quyền giảng viên chính
     * - Lớp chưa kết thúc
     * - Tất cả sinh viên đã có điểm (hoặc xác nhận khóa dù chưa đủ)
     *
     * @param Request $request Có thể chứa 'confirm' để bỏ qua cảnh báo thiếu điểm
     * @param int $lopHocPhanId ID của lớp học phần cần khóa điểm
     * @return \Illuminate\Http\JsonResponse JSON {success, message, can_confirm?}
     * @throws \Exception Khi có lỗi trong quá trình cập nhật database
     */
    public function khoaDiem(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền khóa điểm'
            ], 403);
        }

        $lopHocPhan = LopHocPhan::find($lopHocPhanId);

        // Kiểm tra lớp đã kết thúc chưa - không cho phép khóa điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể khóa điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Kiểm tra tất cả sinh viên đã có điểm chưa
        $tongSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('trang_thai', 'dang_hoc')
            ->count();

        $sinhVienCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('trang_thai', 'dang_hoc');
        })
            ->whereNotNull('diem_he_10')
            ->count();

        if ($sinhVienCoDiem < $tongSinhVien) {
            $soThieu = $tongSinhVien - $sinhVienCoDiem;
            // Cho phép khóa nếu confirm
            if (!$request->has('confirm')) {
                return response()->json([
                    'success' => false,
                    'message' => "Còn {$soThieu} sinh viên chưa có điểm. Bạn có chắc muốn khóa?",
                    'can_confirm' => true,
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Khóa điểm
            $lopHocPhan->update([
                'trang_thai_lop' => 'da_khoa_diem',
            ]);

            // TODO: Gửi thông báo cho đào tạo
            // $this->guiThongBaoKhoaDiem($lopHocPhanId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã khóa điểm thành công. Chờ Đào tạo duyệt.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mở khóa điểm lớp học phần (khi Đào tạo trả về để chỉnh sửa)
     *
     * Cho phép giảng viên chính mở lại điểm đã khóa để chỉnh sửa.
     * Thường được sử dụng khi đào tạo trả về điểm để giảng viên sửa.
     *
     * @param int $lopHocPhanId ID của lớp học phần cần mở khóa điểm
     * @return \Illuminate\Http\JsonResponse JSON {success, message}
     * @throws \Exception Khi có lỗi trong quá trình cập nhật database
     */
    public function moKhoaDiem($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->exists();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền mở khóa điểm'
            ], 403);
        }

        try {
            $lopHocPhan = LopHocPhan::find($lopHocPhanId);

            if ($lopHocPhan->trang_thai_lop !== 'da_khoa_diem') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp chưa khóa điểm'
                ], 400);
            }

            $lopHocPhan->update([
                'trang_thai_lop' => 'dang_hoc',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã mở khóa điểm thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi điểm cho đào tạo để duyệt (hỗ trợ gửi 2 lần: giữa kỳ và cuối kỳ)
     *
     * Quy trình gửi điểm:
     * 1. Xác định lần gửi (1: giữa kỳ, 2: cuối kỳ) dựa trên tham số hoặc tự động
     * 2. Kiểm tra đào tạo đã mở gửi điểm cho lần đó chưa
     * 3. Kiểm tra tất cả sinh viên đã có điểm (hoặc xác nhận gửi dù chưa đủ)
     * 4. Cập nhật trạng thái gửi điểm và khóa điểm
     * 5. Gửi thông báo cho đào tạo
     *
     * @param Request $request Có thể chứa 'lan_gui' (1 hoặc 2) và 'confirm'
     * @param int $lopHocPhanId ID của lớp học phần cần gửi điểm
     * @return \Illuminate\Http\JsonResponse JSON {success, message, lan_gui, can_confirm?}
     * @throws \Exception Khi có lỗi trong quá trình xử lý
     */
    public function guiDiemChoDaoTao(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền (phải là GV chính)
        $gvChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$gvChinh) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giảng viên chính mới có quyền gửi điểm cho đào tạo'
            ], 403);
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        // Kiểm tra lớp đã kết thúc chưa - không cho phép gửi điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể gửi điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Xác định lần gửi điểm (1: giữa kỳ, 2: cuối kỳ)
        $lanGui = $request->input('lan_gui', null);

        // Nếu không chỉ định, tự động xác định dựa trên trạng thái
        if (!$lanGui) {
            if (!$lopHocPhan->trang_thai_gui_diem_lan_1 || $lopHocPhan->trang_thai_gui_diem_lan_1 === 'chua_gui' || $lopHocPhan->trang_thai_gui_diem_lan_1 === 'da_tra_ve') {
                $lanGui = 1;
            } elseif (!$lopHocPhan->trang_thai_gui_diem_lan_2 || $lopHocPhan->trang_thai_gui_diem_lan_2 === 'chua_gui' || $lopHocPhan->trang_thai_gui_diem_lan_2 === 'da_tra_ve') {
                $lanGui = 2;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã gửi điểm cả 2 lần. Bạn không thể gửi điểm nữa.'
                ], 403);
            }
        }

        // Kiểm tra đào tạo đã mở gửi điểm cho lần này chưa
        if ($lanGui == 1 && !$lopHocPhan->cho_phep_gui_diem_lan_1) {
            return response()->json([
                'success' => false,
                'message' => 'Đào tạo chưa mở gửi điểm lần 1 (giữa kỳ). Vui lòng đợi đào tạo mở.'
            ], 403);
        }

        if ($lanGui == 2 && !$lopHocPhan->cho_phep_gui_diem_lan_2) {
            return response()->json([
                'success' => false,
                'message' => 'Đào tạo chưa mở gửi điểm lần 2 (cuối kỳ). Vui lòng đợi đào tạo mở.'
            ], 403);
        }

        // Kiểm tra đã gửi lần 2 chưa - nếu đã gửi thì không cho gửi nữa
        if ($lopHocPhan->trang_thai_gui_diem_lan_2 === 'da_gui' || $lopHocPhan->trang_thai_gui_diem_lan_2 === 'da_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Đã gửi điểm lần 2 (cuối kỳ). Bạn không thể gửi điểm nữa.'
            ], 403);
        }

        // Kiểm tra tất cả sinh viên đã có điểm chưa
        $tongSinhVien = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->count();

        $sinhVienCoDiem = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
        })
            ->whereNotNull('diem_he_10')
            ->count();

        if ($sinhVienCoDiem < $tongSinhVien) {
            $soThieu = $tongSinhVien - $sinhVienCoDiem;
            // Cho phép gửi nếu confirm
            if (!$request->has('confirm')) {
                return response()->json([
                    'success' => false,
                    'message' => "Còn {$soThieu} sinh viên chưa có điểm. Bạn có chắc muốn gửi điểm?",
                    'can_confirm' => true,
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Cập nhật trạng thái gửi điểm theo lần
            $updateData = [
                'lan_gui_diem' => $lanGui,
                'ly_do_tra_ve' => null, // Xóa lý do trả về nếu có
            ];

            if ($lanGui == 1) {
                $updateData['trang_thai_gui_diem_lan_1'] = 'da_gui';
                $updateData['trang_thai_lop'] = 'da_khoa_diem';
            } else {
                $updateData['trang_thai_gui_diem_lan_2'] = 'da_gui';
                $updateData['trang_thai_lop'] = 'da_khoa_diem';
                // Sau khi gửi lần 2, cập nhật điểm kể cả sửa điểm lần 1
                // Điểm sẽ được tính lại từ tất cả các đầu điểm
            }

            $lopHocPhan->update($updateData);

            // Gửi thông báo cho đào tạo
            $this->guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien, $lanGui);

            DB::commit();

            $lanGuiText = $lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
            $message = "Đã gửi điểm {$lanGuiText} cho đào tạo thành công. Chờ đào tạo duyệt.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'lan_gui' => $lanGui
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi thông báo cho đào tạo khi giảng viên gửi điểm
     */
    private function guiThongBaoGuiDiemChoDaoTao($lopHocPhan, $giangVien, $lanGui = 1)
    {
        // Lấy tất cả tài khoản đào tạo
        $daoTaos = \App\Models\DaoTao::with('user')->get();

        if ($daoTaos->isEmpty()) {
            return;
        }

        // Tạo thông báo
        $lanGuiText = $lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
        $tieuDe = 'Giảng viên gửi điểm ' . $lanGuiText . ' lớp ' . $lopHocPhan->ma_lop_hp;

        $noiDung = "Giảng viên {$giangVien->ho_ten} đã gửi điểm {$lanGuiText} lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} ({$lopHocPhan->hocKy->ten_hoc_ky}) để duyệt. Vui lòng truy cập phần 'Duyệt điểm' để xem và duyệt.";

        $thongBao = ThongBao::create([
            'tieu_de' => $tieuDe,
            'noi_dung' => $noiDung,
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'quan_trong',
            'doi_tuong' => 'dao_tao',
            'nguoi_gui_id' => $giangVien->user_id ?? Auth::id(),
            'ngay_gui' => now(),
            'lien_ket_loai' => 'diem',
            'lien_ket_id' => $lopHocPhan->id,
        ]);

        // Gửi cho tất cả đào tạo
        foreach ($daoTaos as $daoTao) {
            if ($daoTao->user) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $daoTao->user_id,
                    'da_doc' => false,
                ]);
            }
        }
    }

    /**
     * Tải xuống file Excel mẫu để nhập điểm hàng loạt
     *
     * File Excel bao gồm:
     * - Header với tên các đầu điểm và số cột tương ứng
     * - Danh sách sinh viên (STT, MSSV, Họ tên)
     * - Điểm đã nhập (nếu có) để giảng viên có thể chỉnh sửa
     * - Format chuẩn để import lại vào hệ thống
     *
     * @param int $lopHocPhanId ID của lớp học phần cần tải template
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File Excel download
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền
     */
    public function downloadTemplate($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.nhap-diem.index')
                ->with('error', 'Bạn không có quyền truy cập lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc'])->find($lopHocPhanId);
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Tạo file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nhập điểm');

        // Header row
        $headers = ['STT', 'MSSV', 'Họ tên'];
        $colIndex = 4; // Bắt đầu từ cột D

        foreach ($cauHinhs as $cauHinh) {
            if ($cauHinh->so_cot > 1) {
                for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                    $headers[] = $cauHinh->ten_dau_diem . ' - Cột ' . $cot;
                }
            } else {
                $headers[] = $cauHinh->ten_dau_diem;
            }
        }

        // Ghi header
        $sheet->fromArray([$headers], null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1')->applyFromArray($headerStyle);

        // Ghi dữ liệu sinh viên
        $row = 2;
        foreach ($sinhViens as $index => $lhpsv) {
            $rowData = [
                $index + 1,
                $lhpsv->sinhVien->ma_sinh_vien,
                $lhpsv->sinhVien->ho_ten,
            ];

            // Lấy điểm đã nhập
            $nhapDiems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->get();
            $diemMap = [];
            foreach ($nhapDiems as $diem) {
                $key = $diem->cau_hinh_id . '_' . $diem->cot_diem;
                $diemMap[$key] = $diem->diem_so;
            }

            // Thêm điểm vào row
            foreach ($cauHinhs as $cauHinh) {
                for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                    $key = $cauHinh->id . '_' . $cot;
                    $rowData[] = $diemMap[$key] ?? '';
                }
            }

            $sheet->fromArray([$rowData], null, 'A' . $row);
            $row++;
        }

        // Auto size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tạo file và download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Nhap_Diem_' . $lopHocPhan->ma_lop_hp . '_' . date('YmdHis') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Import điểm hàng loạt từ file Excel
     *
     * Quy trình import:
     * 1. Validate file Excel (định dạng, kích thước)
     * 2. Kiểm tra quyền và trạng thái lớp (khóa điểm, duyệt điểm, kết thúc)
     * 3. Đọc dữ liệu từ Excel theo format template
     * 4. Validate từng dòng điểm (MSSV hợp lệ, điểm 0-10)
     * 5. Insert/Update điểm vào database
     * 6. Tự động tính lại điểm tổng kết cho từng sinh viên
     * 7. Trả về thống kê số điểm import thành công và danh sách lỗi (nếu có)
     *
     * @param Request $request Chứa file Excel (mimes: xlsx, xls, max: 5MB)
     * @param int $lopHocPhanId ID của lớp học phần cần import điểm
     * @return \Illuminate\Http\JsonResponse JSON {success, message, imported, errors, total_errors}
     * @throws \Exception Khi có lỗi đọc file hoặc xử lý database
     */
    public function importExcel(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->exists();

        if (!$duocPhanCong) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền nhập điểm lớp này'
            ], 403);
        }

        // Validate file
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        $lopHocPhan = LopHocPhan::find($lopHocPhanId);

        // Kiểm tra lớp đã kết thúc chưa - không cho phép import điểm sau khi lớp kết thúc
        if ($lopHocPhan->daKetThuc()) {
            return response()->json([
                'success' => false,
                'message' => 'Lớp học phần đã kết thúc. Bạn không thể import điểm sau khi lớp kết thúc.'
            ], 403);
        }

        // Kiểm tra quyền: đào tạo có thể sửa điểm sau khi duyệt (phúc khảo)
        $user = Auth::user();
        $laDaoTao = $user->hasAnyRole(['truong_phong_dt', 'nhan_vien_dt']);
        $choPhepSuaDiemSauDuyet = $lopHocPhan->cho_phep_sua_diem_sau_duyet;

        // Không cho phép import điểm khi đã gửi cho đào tạo (đang chờ duyệt)
        // Trừ khi là đào tạo và có quyền sửa sau khi duyệt
        if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem') {
            if (!$laDaoTao || !$choPhepSuaDiemSauDuyet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Điểm đã được gửi cho đào tạo. Bạn không thể import điểm khi đang chờ duyệt. Nếu cần sửa, vui lòng đợi đào tạo trả về.'
                ], 403);
            }
        }

        // Không cho phép giảng viên import điểm khi đã được duyệt và công bố
        // Đào tạo có thể import nếu có quyền (phúc khảo)
        if ($lopHocPhan->trang_thai_lop === 'da_duyet_diem') {
            if (!$laDaoTao || !$choPhepSuaDiemSauDuyet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Điểm đã được duyệt và công bố. Bạn không thể import điểm sau khi đã duyệt.'
                ], 403);
            }
        }

        try {
            DB::beginTransaction();

            // Đọc file Excel
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            // Bỏ qua header (dòng đầu tiên)
            array_shift($data);

            // Lấy cấu hình đầu điểm
            $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
                ->orderBy('id')
                ->get();

            // Lấy danh sách sinh viên
            $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->with(['sinhVien'])
                ->get()
                ->keyBy(function ($item) {
                    return $item->sinhVien->ma_sinh_vien;
                });

            $imported = 0;
            $errors = [];
            $colIndex = 3; // Bắt đầu từ cột D (index 3)

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Bỏ qua dòng trống
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                $maSV = trim($row[1]);
                $sinhVien = $sinhViens->get($maSV);

                if (!$sinhVien) {
                    $errors[] = "Dòng {$rowNum}: Không tìm thấy sinh viên có MSSV: {$maSV}";
                    continue;
                }

                $colIndex = 3; // Reset về cột D
                foreach ($cauHinhs as $cauHinh) {
                    for ($cot = 1; $cot <= $cauHinh->so_cot; $cot++) {
                        $diemValue = $row[$colIndex] ?? null;
                        $colIndex++;

                        // Bỏ qua nếu không có giá trị
                        if ($diemValue === null || $diemValue === '') {
                            continue;
                        }

                        // Validate điểm
                        $diemSo = is_numeric($diemValue) ? (float)$diemValue : null;
                        if ($diemSo === null || $diemSo < 0 || $diemSo > 10) {
                            $errors[] = "Dòng {$rowNum} - {$cauHinh->ten_dau_diem} (Cột {$cot}): Điểm không hợp lệ ({$diemValue})";
                            continue;
                        }

                        // Lưu điểm
                        try {
                            NhapDiem::updateOrCreate(
                                [
                                    'lop_hoc_phan_sinh_vien_id' => $sinhVien->id,
                                    'cau_hinh_id' => $cauHinh->id,
                                    'cot_diem' => $cot,
                                ],
                                [
                                    'diem_so' => $diemSo,
                                ]
                            );

                            // Tự động tính điểm tổng
                            $this->diemService->tinhDiemTong($sinhVien->id);
                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum} - {$cauHinh->ten_dau_diem} (Cột {$cot}): " . $e->getMessage();
                        }
                    }
                }
            }

            DB::commit();

            $message = "Import thành công {$imported} điểm.";
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'errors' => array_slice($errors, 0, 10), // Chỉ hiển thị 10 lỗi đầu
                'total_errors' => count($errors),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất danh sách sinh viên đủ điều kiện thi
     * 
     * Điều kiện:
     * - Trung bình các đầu điểm đã nhập (chưa tính điểm thi) > 5
     * - Không vắng quá 20% số buổi đã điểm danh
     */
    public function xuatDanhSachThi($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $duocPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$duocPhanCong) {
            return redirect()->route('giangvien.nhap-diem.index')
                ->with('error', 'Bạn không có quyền truy cập lớp này');
        }

        // Lấy thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        // Kiểm tra số buổi học và số buổi điểm danh
        $tongSoBuoiHoc = \App\Models\LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)->count();
        $soBuoiDaDiemDanh = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function ($q) use ($lopHocPhanId) {
            $q->where('lop_hoc_phan_id', $lopHocPhanId);
        })
            ->distinct('lich_hoc_chi_tiet_id')
            ->count('lich_hoc_chi_tiet_id');

        // Nếu chưa điểm danh đủ số buổi, không cho phép xuất danh sách thi
        if ($soBuoiDaDiemDanh < $tongSoBuoiHoc) {
            return redirect()->route('giangvien.nhap-diem.show', $lopHocPhanId)
                ->with('error', "Chưa thể xuất danh sách thi. Lớp học cần điểm danh đủ {$tongSoBuoiHoc} buổi (hiện tại đã điểm danh {$soBuoiDaDiemDanh}/{$tongSoBuoiHoc} buổi).");
        }

        // Lấy cấu hình đầu điểm
        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        // Lấy danh sách sinh viên
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with(['sinhVien', 'ketQuaHocTap'])
            ->orderBy('sinh_vien_id')
            ->get();

        // Lấy danh sách lịch thi của lớp học phần này
        $lichThis = \App\Models\LichThi::where('lop_hoc_phan_id', $lopHocPhanId)
            ->with(['phongThi', 'caHoc'])
            ->orderBy('ngay_thi')
            ->get();

        $danhSachDuDieuKien = [];
        $danhSachKhongDuDieuKien = [];

        foreach ($sinhViens as $lhpsv) {
            // 1. Tính điểm trung bình các đầu điểm đã nhập (không tính điểm thi cuối kỳ)
            $diemDaNhap = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->get();

            $tongDiem = 0;
            $tongTyLe = 0;

            foreach ($cauHinhs as $cauHinh) {
                // Bỏ qua đầu điểm cuối kỳ/thi
                if (
                    stripos($cauHinh->ten_dau_diem, 'cuối kỳ') !== false ||
                    stripos($cauHinh->ten_dau_diem, 'thi') !== false
                ) {
                    continue;
                }

                $diemCauHinh = $diemDaNhap->where('cau_hinh_id', $cauHinh->id);

                if ($diemCauHinh->isEmpty()) {
                    continue;
                }

                // Tính trung bình cộng các cột của đầu điểm này
                $trungBinhCot = $diemCauHinh->avg('diem_so');

                if ($trungBinhCot !== null) {
                    $tongDiem += $trungBinhCot * $cauHinh->ty_le;
                    $tongTyLe += $cauHinh->ty_le;
                }
            }

            $diemTrungBinh = $tongTyLe > 0 ? $tongDiem / $tongTyLe : 0;

            // 2. Kiểm tra tỷ lệ vắng
            // Lấy tổng số buổi học dự kiến của lớp
            $tongSoBuoiHocDuKien = \App\Models\LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
                ->count();
            
            // Đếm số buổi học đã diễn ra (để tính tỷ lệ vắng chính xác)
            $soBuoiHocDaDienRa = \App\Models\LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhanId)
                ->whereDate('ngay_hoc', '<=', now())
                ->count();

            $soBuoiVang = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                ->where('trang_thai', 'vang')
                ->count();

            // Tính tỷ lệ vắng dựa trên số buổi đã diễn ra, nếu chưa có buổi nào thì dựa trên tổng số buổi dự kiến
            $soBuoiTinhTyLe = $soBuoiHocDaDienRa > 0 ? $soBuoiHocDaDienRa : $tongSoBuoiHocDuKien;
            $tyLeVang = $soBuoiTinhTyLe > 0 ? ($soBuoiVang / $soBuoiTinhTyLe) * 100 : 0;

            // 3. Xác định đủ điều kiện hay không
            $duDieuKien = $diemTrungBinh >= 5 && $tyLeVang <= 20;

            $thongTin = [
                'lhpsv' => $lhpsv,
                'diem_trung_binh' => round($diemTrungBinh, 2),
                'so_buoi_vang' => $soBuoiVang,
                'tong_buoi' => $tongSoBuoiHocDuKien,
                'ty_le_vang' => round($tyLeVang, 2),
                'du_dieu_kien' => $duDieuKien,
                'ly_do' => []
            ];

            if ($diemTrungBinh < 5) {
                $thongTin['ly_do'][] = 'Điểm TB < 5.0';
            }
            if ($tyLeVang > 20) {
                $thongTin['ly_do'][] = 'Vắng > 20%';
            }

            if ($duDieuKien) {
                $danhSachDuDieuKien[] = $thongTin;
            } else {
                $danhSachKhongDuDieuKien[] = $thongTin;
            }
        }

        return view('giangvien.nhap-diem.xuat-danh-sach-thi', compact(
            'lopHocPhan',
            'cauHinhs',
            'danhSachDuDieuKien',
            'danhSachKhongDuDieuKien',
            'lichThis'
        ));
    }

    /**
     * Thêm sinh viên đủ điều kiện vào danh sách thi
     */
    public function themVaoDanhSachThi(Request $request, $lopHocPhanId)
    {
        try {
            $lichThiId = $request->input('lich_thi_id');
            $sinhVienIds = $request->input('sinh_vien_ids', []);

            if (empty($lichThiId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn lịch thi'
                ], 400);
            }

            if (empty($sinhVienIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn sinh viên'
                ], 400);
            }

            $lichThi = \App\Models\LichThi::find($lichThiId);
            if (!$lichThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy lịch thi'
                ], 404);
            }

            DB::beginTransaction();

            $soLuongThem = 0;
            $soLuongDaTonTai = 0;

            foreach ($sinhVienIds as $sinhVienId) {
                // Kiểm tra sinh viên đã có trong danh sách thi chưa
                $daTonTai = \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThiId)
                    ->where('sinh_vien_id', $sinhVienId)
                    ->exists();

                if ($daTonTai) {
                    $soLuongDaTonTai++;
                    continue;
                }

                // Thêm sinh viên vào danh sách thi
                \App\Models\LichThiSinhVien::create([
                    'lich_thi_id' => $lichThiId,
                    'sinh_vien_id' => $sinhVienId,
                    'phong_thi_id' => $lichThi->phong_thi_id,
                    'trang_thai' => 'du_thi'
                ]);

                $soLuongThem++;
            }

            // Cập nhật số sinh viên dự thi trong lịch thi
            $tongSinhVien = \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThiId)->count();
            $lichThi->update(['so_sinh_vien_du_thi' => $tongSinhVien]);

            DB::commit();

            $message = "Đã thêm {$soLuongThem} sinh viên vào danh sách thi.";
            if ($soLuongDaTonTai > 0) {
                $message .= " ({$soLuongDaTonTai} sinh viên đã có trong danh sách)";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'so_luong_them' => $soLuongThem,
                'so_luong_da_ton_tai' => $soLuongDaTonTai
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
