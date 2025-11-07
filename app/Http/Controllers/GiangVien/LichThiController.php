<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $lopHocPhanIds = $giangVien->phanCongGiangDays()
            ->pluck('lop_hoc_phan_id')
            ->unique();

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
            ->where(function($q) use ($giangVien) {
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
        $lopHocPhanIds = $giangVien->phanCongGiangDays()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        $isGiamThi = ($lichThi->giam_thi_1_id == $giangVien->id || 
                      $lichThi->giam_thi_2_id == $giangVien->id);

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id) && !$isGiamThi) {
            return redirect()->route('giangvien.lich-thi.index')
                ->with('error', 'Bạn không có quyền xem lịch thi này!');
        }

        $lichThi->load([
            'lopHocPhan.monHoc', 
            'lopHocPhan.hocKy',
            'lopHocPhan.lopHocPhanSinhViens.sinhVien', 
            'phongThi', 
            'giamThi1', 
            'giamThi2', 
            'hocKy'
        ]);

        return view('giangvien.lich-thi.show', compact('lichThi', 'isGiamThi'));
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
        $lopHocPhanIds = $giangVien->phanCongGiangDays()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền upload đề thi cho lớp này!');
        }

        try {
            // Xóa file cũ nếu có
            if ($lichThi->de_thi) {
                Storage::disk('public')->delete($lichThi->de_thi);
            }

            // Upload file mới
            $path = $request->file('de_thi')->store('de-thi', 'public');
            $lichThi->update(['de_thi' => $path]);

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
        $lopHocPhanIds = $giangVien->phanCongGiangDays()
            ->pluck('lop_hoc_phan_id')
            ->unique();

        if (!$lopHocPhanIds->contains($lichThi->lop_hoc_phan_id)) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền upload đáp án cho lớp này!');
        }

        try {
            // Xóa file cũ nếu có
            if ($lichThi->dap_an) {
                Storage::disk('public')->delete($lichThi->dap_an);
            }

            // Upload file mới
            $path = $request->file('dap_an')->store('dap-an', 'public');
            $lichThi->update(['dap_an' => $path]);

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
        if ($lichThi->giam_thi_1_id != $giangVien->id && 
            $lichThi->giam_thi_2_id != $giangVien->id) {
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
        if (!$lichThi->de_thi) {
            return redirect()->back()
                ->with('error', 'Chưa có đề thi!');
        }

        $path = storage_path('app/public/' . $lichThi->de_thi);
        
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
        if (!$lichThi->dap_an) {
            return redirect()->back()
                ->with('error', 'Chưa có đáp án!');
        }

        $path = storage_path('app/public/' . $lichThi->dap_an);
        
        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'File không tồn tại!');
        }

        return response()->download($path);
    }
}
