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
        $lichHocs = LichHocChiTiet::with(['phongHoc', 'giangVien', 'lichHocCoDinh', 'caHoc'])
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
                        // Kiểm tra xung đột phòng học
                        // Chỉ kiểm tra với lịch học chi tiết khác (không kiểm tra với lịch học cố định vì đây là lịch được tạo từ lịch học cố định)
                        if ($lichCoDinh->phong_hoc_id) {
                            $lichHocChiTietTemp = new LichHocChiTiet([
                                'lop_hoc_phan_id' => $lopHocPhan->id,
                                'ngay_hoc' => $date->format('Y-m-d'),
                                'tiet_bat_dau' => $lichCoDinh->tiet_bat_dau,
                                'tiet_ket_thuc' => $lichCoDinh->tiet_ket_thuc,
                                'phong_hoc_id' => $lichCoDinh->phong_hoc_id,
                            ]);

                            // Kiểm tra xung đột với lịch học chi tiết khác
                            if ($lichHocChiTietTemp->kiemTraXungDotPhongTheoNgay()) {
                                // Bỏ qua lịch này nếu bị trùng với lịch học chi tiết khác
                                continue;
                            }

                            // Kiểm tra xung đột với lịch học cố định KHÁC (không phải lịch hiện tại)
                            // Tức là kiểm tra xem có lịch học cố định khác cùng phòng, cùng thứ, trùng ca không
                            $xungDotCoDinhKhac = LichHocCoDinh::where('phong_hoc_id', $lichCoDinh->phong_hoc_id)
                                ->where('thu_trong_tuan', $thu)
                                ->where('id', '!=', $lichCoDinh->id)
                                ->where(function ($q) use ($lichCoDinh) {
                                    $q->where('tiet_ket_thuc', '>=', $lichCoDinh->tiet_bat_dau)
                                      ->where('tiet_bat_dau', '<=', $lichCoDinh->tiet_ket_thuc);
                                })
                                ->exists();

                            if ($xungDotCoDinhKhac) {
                                // Bỏ qua lịch này nếu bị trùng với lịch học cố định khác
                                continue;
                            }
                        }

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
            ->route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lopHocPhan)
            ->with('success', "Đã tạo $count buổi học chi tiết thành công");
    }

    /**
     * Hiển thị form tạo lịch học chi tiết (thêm buổi bù, bổ sung)
     */
    public function create(LopHocPhan $lopHocPhan)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        
        // Chỉ lấy giảng viên được phân công dạy lớp học phần này
        $giangVienIds = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhan->id)
            ->pluck('giang_vien_id')
            ->unique()
            ->toArray();
        
        $giangViens = GiangVien::whereIn('id', $giangVienIds)
            ->orderBy('ho_ten')
            ->get();

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
            'gio_ket_thuc' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $gioBatDau = $request->input('gio_bat_dau');
                    if ($gioBatDau && $value) {
                        $timeBatDau = \Carbon\Carbon::createFromFormat('H:i', $gioBatDau);
                        $timeKetThuc = \Carbon\Carbon::createFromFormat('H:i', $value);
                        if ($timeKetThuc->lte($timeBatDau)) {
                            $fail('Giờ kết thúc phải sau giờ bắt đầu');
                        }
                    }
                },
            ],
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
            'gio_bat_dau.date_format' => 'Giờ bắt đầu phải có định dạng HH:mm',
            'gio_ket_thuc.required' => 'Giờ kết thúc là bắt buộc',
            'gio_ket_thuc.date_format' => 'Giờ kết thúc phải có định dạng HH:mm',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
        ]);

        $validated['lop_hoc_phan_id'] = $lopHocPhan->id;
        $validated['trang_thai'] = 'chua_day';

        // Kiểm tra xung đột nếu có phòng học (bao gồm cả lịch học cố định và lịch học chi tiết)
        if ($request->phong_hoc_id) {
            $lichHoc = new LichHocChiTiet($validated);

            if ($lichHoc->kiemTraXungDotPhongDayDu()) {
                return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này. Vui lòng chọn phòng hoặc ca khác.'])->withInput();
            }
        }

        LichHocChiTiet::create($validated);

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lopHocPhan)
            ->with('success', 'Đã thêm buổi học thành công');
    }

    /**
     * Hiển thị form chỉnh sửa lịch học chi tiết
     */
    public function edit(LichHocChiTiet $lichChiTiet)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        
        // Chỉ lấy giảng viên được phân công dạy lớp học phần này
        $giangVienIds = \App\Models\PhanCongGiangDay::where('lop_hoc_phan_id', $lichChiTiet->lop_hoc_phan_id)
            ->pluck('giang_vien_id')
            ->unique()
            ->toArray();
        
        $giangViens = GiangVien::whereIn('id', $giangVienIds)
            ->orderBy('ho_ten')
            ->get();
        
        $caHocs = \App\Models\CaHoc::where('trang_thai', true)
            ->orderBy('thu_tu')
            ->get();

        return view('daotao.lich-hoc-chi-tiet.edit', compact('lichChiTiet', 'phongHocs', 'giangViens', 'caHocs'));
    }

    /**
     * Cập nhật lịch học chi tiết
     */
    public function update(Request $request, LichHocChiTiet $lichChiTiet)
    {
        $validated = $request->validate([
            'ngay_hoc' => 'required|date',
            'ca_hoc_id' => 'required|exists:ca_hoc,id',
            'phong_hoc_id' => 'nullable|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'noi_dung_giang_day' => 'nullable|string',
            'tai_lieu_dinh_kem' => 'nullable|string',
            'trang_thai' => 'required|in:chua_day,dang_day,da_day,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ngay_hoc.required' => 'Ngày học là bắt buộc',
            'ca_hoc_id.required' => 'Ca học là bắt buộc',
            'ca_hoc_id.exists' => 'Ca học không tồn tại',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
        ]);

        // Lấy thông tin ca học để điền vào các trường tiet và gio
        $caHoc = \App\Models\CaHoc::findOrFail($validated['ca_hoc_id']);
        $validated['tiet_bat_dau'] = $caHoc->tiet_bat_dau;
        $validated['tiet_ket_thuc'] = $caHoc->tiet_ket_thuc;
        $validated['gio_bat_dau'] = $caHoc->gio_bat_dau;
        $validated['gio_ket_thuc'] = $caHoc->gio_ket_thuc;

        // Kiểm tra xung đột nếu có phòng học (bao gồm cả lịch học cố định và lịch học chi tiết, loại trừ chính nó)
        if ($request->phong_hoc_id) {
            $lichHocTemp = new LichHocChiTiet($validated);

            // Loại trừ lịch học cố định mà lịch học chi tiết này được tạo từ đó (nếu có)
            $excludeLichCoDinhId = $lichChiTiet->lich_hoc_co_dinh_id;

            if ($lichHocTemp->kiemTraXungDotPhongDayDu($lichChiTiet->id, $excludeLichCoDinhId)) {
                return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này. Vui lòng chọn phòng hoặc ca khác.'])->withInput();
            }
        }

        $lichChiTiet->update($validated);

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id)
            ->with('success', 'Đã cập nhật buổi học thành công');
    }

    /**
     * Hủy buổi học
     */
    public function cancel(LichHocChiTiet $lichChiTiet)
    {
        $lichChiTiet->update(['trang_thai' => 'huy']);

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id)
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
            ->route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lopHocPhanId)
            ->with('success', 'Đã xóa buổi học thành công');
    }
}
