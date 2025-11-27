<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\HocKy;
use App\Models\KetQuaHocTap;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\NhapDiem;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Services\DiemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DuyetDiemController extends Controller
{
    protected $diemService;

    public function __construct(DiemService $diemService)
    {
        $this->diemService = $diemService;
    }

    /**
     * Danh sách lớp học phần cần duyệt điểm
     */
    public function index(Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');
        $trangThai = $request->get('trang_thai', 'da_khoa_diem');

        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->limit(5)->get();

        // Query lớp học phần
        $query = LopHocPhan::with(['monHoc', 'hocKy']);

        // Filter theo học kỳ
        if ($hocKyId) {
            $query->where('hoc_ky_id', $hocKyId);
        }

        // Filter theo trạng thái
        if ($trangThai) {
            $query->where('trang_thai_lop', $trangThai);
        }

        $lopHocPhans = $query->get()->map(function ($lhp) {
            // Đếm số sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
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

            // Điểm trung bình lớp
            $diemTB = KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhp) {
                $q->where('lop_hoc_phan_id', $lhp->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh']);
            })
                ->whereNotNull('diem_he_10')
                ->avg('diem_he_10');

            return [
                'id' => $lhp->id,
                'ma_lop_hp' => $lhp->ma_lop_hp,
                'ten_lop_hp' => $lhp->ten_lop_hp,
                'mon_hoc' => $lhp->monHoc->ten_mon,
                'hoc_ky' => $lhp->hocKy->ten_hoc_ky,
                'tong_sv' => $tongSV,
                'sv_co_diem' => $svCoDiem,
                'ty_le' => $tongSV > 0 ? round($svCoDiem / $tongSV * 100, 1) : 0,
                'diem_tb' => $diemTB ? round($diemTB, 2) : null,
                'trang_thai' => $lhp->trang_thai_lop,
            ];
        });

        return view('daotao.duyet-diem.index', compact('lopHocPhans', 'hocKys', 'hocKyId', 'trangThai'));
    }
