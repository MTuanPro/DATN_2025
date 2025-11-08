<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LichHocChiTiet;
use App\Models\PhanCongGiangDay;
use App\Models\LopHocPhan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TeachingSessionController extends Controller
{
    /**
     * Hiển thị danh sách buổi học
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Lấy các lớp mà giảng viên được phân công
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();
        
        // Lọc lớp theo môn học cùng khoa với giảng viên
        $lopHocPhanIds = \App\Models\LopHocPhan::whereIn('id', $lopHocPhanIds)
            ->whereHas('monHoc', function($q) use ($giangVien) {
                $q->whereNotNull('khoa_id')->where('khoa_id', $giangVien->khoa_id);
            })
            ->pluck('id')
            ->toArray();

        // Filter
        $trangThai = $request->get('trang_thai');
        $lopHocPhanId = $request->get('lop_hoc_phan_id');
        $tuNgay = $request->get('tu_ngay');
        $denNgay = $request->get('den_ngay');

        // Query buổi học
        $query = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc', 'lichHocCoDinh'])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->orderBy('ngay_hoc', 'desc')
            ->orderBy('tiet_bat_dau', 'asc');

        // Áp dụng filters
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }

        if ($lopHocPhanId) {
            $query->where('lop_hoc_phan_id', $lopHocPhanId);
        }

        if ($tuNgay) {
            $query->where('ngay_hoc', '>=', $tuNgay);
        }

        if ($denNgay) {
            $query->where('ngay_hoc', '<=', $denNgay);
        }

        $buoiHocs = $query->paginate(20);

        // Lấy danh sách lớp để filter
        $lopHocPhans = \App\Models\LopHocPhan::whereIn('id', $lopHocPhanIds)
            ->with('monHoc')
            ->get();

        return view('giangvien.buoi-hoc.index', compact(
            'buoiHocs',
            'lopHocPhans',
            'trangThai',
            'lopHocPhanId',
            'tuNgay',
            'denNgay'
        ));
    }

    /**
     * Hiển thị form chỉnh sửa buổi học
     */
    public function edit($id)
    {
        $user = request()->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $buoiHoc = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc'])
            ->findOrFail($id);

        // Kiểm tra quyền: giảng viên phải được phân công dạy lớp này
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền chỉnh sửa buổi học này.');
        }

        return view('giangvien.buoi-hoc.edit', compact('buoiHoc'));
    }

    /**
     * Cập nhật buổi học
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $buoiHoc = LichHocChiTiet::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền chỉnh sửa buổi học này.');
        }

        // Validate
        $request->validate([
            'noi_dung_giang_day' => 'nullable|string|max:1000',
            'trang_thai' => 'required|in:chua_day,dang_day,da_day,huy',
            'tai_lieu' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip|max:10240', // 10MB
            'ghi_chu' => 'nullable|string|max:500',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'tai_lieu.mimes' => 'File tài liệu phải là: pdf, doc, docx, ppt, pptx, xls, xlsx, zip',
            'tai_lieu.max' => 'File tài liệu không được vượt quá 10MB',
        ]);

        // Xử lý upload tài liệu
        $taiLieuPath = $buoiHoc->tai_lieu_dinh_kem;
        if ($request->hasFile('tai_lieu')) {
            // Xóa file cũ nếu có
            if ($taiLieuPath && Storage::disk('public')->exists($taiLieuPath)) {
                Storage::disk('public')->delete($taiLieuPath);
            }

            // Upload file mới
            $file = $request->file('tai_lieu');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $taiLieuPath = $file->storeAs('tai-lieu-buoi-hoc', $fileName, 'public');
        }

        // Cập nhật
        $buoiHoc->update([
            'noi_dung_giang_day' => $request->noi_dung_giang_day,
            'trang_thai' => $request->trang_thai,
            'tai_lieu_dinh_kem' => $taiLieuPath,
            'ghi_chu' => $request->ghi_chu,
        ]);

        return redirect()->route('giangvien.buoi-hoc.index')
            ->with('success', 'Cập nhật buổi học thành công!');
    }

    /**
     * Xóa tài liệu đính kèm
     */
    public function deleteTaiLieu($id)
    {
        $user = request()->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $buoiHoc = LichHocChiTiet::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền xóa tài liệu này.');
        }

        // Xóa file
        if ($buoiHoc->tai_lieu_dinh_kem && Storage::disk('public')->exists($buoiHoc->tai_lieu_dinh_kem)) {
            Storage::disk('public')->delete($buoiHoc->tai_lieu_dinh_kem);
        }

        $buoiHoc->update(['tai_lieu_dinh_kem' => null]);

        return back()->with('success', 'Đã xóa tài liệu đính kèm!');
    }

    /**
     * Tải tài liệu đính kèm
     */
    public function downloadTaiLieu($id)
    {
        $user = request()->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        $buoiHoc = LichHocChiTiet::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $buoiHoc->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->first();

        if (!$phanCong) {
            abort(403, 'Bạn không có quyền tải tài liệu này.');
        }

        if (!$buoiHoc->tai_lieu_dinh_kem) {
            abort(404, 'Không tìm thấy tài liệu.');
        }

        $filePath = storage_path('app/public/' . $buoiHoc->tai_lieu_dinh_kem);
        $fileName = basename($buoiHoc->tai_lieu_dinh_kem);

        return response()->download($filePath, $fileName);
    }

    /**
     * Lịch sử các buổi đã dạy
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $giangVien = $user->giangVien ?? null;

        if (!$giangVien) {
            abort(403, 'Không tìm thấy hồ sơ giảng viên cho tài khoản hiện tại.');
        }

        // Lấy các lớp mà giảng viên được phân công theo chuyên môn
        $lopHocPhanIds = PhanCongGiangDay::where('giang_vien_id', $giangVien->id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();
        
        $lopHocPhanIds = \App\Models\LopHocPhan::whereIn('id', $lopHocPhanIds)
            ->whereHas('monHoc', function($q) use ($giangVien) {
                $q->whereNotNull('khoa_id')->where('khoa_id', $giangVien->khoa_id);
            })
            ->pluck('id')
            ->toArray();

        // Danh sách lớp học phần để filter
        $danhSachLopHocPhan = LopHocPhan::with('monHoc')
            ->whereIn('id', $lopHocPhanIds)
            ->get();

        // Query buổi học đã dạy
        $query = LichHocChiTiet::with(['lopHocPhan.monHoc', 'phongHoc'])
            ->whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('trang_thai', 'da_day');

        // Áp dụng bộ lọc
        if ($request->filled('lop_hoc_phan_id')) {
            $query->where('lop_hoc_phan_id', $request->lop_hoc_phan_id);
        }

        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_hoc', '>=', $request->tu_ngay);
        }

        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_hoc', '<=', $request->den_ngay);
        }

        if ($request->filled('co_tai_lieu')) {
            if ($request->co_tai_lieu == '1') {
                $query->whereNotNull('tai_lieu_dinh_kem');
            } else {
                $query->whereNull('tai_lieu_dinh_kem');
            }
        }

        $buoiHocList = $query->orderBy('ngay_hoc', 'desc')->paginate(20);

        // Thống kê tổng quan
        $tongBuoiDay = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('trang_thai', 'da_day')
            ->count();

        $buoiCoTaiLieu = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('trang_thai', 'da_day')
            ->whereNotNull('tai_lieu_dinh_kem')
            ->count();

        $soLopHocPhan = count($lopHocPhanIds);

        $tongBuoi = LichHocChiTiet::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereIn('trang_thai', ['chua_day', 'da_day', 'dang_day'])
            ->count();

        $tyLeHoanThanh = $tongBuoi > 0 ? round(($tongBuoiDay / $tongBuoi) * 100, 1) : 0;

        return view('giangvien.buoi-hoc.history', compact(
            'buoiHocList',
            'danhSachLopHocPhan',
            'tongBuoiDay',
            'buoiCoTaiLieu',
            'soLopHocPhan',
            'tyLeHoanThanh'
        ));
    }
}
