<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichHocCoDinh;
use App\Models\LopHocPhan;
use App\Models\DaoTao\PhongHoc;
use App\Models\GiangVien;
use Illuminate\Http\Request;

class LichHocCoDinhController extends Controller
{
    /**
     * Hiển thị danh sách lịch học cố định của lớp học phần
     */
    public function index(LopHocPhan $lopHocPhan)
    {
        $lichHocs = LichHocCoDinh::with(['phongHoc', 'giangVien'])
            ->where('lop_hoc_phan_id', $lopHocPhan->id)
            ->orderBy('thu_trong_tuan')
            ->orderBy('tiet_bat_dau')
            ->get();

        return view('daotao.lich-hoc-co-dinh.index', compact('lopHocPhan', 'lichHocs'));
    }

    /**
     * Hiển thị form tạo lịch học cố định
     */
    public function create(LopHocPhan $lopHocPhan)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lich-hoc-co-dinh.create', compact('lopHocPhan', 'phongHocs', 'giangViens'));
    }

    /**
     * Lưu lịch học cố định mới
     */
    public function store(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'thu_trong_tuan' => 'required|integer|min:2|max:8',
            'tiet_bat_dau' => 'required|integer|min:1|max:10',
            'tiet_ket_thuc' => 'required|integer|min:1|max:10|gte:tiet_bat_dau',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'phong_hoc_id' => 'required|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'ghi_chu' => 'nullable|string',
        ], [
            'thu_trong_tuan.required' => 'Thứ trong tuần là bắt buộc',
            'thu_trong_tuan.min' => 'Thứ phải từ 2 đến 8',
            'thu_trong_tuan.max' => 'Thứ phải từ 2 đến 8',
            'tiet_bat_dau.required' => 'Tiết bắt đầu là bắt buộc',
            'tiet_ket_thuc.required' => 'Tiết kết thúc là bắt buộc',
            'tiet_ket_thuc.gte' => 'Tiết kết thúc phải lớn hơn hoặc bằng tiết bắt đầu',
            'gio_bat_dau.required' => 'Giờ bắt đầu là bắt buộc',
            'gio_ket_thuc.required' => 'Giờ kết thúc là bắt buộc',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'phong_hoc_id.required' => 'Phòng học là bắt buộc',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
        ]);

        $validated['lop_hoc_phan_id'] = $lopHocPhan->id;

        // Kiểm tra xung đột
        $lichHoc = new LichHocCoDinh($validated);

        if ($lichHoc->kiemTraXungDotPhong()) {
            return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này'])->withInput();
        }

        if ($lichHoc->kiemTraXungDotGiangVien()) {
            return back()->withErrors(['giang_vien_id' => 'Giảng viên đã có lịch dạy vào thời gian này'])->withInput();
        }

        LichHocCoDinh::create($validated);

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-co-dinh', $lopHocPhan)
            ->with('success', 'Đã thêm lịch học cố định thành công');
    }

    /**
     * Hiển thị form chỉnh sửa lịch học cố định
     */
    public function edit(LichHocCoDinh $lichCoDinh)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lich-hoc-co-dinh.edit', compact('lichCoDinh', 'phongHocs', 'giangViens'));
    }

    /**
     * Cập nhật lịch học cố định
     */
    public function update(Request $request, LichHocCoDinh $lichCoDinh)
    {
        $validated = $request->validate([
            'thu_trong_tuan' => 'required|integer|min:2|max:8',
            'tiet_bat_dau' => 'required|integer|min:1|max:10',
            'tiet_ket_thuc' => 'required|integer|min:1|max:10|gte:tiet_bat_dau',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'phong_hoc_id' => 'required|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'ghi_chu' => 'nullable|string',
        ]);

        // Kiểm tra xung đột (loại trừ chính nó)
        $lichHocTemp = new LichHocCoDinh($validated);

        if ($lichHocTemp->kiemTraXungDotPhong($lichCoDinh->id)) {
            return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này'])->withInput();
        }

        if ($lichHocTemp->kiemTraXungDotGiangVien($lichCoDinh->id)) {
            return back()->withErrors(['giang_vien_id' => 'Giảng viên đã có lịch dạy vào thời gian này'])->withInput();
        }

        $lichCoDinh->update($validated);

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id)
            ->with('success', 'Đã cập nhật lịch học cố định thành công');
    }

    /**
     * Xóa lịch học cố định
     */
    public function destroy(LichHocCoDinh $lichCoDinh)
    {
        $lopHocPhanId = $lichCoDinh->lop_hoc_phan_id;
        $lichCoDinh->delete();

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-co-dinh', $lopHocPhanId)
            ->with('success', 'Đã xóa lịch học cố định thành công');
    }

    /**
     * API kiểm tra xung đột phòng học
     */
    public function checkPhongConflict(Request $request)
    {
        $lichHoc = new LichHocCoDinh([
            'phong_hoc_id' => $request->phong_hoc_id,
            'thu_trong_tuan' => $request->thu_trong_tuan,
            'tiet_bat_dau' => $request->tiet_bat_dau,
            'tiet_ket_thuc' => $request->tiet_ket_thuc,
        ]);

        $conflict = $lichHoc->kiemTraXungDotPhong($request->exclude_id);

        return response()->json(['conflict' => $conflict]);
    }

    /**
     * API kiểm tra xung đột giảng viên
     */
    public function checkGiangVienConflict(Request $request)
    {
        $lichHoc = new LichHocCoDinh([
            'giang_vien_id' => $request->giang_vien_id,
            'thu_trong_tuan' => $request->thu_trong_tuan,
            'tiet_bat_dau' => $request->tiet_bat_dau,
            'tiet_ket_thuc' => $request->tiet_ket_thuc,
        ]);

        $conflict = $lichHoc->kiemTraXungDotGiangVien($request->exclude_id);

        return response()->json(['conflict' => $conflict]);
    }
}