/**
     * Xem chi tiết bảng điểm
     */
    public function show($lopHocPhanId)
    {
        // Lấy thông tin lớp học phần
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        if (!$lopHocPhan) {
            return redirect()->route('daotao.duyet-diem.index')
                ->with('error', 'Không tìm thấy lớp học phần');
        }

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

        // Thống kê
        $thongKe = [
            'tong_sv' => $sinhViens->count(),
            'sv_co_diem' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->diem_he_10 !== null;
            })->count(),
            'sv_qua_mon' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->qua_mon;
            })->count(),
            'sv_khong_qua_mon' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && !$sv->ketQuaHocTap->qua_mon;
            })->count(),
            'diem_tb' => $sinhViens->filter(function ($sv) {
                return $sv->ketQuaHocTap && $sv->ketQuaHocTap->diem_he_10 !== null;
            })->avg(function ($sv) {
                return $sv->ketQuaHocTap->diem_he_10;
            }),
        ];

        // Kiểm tra quyền sửa điểm sau khi duyệt
        $choPhepSuaDiem = $lopHocPhan->cho_phep_sua_diem_sau_duyet ?? false;
        $daDuyetCa2Lan = ($lopHocPhan->trang_thai_gui_diem_lan_1 === 'da_duyet') && 
                         ($lopHocPhan->trang_thai_gui_diem_lan_2 === 'da_duyet');

        return view('daotao.duyet-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'sinhViens',
            'nhapDiems',
            'thongKe',
            'choPhepSuaDiem',
            'daDuyetCa2Lan'
        ));
    }

    /**
     * Duyệt điểm hoặc trả về
     */
    public function duyetDiem(Request $request, $lopHocPhanId)
    {
        // Validate
        $validated = $request->validate([
            'hanh_dong' => 'required|in:phe_duyet,tra_ve',
            'ly_do_tra_ve' => 'required_if:hanh_dong,tra_ve|max:500',
            'lan_gui' => 'nullable|integer|in:1,2',
        ]);

        // Kiểm tra trạng thái
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->find($lopHocPhanId);

        if ($lopHocPhan->trang_thai_lop !== 'da_khoa_diem') {
            return response()->json([
                'success' => false,
                'message' => 'Lớp chưa khóa điểm hoặc đã được duyệt'
            ], 400);
        }

        // Xác định lần gửi điểm đang duyệt
        $lanGui = $validated['lan_gui'] ?? $lopHocPhan->lan_gui_diem;
        
        if (!$lanGui) {
            // Xác định dựa trên trạng thái gửi điểm
            $trangThaiLan1 = $lopHocPhan->trang_thai_gui_diem_lan_1 ?? 'chua_gui';
            $trangThaiLan2 = $lopHocPhan->trang_thai_gui_diem_lan_2 ?? 'chua_gui';
            
            // Logic: ưu tiên duyệt lần 2 nếu đã gửi, nếu không thì duyệt lần 1
            if ($trangThaiLan1 === 'da_duyet' && $trangThaiLan2 === 'da_gui') {
                $lanGui = 2; // Lần 1 đã duyệt, lần 2 đang chờ
            } elseif ($trangThaiLan1 === 'da_gui' && $trangThaiLan2 === 'da_gui') {
                $lanGui = 2; // Cả 2 đều đã gửi, ưu tiên duyệt lần 2
            } elseif ($trangThaiLan2 === 'da_gui') {
                $lanGui = 2; // Lần 2 đã gửi
            } elseif ($trangThaiLan1 === 'da_gui') {
                $lanGui = 1; // Lần 1 đã gửi, chưa gửi lần 2
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không xác định được lần gửi điểm. Vui lòng kiểm tra lại trạng thái gửi điểm.'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            if ($validated['hanh_dong'] === 'phe_duyet') {
                // Phê duyệt
                $updateData = [];
                
                if ($lanGui == 1) {
                    $updateData['trang_thai_gui_diem_lan_1'] = 'da_duyet';
                    $updateData['trang_thai_lop'] = 'dang_hoc'; // Vẫn cho phép sửa để chuẩn bị lần 2
                } else {
                    $updateData['trang_thai_gui_diem_lan_2'] = 'da_duyet';
                    $updateData['trang_thai_lop'] = 'da_duyet_diem'; // Sau lần 2, không cho sửa nữa
                    
                    // Cập nhật bảng điểm cho từng sinh viên (sau khi duyệt lần 2)
                    $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                        ->get();

                    foreach ($sinhViens as $sv) {
                        $this->diemService->capNhatBangDiem($sv->sinh_vien_id, $lopHocPhan->hoc_ky_id);
                    }

                    // Gửi thông báo cho sinh viên (chỉ sau khi duyệt lần 2)
                    $this->guiThongBaoCongBoDiem($lopHocPhan);
                }
                
                $lopHocPhan->update($updateData);

                $lanGuiText = $lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
                $message = $lanGui == 2 
                    ? 'Đã phê duyệt và công bố điểm lần 2 thành công'
                    : "Đã phê duyệt điểm {$lanGuiText} thành công";
            } else {
                // Trả về
                $updateData = [
                    'trang_thai_lop' => 'dang_hoc',
                    'ly_do_tra_ve' => $validated['ly_do_tra_ve'],
                ];
                
                if ($lanGui == 1) {
                    $updateData['trang_thai_gui_diem_lan_1'] = 'da_tra_ve';
                } else {
                    $updateData['trang_thai_gui_diem_lan_2'] = 'da_tra_ve';
                }
                
                $lopHocPhan->update($updateData);

                // Gửi thông báo cho giảng viên
                $this->guiThongBaoTraVeDiem($lopHocPhan, $validated['ly_do_tra_ve'], $lanGui);

                $lanGuiText = $lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
                $message = "Đã trả về điểm {$lanGuiText} cho giảng viên";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
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
     * Gửi thông báo công bố điểm cho sinh viên
     */
    private function guiThongBaoCongBoDiem($lopHocPhan)
    {
        // Lấy danh sách sinh viên (bao gồm cả da_xep_lop, dang_hoc, da_hoan_thanh)
        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
            ->with('sinhVien.user')
            ->get();

        // Tạo thông báo
        $thongBao = ThongBao::create([
            'tieu_de' => 'Công bố điểm môn ' . $lopHocPhan->monHoc->ten_mon,
            'noi_dung' => "Điểm môn {$lopHocPhan->monHoc->ten_mon} - Lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->hocKy->ten_hoc_ky} đã được công bố. Vui lòng truy cập để xem chi tiết.",
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'quan_trong',
            'doi_tuong' => 'lop_hoc_phan',
            'doi_tuong_cu_the_id' => $lopHocPhan->id,
            'nguoi_gui_id' => Auth::id(),
            'ngay_gui' => now(),
            'lien_ket_loai' => 'diem',
            'lien_ket_id' => $lopHocPhan->id,
        ]);

        // Gửi cho từng sinh viên
        foreach ($sinhViens as $sv) {
            if ($sv->sinhVien && $sv->sinhVien->user) {
                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $sv->sinhVien->user_id,
                    'da_doc' => false,
                ]);
            }
        }
    }

    /**
     * Gửi thông báo trả về điểm cho giảng viên
     */
    private function guiThongBaoTraVeDiem($lopHocPhan, $lyDo, $lanGui = 1)
    {
        // Lấy giảng viên chính
        $phanCong = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhan->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->with('giangVien.user')
            ->first();

        if (!$phanCong || !$phanCong->giangVien || !$phanCong->giangVien->user) {
            return;
        }

        // Tạo thông báo
        $lanGuiText = $lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
        $thongBao = ThongBao::create([
            'tieu_de' => 'Trả về điểm ' . $lanGuiText . ' lớp ' . $lopHocPhan->ma_lop_hp,
            'noi_dung' => "Điểm {$lanGuiText} lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} đã được trả về để chỉnh sửa.\n\nLý do: {$lyDo}",
            'loai_nguon' => 'thu_cong',
            'loai_thong_bao' => 'diem',
            'muc_do_quan_trong' => 'quan_trong',
            'doi_tuong' => 'lop_hoc_phan',
            'doi_tuong_cu_the_id' => $lopHocPhan->id,
            'nguoi_gui_id' => Auth::id(),
            'ngay_gui' => now(),
            'lien_ket_loai' => 'diem',
            'lien_ket_id' => $lopHocPhan->id,
        ]);

        // Gửi cho giảng viên
        NguoiNhanThongBao::create([
            'thong_bao_id' => $thongBao->id,
            'nguoi_nhan_id' => $phanCong->giangVien->user_id,
            'da_doc' => false,
        ]);
    }

    /**
     * Quản lý mở/đóng gửi điểm cho giảng viên
     */
    public function quanLyGuiDiem(Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');
        
        // Lấy danh sách học kỳ
        $hocKys = HocKy::orderBy('ngay_bat_dau', 'desc')->limit(5)->get();

        // Query lớp học phần
        $query = LopHocPhan::with(['monHoc', 'hocKy']);

        // Filter theo học kỳ
        if ($hocKyId) {
            $query->where('hoc_ky_id', $hocKyId);
        } else {
            // Mặc định lấy học kỳ hiện tại
            $hocKyHienTai = HocKy::where('dang_mo_dang_ky', true)->first();
            if ($hocKyHienTai) {
                $query->where('hoc_ky_id', $hocKyHienTai->id);
            }
        }

        $lopHocPhans = $query->orderBy('ma_lop_hp')->get();

        return view('daotao.duyet-diem.quan-ly-gui-diem', compact('lopHocPhans', 'hocKys', 'hocKyId'));
    }

    /**
     * Cập nhật trạng thái mở/đóng gửi điểm
     */
    public function capNhatTrangThaiGuiDiem(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'cho_phep_gui_diem_lan_1' => 'nullable|boolean',
            'cho_phep_gui_diem_lan_2' => 'nullable|boolean',
        ]);

        $lopHocPhan = LopHocPhan::findOrFail($lopHocPhanId);

        $updateData = [];
        if ($request->has('cho_phep_gui_diem_lan_1')) {
            $updateData['cho_phep_gui_diem_lan_1'] = $request->cho_phep_gui_diem_lan_1;
        }
        if ($request->has('cho_phep_gui_diem_lan_2')) {
            $updateData['cho_phep_gui_diem_lan_2'] = $request->cho_phep_gui_diem_lan_2;
        }

        $lopHocPhan->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái mở/đóng gửi điểm thành công'
        ]);
    }

    /**
     * Mở/đóng gửi điểm hàng loạt
     */
    public function capNhatTrangThaiGuiDiemHangLoat(Request $request)
    {
        $validated = $request->validate([
            'lop_hoc_phan_ids' => 'required|array',
            'lop_hoc_phan_ids.*' => 'exists:lop_hoc_phan,id',
            'cho_phep_gui_diem_lan_1' => 'nullable|boolean',
            'cho_phep_gui_diem_lan_2' => 'nullable|boolean',
        ]);

        $updateData = [];
        if ($request->has('cho_phep_gui_diem_lan_1')) {
            $updateData['cho_phep_gui_diem_lan_1'] = $request->cho_phep_gui_diem_lan_1;
        }
        if ($request->has('cho_phep_gui_diem_lan_2')) {
            $updateData['cho_phep_gui_diem_lan_2'] = $request->cho_phep_gui_diem_lan_2;
        }

        LopHocPhan::whereIn('id', $validated['lop_hoc_phan_ids'])
            ->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái mở/đóng gửi điểm cho ' . count($validated['lop_hoc_phan_ids']) . ' lớp thành công'
        ]);
    }

    /**
     * Cho phép đào tạo sửa điểm sau khi duyệt (phúc khảo)
     */
    public function choPhepSuaDiemSauDuyet(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'cho_phep_sua_diem_sau_duyet' => 'required|boolean',
        ]);

        $lopHocPhan = LopHocPhan::findOrFail($lopHocPhanId);

        // Chỉ cho phép khi đã duyệt cả 2 lần
        if ($lopHocPhan->trang_thai_gui_diem_lan_1 !== 'da_duyet' || $lopHocPhan->trang_thai_gui_diem_lan_2 !== 'da_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể cho phép sửa điểm sau khi đã duyệt cả 2 lần gửi điểm'
            ], 400);
        }

        $lopHocPhan->update([
            'cho_phep_sua_diem_sau_duyet' => $validated['cho_phep_sua_diem_sau_duyet']
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['cho_phep_sua_diem_sau_duyet'] 
                ? 'Đã cho phép đào tạo sửa điểm (phúc khảo)'
                : 'Đã tắt quyền sửa điểm sau khi duyệt'
        ]);
    }

    /**
     * Đào tạo sửa điểm sau khi duyệt (phúc khảo)
     */
    public function suaDiem(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
            'cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
            'cot_diem' => 'required|integer|min:1',
            'diem_so' => 'nullable|numeric|min:0|max:10',
        ]);

        $lopHocPhan = LopHocPhan::findOrFail($lopHocPhanId);

        // Kiểm tra quyền sửa điểm
        if (!$lopHocPhan->cho_phep_sua_diem_sau_duyet) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa được phép sửa điểm. Vui lòng bật quyền sửa điểm trong phần quản lý mở/đóng gửi điểm.'
            ], 403);
        }

        // Kiểm tra đã duyệt cả 2 lần chưa
        if ($lopHocPhan->trang_thai_gui_diem_lan_1 !== 'da_duyet' || $lopHocPhan->trang_thai_gui_diem_lan_2 !== 'da_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể sửa điểm sau khi đã duyệt cả 2 lần gửi điểm'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Kiểm tra cấu hình đầu điểm
            $cauHinh = CauHinhDauDiem::findOrFail($validated['cau_hinh_id']);
            if ($cauHinh->lop_hoc_phan_id != $lopHocPhanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cấu hình đầu điểm không thuộc lớp học phần này'
                ], 400);
            }

            if ($validated['cot_diem'] > $cauHinh->so_cot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cột điểm không hợp lệ'
                ], 400);
            }

            // Kiểm tra sinh viên thuộc lớp học phần
            $lhpsv = LopHocPhanSinhVien::findOrFail($validated['lop_hoc_phan_sinh_vien_id']);
            if ($lhpsv->lop_hoc_phan_id != $lopHocPhanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinh viên không thuộc lớp học phần này'
                ], 400);
            }

            // Cập nhật hoặc tạo điểm
            $nhapDiem = \App\Models\NhapDiem::updateOrCreate(
                [
                    'lop_hoc_phan_sinh_vien_id' => $validated['lop_hoc_phan_sinh_vien_id'],
                    'cau_hinh_id' => $validated['cau_hinh_id'],
                    'cot_diem' => $validated['cot_diem'],
                ],
                [
                    'diem_so' => $validated['diem_so'],
                ]
            );

            // Cập nhật điểm tổng kết
            $this->diemService->capNhatBangDiem($lhpsv->sinh_vien_id, $lopHocPhan->hoc_ky_id);

            // Lấy điểm tổng kết mới
            $ketQua = \App\Models\KetQuaHocTap::whereHas('lopHocPhanSinhVien', function ($q) use ($lhpsv) {
                $q->where('id', $lhpsv->id);
            })->first();

            DB::commit();

            // Gửi thông báo cho sinh viên và giảng viên (chỉ khi lưu tất cả, không gửi khi sửa từng điểm)
            // Thông báo sẽ được gửi trong hàm luuTatCaDiem

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật điểm thành công',
                'diem_tong_ket' => $ketQua ? $ketQua->diem_he_10 : null
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
     * Đào tạo lưu tất cả điểm sau khi duyệt (phúc khảo)
     */
    public function luuTatCaDiem(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'diem_data' => 'required|array',
            'diem_data.*.lop_hoc_phan_sinh_vien_id' => 'required|exists:lop_hoc_phan_sinh_vien,id',
            'diem_data.*.cau_hinh_id' => 'required|exists:cau_hinh_dau_diem,id',
            'diem_data.*.cot_diem' => 'required|integer|min:1',
            'diem_data.*.diem_so' => 'nullable|numeric|min:0|max:10',
        ]);

        $lopHocPhan = LopHocPhan::findOrFail($lopHocPhanId);

        // Kiểm tra quyền sửa điểm
        if (!$lopHocPhan->cho_phep_sua_diem_sau_duyet) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa được phép sửa điểm. Vui lòng bật quyền sửa điểm trong phần quản lý mở/đóng gửi điểm.'
            ], 403);
        }

        // Kiểm tra đã duyệt cả 2 lần chưa
        if ($lopHocPhan->trang_thai_gui_diem_lan_1 !== 'da_duyet' || $lopHocPhan->trang_thai_gui_diem_lan_2 !== 'da_duyet') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể sửa điểm sau khi đã duyệt cả 2 lần gửi điểm'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $sinhVienIds = [];
            $lhpsvIds = []; // Lưu danh sách lop_hoc_phan_sinh_vien_id để tính lại điểm tổng kết
            foreach ($validated['diem_data'] as $diemData) {
                // Kiểm tra sinh viên thuộc lớp học phần
                $lhpsv = LopHocPhanSinhVien::findOrFail($diemData['lop_hoc_phan_sinh_vien_id']);
                if ($lhpsv->lop_hoc_phan_id != $lopHocPhanId) {
                    continue; // Bỏ qua nếu không thuộc lớp
                }

                // Kiểm tra cấu hình đầu điểm
                $cauHinh = CauHinhDauDiem::findOrFail($diemData['cau_hinh_id']);
                if ($cauHinh->lop_hoc_phan_id != $lopHocPhanId || $diemData['cot_diem'] > $cauHinh->so_cot) {
                    continue; // Bỏ qua nếu không hợp lệ
                }

                // Cập nhật hoặc tạo điểm
                \App\Models\NhapDiem::updateOrCreate(
                    [
                        'lop_hoc_phan_sinh_vien_id' => $diemData['lop_hoc_phan_sinh_vien_id'],
                        'cau_hinh_id' => $diemData['cau_hinh_id'],
                        'cot_diem' => $diemData['cot_diem'],
                    ],
                    [
                        'diem_so' => $diemData['diem_so'],
                    ]
                );

                // Lưu lhpsv_id để tính lại điểm tổng kết
                if (!in_array($lhpsv->id, $lhpsvIds)) {
                    $lhpsvIds[] = $lhpsv->id;
                }

                if (!in_array($lhpsv->sinh_vien_id, $sinhVienIds)) {
                    $sinhVienIds[] = $lhpsv->sinh_vien_id;
                }
            }

            // Tính lại điểm tổng kết cho từng sinh viên trong lớp học phần
            foreach ($lhpsvIds as $lhpsvId) {
                $this->diemService->tinhDiemTong($lhpsvId);
            }

            // Cập nhật bảng điểm học kỳ cho tất cả sinh viên
            $sinhVienDaCapNhat = [];
            foreach ($sinhVienIds as $sinhVienId) {
                $this->diemService->capNhatBangDiem($sinhVienId, $lopHocPhan->hoc_ky_id);
                $sinhVienDaCapNhat[] = $sinhVienId;
            }

            DB::commit();

            // Gửi thông báo cho sinh viên và giảng viên
            $this->guiThongBaoSuaDiemSauDuyet($lopHocPhan, $sinhVienDaCapNhat);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu tất cả điểm thành công. Đã gửi thông báo cho sinh viên và giảng viên.'
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
     * Gửi thông báo cho sinh viên và giảng viên khi đào tạo sửa điểm sau khi duyệt
     */
    private function guiThongBaoSuaDiemSauDuyet($lopHocPhan, $sinhVienIds = [])
    {
        try {
            // Lấy danh sách sinh viên trong lớp
            $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc', 'da_hoan_thanh'])
                ->with('sinhVien.user')
                ->get();

            // Nếu có danh sách sinh viên cụ thể, chỉ gửi cho những sinh viên đó
            if (!empty($sinhVienIds)) {
                $sinhViens = $sinhViens->filter(function ($sv) use ($sinhVienIds) {
                    return in_array($sv->sinh_vien_id, $sinhVienIds);
                });
            }

            // Lấy giảng viên chính
            $phanCong = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('vai_tro', 'giang_vien_chinh')
                ->with('giangVien.user')
                ->first();

            // Tạo thông báo cho sinh viên
            foreach ($sinhViens as $lhpsv) {
                if ($lhpsv->sinhVien && $lhpsv->sinhVien->user) {
                    $ketQua = $lhpsv->ketQuaHocTap;
                    $diemText = $ketQua && $ketQua->diem_he_10 !== null 
                        ? number_format($ketQua->diem_he_10, 2) 
                        : 'chưa có';

                    $thongBao = ThongBao::create([
                        'tieu_de' => 'Điểm đã được cập nhật - ' . $lopHocPhan->ma_lop_hp,
                        'noi_dung' => "Điểm môn {$lopHocPhan->monHoc->ten_mon} ({$lopHocPhan->ma_lop_hp}) đã được đào tạo cập nhật do phúc khảo hoặc điều chỉnh.\n\nĐiểm tổng kết mới: {$diemText}/10\n\nVui lòng truy cập hệ thống để xem chi tiết.",
                        'loai_nguon' => 'thu_cong',
                        'loai_thong_bao' => 'diem',
                        'muc_do_quan_trong' => 'quan_trong',
                        'doi_tuong' => 'sinh_vien',
                        'doi_tuong_cu_the_id' => $lhpsv->sinh_vien_id,
                        'nguoi_gui_id' => Auth::id(),
                        'ngay_gui' => now(),
                        'lien_ket_loai' => 'diem',
                        'lien_ket_id' => $lopHocPhan->id,
                    ]);

                    NguoiNhanThongBao::create([
                        'thong_bao_id' => $thongBao->id,
                        'nguoi_nhan_id' => $lhpsv->sinhVien->user_id,
                        'da_doc' => false,
                    ]);
                }
            }

            // Tạo thông báo cho giảng viên
            if ($phanCong && $phanCong->giangVien && $phanCong->giangVien->user) {
                $thongBao = ThongBao::create([
                    'tieu_de' => 'Điểm lớp ' . $lopHocPhan->ma_lop_hp . ' đã được đào tạo cập nhật',
                    'noi_dung' => "Điểm lớp {$lopHocPhan->ma_lop_hp} - {$lopHocPhan->monHoc->ten_mon} đã được đào tạo cập nhật do phúc khảo hoặc điều chỉnh.\n\nVui lòng truy cập hệ thống để xem chi tiết.",
                    'loai_nguon' => 'thu_cong',
                    'loai_thong_bao' => 'diem',
                    'muc_do_quan_trong' => 'quan_trong',
                    'doi_tuong' => 'lop_hoc_phan',
                    'doi_tuong_cu_the_id' => $lopHocPhan->id,
                    'nguoi_gui_id' => Auth::id(),
                    'ngay_gui' => now(),
                    'lien_ket_loai' => 'diem',
                    'lien_ket_id' => $lopHocPhan->id,
                ]);

                NguoiNhanThongBao::create([
                    'thong_bao_id' => $thongBao->id,
                    'nguoi_nhan_id' => $phanCong->giangVien->user_id,
                    'da_doc' => false,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi thông báo khi đào tạo sửa điểm: ' . $e->getMessage());
        }
    }
}