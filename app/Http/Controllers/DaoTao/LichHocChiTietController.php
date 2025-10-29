<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichHocChiTiet;
use App\Models\LichHocCoDinh;
use App\Models\LopHocPhan;
use App\Models\DaoTao\PhongHoc;
use App\Models\GiangVien;
use App\Models\HocKy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LichHocChiTietController extends Controller
{
    /**
     * Hiển thị danh sách lịch học chi tiết
     */
    public function index(LopHocPhan $lopHocPhan)
    {
        $lichHocs = LichHocChiTiet::with(['phongHoc', 'giangVien', 'lichHocCoDinh'])
            ->where('lop_hoc_phan_id', $lopHocPhan->id)
            ->orderBy('ngay_hoc')
            ->orderBy('tiet_bat_dau')
            ->paginate(20);

        return view('daotao.lich-hoc-chi-tiet.index', compact('lopHocPhan', 'lichHocs'));
    }

    /**
     * Tự động sinh lịch chi tiết từ lịch cố định
     */
    public function generate(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
        ], [
            'ngay_bat_dau.required' => 'Ngày bắt đầu là bắt buộc',
            'ngay_ket_thuc.required' => 'Ngày kết thúc là bắt buộc',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
        ]);

        $lichCoDinhs = LichHocCoDinh::where('lop_hoc_phan_id', $lopHocPhan->id)->get();

        if ($lichCoDinhs->isEmpty()) {
            return back()->withErrors(['error' => 'Chưa có lịch học cố định. Vui lòng tạo lịch cố định trước.']);
        }

        $ngayBatDau = Carbon::parse($validated['ngay_bat_dau']);
        $ngayKetThuc = Carbon::parse($validated['ngay_ket_thuc']);
        $count = 0;

        // Duyệt qua từng ngày trong khoảng thời gian
        for ($date = $ngayBatDau->copy(); $date->lte($ngayKetThuc); $date->addDay()) {
            // Lấy thứ trong tuần (2-8)
            $thu = $date->dayOfWeek == 0 ? 8 : $date->dayOfWeek + 1;

            // Tìm lịch cố định tương ứng với thứ này
            foreach ($lichCoDinhs as $lichCoDinh) {
                if ($lichCoDinh->thu_trong_tuan == $thu) {
                    // Kiểm tra xem đã có lịch này chưa
                    $exists = LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                        ->where('ngay_hoc', $date->format('Y-m-d'))
                        ->where('tiet_bat_dau', $lichCoDinh->tiet_bat_dau)
                        ->exists();

                    if (!$exists) {
                        LichHocChiTiet::create([
                            'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                            'lop_hoc_phan_id' => $lopHocPhan->id,
                            'ngay_hoc' => $date->format('Y-m-d'),
                            'tiet_bat_dau' => $lichCoDinh->tiet_bat_dau,
                            'tiet_ket_thuc' => $lichCoDinh->tiet_ket_thuc,
                            'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                            'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                            'phong_hoc_id' => $lichCoDinh->phong_hoc_id,
                            'giang_vien_id' => $lichCoDinh->giang_vien_id,
                            'hinh_thuc' => $lichCoDinh->hinh_thuc,
                            'link_online' => $lichCoDinh->link_online,
                            'trang_thai' => 'chua_day',
                        ]);
                        $count++;
                    }
                }
            }
        }

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-chi-tiet', $lopHocPhan)
            ->with('success', "Đã tạo $count buổi học chi tiết thành công");
    }

    /**
     * Hiển thị form tạo lịch học chi tiết (thêm buổi bù, bổ sung)
     */
    public function create(LopHocPhan $lopHocPhan)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lich-hoc-chi-tiet.create', compact('lopHocPhan', 'phongHocs', 'giangViens'));
    }

    /**
     * Lưu lịch học chi tiết mới (buổi bù, bổ sung)
     */
    public function store(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ngay_hoc' => 'required|date',
            'tiet_bat_dau' => 'required|integer|min:1|max:10',
            'tiet_ket_thuc' => 'required|integer|min:1|max:10|gte:tiet_bat_dau',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'phong_hoc_id' => 'nullable|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'noi_dung_giang_day' => 'nullable|string',
            'ghi_chu' => 'nullable|string',
        ], [
            'ngay_hoc.required' => 'Ngày học là bắt buộc',
            'tiet_bat_dau.required' => 'Tiết bắt đầu là bắt buộc',
            'tiet_ket_thuc.required' => 'Tiết kết thúc là bắt buộc',
            'tiet_ket_thuc.gte' => 'Tiết kết thúc phải lớn hơn hoặc bằng tiết bắt đầu',
            'gio_bat_dau.required' => 'Giờ bắt đầu là bắt buộc',
            'gio_ket_thuc.required' => 'Giờ kết thúc là bắt buộc',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
        ]);

        $validated['lop_hoc_phan_id'] = $lopHocPhan->id;
        $validated['trang_thai'] = 'chua_day';

        // Kiểm tra xung đột nếu có phòng học
        if ($request->phong_hoc_id) {
            $lichHoc = new LichHocChiTiet($validated);

            if ($lichHoc->kiemTraXungDotPhongTheoNgay()) {
                return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này'])->withInput();
            }
        }

        LichHocChiTiet::create($validated);

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-chi-tiet', $lopHocPhan)
            ->with('success', 'Đã thêm buổi học thành công');
    }

    /**
     * Hiển thị form chỉnh sửa lịch học chi tiết
     */
    public function edit(LichHocChiTiet $lichChiTiet)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();

        return view('daotao.lich-hoc-chi-tiet.edit', compact('lichChiTiet', 'phongHocs', 'giangViens'));
    }

    /**
     * Cập nhật lịch học chi tiết
     */
    public function update(Request $request, LichHocChiTiet $lichChiTiet)
    {
        $validated = $request->validate([
            'ngay_hoc' => 'required|date',
            'tiet_bat_dau' => 'required|integer|min:1|max:10',
            'tiet_ket_thuc' => 'required|integer|min:1|max:10|gte:tiet_bat_dau',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'phong_hoc_id' => 'nullable|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'noi_dung_giang_day' => 'nullable|string',
            'tai_lieu_dinh_kem' => 'nullable|string',
            'trang_thai' => 'required|in:chua_day,dang_day,da_day,huy',
            'ghi_chu' => 'nullable|string',
        ]);

        // Kiểm tra xung đột nếu có phòng học (loại trừ chính nó)
        if ($request->phong_hoc_id) {
            $lichHocTemp = new LichHocChiTiet($validated);

            if ($lichHocTemp->kiemTraXungDotPhongTheoNgay($lichChiTiet->id)) {
                return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này'])->withInput();
            }
        }

        $lichChiTiet->update($validated);

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id)
            ->with('success', 'Đã cập nhật buổi học thành công');
    }

    /**
     * Hủy buổi học
     */
    public function cancel(LichHocChiTiet $lichChiTiet)
    {
        $lichChiTiet->update(['trang_thai' => 'huy']);

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id)
            ->with('success', 'Đã hủy buổi học thành công');
    }

    /**
     * Xóa lịch học chi tiết
     */
    public function destroy(LichHocChiTiet $lichChiTiet)
    {
        $lopHocPhanId = $lichChiTiet->lop_hoc_phan_id;
        $lichChiTiet->delete();

        return redirect()
            ->route('daotao.lop-hoc-phan.lich-chi-tiet', $lopHocPhanId)
            ->with('success', 'Đã xóa buổi học thành công');
    }
}
