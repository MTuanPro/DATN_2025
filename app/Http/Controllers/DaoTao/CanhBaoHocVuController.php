<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\BangDiem;
use App\Models\CanhBaoHocVu;
use App\Models\DaoTao\SinhVien;
use App\Models\KetQuaHocTap;
use App\Models\DiemDanh;
use App\Models\HocPhiHocKy;
use App\Models\HocKy;
use App\Models\LopHocPhanSinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CanhBaoHocVuController extends Controller
{
    /**
     * Xem danh sách cảnh báo học vụ
     */
    public function index(Request $request)
    {
        $query = CanhBaoHocVu::with([
            'sinhVien.user',
            'sinhVien.lopHanhChinh',
            'hocKy',
            'nguoiCanhBao'
        ]);

        // Lọc theo loại cảnh báo
        if ($request->filled('loai_canh_bao')) {
            $query->where('loai_canh_bao', $request->loai_canh_bao);
        }

        // Lọc theo mức độ
        if ($request->filled('muc_do')) {
            $query->where('muc_do', $request->muc_do);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Tìm kiếm theo MSSV hoặc tên sinh viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sinhVien', function($q) use ($search) {
                $q->where('ma_sinh_vien', 'LIKE', "%{$search}%")
                  ->orWhere('ho_ten', 'LIKE', "%{$search}%");
            });
        }

        $canhBaos = $query->orderBy('created_at', 'desc')->paginate(20);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.canh-bao-hoc-vu.index', compact('canhBaos', 'hocKys'));
    }

    /**
     * Hiển thị form tạo cảnh báo thủ công
     */
    public function create()
    {
        $sinhViens = SinhVien::with('user', 'lopHanhChinh')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.canh-bao-hoc-vu.create', compact('sinhViens', 'hocKys'));
    }

    /**
     * Lưu cảnh báo mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sinh_vien_id' => 'required|exists:sinh_vien,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'loai_canh_bao' => 'required|in:diem_thap,vang_nhieu,no_hoc_phi,hoc_ky_lien_tiep',
            'muc_do' => 'required|in:canh_cao,dinh_chi,buoc_thoi_hoc',
            'ly_do' => 'required|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
        ], [
            'sinh_vien_id.required' => 'Vui lòng chọn sinh viên',
            'sinh_vien_id.exists' => 'Sinh viên không tồn tại',
            'hoc_ky_id.required' => 'Vui lòng chọn học kỳ',
            'hoc_ky_id.exists' => 'Học kỳ không tồn tại',
            'loai_canh_bao.required' => 'Vui lòng chọn loại cảnh báo',
            'muc_do.required' => 'Vui lòng chọn mức độ cảnh báo',
            'ly_do.required' => 'Vui lòng nhập lý do cảnh báo',
            'ly_do.max' => 'Lý do không được vượt quá 1000 ký tự',
        ]);

        try {
            DB::beginTransaction();

            $validated['nguoi_tao_id'] = Auth::id();
            $validated['ngay_canh_bao'] = $request->filled('ngay_canh_bao') ? $request->ngay_canh_bao : now();
            $validated['trang_thai'] = 'chua_xu_ly';

            $canhBao = CanhBaoHocVu::create($validated);

            // Gửi email thông báo cho sinh viên nếu có yêu cầu
            if ($request->has('gui_email')) {
                try {
                    $this->guiEmailCanhBao($canhBao);
                } catch (\Exception $e) {
                    Log::warning('Không thể gửi email cảnh báo: ' . $e->getMessage());
                }
            }

            DB::commit();

            $sinhVien = $canhBao->sinhVien;
            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', "Đã tạo cảnh báo học vụ cho sinh viên {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten} thành công!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết cảnh báo
     */
    public function show(CanhBaoHocVu $canhBaoHocVu)
    {
        $canhBaoHocVu->load([
            'sinhVien.user',
            'sinhVien.lopHanhChinh',
            'sinhVien.ketQuaHocTaps',
            'hocKy',
            'nguoiTao',
            'nguoiXuLy'
        ]);

        $canhBao = $canhBaoHocVu; // Alias for view
        return view('daotao.canh-bao-hoc-vu.show', compact('canhBao'));
    }

    /**
     * Hiển thị form sửa cảnh báo
     */
    public function edit(CanhBaoHocVu $canhBaoHocVu)
    {
        $sinhViens = SinhVien::with('user', 'lopHanhChinh')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        $canhBao = $canhBaoHocVu; // Alias for view
        return view('daotao.canh-bao-hoc-vu.edit', compact('canhBao', 'sinhViens', 'hocKys'));
    }

    /**
     * Cập nhật cảnh báo
     */
    public function update(Request $request, CanhBaoHocVu $canhBaoHocVu)
    {
        $validated = $request->validate([
            'loai_canh_bao' => 'required|in:diem_thap,vang_nhieu,no_hoc_phi,hoc_ky_lien_tiep',
            'muc_do' => 'required|in:canh_cao,dinh_chi,buoc_thoi_hoc',
            'ly_do' => 'required|string|max:1000',
            'trang_thai' => 'required|in:chua_xu_ly,dang_xu_ly,da_xu_ly',
            'ket_qua_xu_ly' => 'nullable|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
        ], [
            'loai_canh_bao.required' => 'Vui lòng chọn loại cảnh báo',
            'muc_do.required' => 'Vui lòng chọn mức độ',
            'ly_do.required' => 'Vui lòng nhập lý do cảnh báo',
            'ly_do.max' => 'Lý do không được vượt quá 1000 ký tự',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
        ]);

        try {
            $canhBaoHocVu->update($validated);

            // Gửi email nếu có yêu cầu
            if ($request->has('gui_email')) {
                try {
                    $this->guiEmailCanhBao($canhBaoHocVu);
                } catch (\Exception $e) {
                    Log::warning('Không thể gửi email cảnh báo: ' . $e->getMessage());
                }
            }

            $sinhVien = $canhBaoHocVu->sinhVien;
            return redirect()->route('dao-tao.canh-bao-hoc-vu.show', $canhBaoHocVu)
                ->with('success', "Đã cập nhật cảnh báo cho sinh viên {$sinhVien->ma_sinh_vien} - {$sinhVien->ho_ten} thành công!");

        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cảnh báo
     */
    public function destroy(CanhBaoHocVu $canhBaoHocVu)
    {
        try {
            $sinhVien = $canhBaoHocVu->sinhVien;
            $ma_sv = $sinhVien->ma_sinh_vien;
            $ten_sv = $sinhVien->ho_ten;
            
            $canhBaoHocVu->delete();

            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', "Đã xóa cảnh báo của sinh viên {$ma_sv} - {$ten_sv} thành công!");

        } catch (\Exception $e) {
            Log::error('Lỗi xóa cảnh báo học vụ: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Tự động phát hiện và tạo cảnh báo học vụ
     */
    public function tuDongPhatHien(Request $request)
    {
        try {
            DB::beginTransaction();

            // Lấy học kỳ
            $hocKy = null;
            if ($request->hoc_ky_id) {
                $hocKy = HocKy::find($request->hoc_ky_id);
            } else {
                $hocKy = HocKy::where('la_hoc_ky_hien_tai', true)->first();
            }

            if (!$hocKy) {
                return redirect()->back()->with('error', 'Không tìm thấy học kỳ hiện tại! Vui lòng thiết lập học kỳ hiện tại trước.');
            }

            Log::info("Bắt đầu phát hiện tự động cảnh báo học vụ cho học kỳ: {$hocKy->ten_hoc_ky}");

            $count = 0;
            $details = [];

            // 1. Phát hiện sinh viên có GPA < 1.0
            try {
                $diemThap = $this->phatHienDiemThap($hocKy);
                $count += $diemThap;
                $details[] = "Điểm thấp: {$diemThap}";
                Log::info("Phát hiện {$diemThap} sinh viên có điểm thấp");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện điểm thấp: " . $e->getMessage());
                $details[] = "Điểm thấp: Lỗi - " . $e->getMessage();
            }

            // 2. Phát hiện sinh viên vắng > 20%
            try {
                $vangNhieu = $this->phatHienVangNhieu($hocKy);
                $count += $vangNhieu;
                $details[] = "Vắng nhiều: {$vangNhieu}";
                Log::info("Phát hiện {$vangNhieu} sinh viên vắng nhiều");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện vắng nhiều: " . $e->getMessage());
                $details[] = "Vắng nhiều: Lỗi - " . $e->getMessage();
            }

            // 3. Phát hiện sinh viên nợ học phí quá hạn
            try {
                $noHocPhi = $this->phatHienNoHocPhi($hocKy);
                $count += $noHocPhi;
                $details[] = "Nợ học phí: {$noHocPhi}";
                Log::info("Phát hiện {$noHocPhi} sinh viên nợ học phí");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện nợ học phí: " . $e->getMessage());
                $details[] = "Nợ học phí: Lỗi - " . $e->getMessage();
            }

            // 4. Phát hiện sinh viên học kỳ liên tiếp không đạt
            try {
                $hocKyLienTiep = $this->phatHienHocKyLienTiep($hocKy);
                $count += $hocKyLienTiep;
                $details[] = "Học kỳ liên tiếp: {$hocKyLienTiep}";
                Log::info("Phát hiện {$hocKyLienTiep} sinh viên học kỳ liên tiếp không đạt");
            } catch (\Exception $e) {
                Log::error("Lỗi phát hiện học kỳ liên tiếp: " . $e->getMessage());
                $details[] = "Học kỳ liên tiếp: Lỗi - " . $e->getMessage();
            }

            DB::commit();

            $message = "Đã phát hiện và tạo {$count} cảnh báo học vụ tự động!<br>" . implode('<br>', $details);
            
            if ($count == 0) {
                return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                    ->with('info', 'Không phát hiện sinh viên nào có nguy cơ. Hệ thống đã kiểm tra:<br>' . implode('<br>', $details));
            }

            return redirect()->route('dao-tao.canh-bao-hoc-vu.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi phát hiện tự động cảnh báo: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage() . '<br>Vui lòng kiểm tra log để biết chi tiết!');
        }
    }

    /**
     * Phát hiện sinh viên có điểm thấp (GPA < 1.0)
     */
    private function phatHienDiemThap($hocKy)
    {
        $count = 0;

        // Sinh viên có điểm trung bình học kỳ < 1.0 trong bảng điểm
        $bangDiems = BangDiem::where('hoc_ky_id', $hocKy->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->get();

        foreach ($bangDiems as $bangDiem) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $bangDiem->sinh_vien_id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'diem_thap')
                ->exists();

            if (!$exists) {
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $bangDiem->sinh_vien_id,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'diem_thap',
                    'muc_do' => $bangDiem->diem_trung_binh_hoc_ky < 0.5 ? 'buoc_thoi_hoc' : ($bangDiem->diem_trung_binh_hoc_ky < 0.8 ? 'dinh_chi' : 'canh_cao'),
                    'ly_do' => "Điểm trung bình học kỳ " . number_format($bangDiem->diem_trung_binh_hoc_ky, 2) . "/4.0 (< 1.0)",
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }    /**
     * Phát hiện sinh viên vắng > 20%
     */
    private function phatHienVangNhieu($hocKy)
    {
        $count = 0;
        
        // Lấy danh sách sinh viên đăng ký lớp trong học kỳ này
        $sinhViens = SinhVien::whereHas('lopHocPhanSinhViens.lopHocPhan', function($q) use ($hocKy) {
            $q->where('hoc_ky_id', $hocKy->id);
        })->get();

        foreach ($sinhViens as $sv) {
            // Đếm tổng số buổi điểm danh của sinh viên trong học kỳ
            // Join: lop_hoc_phan_sinh_vien -> diem_danh -> lich_hoc_chi_tiet -> lop_hoc_phan
            $lopHocPhanSinhVienIds = $sv->lopHocPhanSinhViens()
                ->whereHas('lopHocPhan', function($q) use ($hocKy) {
                    $q->where('hoc_ky_id', $hocKy->id);
                })->pluck('id');

            if ($lopHocPhanSinhVienIds->isEmpty()) {
                continue;
            }

            $tongBuoiHoc = DiemDanh::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)->count();
            
            $soBuoiVang = DiemDanh::whereIn('lop_hoc_phan_sinh_vien_id', $lopHocPhanSinhVienIds)
                ->where('trang_thai', 'vang')
                ->count();

            if ($tongBuoiHoc > 0) {
                $tyLeVang = ($soBuoiVang / $tongBuoiHoc) * 100;

                if ($tyLeVang > 20) {
                    // Kiểm tra đã có cảnh báo chưa
                    $exists = CanhBaoHocVu::where('sinh_vien_id', $sv->id)
                        ->where('hoc_ky_id', $hocKy->id)
                        ->where('loai_canh_bao', 'vang_nhieu')
                        ->exists();

                    if (!$exists) {
                        $canhBao = CanhBaoHocVu::create([
                            'sinh_vien_id' => $sv->id,
                            'hoc_ky_id' => $hocKy->id,
                            'loai_canh_bao' => 'vang_nhieu',
                            'muc_do' => $tyLeVang > 50 ? 'dinh_chi' : 'canh_cao',
                            'ly_do' => "Vắng {$soBuoiVang}/{$tongBuoiHoc} buổi (" . number_format($tyLeVang, 1) . "%, vượt ngưỡng 20%)",
                            'nguoi_canh_bao_id' => Auth::id(),
                            'ngay_canh_bao' => now(),
                            'trang_thai' => 'chua_xu_ly',
                        ]);

                        $this->guiEmailCanhBao($canhBao);
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Phát hiện sinh viên nợ học phí quá hạn
     */
    private function phatHienNoHocPhi($hocKy)
    {
        $count = 0;

        $hocPhis = HocPhiHocKy::where('hoc_ky_id', $hocKy->id)
            ->where('trang_thai', 'qua_han')
            ->get();

        foreach ($hocPhis as $hocPhi) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $hocPhi->sinh_vien_id)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'no_hoc_phi')
                ->exists();

            if (!$exists) {
                $soTienNo = $hocPhi->tong_hoc_phi - $hocPhi->so_tien_da_dong;
                
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $hocPhi->sinh_vien_id,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'no_hoc_phi',
                    'muc_do' => 'canh_cao',
                    'ly_do' => "Nợ học phí " . number_format($soTienNo) . " VNĐ, quá hạn từ " . $hocPhi->han_dong->format('d/m/Y'),
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Phát hiện sinh viên học kỳ liên tiếp không đạt
     */
    private function phatHienHocKyLienTiep($hocKy)
    {
        $count = 0;

        // Lấy học kỳ trước
        $hocKyTruoc = HocKy::where('nam_hoc', '<=', $hocKy->nam_hoc)
            ->where('id', '!=', $hocKy->id)
            ->orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->first();

        if (!$hocKyTruoc) {
            return 0;
        }

        // Sinh viên có GPA < 1.0 cả 2 kỳ liên tiếp trong bảng điểm
        $bangDiemHienTai = BangDiem::where('hoc_ky_id', $hocKy->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->pluck('sinh_vien_id');

        $bangDiemTruoc = BangDiem::where('hoc_ky_id', $hocKyTruoc->id)
            ->where('diem_trung_binh_hoc_ky', '<', 1.0)
            ->pluck('sinh_vien_id');

        // Sinh viên có điểm thấp cả 2 kỳ
        $sinhVienIds = $bangDiemHienTai->intersect($bangDiemTruoc);

        foreach ($sinhVienIds as $sinhVienId) {
            // Kiểm tra đã có cảnh báo chưa
            $exists = CanhBaoHocVu::where('sinh_vien_id', $sinhVienId)
                ->where('hoc_ky_id', $hocKy->id)
                ->where('loai_canh_bao', 'hoc_ky_lien_tiep')
                ->exists();

            if (!$exists) {
                $canhBao = CanhBaoHocVu::create([
                    'sinh_vien_id' => $sinhVienId,
                    'hoc_ky_id' => $hocKy->id,
                    'loai_canh_bao' => 'hoc_ky_lien_tiep',
                    'muc_do' => 'buoc_thoi_hoc',
                    'ly_do' => "Điểm trung bình < 1.0 trong 2 học kỳ liên tiếp ({$hocKyTruoc->ten_hoc_ky} và {$hocKy->ten_hoc_ky})",
                    'nguoi_canh_bao_id' => Auth::id(),
                    'ngay_canh_bao' => now(),
                    'trang_thai' => 'chua_xu_ly',
                ]);

                $this->guiEmailCanhBao($canhBao);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Gửi email cảnh báo cho sinh viên
     */
    private function guiEmailCanhBao($canhBao)
    {
        try {
            $canhBao->load('sinhVien.user', 'hocKy');
            
            if ($canhBao->sinhVien && $canhBao->sinhVien->user && $canhBao->sinhVien->user->email) {
                Mail::to($canhBao->sinhVien->user->email)->send(
                    new \App\Mail\CanhBaoHocVuMail($canhBao)
                );
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * Xuất danh sách cảnh báo Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Chức năng xuất file đang được phát triển!');
    }

    /**
     * Xử lý cảnh báo
     */
    public function xuLy(Request $request, CanhBaoHocVu $canhBaoHocVu)
    {
        $validated = $request->validate([
            'trang_thai' => 'required|in:dang_xu_ly,da_xu_ly',
            'ket_qua_xu_ly' => 'required|string|max:1000',
        ]);

        try {
            $canhBaoHocVu->update($validated);

            return redirect()->back()
                ->with('success', 'Đã cập nhật trạng thái xử lý cảnh báo!');

        } catch (\Exception $e) {
            Log::error('Lỗi xử lý cảnh báo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý cảnh báo!');
        }
    }
}
