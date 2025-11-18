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
        $lichHocs = LichHocCoDinh::with(['phongHoc', 'giangVien', 'caHoc'])
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
        $caHocs = \App\Models\CaHoc::where('trang_thai', true)
            ->orderBy('thu_tu')
            ->get();
        
        // Lấy giảng viên chính từ phân công (nếu có) để làm giá trị mặc định
        $giangVienChinh = $lopHocPhan->giangVienChinh;
        $giangVienChinhId = $giangVienChinh ? $giangVienChinh->giang_vien_id : null;

        return view('daotao.lich-hoc-co-dinh.create', compact('lopHocPhan', 'phongHocs', 'giangViens', 'caHocs', 'giangVienChinhId'));
    }

    /**
     * Lưu lịch học cố định mới - TẠO NHIỀU BUỔI HỌC TỰ ĐỘNG
     */
    public function store(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ca_hoc_id' => 'required|exists:ca_hoc,id',
            'so_buoi_hoc' => 'required|integer|min:1|max:50',
            'ngay_bat_dau_lich' => 'required|date',
            'thu_trong_tuan' => 'required|array|min:1',
            'thu_trong_tuan.*' => 'integer|min:2|max:8',
            'phong_hoc_id' => 'required|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'ghi_chu' => 'nullable|string',
        ], [
            'ca_hoc_id.required' => 'Ca học là bắt buộc',
            'ca_hoc_id.exists' => 'Ca học không tồn tại',
            'so_buoi_hoc.required' => 'Số buổi học là bắt buộc',
            'so_buoi_hoc.min' => 'Số buổi học tối thiểu là 1',
            'so_buoi_hoc.max' => 'Số buổi học tối đa là 50',
            'ngay_bat_dau_lich.required' => 'Ngày bắt đầu là bắt buộc',
            'ngay_bat_dau_lich.date' => 'Ngày bắt đầu phải là ngày hợp lệ',
            'thu_trong_tuan.required' => 'Vui lòng chọn ít nhất một thứ trong tuần',
            'thu_trong_tuan.min' => 'Vui lòng chọn ít nhất một thứ trong tuần',
            'phong_hoc_id.required' => 'Phòng học là bắt buộc',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
        ]);

        try {
            \DB::beginTransaction();
            
            \Log::info('Bắt đầu tạo lịch học tự động', [
                'lop_hoc_phan_id' => $lopHocPhan->id,
                'ca_hoc_id' => $validated['ca_hoc_id'],
                'so_buoi_hoc' => $validated['so_buoi_hoc'],
                'ngay_bat_dau' => $validated['ngay_bat_dau_lich'],
                'thu_trong_tuan' => $validated['thu_trong_tuan'],
            ]);

            // Lấy thông tin ca học
            $caHoc = \App\Models\CaHoc::findOrFail($validated['ca_hoc_id']);
            
            // Tính tiết bắt đầu và kết thúc dựa trên ca học (giả sử mỗi ca = 2 tiết)
            $tietBatDau = ($caHoc->thu_tu * 2) - 1;
            $tietKetThuc = $caHoc->thu_tu * 2;

            // Sắp xếp các thứ trong tuần
            $thuList = collect($validated['thu_trong_tuan'])->sort()->values()->toArray();
            
            // Tạo danh sách các ngày học
            $ngayHocList = $this->generateScheduleDates(
                $validated['ngay_bat_dau_lich'],
                $validated['so_buoi_hoc'],
                $thuList,
                $lopHocPhan->ngay_ket_thuc
            );

            if (count($ngayHocList) < $validated['so_buoi_hoc']) {
                \DB::rollBack();
                
                // Tính toán thông tin chi tiết để hiển thị
                $ngayBatDauFormatted = \Carbon\Carbon::parse($validated['ngay_bat_dau_lich'])->format('d/m/Y');
                $ngayKetThucFormatted = \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y');
                $soNgayConLai = \Carbon\Carbon::parse($validated['ngay_bat_dau_lich'])->diffInDays($lopHocPhan->ngay_ket_thuc);
                $thuNames = [2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7', 8 => 'Chủ nhật'];
                $thuSelected = collect($thuList)->map(function($thu) use ($thuNames) {
                    return $thuNames[$thu] ?? '';
                })->filter()->join(', ');
                
                $errorMessage = "Không thể tạo đủ {$validated['so_buoi_hoc']} buổi học trong khoảng thời gian của lớp học phần.\n\n";
                $errorMessage .= "📅 Thông tin:\n";
                $errorMessage .= "• Ngày bắt đầu: {$ngayBatDauFormatted}\n";
                $errorMessage .= "• Ngày kết thúc lớp: {$ngayKetThucFormatted}\n";
                $errorMessage .= "• Số ngày còn lại: {$soNgayConLai} ngày\n";
                $errorMessage .= "• Các thứ đã chọn: {$thuSelected}\n";
                $errorMessage .= "• Chỉ tạo được: " . count($ngayHocList) . " buổi\n\n";
                $errorMessage .= "💡 Giải pháp:\n";
                $errorMessage .= "• Chọn ngày bắt đầu sớm hơn (trước {$ngayBatDauFormatted})\n";
                $errorMessage .= "• Hoặc giảm số buổi học xuống " . count($ngayHocList) . " buổi\n";
                $errorMessage .= "• Hoặc kiểm tra lại ngày kết thúc của lớp học phần";
                
                return back()
                    ->withErrors(['ngay_bat_dau_lich' => $errorMessage])
                    ->withInput();
            }

            // Kiểm tra xung đột cho TẤT CẢ các buổi học trước khi tạo
            // Kiểm tra với LichHocChiTiet vì đây là lịch học theo ngày cụ thể
            $conflicts = [];
            foreach ($ngayHocList as $index => $ngayHoc) {
                $ngayHocDate = $ngayHoc['ngay']->format('Y-m-d');
                
                // Kiểm tra xung đột phòng học (kiểm tra với các lịch chi tiết đã tồn tại)
                $conflictPhong = \App\Models\LichHocChiTiet::where('phong_hoc_id', $validated['phong_hoc_id'])
                    ->where('ngay_hoc', $ngayHocDate)
                    ->where(function ($q) use ($tietBatDau, $tietKetThuc) {
                        $q->where(function ($q2) use ($tietBatDau, $tietKetThuc) {
                            $q2->where('tiet_ket_thuc', '>=', $tietBatDau)
                               ->where('tiet_bat_dau', '<=', $tietKetThuc);
                        });
                    })
                    ->exists();
                
                if ($conflictPhong) {
                    $conflicts[] = "Buổi " . ($index + 1) . " ({$ngayHoc['ngay_str']}): Phòng học đã bị trùng lịch";
                }

                // Kiểm tra xung đột giảng viên (kiểm tra với các lịch chi tiết đã tồn tại)
                $conflictGiangVien = \App\Models\LichHocChiTiet::where('giang_vien_id', $validated['giang_vien_id'])
                    ->where('ngay_hoc', $ngayHocDate)
                    ->where(function ($q) use ($tietBatDau, $tietKetThuc) {
                        $q->where(function ($q2) use ($tietBatDau, $tietKetThuc) {
                            $q2->where('tiet_ket_thuc', '>=', $tietBatDau)
                               ->where('tiet_bat_dau', '<=', $tietKetThuc);
                        });
                    })
                    ->exists();
                
                if ($conflictGiangVien) {
                    $conflicts[] = "Buổi " . ($index + 1) . " ({$ngayHoc['ngay_str']}): Giảng viên đã có lịch dạy";
                }
            }
            
            \Log::info('Kết quả kiểm tra xung đột', [
                'so_buoi_kiem_tra' => count($ngayHocList),
                'so_xung_dot' => count($conflicts),
                'xung_dot' => $conflicts
            ]);

            if (!empty($conflicts)) {
                \DB::rollBack();
                return back()
                    ->withErrors(['general' => 'Phát hiện xung đột lịch:<br>- ' . implode('<br>- ', $conflicts)])
                    ->withInput();
            }

            // Tạo các LichHocCoDinh cho từng thứ trong tuần (nếu chưa tồn tại)
            // LichHocCoDinh là lịch cố định theo tuần, không phải theo ngày
            $lichHocCoDinhMap = []; // Map: thu_trong_tuan => LichHocCoDinh
            $createdLichCoDinh = 0;
            
            foreach ($thuList as $thu) {
                // Kiểm tra xem đã có lịch cố định cho thứ này chưa
                $lichCoDinh = LichHocCoDinh::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('thu_trong_tuan', $thu)
                    ->where('tiet_bat_dau', $tietBatDau)
                    ->where('tiet_ket_thuc', $tietKetThuc)
                    ->first();
                
                if (!$lichCoDinh) {
                    // Tạo mới lịch cố định cho thứ này
                    $lichCoDinh = LichHocCoDinh::create([
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ca_hoc_id' => $validated['ca_hoc_id'],
                        'thu_trong_tuan' => $thu,
                        'tiet_bat_dau' => $tietBatDau,
                        'tiet_ket_thuc' => $tietKetThuc,
                        'gio_bat_dau' => $caHoc->gio_bat_dau,
                        'gio_ket_thuc' => $caHoc->gio_ket_thuc,
                        'phong_hoc_id' => $validated['phong_hoc_id'],
                        'giang_vien_id' => $validated['giang_vien_id'],
                        'hinh_thuc' => $validated['hinh_thuc'],
                        'link_online' => $validated['link_online'],
                        'ghi_chu' => $validated['ghi_chu'],
                    ]);
                    $createdLichCoDinh++;
                }
                
                $lichHocCoDinhMap[$thu] = $lichCoDinh;
            }
            
            // Tạo các LichHocChiTiet cho từng ngày cụ thể
            $createdLichChiTiet = 0;
            foreach ($ngayHocList as $ngayHoc) {
                try {
                    $lichCoDinh = $lichHocCoDinhMap[$ngayHoc['thu']];
                    
                    \App\Models\LichHocChiTiet::create([
                        'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                        'lop_hoc_phan_id' => $lopHocPhan->id,
                        'ca_hoc_id' => $validated['ca_hoc_id'],
                        'ngay_hoc' => $ngayHoc['ngay']->format('Y-m-d'),
                        'tiet_bat_dau' => $tietBatDau,
                        'tiet_ket_thuc' => $tietKetThuc,
                        'gio_bat_dau' => $caHoc->gio_bat_dau,
                        'gio_ket_thuc' => $caHoc->gio_ket_thuc,
                        'phong_hoc_id' => $validated['phong_hoc_id'],
                        'giang_vien_id' => $validated['giang_vien_id'],
                        'hinh_thuc' => $validated['hinh_thuc'],
                        'link_online' => $validated['link_online'],
                        'ghi_chu' => $validated['ghi_chu'],
                        'trang_thai' => 'chua_day',
                    ]);
                    $createdLichChiTiet++;
                } catch (\Exception $e) {
                    \Log::error('Lỗi khi tạo lịch học chi tiết', [
                        'ngay_hoc' => $ngayHoc['ngay_str'],
                        'thu' => $ngayHoc['thu'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
            
            $created = $createdLichChiTiet;

            \DB::commit();
            
            \Log::info('Tạo lịch học tự động thành công', [
                'lop_hoc_phan_id' => $lopHocPhan->id,
                'so_lich_co_dinh_da_tao' => $createdLichCoDinh,
                'so_lich_chi_tiet_da_tao' => $createdLichChiTiet
            ]);

            $message = "Đã tạo thành công {$createdLichChiTiet} buổi học chi tiết";
            if ($createdLichCoDinh > 0) {
                $message .= " và {$createdLichCoDinh} lịch học cố định";
            }
            $message .= "!";

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhan)
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::warning('Validation error khi tạo lịch học tự động', [
                'errors' => $e->errors()
            ]);
            throw $e; // Re-throw để Laravel xử lý validation errors
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Lỗi khi tạo lịch học tự động', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['general' => 'Có lỗi xảy ra khi tạo lịch học: ' . $e->getMessage() . '. Vui lòng kiểm tra log để biết thêm chi tiết.'])
                ->withInput();
        }
    }

    /**
     * Tạo danh sách các ngày học theo pattern
     */
    private function generateScheduleDates($ngayBatDau, $soBuoiHoc, $thuList, $ngayKetThuc)
    {
        $ngayHocList = [];
        $currentDate = \Carbon\Carbon::parse($ngayBatDau);
        $endDate = \Carbon\Carbon::parse($ngayKetThuc);
        $maxIterations = 365; // Tránh vòng lặp vô hạn
        $iterations = 0;

        while (count($ngayHocList) < $soBuoiHoc && $iterations < $maxIterations) {
            if ($currentDate->gt($endDate)) {
                // Đã vượt quá ngày kết thúc của lớp học phần
                break;
            }

            // Lấy thứ trong tuần (Carbon: 1=Monday, 7=Sunday)
            $dayOfWeek = $currentDate->dayOfWeek;
            // Chuyển đổi: Carbon (1=T2,...,0=CN) -> Hệ thống (2=T2,...,8=CN)
            $thuTrongTuan = $dayOfWeek === 0 ? 8 : $dayOfWeek + 1;

            // Kiểm tra nếu thứ hiện tại nằm trong danh sách được chọn
            if (in_array($thuTrongTuan, $thuList)) {
                $ngayHocList[] = [
                    'ngay' => $currentDate->copy(),
                    'ngay_str' => $currentDate->format('d/m/Y'),
                    'thu' => $thuTrongTuan
                ];
            }

            // Chuyển sang ngày tiếp theo
            $currentDate->addDay();
            $iterations++;
        }

        return $ngayHocList;
    }

    /**
     * Hiển thị form chỉnh sửa lịch học cố định
     */
    public function edit(LichHocCoDinh $lichCoDinh)
    {
        $phongHocs = PhongHoc::orderBy('ten_phong')->get();
        $giangViens = GiangVien::orderBy('ho_ten')->get();
        $caHocs = \App\Models\CaHoc::where('trang_thai', true)
            ->orderBy('thu_tu')
            ->get();
        
        // Lấy giảng viên chính từ phân công (nếu có) để hiển thị gợi ý
        $giangVienChinh = $lichCoDinh->lopHocPhan->giangVienChinh;
        $giangVienChinhId = $giangVienChinh ? $giangVienChinh->giang_vien_id : null;

        return view('daotao.lich-hoc-co-dinh.edit', compact('lichCoDinh', 'phongHocs', 'giangViens', 'caHocs', 'giangVienChinhId'));
    }

    /**
     * Cập nhật lịch học cố định
     */
    public function update(Request $request, LichHocCoDinh $lichCoDinh)
    {
        $validated = $request->validate([
            'thu_trong_tuan' => 'required|integer|min:2|max:8',
            'ca_hoc_id' => 'required|exists:ca_hoc,id',
            'phong_hoc_id' => 'required|exists:phong_hoc,id',
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url',
            'ghi_chu' => 'nullable|string',
        ], [
            'thu_trong_tuan.required' => 'Thứ trong tuần là bắt buộc',
            'thu_trong_tuan.min' => 'Thứ phải từ 2 đến 8',
            'thu_trong_tuan.max' => 'Thứ phải từ 2 đến 8',
            'ca_hoc_id.required' => 'Ca học là bắt buộc',
            'ca_hoc_id.exists' => 'Ca học không tồn tại',
            'phong_hoc_id.required' => 'Phòng học là bắt buộc',
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
        ]);

        // Lấy thông tin ca học để điền vào các trường tiet và gio
        $caHoc = \App\Models\CaHoc::findOrFail($validated['ca_hoc_id']);
        
        // Tính tiết bắt đầu và kết thúc dựa trên ca học (mỗi ca = 2 tiết)
        // Ca 1: tiết 1-2, Ca 2: tiết 3-4, Ca 3: tiết 5-6, v.v.
        $validated['tiet_bat_dau'] = ($caHoc->thu_tu * 2) - 1;
        $validated['tiet_ket_thuc'] = $caHoc->thu_tu * 2;
        $validated['gio_bat_dau'] = $caHoc->gio_bat_dau;
        $validated['gio_ket_thuc'] = $caHoc->gio_ket_thuc;

        // Kiểm tra xung đột (loại trừ chính nó)
        $lichHocTemp = new LichHocCoDinh($validated);

        if ($lichHocTemp->kiemTraXungDotPhong($lichCoDinh->id)) {
            return back()->withErrors(['phong_hoc_id' => 'Phòng học đã bị trùng lịch vào thời gian này'])->withInput();
        }

        if ($lichHocTemp->kiemTraXungDotGiangVien($lichCoDinh->id)) {
            return back()->withErrors(['giang_vien_id' => 'Giảng viên đã có lịch dạy vào thời gian này'])->withInput();
        }

        // Lưu giá trị cũ để so sánh
        $thuTrongTuanCu = $lichCoDinh->thu_trong_tuan;
        $caHocIdCu = $lichCoDinh->ca_hoc_id;
        
        // Kiểm tra xem thu_trong_tuan có thay đổi không
        $thuTrongTuanThayDoi = $thuTrongTuanCu != $validated['thu_trong_tuan'];
        
        // Cập nhật LichHocCoDinh trước
        $lichCoDinh->update($validated);
        
        // Nếu thứ thay đổi, xóa các LichHocChiTiet chưa dạy và tạo lại dựa trên thứ mới
        if ($thuTrongTuanThayDoi) {
            // Đếm số buổi học chi tiết hiện có (chưa dạy) để tạo lại
            $soBuoiHocHienCo = \App\Models\LichHocChiTiet::where('lich_hoc_co_dinh_id', $lichCoDinh->id)
                ->where('trang_thai', '!=', 'da_day')
                ->count();
            
            // Xóa các LichHocChiTiet chưa dạy (vì chúng không còn phù hợp với thứ mới)
            $soLichChiTietXoa = \App\Models\LichHocChiTiet::where('lich_hoc_co_dinh_id', $lichCoDinh->id)
                ->where('trang_thai', '!=', 'da_day') // Chỉ xóa các buổi chưa dạy
                ->delete();
            
            if ($soLichChiTietXoa > 0) {
                \Log::info('Đã xóa LichHocChiTiet khi thu_trong_tuan thay đổi', [
                    'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                    'thu_cu' => $thuTrongTuanCu,
                    'thu_moi' => $validated['thu_trong_tuan'],
                    'so_lich_chi_tiet_da_xoa' => $soLichChiTietXoa
                ]);
                
                // Tự động tạo lại các buổi học chi tiết dựa trên thứ mới
                $this->taoLaiLichHocChiTiet($lichCoDinh, $soBuoiHocHienCo);
            }
        }
        
        // Refresh model để có dữ liệu mới nhất (quan trọng để observer có thể lấy đúng giá trị)
        $lichCoDinh->refresh();

        $message = 'Đã cập nhật lịch học cố định thành công';
        if ($thuTrongTuanThayDoi) {
            $message .= '. Các buổi học chi tiết chưa dạy đã được tự động tạo lại dựa trên thứ mới.';
        }

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id)
            ->with('success', $message);
    }

    /**
     * Xóa lịch học cố định và tất cả lịch học chi tiết liên quan
     */
    public function destroy(LichHocCoDinh $lichCoDinh)
    {
        try {
            \DB::beginTransaction();
            
        $lopHocPhanId = $lichCoDinh->lop_hoc_phan_id;
            
            // Xóa tất cả lịch học chi tiết liên quan
            $soLichChiTiet = \App\Models\LichHocChiTiet::where('lich_hoc_co_dinh_id', $lichCoDinh->id)->count();
            \App\Models\LichHocChiTiet::where('lich_hoc_co_dinh_id', $lichCoDinh->id)->delete();
            
            // Xóa lịch học cố định
        $lichCoDinh->delete();
            
            \DB::commit();
            
            \Log::info('Đã xóa lịch học cố định và lịch học chi tiết', [
                'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                'so_lich_chi_tiet_da_xoa' => $soLichChiTiet
            ]);
            
            $message = 'Đã xóa lịch học cố định thành công';
            if ($soLichChiTiet > 0) {
                $message .= " và {$soLichChiTiet} buổi học chi tiết liên quan";
            }

        return redirect()
            ->route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhanId)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Lỗi khi xóa lịch học cố định', [
                'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id)
                ->with('error', 'Có lỗi xảy ra khi xóa lịch học: ' . $e->getMessage());
        }
    }

    /**
     * Tạo lại lịch học chi tiết khi thứ trong tuần thay đổi
     */
    private function taoLaiLichHocChiTiet(LichHocCoDinh $lichCoDinh, $soBuoiHocCanTao)
    {
        try {
            $lopHocPhan = $lichCoDinh->lopHocPhan;
            if (!$lopHocPhan) {
                \Log::warning('Không tìm thấy lớp học phần', [
                    'lich_hoc_co_dinh_id' => $lichCoDinh->id
                ]);
                return;
            }

            // Lấy ngày bắt đầu và kết thúc của lớp học phần
            $ngayBatDau = \Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau);
            $ngayKetThuc = \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc);
            
            // Bắt đầu từ ngày hôm nay hoặc ngày bắt đầu lớp học phần (lấy ngày nào muộn hơn)
            $ngayBatDauLich = \Carbon\Carbon::now()->gt($ngayBatDau) ? \Carbon\Carbon::now() : $ngayBatDau;
            
            // Tạo danh sách ngày học dựa trên thứ mới
            $thuList = [$lichCoDinh->thu_trong_tuan];
            $ngayHocList = $this->generateScheduleDates(
                $ngayBatDauLich->format('Y-m-d'),
                $soBuoiHocCanTao,
                $thuList,
                $ngayKetThuc->format('Y-m-d')
            );

            if (empty($ngayHocList)) {
                \Log::warning('Không thể tạo lịch học chi tiết mới', [
                    'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                    'thu_trong_tuan' => $lichCoDinh->thu_trong_tuan,
                    'ngay_bat_dau' => $ngayBatDauLich->format('Y-m-d'),
                    'ngay_ket_thuc' => $ngayKetThuc->format('Y-m-d')
                ]);
                return;
            }

            // Tạo các LichHocChiTiet mới
            $createdCount = 0;
            foreach ($ngayHocList as $ngayHoc) {
                // Kiểm tra xem đã có lịch học chi tiết cho ngày này chưa
                $exists = \App\Models\LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
                    ->where('ngay_hoc', $ngayHoc['ngay']->format('Y-m-d'))
                    ->where('tiet_bat_dau', $lichCoDinh->tiet_bat_dau)
                    ->where('tiet_ket_thuc', $lichCoDinh->tiet_ket_thuc)
                    ->exists();

                if (!$exists) {
                    // Kiểm tra xung đột trước khi tạo
                    $conflictPhong = \App\Models\LichHocChiTiet::where('phong_hoc_id', $lichCoDinh->phong_hoc_id)
                        ->where('ngay_hoc', $ngayHoc['ngay']->format('Y-m-d'))
                        ->where(function ($q) use ($lichCoDinh) {
                            $q->where(function ($q2) use ($lichCoDinh) {
                                $q2->where('tiet_ket_thuc', '>=', $lichCoDinh->tiet_bat_dau)
                                   ->where('tiet_bat_dau', '<=', $lichCoDinh->tiet_ket_thuc);
                            });
                        })
                        ->exists();

                    $conflictGiangVien = \App\Models\LichHocChiTiet::where('giang_vien_id', $lichCoDinh->giang_vien_id)
                        ->where('ngay_hoc', $ngayHoc['ngay']->format('Y-m-d'))
                        ->where(function ($q) use ($lichCoDinh) {
                            $q->where(function ($q2) use ($lichCoDinh) {
                                $q2->where('tiet_ket_thuc', '>=', $lichCoDinh->tiet_bat_dau)
                                   ->where('tiet_bat_dau', '<=', $lichCoDinh->tiet_ket_thuc);
                            });
                        })
                        ->exists();

                    if (!$conflictPhong && !$conflictGiangVien) {
                        \App\Models\LichHocChiTiet::create([
                            'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                            'lop_hoc_phan_id' => $lopHocPhan->id,
                            'ca_hoc_id' => $lichCoDinh->ca_hoc_id,
                            'ngay_hoc' => $ngayHoc['ngay']->format('Y-m-d'),
                            'tiet_bat_dau' => $lichCoDinh->tiet_bat_dau,
                            'tiet_ket_thuc' => $lichCoDinh->tiet_ket_thuc,
                            'gio_bat_dau' => $lichCoDinh->gio_bat_dau,
                            'gio_ket_thuc' => $lichCoDinh->gio_ket_thuc,
                            'phong_hoc_id' => $lichCoDinh->phong_hoc_id,
                            'giang_vien_id' => $lichCoDinh->giang_vien_id,
                            'hinh_thuc' => $lichCoDinh->hinh_thuc,
                            'link_online' => $lichCoDinh->link_online,
                            'ghi_chu' => $lichCoDinh->ghi_chu,
                            'trang_thai' => 'chua_day',
                        ]);
                        $createdCount++;
                    }
                }
            }

            if ($createdCount > 0) {
                \Log::info('Đã tự động tạo lại LichHocChiTiet khi thứ thay đổi', [
                    'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                    'thu_moi' => $lichCoDinh->thu_trong_tuan,
                    'so_lich_chi_tiet_da_tao' => $createdCount,
                    'so_lich_chi_tiet_can_tao' => $soBuoiHocCanTao
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Lỗi khi tạo lại lịch học chi tiết', [
                'lich_hoc_co_dinh_id' => $lichCoDinh->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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
