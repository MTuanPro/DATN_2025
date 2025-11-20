<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\DangKyMonHocTam;
use App\Models\LopHocPhanSinhVien;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Services\HocPhiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XepLopController extends Controller
{
    /**
     * Hiển thị danh sách đăng ký chờ xếp lớp
     */
    public function index(Request $request)
    {
        $query = DangKyMonHocTam::with(['sinhVien', 'monHoc', 'hocKy'])
            ->orderBy('uu_tien', 'desc')
            ->orderBy('ngay_dang_ky', 'asc');

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        } else {
            $query->where('trang_thai', 'cho_xep_lop'); // Mặc định chỉ hiện chờ xếp lớp
        }

        // Lọc theo môn học
        if ($request->filled('mon_hoc_id')) {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        $dangKys = $query->paginate(20);

        // Thống kê
        $thongKe = [
            'cho_xep_lop' => DangKyMonHocTam::choXepLop()->count(),
            'da_xep_lop' => DangKyMonHocTam::daXepLop()->count(),
            'that_bai' => DangKyMonHocTam::thatBai()->count(),
        ];

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.xep-lop.index', compact('dangKys', 'thongKe', 'hocKys'));
    }

    /**
     * Xếp lớp tự động - PHIÊN BẢN TỐI ƯU
     * Sử dụng batch processing và eager loading để giảm số lượng queries
     */
    public function autoAssign(Request $request)
    {
        $request->validate([
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
        ]);

        DB::beginTransaction();
        try {
            $hocKyId = $request->hoc_ky_id;
            
            // 1. Lấy tất cả đăng ký chờ xếp lớp (đã sắp xếp theo ưu tiên)
            $dangKys = DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
                ->where('trang_thai', 'cho_xep_lop')
                ->orderBy('uu_tien', 'desc')
                ->orderBy('ngay_dang_ky', 'asc')
                ->get();

            if ($dangKys->isEmpty()) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Không có đăng ký nào cần xếp lớp!',
                    'data' => ['thanh_cong' => 0, 'that_bai' => 0]
                ]);
            }

            // 2. Lấy tất cả môn học liên quan
            $monHocIds = $dangKys->pluck('mon_hoc_id')->unique()->values();

            // 3. Load trước tất cả lớp học phần phù hợp (Eager Loading)
            $lopHocPhans = LopHocPhan::whereIn('mon_hoc_id', $monHocIds)
                ->where('hoc_ky_id', $hocKyId)
                ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
                ->get()
                ->groupBy('mon_hoc_id');

            // 4. Load trước số lượng sinh viên đã đăng ký cho mỗi lớp
            $lopHocPhanIds = $lopHocPhans->flatten()->pluck('id')->unique();
            $soLuongDaDangKy = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->select('lop_hoc_phan_id', DB::raw('COUNT(*) as so_luong'))
                ->groupBy('lop_hoc_phan_id')
                ->pluck('so_luong', 'lop_hoc_phan_id')
                ->toArray();

            // 5. Load trước danh sách sinh viên đã đăng ký (để kiểm tra trùng)
            $sinhVienIds = $dangKys->pluck('sinh_vien_id')->unique();
            $daDangKyLop = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
                ->whereIn('sinh_vien_id', $sinhVienIds)
                ->select('lop_hoc_phan_id', 'sinh_vien_id')
                ->get()
                ->groupBy('sinh_vien_id')
                ->map(function ($group) {
                    return $group->pluck('lop_hoc_phan_id')->toArray();
                })
                ->toArray();

            // 6. Xử lý xếp lớp
            $dataToInsert = [];
            $dangKyUpdates = [
                'thanh_cong' => [],
                'that_bai' => []
            ];

            foreach ($dangKys as $dangKy) {
                $monHocId = $dangKy->mon_hoc_id;
                $sinhVienId = $dangKy->sinh_vien_id;
                
                // Lấy các lớp của môn học này
                $cacLopHocPhan = $lopHocPhans->get($monHocId, collect());
                
                $daXepLop = false;
                
                foreach ($cacLopHocPhan as $lopHocPhan) {
                    $lopId = $lopHocPhan->id;
                    
                    // Kiểm tra sinh viên đã đăng ký lớp này chưa
                    if (isset($daDangKyLop[$sinhVienId]) && in_array($lopId, $daDangKyLop[$sinhVienId])) {
                        continue; // Đã đăng ký rồi, thử lớp khác
                    }
                    
                    // Lấy sức chứa và số lượng hiện tại
                    $sucChua = $lopHocPhan->suc_chua ?? 0;
                    $soLuongHienTai = $soLuongDaDangKy[$lopId] ?? 0;
                    
                    // Kiểm tra còn chỗ không
                    if ($soLuongHienTai < $sucChua) {
                        // Thêm vào danh sách insert
                        $dataToInsert[] = [
                            'lop_hoc_phan_id' => $lopId,
                            'sinh_vien_id' => $sinhVienId,
                            'dang_ky_tam_id' => $dangKy->id,
                            'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                            'ngay_xep_lop' => now(),
                            'phuong_thuc_xep' => 'tu_dong',
                            'trang_thai' => 'da_xep_lop',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        // Đánh dấu đã xếp lớp
                        $dangKyUpdates['thanh_cong'][] = $dangKy->id;
                        
                        // Cập nhật số lượng trong bộ nhớ để các lần xử lý sau chính xác
                        $soLuongDaDangKy[$lopId] = $soLuongHienTai + 1;
                        
                        // Cập nhật danh sách đã đăng ký trong bộ nhớ
                        if (!isset($daDangKyLop[$sinhVienId])) {
                            $daDangKyLop[$sinhVienId] = [];
                        }
                        $daDangKyLop[$sinhVienId][] = $lopId;
                        
                        $daXepLop = true;
                        break; // Đã xếp được lớp, dừng vòng lặp
                    }
                }
                
                // Nếu không xếp được lớp nào
                if (!$daXepLop) {
                    $dangKyUpdates['that_bai'][] = $dangKy->id;
                }
            }

            // 7. Bulk Insert vào bảng lop_hoc_phan_sinh_vien
            if (!empty($dataToInsert)) {
                // Insert theo batch 500 records để tránh query quá lớn
                foreach (array_chunk($dataToInsert, 500) as $chunk) {
                    LopHocPhanSinhVien::insert($chunk);
                }
            }

            // 8. Bulk Update trạng thái đăng ký tạm
            if (!empty($dangKyUpdates['thanh_cong'])) {
                DangKyMonHocTam::whereIn('id', $dangKyUpdates['thanh_cong'])
                    ->update(['trang_thai' => 'da_xep_lop']);
            }

            if (!empty($dangKyUpdates['that_bai'])) {
                DangKyMonHocTam::whereIn('id', $dangKyUpdates['that_bai'])
                    ->update([
                        'trang_thai' => 'that_bai',
                        'ly_do_that_bai' => 'Không còn chỗ trong các lớp học phần'
                    ]);
            }

            // 9. Đồng bộ số lượng trong bảng lop_hoc_phan
            foreach ($soLuongDaDangKy as $lopId => $soLuong) {
                LopHocPhan::where('id', $lopId)
                    ->update(['so_luong_dang_ky' => $soLuong]);
            }

            // 10. TÍNH HỌC PHÍ CHO SINH VIÊN ĐÃ XẾP LỚP THÀNH CÔNG
            if (!empty($dangKyUpdates['thanh_cong'])) {
                $this->tinhHocPhiSauXepLop($hocKyId, $dataToInsert);
            }

            DB::commit();

            $soLuongThanhCong = count($dangKyUpdates['thanh_cong']);
            $soLuongThatBai = count($dangKyUpdates['that_bai']);

            return response()->json([
                'success' => true,
                'message' => "Xếp lớp hoàn tất! Thành công: {$soLuongThanhCong}, Thất bại: {$soLuongThatBai}",
                'data' => [
                    'thanh_cong' => $soLuongThanhCong,
                    'that_bai' => $soLuongThatBai
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xếp lớp tự động: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xếp lớp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xếp lớp tự động - PHIÊN BẢN CŨ (giữ lại để tham khảo)
     * KHÔNG SỬ DỤNG - Code này xử lý từng sinh viên một (chậm)
     */
    private function autoAssign_OLD(Request $request)
    {
        $request->validate([
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
        ]);

        $hocKyId = $request->hoc_ky_id;

        DB::beginTransaction();
        try {
            // Lấy tất cả đăng ký chờ xếp lớp, sắp xếp theo độ ưu tiên
            $dangKys = DangKyMonHocTam::where('hoc_ky_id', $hocKyId)
                ->where('trang_thai', 'cho_xep_lop')
                ->orderBy('uu_tien', 'desc')
                ->orderBy('ngay_dang_ky', 'asc')
                ->get();

            $soLuongXepThanhCong = 0;
            $soLuongThatBai = 0;

            foreach ($dangKys as $dangKy) {
                $result = $this->xepLopChoSinhVien($dangKy);

                if ($result['success']) {
                    $soLuongXepThanhCong++;
                } else {
                    $soLuongThatBai++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Xếp lớp hoàn tất! Thành công: {$soLuongXepThanhCong}, Thất bại: {$soLuongThatBai}",
                'data' => [
                    'thanh_cong' => $soLuongXepThanhCong,
                    'that_bai' => $soLuongThatBai
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xếp lớp tự động: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xếp lớp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xếp lớp cho một sinh viên cụ thể - PHIÊN BẢN TỐI ƯU
     * Tối ưu số lượng queries
     */
    private function xepLopChoSinhVien($dangKy)
    {
        // Tìm tất cả lớp học phần phù hợp với eager loading số lượng sinh viên
        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $dangKy->mon_hoc_id)
            ->where('hoc_ky_id', $dangKy->hoc_ky_id)
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->get();

        if ($lopHocPhans->isEmpty()) {
            $dangKy->update([
                'trang_thai' => 'that_bai',
                'ly_do_that_bai' => 'Không tìm thấy lớp học phần phù hợp'
            ]);
            return ['success' => false, 'message' => 'Không tìm thấy lớp'];
        }

        // Lấy IDs của các lớp
        $lopHocPhanIds = $lopHocPhans->pluck('id')->toArray();

        // Query 1 lần để lấy số lượng sinh viên của tất cả các lớp
        $soLuongDaDangKy = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->select('lop_hoc_phan_id', DB::raw('COUNT(*) as so_luong'))
            ->groupBy('lop_hoc_phan_id')
            ->pluck('so_luong', 'lop_hoc_phan_id')
            ->toArray();

        // Query 1 lần để kiểm tra sinh viên đã đăng ký lớp nào chưa
        $daDangKyLopIds = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopHocPhanIds)
            ->where('sinh_vien_id', $dangKy->sinh_vien_id)
            ->pluck('lop_hoc_phan_id')
            ->toArray();

        foreach ($lopHocPhans as $lopHocPhan) {
            // Kiểm tra sinh viên đã đăng ký lớp này chưa
            if (in_array($lopHocPhan->id, $daDangKyLopIds)) {
                continue; // Đã đăng ký rồi, thử lớp khác
            }

            // Lấy sức chứa và số lượng thực tế
            $sucChua = $lopHocPhan->suc_chua ?? 0;
            $soLuongThucTe = $soLuongDaDangKy[$lopHocPhan->id] ?? 0;

            // Kiểm tra còn chỗ không
            if ($soLuongThucTe < $sucChua) {
                // Đồng bộ so_luong_dang_ky với số lượng thực tế trước khi xếp lớp
                if ($lopHocPhan->so_luong_dang_ky != $soLuongThucTe) {
                    $lopHocPhan->so_luong_dang_ky = $soLuongThucTe;
                    $lopHocPhan->save();
                }

                // Xếp vào lớp này
                // Observer sẽ tự động cập nhật so_luong_dang_ky, không cần cập nhật thủ công
                LopHocPhanSinhVien::create([
                    'lop_hoc_phan_id' => $lopHocPhan->id,
                    'sinh_vien_id' => $dangKy->sinh_vien_id,
                    'dang_ky_tam_id' => $dangKy->id,
                    'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                    'ngay_xep_lop' => now(),
                    'phuong_thuc_xep' => 'tu_dong',
                    'trang_thai' => 'da_xep_lop',
                ]);

                // Cập nhật trạng thái đăng ký tạm
                $dangKy->update([
                    'trang_thai' => 'da_xep_lop',
                ]);

                return ['success' => true, 'lop_hoc_phan_id' => $lopHocPhan->id];
            }
        }

        // Không tìm thấy lớp phù hợp
        $dangKy->update([
            'trang_thai' => 'that_bai',
            'ly_do_that_bai' => 'Không còn chỗ trong các lớp học phần'
        ]);

        return ['success' => false, 'message' => 'Không còn chỗ'];
    }

    /**
     * Xếp lớp thủ công
     */
    public function manualAssign(Request $request)
    {
        $request->validate([
            'dang_ky_tam_id' => 'required|exists:dang_ky_mon_hoc_tam,id',
            'lop_hoc_phan_id' => 'required|exists:lop_hoc_phan,id',
        ]);

        DB::beginTransaction();
        try {
            $dangKy = DangKyMonHocTam::findOrFail($request->dang_ky_tam_id);
            $lopHocPhan = LopHocPhan::findOrFail($request->lop_hoc_phan_id);

            // Kiểm tra lớp còn chỗ không - tính từ bảng thực tế
            $sucChua = $lopHocPhan->suc_chua ?? 0;
            $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                ->count();

            if ($soLuongThucTe >= $sucChua) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lớp học phần đã đầy!'
                ], 400);
            }

            // Đồng bộ so_luong_dang_ky với số lượng thực tế trước khi xếp lớp
            if ($lopHocPhan->so_luong_dang_ky != $soLuongThucTe) {
                $lopHocPhan->so_luong_dang_ky = $soLuongThucTe;
                $lopHocPhan->save();
            }

            // Kiểm tra sinh viên đã đăng ký lớp này chưa
            $exists = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                ->where('sinh_vien_id', $dangKy->sinh_vien_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinh viên đã đăng ký lớp này rồi!'
                ], 400);
            }

            // Xếp vào lớp
            // Observer sẽ tự động cập nhật so_luong_dang_ky, không cần cập nhật thủ công
            LopHocPhanSinhVien::create([
                'lop_hoc_phan_id' => $lopHocPhan->id,
                'sinh_vien_id' => $dangKy->sinh_vien_id,
                'dang_ky_tam_id' => $dangKy->id,
                'ngay_dang_ky' => $dangKy->ngay_dang_ky,
                'ngay_xep_lop' => now(),
                'nguoi_duyet_id' => Auth::id(),
                'ngay_duyet' => now(),
                'phuong_thuc_xep' => 'thu_cong',
                'trang_thai' => 'da_xep_lop',
            ]);

            // Cập nhật trạng thái đăng ký tạm
            $dangKy->update([
                'trang_thai' => 'da_xep_lop',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xếp lớp thủ công thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xếp lớp thủ công: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Danh sách chờ (waiting list)
     */
    public function waitingList(Request $request)
    {
        $query = DangKyMonHocTam::with(['sinhVien', 'monHoc', 'hocKy'])
            ->where('trang_thai', 'that_bai')
            ->orderBy('uu_tien', 'desc')
            ->orderBy('ngay_dang_ky', 'asc');

        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        $waitingList = $query->paginate(20);
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.xep-lop.waiting-list', compact('waitingList', 'hocKys'));
    }

    /**
     * Xem danh sách sinh viên trong lớp học phần
     */
    public function danhSachLop($lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'giangVienChinh.giangVien'])
            ->findOrFail($lopHocPhanId);

        $sinhViens = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhanId)
            ->with(['sinhVien.lopHanhChinh', 'dangKyTam'])
            ->orderBy('ngay_xep_lop', 'asc')
            ->get();

        return view('daotao.xep-lop.danh-sach-lop', compact('lopHocPhan', 'sinhViens'));
    }

    /**
     * Lấy danh sách lớp học phần theo môn học (cho modal xếp lớp thủ công)
     */
    public function getLopHocPhanByMonHoc($monHocId, Request $request)
    {
        $hocKyId = $request->get('hoc_ky_id');

        $lopHocPhans = LopHocPhan::where('mon_hoc_id', $monHocId)
            ->when($hocKyId, function ($query) use ($hocKyId) {
                $query->where('hoc_ky_id', $hocKyId);
            })
            ->whereIn('trang_thai_lop', ['mo_dang_ky', 'dang_hoc'])
            ->with('monHoc')
            ->get()
            ->map(function ($lop) {
                $sucChua = $lop->suc_chua ?? 0;
                // Tính số lượng thực tế từ bảng lop_hoc_phan_sinh_vien thay vì dùng so_luong_dang_ky
                $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                    ->count();
                $conTrong = $sucChua - $soLuongThucTe;

                return [
                    'id' => $lop->id,
                    'ma_lop_hoc_phan' => $lop->ma_lop_hp,
                    'ten_lop_hoc_phan' => $lop->ten_lop_hp,
                    'so_luong_toi_da' => $sucChua,
                    'so_luong_hien_tai' => $soLuongThucTe,
                    'con_trong' => $conTrong,
                ];
            })
            ->filter(function ($lop) {
                return $lop['con_trong'] > 0;
            });

        return response()->json([
            'success' => true,
            'data' => $lopHocPhans->values()
        ]);
    }

    /**
     * Xóa sinh viên khỏi lớp học phần
     */
    public function xoaKhoiLop($lhpsvId)
    {
        try {
            DB::beginTransaction();

            $lhpsv = LopHocPhanSinhVien::findOrFail($lhpsvId);

            // Kiểm tra trạng thái
            if (!in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sinh viên với trạng thái này!'
                ], 400);
            }

            // Observer sẽ tự động cập nhật so_luong_dang_ky khi xóa, không cần cập nhật thủ công
            $lopHocPhan = $lhpsv->lopHocPhan;

            // Cập nhật trạng thái đăng ký tạm về chờ xếp lớp
            if ($lhpsv->dang_ky_tam_id) {
                DangKyMonHocTam::where('id', $lhpsv->dang_ky_tam_id)
                    ->update([
                        'trang_thai' => 'cho_xep_lop',
                        'ly_do_that_bai' => null
                    ]);
            }

            // Xóa bản ghi xếp lớp
            $lhpsv->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sinh viên khỏi lớp thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa sinh viên khỏi lớp: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tính học phí cho sinh viên sau khi xếp lớp thành công
     * 
     * @param int $hocKyId
     * @param array $dataToInsert Mảng data đã insert vào lop_hoc_phan_sinh_vien
     */
    private function tinhHocPhiSauXepLop($hocKyId, $dataToInsert)
    {
        try {
            $hocPhiService = app(HocPhiService::class);

            // Nhóm theo sinh viên
            $sinhVienGroups = [];
            foreach ($dataToInsert as $item) {
                $sinhVienId = $item['sinh_vien_id'];
                if (!isset($sinhVienGroups[$sinhVienId])) {
                    $sinhVienGroups[$sinhVienId] = [];
                }
                // Lưu ID của bản ghi lop_hoc_phan_sinh_vien vừa được tạo
                // (cần query lại vì insert() không trả về ID)
            }

            // Query lại các bản ghi vừa tạo để lấy ID
            foreach ($sinhVienGroups as $sinhVienId => $items) {
                $lopHocPhanSinhVienIds = LopHocPhanSinhVien::where('sinh_vien_id', $sinhVienId)
                    ->whereHas('lopHocPhan', function ($query) use ($hocKyId) {
                        $query->where('hoc_ky_id', $hocKyId);
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($lopHocPhanSinhVienIds)) {
                    $hocPhiService->tinhHocPhiKhiDangKy($sinhVienId, $hocKyId, $lopHocPhanSinhVienIds);
                    Log::info("Đã tính học phí cho sinh viên ID: {$sinhVienId}, Học kỳ: {$hocKyId}");
                }
            }

        } catch (\Exception $e) {
            Log::error('Lỗi tính học phí sau xếp lớp: ' . $e->getMessage());
            // Không throw exception để không rollback việc xếp lớp
        }
    }
}
