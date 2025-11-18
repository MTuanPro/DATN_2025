<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\DanhMuc\PhongHoc;
use App\Models\GiangVien;
use App\Models\HocKy;
use App\Models\CaHoc;
use App\Models\DiemDanh;
use App\Models\CauHinhDauDiem;
use App\Models\NhapDiem;
use App\Models\LichHocChiTiet;
use App\Http\Requests\StoreLichThiRequest;
use App\Http\Requests\UpdateLichThiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LichThiController extends Controller
{
    /**
     * Hiển thị danh sách lịch thi
     */
    public function index(Request $request)
    {
        $query = LichThi::with([
            'lopHocPhan.monHoc', 
            'lopHocPhan.hocKy',
            'phongThi', 
            'giamThi1', 
            'giamThi2',
            'hocKy'
        ]);

        // Lọc theo học kỳ (thông qua lop_hoc_phan)
        if ($request->filled('hoc_ky_id')) {
            $query->whereHas('lopHocPhan', function($q) use ($request) {
                $q->where('hoc_ky_id', $request->hoc_ky_id);
            });
        }

        // Lọc theo loại thi
        if ($request->filled('loai_thi')) {
            $query->where('loai_thi', $request->loai_thi);
        }

        // Lọc theo ngày thi
        if ($request->filled('ngay_thi_from')) {
            $query->whereDate('ngay_thi', '>=', $request->ngay_thi_from);
        }
        if ($request->filled('ngay_thi_to')) {
            $query->whereDate('ngay_thi', '<=', $request->ngay_thi_to);
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

        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();

        return view('daotao.lich-thi.index', compact('lichThis', 'hocKys'));
    }

    /**
     * Hiển thị form tạo lịch thi mới
     */
    public function create()
    {
        // Load tất cả lớp học phần (sắp xếp theo học kỳ mới nhất)
        $lopHocPhans = LopHocPhan::with('monHoc', 'hocKy')
            ->whereHas('hocKy')
            ->orderBy('hoc_ky_id', 'desc')
            ->get();

        $phongHocs = PhongHoc::all();
        $giangViens = GiangVien::with('user')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->orderBy('ten_hoc_ky', 'desc')->get();
        $caHocs = CaHoc::getCaHocHoatDong();

        return view('daotao.lich-thi.create', compact('lopHocPhans', 'phongHocs', 'giangViens', 'hocKys', 'caHocs'));
    }

    /**
     * Lưu lịch thi mới
     */
    public function store(StoreLichThiRequest $request)
    {
        try {
            DB::beginTransaction();

            // Thu thập tất cả lỗi validation
            $errors = [];

            // 1. Lấy lớp học phần
            $lopHocPhan = LopHocPhan::with(['hocKy', 'lopHocPhanSinhViens'])->findOrFail($request->lop_hoc_phan_id);

            // 2. Kiểm tra ngày thi trong phạm vi học kỳ
            $hocKy = $lopHocPhan->hocKy;
            $ngayThi = \Carbon\Carbon::parse($request->ngay_thi);
            
            if ($ngayThi->lt($hocKy->ngay_bat_dau) || $ngayThi->gt($hocKy->ngay_ket_thuc)) {
                $errors[] = 'Ngày thi phải nằm trong phạm vi học kỳ (' . 
                           $hocKy->ngay_bat_dau->format('d/m/Y') . ' - ' . 
                           $hocKy->ngay_ket_thuc->format('d/m/Y') . ')';
            }

            // 3. Lấy thông tin ca học và tính giờ từ ca học
            if (!$request->ca_hoc_id) {
                $errors[] = 'Vui lòng chọn ca thi';
            } else {
                $caHoc = CaHoc::findOrFail($request->ca_hoc_id);
                $gioBatDau = $caHoc->gio_bat_dau;
                $gioKetThuc = $caHoc->gio_ket_thuc;
            }

            // 4. Kiểm tra trùng lịch thi sinh viên (sinh viên không được thi 2 môn cùng lúc)
            $sinhVienIds = $lopHocPhan->lopHocPhanSinhViens->pluck('sinh_vien_id')->toArray();
            
            if (!empty($sinhVienIds) && isset($gioBatDau) && isset($gioKetThuc)) {
                $trungLichSinhVien = LichThi::kiemTraXungDotSinhVien(
                    $sinhVienIds,
                    $request->ngay_thi,
                    $gioBatDau,
                    $gioKetThuc
                );

                if ($trungLichSinhVien) {
                    $errors[] = 'Có sinh viên trong lớp đã có lịch thi trùng giờ (Môn: ' . $trungLichSinhVien->lopHocPhan->monHoc->ten_mon . ' vào ' . $trungLichSinhVien->gio_bat_dau . '-' . $trungLichSinhVien->gio_ket_thuc . ')';
                }
            }

            // 5. Kiểm tra sức chứa phòng
            if ($request->phong_thi_id) {
                $phongHoc = PhongHoc::find($request->phong_thi_id);
                $soSinhVienDuThi = $request->so_sinh_vien_du_thi ?? $lopHocPhan->lopHocPhanSinhViens->count();
                
                if ($soSinhVienDuThi > $phongHoc->suc_chua) {
                    $errors['phong_thi_id'] = 'Phòng thi chỉ chứa được ' . $phongHoc->suc_chua . ' sinh viên, không đủ cho ' . $soSinhVienDuThi . ' sinh viên dự thi!';
                }
            }

            // Nếu có lỗi thì rollback và trả về tất cả lỗi
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->withErrors($errors);
            }

            $data = $request->validated();

            // Lấy giờ từ ca học và thêm vào data
            if (isset($caHoc)) {
                $data['ca_hoc_id'] = $caHoc->id;
                $data['gio_bat_dau'] = $gioBatDau;
                $data['gio_ket_thuc'] = $gioKetThuc;
            }

            // Xử lý upload file đề thi (timestamp + slug)
            if ($request->hasFile('de_thi_file')) {
                $file = $request->file('de_thi_file');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
                $data['de_thi_file'] = $file->storeAs('de-thi', $fileName, 'public');
            }

            // Xử lý upload file đáp án (timestamp + slug)
            if ($request->hasFile('dap_an_file')) {
                $file = $request->file('dap_an_file');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
                $data['dap_an_file'] = $file->storeAs('dap-an', $fileName, 'public');
            }

            // Tự động tính số sinh viên dự thi nếu không nhập
            if (!isset($data['so_sinh_vien_du_thi'])) {
                $data['so_sinh_vien_du_thi'] = $lopHocPhan->lopHocPhanSinhViens->count();
            }

            $lichThi = LichThi::create($data);

            // 5. Tự động phân sinh viên vào phòng thi
            $sinhViens = $lopHocPhan->lopHocPhanSinhViens()->with('sinhVien')->get();
            
            if ($sinhViens->isNotEmpty()) {
                $lichThiSinhVienData = [];
                $soBaoDanhCounter = 1;
                
                foreach ($sinhViens as $lopHocPhanSinhVien) {
                    $lichThiSinhVienData[] = [
                        'lich_thi_id' => $lichThi->id,
                        'sinh_vien_id' => $lopHocPhanSinhVien->sinh_vien_id,
                        'phong_thi_id' => $request->phong_thi_id, // Dùng phòng mặc định từ lịch thi
                        'so_bao_danh' => str_pad($soBaoDanhCounter, 4, '0', STR_PAD_LEFT), // 0001, 0002, ...
                        'trang_thai' => 'du_thi',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $soBaoDanhCounter++;
                }
                
                // Insert hàng loạt để tối ưu performance
                \App\Models\LichThiSinhVien::insert($lichThiSinhVienData);
                
                Log::info('Đã tự động phân ' . count($lichThiSinhVienData) . ' sinh viên vào lịch thi ID: ' . $lichThi->id);
            }

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Thêm lịch thi thành công! Đã tự động phân ' . ($sinhViens->count() ?? 0) . ' sinh viên vào phòng thi.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e; // Để Laravel tự xử lý validation errors
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi thêm lịch thi: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra khi thêm lịch thi. Vui lòng thử lại hoặc liên hệ quản trị viên.']);
        }
    }

    /**
     * Hiển thị chi tiết lịch thi
     */
    public function show(LichThi $lichThi)
    {
        $lichThi->load([
            'lopHocPhan.monHoc', 
            'lopHocPhan.hocKy',
            'lopHocPhan.lopHocPhanSinhViens.sinhVien.lopHanhChinh', 
            'hocKy',
            'phongThi', 
            'giamThi1', 
            'giamThi2',
            'caHoc'
        ]);

        // Kiểm tra điều kiện dự thi cho từng sinh viên
        $tongBuoiHoc = LichHocChiTiet::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->where('ngay_hoc', '<=', now()->toDateString())
            ->where('trang_thai', '!=', 'huy')
            ->count();

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lichThi->lop_hoc_phan_id)
            ->orderBy('id')
            ->get();

        $danhSachSinhVienDiThi = [];

        foreach ($lichThi->lopHocPhan->lopHocPhanSinhViens as $lhpsv) {
            // 1. Kiểm tra chuyên cần (vắng quá 25% = có mặt < 75%)
            $diemDanhStats = DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                ->selectRaw('
                    COUNT(*) as tong_buoi_diem_danh,
                    SUM(CASE WHEN trang_thai = "co_mat" THEN 1 ELSE 0 END) as co_mat,
                    SUM(CASE WHEN trang_thai = "vang" THEN 1 ELSE 0 END) as vang,
                    SUM(CASE WHEN trang_thai = "nghi_phep" THEN 1 ELSE 0 END) as nghi_phep
                ')
                ->first();

            $coMat = $diemDanhStats ? ($diemDanhStats->co_mat ?? 0) : 0;
            $vang = $diemDanhStats ? ($diemDanhStats->vang ?? 0) : 0;
            $nghiPhep = $diemDanhStats ? ($diemDanhStats->nghi_phep ?? 0) : 0;
            
            $tyLeCoMat = $tongBuoiHoc > 0 
                ? round(($coMat / $tongBuoiHoc) * 100, 1) 
                : 0;
            
            $khongDatChuyenCan = $tyLeCoMat < 75;

            // 2. Kiểm tra điểm trung bình các đầu điểm < 5
            $diemTrungBinh = null;
            $khongDatDiem = false;

            if ($cauHinhs->isNotEmpty()) {
                $tongDiem = 0;
                $tongTyLe = 0;
                $coDiem = false;

                foreach ($cauHinhs as $cauHinh) {
                    $diems = NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                        ->where('cau_hinh_id', $cauHinh->id)
                        ->get();

                    if ($diems->isEmpty()) {
                        continue;
                    }

                    $coDiem = true;
                    $diemTrungBinhDauDiem = $diems->avg('diem_so');
                    
                    if ($diemTrungBinhDauDiem !== null) {
                        $tongDiem += $diemTrungBinhDauDiem * ($cauHinh->ty_le / 100);
                        $tongTyLe += $cauHinh->ty_le;
                    }
                }

                if ($coDiem && $tongTyLe > 0) {
                    if ($tongTyLe < 100) {
                        $diemTrungBinh = round(($tongDiem / $tongTyLe) * 100, 2);
                    } else {
                        $diemTrungBinh = round($tongDiem, 2);
                    }
                    
                    $khongDatDiem = $diemTrungBinh < 5;
                }
            }

            // Không được đi thi nếu: vắng quá 25% HOẶC điểm < 5
            $khongDuocDiThi = $khongDatChuyenCan || $khongDatDiem;

            $danhSachSinhVienDiThi[] = [
                'lop_hoc_phan_sinh_vien' => $lhpsv,
                'ty_le_co_mat' => $tyLeCoMat,
                'khong_dat_chuyen_can' => $khongDatChuyenCan,
                'diem_trung_binh' => $diemTrungBinh,
                'khong_dat_diem' => $khongDatDiem,
                'khong_duoc_di_thi' => $khongDuocDiThi,
                'ly_do' => $this->taoLyDoKhongDuocDiThi($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh),
            ];
        }

        // Sắp xếp: sinh viên không được đi thi lên đầu
        usort($danhSachSinhVienDiThi, function($a, $b) {
            if ($a['khong_duoc_di_thi'] && !$b['khong_duoc_di_thi']) {
                return -1;
            }
            if (!$a['khong_duoc_di_thi'] && $b['khong_duoc_di_thi']) {
                return 1;
            }
            return strcmp($a['lop_hoc_phan_sinh_vien']->sinhVien->ho_ten, $b['lop_hoc_phan_sinh_vien']->sinhVien->ho_ten);
        });
        
        return view('daotao.lich-thi.show', compact('lichThi', 'danhSachSinhVienDiThi', 'tongBuoiHoc'));
    }

    /**
     * Tạo lý do không được đi thi
     */
    private function taoLyDoKhongDuocDiThi($khongDatChuyenCan, $khongDatDiem, $tyLeCoMat, $diemTrungBinh)
    {
        $lyDo = [];

        if ($khongDatChuyenCan) {
            $lyDo[] = "Vắng quá 25% số buổi học (Tỷ lệ có mặt: {$tyLeCoMat}%)";
        }

        if ($khongDatDiem && $diemTrungBinh !== null) {
            $lyDo[] = "Điểm trung bình các đầu điểm không đạt 5 điểm (Điểm: {$diemTrungBinh})";
        }

        return implode('; ', $lyDo);
    }

    /**
     * Hiển thị form chỉnh sửa lịch thi
     */
    public function edit(LichThi $lichThi)
    {
        $lopHocPhans = LopHocPhan::with('monHoc', 'hocKy')->get();
        $phongHocs = PhongHoc::all();
        $giangViens = GiangVien::with('user')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
        $caHocs = CaHoc::getCaHocHoatDong();

        return view('daotao.lich-thi.edit', compact('lichThi', 'lopHocPhans', 'phongHocs', 'giangViens', 'hocKys', 'caHocs'));
    }

    /**
     * Cập nhật lịch thi
     */
    public function update(UpdateLichThiRequest $request, LichThi $lichThi)
    {
        try {
            DB::beginTransaction();

            // Thu thập tất cả lỗi validation
            $errors = [];
            $errorMessages = []; // Để hiển thị trong alert box

            // 1. Kiểm tra lớp học phần hợp lệ
            $lopHocPhan = LopHocPhan::with(['hocKy', 'lopHocPhanSinhViens'])->findOrFail($request->lop_hoc_phan_id);

            // 2. Kiểm tra ngày thi trong phạm vi học kỳ
            $hocKy = $lopHocPhan->hocKy;
            $ngayThi = \Carbon\Carbon::parse($request->ngay_thi);
            
            if ($ngayThi->lt($hocKy->ngay_bat_dau) || $ngayThi->gt($hocKy->ngay_ket_thuc)) {
                $errorMessages[] = 'Ngày thi phải nằm trong phạm vi học kỳ (' . 
                           $hocKy->ngay_bat_dau->format('d/m/Y') . ' - ' . 
                           $hocKy->ngay_ket_thuc->format('d/m/Y') . ')';
            }

            // 3. Lấy thông tin ca học và tính giờ từ ca học
            if (!$request->ca_hoc_id) {
                $errorMessages[] = 'Vui lòng chọn ca thi';
            } else {
                $caHoc = CaHoc::findOrFail($request->ca_hoc_id);
                $gioBatDau = $caHoc->gio_bat_dau;
                $gioKetThuc = $caHoc->gio_ket_thuc;
            }

            // 4. Kiểm tra trùng lịch thi sinh viên
            $sinhVienIds = $lopHocPhan->lopHocPhanSinhViens->pluck('sinh_vien_id')->toArray();
            
            if (!empty($sinhVienIds) && isset($gioBatDau) && isset($gioKetThuc)) {
                $trungLichSinhVien = LichThi::kiemTraXungDotSinhVien(
                    $sinhVienIds,
                    $request->ngay_thi,
                    $gioBatDau,
                    $gioKetThuc,
                    $lichThi->id
                );

                if ($trungLichSinhVien) {
                    $errorMessages[] = 'Có sinh viên trong lớp đã có lịch thi trùng giờ (Môn: ' . $trungLichSinhVien->lopHocPhan->monHoc->ten_mon . ' vào ' . $trungLichSinhVien->gio_bat_dau . '-' . $trungLichSinhVien->gio_ket_thuc . ')!';
                }
            }

            // 5. Kiểm tra trùng phòng thi (loại trừ bản ghi hiện tại)
            if ($request->phong_thi_id && isset($gioBatDau) && isset($gioKetThuc)) {
                $trungPhong = LichThi::kiemTraXungDotPhong(
                    $request->phong_thi_id,
                    $request->ngay_thi,
                    $gioBatDau,
                    $gioKetThuc,
                    $lichThi->id
                );

                if ($trungPhong) {
                    $errors['phong_thi_id'] = 'Phòng thi đã có lịch thi trùng thời gian!';
                    $errorMessages[] = 'Phòng thi đã có lịch thi trùng thời gian!';
                }

                // 6. Kiểm tra sức chứa phòng
                $phongHoc = PhongHoc::find($request->phong_thi_id);
                
                if ($phongHoc) {
                    $soSinhVienDuThi = $request->so_sinh_vien_du_thi ?? $lopHocPhan->lopHocPhanSinhViens->count();
                    
                    if ($soSinhVienDuThi > $phongHoc->suc_chua) {
                        $msg = 'Phòng thi chỉ chứa được ' . $phongHoc->suc_chua . ' sinh viên, không đủ cho ' . $soSinhVienDuThi . ' sinh viên dự thi!';
                        $errors['phong_thi_id'] = $msg;
                        $errorMessages[] = $msg;
                    }
                } else {
                    $errors['phong_thi_id'] = 'Phòng thi không tồn tại!';
                    $errorMessages[] = 'Phòng thi không tồn tại!';
                }
            }

            // 7. Kiểm tra trùng lịch giám thị
            $giamThiIds = array_filter([$request->giam_thi_1_id, $request->giam_thi_2_id]);
            
            if (!empty($giamThiIds) && isset($gioBatDau) && isset($gioKetThuc)) {
                $trungGiamThi = LichThi::kiemTraXungDotGiamThi(
                    $giamThiIds,
                    $request->ngay_thi,
                    $gioBatDau,
                    $gioKetThuc,
                    $lichThi->id
                );

                if ($trungGiamThi) {
                    $errorMessages[] = 'Giảng viên giám thị đã có lịch coi thi trùng giờ!';
                }
            }

            // 7. Giới hạn số lịch thi theo loại (CHỈ CHECK KHI ĐỔI LOẠI THI HOẶC ĐỔI LỚP)
            $daDoiLoaiThi = $lichThi->loai_thi != $request->loai_thi;
            $daDoiLop = $lichThi->lop_hoc_phan_id != $request->lop_hoc_phan_id;
            
            if ($daDoiLoaiThi || $daDoiLop) {
                $soLichThiCungLoai = LichThi::where('id', '!=', $lichThi->id)
                    ->where('lop_hoc_phan_id', $request->lop_hoc_phan_id)
                    ->where('loai_thi', $request->loai_thi)
                    ->count();

                $gioiHan = [
                    'giua_ky' => 1,
                    'cuoi_ky' => 1,
                    'thi_lai' => 2
                ];

                if ($soLichThiCungLoai >= ($gioiHan[$request->loai_thi] ?? 1)) {
                    $errorMessages[] = 'Lớp học phần đã đạt giới hạn số lần thi ' . 
                               ($request->loai_thi == 'giua_ky' ? 'giữa kỳ' : 
                               ($request->loai_thi == 'cuoi_ky' ? 'cuối kỳ' : 'thi lại')) . '!';
                }
            }

            // Nếu có lỗi thì rollback và trả về
            if (!empty($errorMessages)) {
                DB::rollBack();
                \Log::info('Errors array:', $errors);
                \Log::info('Error messages:', $errorMessages);
                return redirect()->back()
                    ->withInput()
                    ->withErrors($errors)
                    ->with('validation_errors', $errorMessages);
            }

            $data = $request->validated();

            // Lấy giờ từ ca học và thêm vào data
            if (isset($caHoc)) {
                $data['ca_hoc_id'] = $caHoc->id;
                $data['gio_bat_dau'] = $gioBatDau;
                $data['gio_ket_thuc'] = $gioKetThuc;
            }

            // Xử lý upload file đề thi (timestamp + tên gốc)
            if ($request->hasFile('de_thi_file')) {
                // Xóa file cũ
                if ($lichThi->de_thi_file) {
                    Storage::disk('public')->delete($lichThi->de_thi_file);
                }
                $file = $request->file('de_thi_file');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
                $data['de_thi_file'] = $file->storeAs('de-thi', $fileName, 'public');
            }

            // Xử lý upload file đáp án (timestamp + tên gốc)
            if ($request->hasFile('dap_an_file')) {
                // Xóa file cũ
                if ($lichThi->dap_an_file) {
                    Storage::disk('public')->delete($lichThi->dap_an_file);
                }
                $file = $request->file('dap_an_file');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . \Illuminate\Support\Str::slug($originalName) . '.' . $extension;
                $data['dap_an_file'] = $file->storeAs('dap-an', $fileName, 'public');
            }

            // Tự động tính số sinh viên dự thi nếu không nhập
            if (!isset($data['so_sinh_vien_du_thi'])) {
                $data['so_sinh_vien_du_thi'] = $lopHocPhan->lopHocPhanSinhViens->count();
            }

            $lichThi->update($data);

            // 8. Cập nhật phân công sinh viên nếu đổi lớp học phần
            if ($lichThi->wasChanged('lop_hoc_phan_id')) {
                // Xóa phân công cũ
                \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)->delete();
                
                // Tạo phân công mới
                $sinhViens = $lopHocPhan->lopHocPhanSinhViens()->with('sinhVien')->get();
                
                if ($sinhViens->isNotEmpty()) {
                    $lichThiSinhVienData = [];
                    $soBaoDanhCounter = 1;
                    
                    foreach ($sinhViens as $lopHocPhanSinhVien) {
                        $lichThiSinhVienData[] = [
                            'lich_thi_id' => $lichThi->id,
                            'sinh_vien_id' => $lopHocPhanSinhVien->sinh_vien_id,
                            'phong_thi_id' => $request->phong_thi_id,
                            'so_bao_danh' => str_pad($soBaoDanhCounter, 4, '0', STR_PAD_LEFT),
                            'trang_thai' => 'du_thi',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $soBaoDanhCounter++;
                    }
                    
                    \App\Models\LichThiSinhVien::insert($lichThiSinhVienData);
                    Log::info('Đã cập nhật phân công ' . count($lichThiSinhVienData) . ' sinh viên cho lịch thi ID: ' . $lichThi->id);
                }
            }
            // 9. Cập nhật phòng thi mặc định nếu thay đổi phòng
            elseif ($lichThi->wasChanged('phong_thi_id')) {
                \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)
                    ->update(['phong_thi_id' => $request->phong_thi_id]);
                    
                Log::info('Đã cập nhật phòng thi cho tất cả sinh viên của lịch thi ID: ' . $lichThi->id);
            }

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Cập nhật lịch thi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa lịch thi
     */
    public function destroy(LichThi $lichThi)
    {
        try {
            // Xóa files đính kèm
            if ($lichThi->de_thi_file) {
                Storage::disk('public')->delete($lichThi->de_thi_file);
            }
            if ($lichThi->dap_an_file) {
                Storage::disk('public')->delete($lichThi->dap_an_file);
            }

            $lichThi->delete();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Xóa lịch thi thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo lịch thi
     */
    public function guiThongBao(LichThi $lichThi)
    {
        try {
            // TODO: Implement gửi email/thông báo cho sinh viên và giảng viên
            // Có thể sử dụng Queue để gửi hàng loạt

            return redirect()->back()
                ->with('success', 'Đã gửi thông báo lịch thi thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xuất lịch thi Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export Excel/PDF
        // Có thể sử dụng Laravel Excel hoặc DomPDF
        
        return redirect()->back()
            ->with('info', 'Chức năng xuất file đang được phát triển!');
    }

    /**
     * Tải đề thi
     */
    public function downloadDeThi(LichThi $lichThi)
    {
        if (!$lichThi->de_thi_file) {
            return redirect()->back()
                ->with('error', 'Chưa có đề thi!');
        }

        $path = storage_path('app/public/' . $lichThi->de_thi_file);
        
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
        if (!$lichThi->dap_an_file) {
            return redirect()->back()
                ->with('error', 'Chưa có đáp án!');
        }

        $path = storage_path('app/public/' . $lichThi->dap_an_file);
        
        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'File không tồn tại!');
        }

        return response()->download($path);
    }

    /**
     * Trang phân phòng thi cho sinh viên
     */
    public function phanPhong(LichThi $lichThi)
    {
        $lichThi->load([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'lichThiSinhViens.sinhVien',
            'lichThiSinhViens.phongThi'
        ]);

        // Lấy danh sách phòng đã được sử dụng cho lịch thi này
        $phongDangDungIds = $lichThi->lichThiSinhViens
            ->pluck('phong_thi_id')
            ->unique()
            ->filter()
            ->toArray();

        // Thêm phòng mặc định vào danh sách (nếu có)
        if ($lichThi->phong_thi_id && !in_array($lichThi->phong_thi_id, $phongDangDungIds)) {
            $phongDangDungIds[] = $lichThi->phong_thi_id;
        }

        // CHỈ lấy các phòng đang được dùng hoặc phòng mặc định
        $phongHocs = PhongHoc::whereIn('id', $phongDangDungIds)
            ->orderBy('ten_phong')
            ->get();

        // Lấy THÊM các phòng trống (cho option "thêm phòng mới")
        // Lấy các phòng đang bận trong cùng khung giờ
        $phongBanIds = LichThi::where('ngay_thi', $lichThi->ngay_thi)
            ->where(function ($q) use ($lichThi) {
                $q->where('gio_ket_thuc', '>=', $lichThi->gio_bat_dau)
                  ->where('gio_bat_dau', '<=', $lichThi->gio_ket_thuc);
            })
            ->whereNotNull('phong_thi_id')
            ->where('id', '!=', $lichThi->id) // Loại trừ chính lịch thi này
            ->pluck('phong_thi_id')
            ->unique()
            ->toArray();

        $phongTrong = PhongHoc::whereNotIn('id', $phongBanIds)
            ->whereNotIn('id', $phongDangDungIds) // Loại bỏ các phòng đã dùng
            ->orderBy('ten_phong')
            ->get();

        // Group sinh viên theo phòng
        $sinhVienTheoPhong = $lichThi->lichThiSinhViens->groupBy('phong_thi_id');

        return view('daotao.lich-thi.phan-phong', compact('lichThi', 'phongHocs', 'phongTrong', 'sinhVienTheoPhong'));
    }

    /**
     * Cập nhật phòng thi cho sinh viên
     */
    public function capNhatPhong(Request $request, LichThi $lichThi)
    {
        $request->validate([
            'sinh_vien_ids' => 'required|array',
            'sinh_vien_ids.*' => 'exists:sinh_vien,id',
            'phong_thi_id' => 'required|exists:phong_hoc,id',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra sức chứa phòng
            $phongThi = PhongHoc::findOrFail($request->phong_thi_id);
            $soSinhVienHienTai = \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)
                ->where('phong_thi_id', $request->phong_thi_id)
                ->count();
            
            $soSinhVienChuyenDen = count($request->sinh_vien_ids);
            $tongSinhVienSauKhiChuyen = $soSinhVienHienTai + $soSinhVienChuyenDen;

            if ($tongSinhVienSauKhiChuyen > $phongThi->suc_chua) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Phòng ' . $phongThi->ten_phong . ' chỉ chứa được ' . $phongThi->suc_chua . ' sinh viên. ' .
                           'Hiện đang có ' . $soSinhVienHienTai . ' sinh viên, ' .
                           'không thể thêm ' . $soSinhVienChuyenDen . ' sinh viên nữa (tổng: ' . $tongSinhVienSauKhiChuyen . ').');
            }

            // Cập nhật phòng cho các sinh viên được chọn
            \App\Models\LichThiSinhVien::where('lich_thi_id', $lichThi->id)
                ->whereIn('sinh_vien_id', $request->sinh_vien_ids)
                ->update(['phong_thi_id' => $request->phong_thi_id]);

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.phan-phong', $lichThi)
                ->with('success', 'Đã chuyển ' . count($request->sinh_vien_ids) . ' sinh viên sang phòng ' . $phongThi->ten_phong . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xem danh sách sinh viên dự thi
     */
    public function danhSachSinhVien(LichThi $lichThi)
    {
        $lichThi->load([
            'lopHocPhan.monHoc',
            'lopHocPhan.hocKy',
            'phongThi',
            'lichThiSinhViens.sinhVien.lopHanhChinh',
            'lichThiSinhViens.phongThi'
        ]);

        // Lọc theo phòng nếu có
        $phongThiId = request('phong_thi_id');
        $sinhViens = $lichThi->lichThiSinhViens()
            ->with(['sinhVien.lopHanhChinh', 'phongThi'])
            ->when($phongThiId, function($q) use ($phongThiId) {
                $q->where('phong_thi_id', $phongThiId);
            })
            ->orderBy('so_bao_danh')
            ->get();

        $phongHocs = PhongHoc::orderBy('ten_phong')->get();

        return view('daotao.lich-thi.danh-sach-sinh-vien', compact('lichThi', 'sinhViens', 'phongHocs'));
    }
}
