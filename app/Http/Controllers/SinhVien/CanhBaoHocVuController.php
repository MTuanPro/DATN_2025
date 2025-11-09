<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\CanhBaoHocVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanhBaoHocVuController extends Controller
{
    /**
     * Xem danh sách cảnh báo của sinh viên
     */
    public function index(Request $request)
    {
        // Lấy thông tin sinh viên từ user hiện tại
        $user = auth()->user();
        $sinhVien = $user->sinhVien ?? \App\Models\DaoTao\SinhVien::where('user_id', $user->id)->first();

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        $query = CanhBaoHocVu::where('sinh_vien_id', $sinhVien->id)
            ->with(['hocKy', 'nguoiTao', 'nguoiXuLy']);

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

        $canhBaos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('sinhvien.canh-bao-hoc-vu.index', compact('canhBaos'));
    }

    /**
     * Xem chi tiết cảnh báo
     */
    public function show(CanhBaoHocVu $canhBaoHocVu)
    {
        // Lấy thông tin sinh viên từ user hiện tại
        $user = auth()->user();
        $sinhVien = $user->sinhVien ?? \App\Models\DaoTao\SinhVien::where('user_id', $user->id)->first();

        if (!$sinhVien) {
            return redirect()->route('sinh-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin sinh viên!');
        }

        // Kiểm tra quyền xem
        if ($canhBaoHocVu->sinh_vien_id != $sinhVien->id) {
            return redirect()->route('sinh-vien.canh-bao-hoc-vu.index')
                ->with('error', 'Bạn không có quyền xem cảnh báo này!');
        }

        $canhBaoHocVu->load(['hocKy', 'nguoiTao', 'nguoiXuLy', 'sinhVien']);
        $canhBao = $canhBaoHocVu; // Alias for view

        return view('sinhvien.canh-bao-hoc-vu.show', compact('canhBao'));
    }
}
