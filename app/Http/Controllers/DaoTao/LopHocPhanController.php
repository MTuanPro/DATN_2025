<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\LopHocPhanSinhVien;
use App\Models\DaoTao\MonHoc;
use App\Models\HocKy;
use App\Models\CauHinhDauDiem;
use App\Models\CauHinhDauDiemMacDinh;
use App\Models\LichHocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ImportHelper;

class LopHocPhanController extends Controller
{
    use ImportHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LopHocPhan::with(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien']);

        // Lọc theo học kỳ
        if ($request->has('hoc_ky_id') && $request->hoc_ky_id != '') {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Lọc theo môn học
        if ($request->has('mon_hoc_id') && $request->mon_hoc_id != '') {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai != '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'like', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'like', "%{$search}%");
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Tính số lượng thực tế từ bảng lop_hoc_phan_sinh_vien cho mỗi lớp
        // Sử dụng cùng logic với getLopHocPhanByMonHoc để đảm bảo đồng bộ
        $lopIds = $lopHocPhans->pluck('id')->toArray();
        $soLuongThucTe = LopHocPhanSinhVien::whereIn('lop_hoc_phan_id', $lopIds)
            ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
            ->selectRaw('lop_hoc_phan_id, COUNT(*) as so_luong')
            ->groupBy('lop_hoc_phan_id')
            ->pluck('so_luong', 'lop_hoc_phan_id')
            ->toArray();
        
        foreach ($lopHocPhans as $lop) {
            $lop->so_luong_thuc_te = $soLuongThucTe[$lop->id] ?? 0;
        }
        
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();
        $monHocs = MonHoc::orderBy('ten_mon')->get();

        return view('daotao.lop-hoc-phan.index', compact('lopHocPhans', 'hocKys', 'monHocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.create', compact('monHocs', 'hocKys'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_lop_hp' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hp',
            'ten_lop_hp' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'nhom_lop' => 'nullable|integer|min:1',
            'suc_chua' => 'required|integer|min:10|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:5|lte:suc_chua',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url|required_if:hinh_thuc,online,hybrid',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after:ngay_bat_dau',
            'trang_thai_lop' => 'required|in:mo_dang_ky,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hp.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hp.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hp.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'mon_hoc_id.exists' => 'Môn học không tồn tại',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'hoc_ky_id.exists' => 'Học kỳ không tồn tại',
            'nhom_lop.min' => 'Nhóm lớp phải lớn hơn 0',
            'suc_chua.required' => 'Sức chứa là bắt buộc',
            'suc_chua.min' => 'Sức chứa phải từ 10 sinh viên trở lên',
            'suc_chua.max' => 'Sức chứa không được vượt quá 200',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.min' => 'Số lượng tối thiểu phải từ 5 sinh viên',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng sức chứa',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'link_online.url' => 'Link online phải là URL hợp lệ',
            'link_online.required_if' => 'Link online là bắt buộc khi chọn hình thức Online hoặc Hybrid',
            'ngay_bat_dau.date' => 'Ngày bắt đầu phải là ngày hợp lệ',
            'ngay_ket_thuc.date' => 'Ngày kết thúc phải là ngày hợp lệ',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'trang_thai_lop.required' => 'Trạng thái là bắt buộc',
        ]);

        // Kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop
        $nhomLop = $validated['nhom_lop'] ?? 1;
        $exists = LopHocPhan::where('mon_hoc_id', $validated['mon_hoc_id'])
            ->where('hoc_ky_id', $validated['hoc_ky_id'])
            ->where('nhom_lop', $nhomLop)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'nhom_lop' => 'Lớp học phần này đã tồn tại (cùng môn học, học kỳ và nhóm lớp). Vui lòng chọn nhóm lớp khác.'
            ]);
        }

        $validated['nhom_lop'] = $nhomLop;
        $validated['so_luong_dang_ky'] = 0; // Đặt giá trị mặc định
        
        // Kiểm tra nếu trạng thái là "Mở đăng ký" thì học kỳ phải đang mở đăng ký
        if ($validated['trang_thai_lop'] === 'mo_dang_ky') {
            $hocKy = HocKy::find($validated['hoc_ky_id']);
            
            if (!$hocKy->dang_mo_dang_ky) {
                return back()->withInput()->withErrors([
                    'trang_thai_lop' => 'Không thể mở đăng ký lớp học phần này vì học kỳ "' . $hocKy->ten_hoc_ky . '" chưa mở đăng ký. Vui lòng mở đăng ký học kỳ trước!'
                ]);
            }
        }
        
        DB::beginTransaction();
        try {
            $lopHocPhan = LopHocPhan::create($validated);

            // Tự động copy cấu hình đầu điểm từ môn học sang lớp học phần
            $this->copyCauHinhDauDiemTuMonHoc($lopHocPhan->id, $validated['mon_hoc_id']);

            DB::commit();

        return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('success', 'Tạo lớp học phần thành công! Đã tự động tạo cấu hình đầu điểm từ môn học.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LopHocPhan $lopHocPhan)
    {
        $lopHocPhan->load(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien', 'cauHinhDauDiem']);

        // Lấy lịch học chi tiết của lớp học phần
        $lichHocChiTiets = \App\Models\LichHocChiTiet::where('lop_hoc_phan_id', $lopHocPhan->id)
            ->with(['phongHoc', 'giangVien', 'caHoc', 'lichHocCoDinh'])
            ->orderBy('ngay_hoc', 'asc')
            ->orderBy('tiet_bat_dau', 'asc')
            ->get();

        // Nhóm lịch theo phòng học
        $lichTheoPhong = $lichHocChiTiets->groupBy(function ($item) {
            return $item->phongHoc ? $item->phongHoc->id : 'no-room';
        })->map(function ($group, $phongId) {
            $phong = $group->first()->phongHoc;
            return [
                'phong' => $phong ? $phong->ten_phong : 'Chưa phân phòng',
                'phong_id' => $phong ? $phong->id : null,
                'lich_hocs' => $group
            ];
        });

        // Nhóm lịch theo giảng viên
        $lichTheoGiangVien = $lichHocChiTiets->groupBy('giang_vien_id')->map(function ($group, $gvId) {
            $giangVien = $group->first()->giangVien;
            return [
                'giang_vien' => $giangVien ? $giangVien->ho_ten : 'Chưa phân công',
                'giang_vien_id' => $gvId,
                'lich_hocs' => $group
            ];
        });

        return view('daotao.lop-hoc-phan.show', compact('lopHocPhan', 'lichTheoPhong', 'lichTheoGiangVien', 'lichHocChiTiets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LopHocPhan $lopHocPhan)
    {
        $monHocs = MonHoc::orderBy('ten_mon')->get();
        $hocKys = HocKy::orderBy('nam_hoc', 'desc')->get();

        return view('daotao.lop-hoc-phan.edit', compact('lopHocPhan', 'monHocs', 'hocKys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LopHocPhan $lopHocPhan)
    {
        $validated = $request->validate([
            'ma_lop_hp' => 'required|string|max:20|unique:lop_hoc_phan,ma_lop_hp,' . $lopHocPhan->id,
            'ten_lop_hp' => 'required|string|max:255',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'hoc_ky_id' => 'required|exists:hoc_ky,id',
            'nhom_lop' => 'nullable|integer|min:1',
            'suc_chua' => 'required|integer|min:10|max:200',
            'so_luong_toi_thieu' => 'required|integer|min:5|lte:suc_chua',
            'hinh_thuc' => 'required|in:offline,online,hybrid',
            'link_online' => 'nullable|url|required_if:hinh_thuc,online,hybrid',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date|after:ngay_bat_dau',
            'trang_thai_lop' => 'required|in:mo_dang_ky,dang_hoc,ket_thuc,huy',
            'ghi_chu' => 'nullable|string',
        ], [
            'ma_lop_hp.required' => 'Mã lớp học phần là bắt buộc',
            'ma_lop_hp.unique' => 'Mã lớp học phần đã tồn tại',
            'ten_lop_hp.required' => 'Tên lớp học phần là bắt buộc',
            'mon_hoc_id.required' => 'Môn học là bắt buộc',
            'hoc_ky_id.required' => 'Học kỳ là bắt buộc',
            'nhom_lop.min' => 'Nhóm lớp phải lớn hơn 0',
            'suc_chua.required' => 'Sức chứa là bắt buộc',
            'so_luong_toi_thieu.required' => 'Số lượng tối thiểu là bắt buộc',
            'so_luong_toi_thieu.lte' => 'Số lượng tối thiểu phải nhỏ hơn hoặc bằng sức chứa',
            'hinh_thuc.required' => 'Hình thức học là bắt buộc',
            'trang_thai_lop.required' => 'Trạng thái là bắt buộc',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
        ]);

        // Kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop (trừ record hiện tại)
        $nhomLop = $validated['nhom_lop'] ?? 1;
        $exists = LopHocPhan::where('mon_hoc_id', $validated['mon_hoc_id'])
            ->where('hoc_ky_id', $validated['hoc_ky_id'])
            ->where('nhom_lop', $nhomLop)
            ->where('id', '!=', $lopHocPhan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'nhom_lop' => 'Lớp học phần này đã tồn tại (cùng môn học, học kỳ và nhóm lớp). Vui lòng chọn nhóm lớp khác.'
            ]);
        }

        $validated['nhom_lop'] = $nhomLop;
        
        // Kiểm tra nếu trạng thái là "Mở đăng ký" thì học kỳ phải đang mở đăng ký
        if ($validated['trang_thai_lop'] === 'mo_dang_ky') {
            $hocKy = HocKy::find($validated['hoc_ky_id']);
            
            if (!$hocKy->dang_mo_dang_ky) {
                return back()->withInput()->withErrors([
                    'trang_thai_lop' => 'Không thể mở đăng ký lớp học phần này vì học kỳ "' . $hocKy->ten_hoc_ky . '" chưa mở đăng ký. Vui lòng mở đăng ký học kỳ trước!'
                ]);
            }
        }
        
        $lopHocPhan->update($validated);

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Cập nhật lớp học phần thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LopHocPhan $lopHocPhan)
    {
        // Kiểm tra xem lớp đã có sinh viên đăng ký chưa
        if ($lopHocPhan->so_luong_dang_ky > 0) {
            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('error', 'Không thể xóa lớp học phần đã có sinh viên đăng ký!');
        }

        $lopHocPhan->delete();

        return redirect()->route('dao-tao.lop-hoc-phan.index')
            ->with('success', 'Xóa lớp học phần thành công!');
    }

    /**
     * Đồng bộ lại số lượng đăng ký từ bảng lop_hoc_phan_sinh_vien
     */
    public function syncSoLuongDangKy()
    {
        try {
            DB::beginTransaction();

            // Lấy tất cả lớp học phần
            $lopHocPhans = LopHocPhan::all();
            $updated = 0;

            foreach ($lopHocPhans as $lop) {
                // Đếm số lượng sinh viên thực tế có trạng thái da_xep_lop hoặc dang_hoc
                $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lop->id)
                    ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                    ->count();

                // Cập nhật số lượng
                if ($lop->so_luong_dang_ky != $soLuongThucTe) {
                    $lop->so_luong_dang_ky = $soLuongThucTe;
                    $lop->save();
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('success', "Đã đồng bộ lại số lượng đăng ký cho {$updated} lớp học phần!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('error', 'Lỗi khi đồng bộ: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form import
     */
    public function showImportForm()
    {
        return view('daotao.lop-hoc-phan.import');
    }

    /**
     * Download template Excel/CSV
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="lop_hoc_phan_template.csv"',
        ];

        $columns = [
            'ma_lop_hp',
            'ten_lop_hp',
            'mon_hoc',
            'hoc_ky',
            'nhom_lop',
            'suc_chua',
            'so_luong_toi_thieu',
            'hinh_thuc',
            'link_online',
            'ngay_bat_dau',
            'ngay_ket_thuc',
            'trang_thai_lop',
            'ghi_chu',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header
            fputcsv($file, $columns);

            // Sample data
            fputcsv($file, [
                'CNTT101.01',
                'Lập trình web - Nhóm 1',
                'Lập trình web',
                'Học kỳ 1 - Năm học 2024-2025',
                '1',
                '50',
                '10',
                'offline',
                '',
                '2024-09-01',
                '2024-12-31',
                'mo_dang_ky',
                'Lớp học phần mẫu',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import lớp học phần từ Excel hoặc CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel hoặc CSV',
            'file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls) hoặc CSV (.csv, .txt)',
            'file.max' => 'File không được vượt quá 5MB',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            $data = [];
            
            // Đọc file Excel
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Kiểm tra extension zip có sẵn không
                if (!extension_loaded('zip')) {
                    DB::rollBack();
                    return back()->with('error', 'PHP extension "zip" chưa được cài đặt. Vui lòng bật extension zip trong php.ini hoặc sử dụng file CSV thay vì Excel.');
                }
                
                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                    $worksheet = $spreadsheet->getActiveSheet();
                    $data = $worksheet->toArray();
                    
                    // Bỏ qua header (dòng đầu tiên)
                    array_shift($data);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Không thể đọc file Excel: ' . $e->getMessage());
                }
            } 
            // Đọc file CSV
            else {
                $handle = fopen($file->getRealPath(), 'r');
                
                if ($handle === false) {
                    throw new \Exception('Không thể đọc file');
                }
                
                // Bỏ qua dòng header
                fgetcsv($handle);
                
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = $row;
                }
                
                fclose($handle);
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $rowNum => $row) {
                $rowNum += 2; // +2 vì bỏ header và index bắt đầu từ 0

                // Kiểm tra dòng trống
                if (empty($row[0])) {
                    continue;
                }

                try {
                    // Parse data
                    $maLopHp = trim($row[0] ?? '');
                    $tenLopHp = trim($row[1] ?? '');
                    $tenMonHoc = trim($row[2] ?? '');
                    $tenHocKy = trim($row[3] ?? '');
                    $nhomLop = !empty($row[4]) ? (int)trim($row[4]) : 1;
                    $sucChua = !empty($row[5]) ? (int)trim($row[5]) : 50;
                    $soLuongToiThieu = !empty($row[6]) ? (int)trim($row[6]) : 10;
                    $hinhThuc = !empty($row[7]) ? trim($row[7]) : 'offline';
                    $linkOnline = !empty($row[8]) ? trim($row[8]) : null;
                    $ngayBatDau = !empty($row[9]) ? trim($row[9]) : null;
                    $ngayKetThuc = !empty($row[10]) ? trim($row[10]) : null;
                    $trangThaiLop = !empty($row[11]) ? trim($row[11]) : 'mo_dang_ky';
                    $ghiChu = !empty($row[12]) ? trim($row[12]) : null;

                    // Validate các trường bắt buộc
                    if (empty($maLopHp) || empty($tenLopHp) || empty($tenMonHoc) || empty($tenHocKy)) {
                        $errors[] = "Dòng {$rowNum}: Thiếu thông tin bắt buộc (Mã lớp, Tên lớp, Môn học, Học kỳ)";
                        continue;
                    }

                    // Không cần kiểm tra trùng vì sẽ update nếu đã tồn tại

                    // Tìm môn học theo tên hoặc mã
                    $monHoc = MonHoc::where('ten_mon', $tenMonHoc)
                        ->orWhere('ma_mon', $tenMonHoc)
                        ->first();
                    
                    if (!$monHoc) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy môn học với tên/mã: {$tenMonHoc}";
                        continue;
                    }

                    // Tìm học kỳ theo tên
                    $hocKy = HocKy::where('ten_hoc_ky', $tenHocKy)->first();
                    
                    if (!$hocKy) {
                        $errors[] = "Dòng {$rowNum}: Không tìm thấy học kỳ với tên: {$tenHocKy}";
                        continue;
                    }

                    // Kiểm tra unique constraint: mon_hoc_id + hoc_ky_id + nhom_lop
                    $exists = LopHocPhan::where('mon_hoc_id', $monHoc->id)
                        ->where('hoc_ky_id', $hocKy->id)
                        ->where('nhom_lop', $nhomLop)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Dòng {$rowNum}: Lớp học phần đã tồn tại (cùng môn học, học kỳ và nhóm lớp)";
                        continue;
                    }

                    // Validate hình thức
                    if (!in_array($hinhThuc, ['offline', 'online', 'hybrid'])) {
                        $errors[] = "Dòng {$rowNum}: Hình thức học không hợp lệ (phải là: offline, online, hybrid)";
                        continue;
                    }

                    // Validate link online nếu hình thức là online hoặc hybrid
                    if (in_array($hinhThuc, ['online', 'hybrid']) && empty($linkOnline)) {
                        $errors[] = "Dòng {$rowNum}: Link online là bắt buộc khi hình thức là online hoặc hybrid";
                        continue;
                    }

                    // Validate trạng thái
                    if (!in_array($trangThaiLop, ['mo_dang_ky', 'dang_hoc', 'ket_thuc', 'huy'])) {
                        $errors[] = "Dòng {$rowNum}: Trạng thái không hợp lệ (phải là: mo_dang_ky, dang_hoc, ket_thuc, huy)";
                        continue;
                    }

                    // Parse ngày với nhiều định dạng hỗ trợ
                    $parseDate = function($dateString) {
                        if (empty($dateString)) {
                            return null;
                        }
                        
                        $dateString = trim($dateString);
                        
                        // Thử các định dạng phổ biến
                        $formats = [
                            'Y-m-d',           // 2025-12-18
                            'd/m/Y',           // 18/12/2025
                            'd-m-Y',           // 18-12-2025
                            'd.m.Y',           // 18.12.2025
                            'Y/m/d',           // 2025/12/18
                            'm/d/Y',           // 12/18/2025 (US format)
                        ];
                        
                        foreach ($formats as $format) {
                            try {
                                $date = \Carbon\Carbon::createFromFormat($format, $dateString);
                                return $date->format('Y-m-d'); // Chuẩn hóa về Y-m-d
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                        
                        // Nếu không parse được với format cụ thể, thử parse tự động
                        try {
                            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
                        } catch (\Exception $e) {
                            throw new \Exception("Không thể parse ngày: {$dateString}. Vui lòng sử dụng định dạng YYYY-MM-DD hoặc DD/MM/YYYY");
                        }
                    };
                    
                    // Parse ngày bắt đầu và kết thúc
                    $ngayBatDauParsed = null;
                    $ngayKetThucParsed = null;
                    
                    if (!empty($ngayBatDau)) {
                        try {
                            $ngayBatDauParsed = $parseDate($ngayBatDau);
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum}: {$e->getMessage()}";
                            continue;
                        }
                    }
                    
                    if (!empty($ngayKetThuc)) {
                        try {
                            $ngayKetThucParsed = $parseDate($ngayKetThuc);
                        } catch (\Exception $e) {
                            $errors[] = "Dòng {$rowNum}: {$e->getMessage()}";
                            continue;
                        }
                    }
                    
                    // Validate ngày
                    if ($ngayBatDauParsed && $ngayKetThucParsed) {
                        $ngayBD = \Carbon\Carbon::parse($ngayBatDauParsed);
                        $ngayKT = \Carbon\Carbon::parse($ngayKetThucParsed);
                        
                        if ($ngayKT->lt($ngayBD)) {
                            $errors[] = "Dòng {$rowNum}: Ngày kết thúc phải sau hoặc bằng ngày bắt đầu";
                            continue;
                        }
                    }

                    // Validate số lượng
                    if ($soLuongToiThieu > $sucChua) {
                        $errors[] = "Dòng {$rowNum}: Số lượng tối thiểu phải nhỏ hơn hoặc bằng sức chứa";
                        continue;
                    }

                    // Kiểm tra nếu trạng thái là "Mở đăng ký" thì học kỳ phải đang mở đăng ký
                    if ($trangThaiLop === 'mo_dang_ky' && !$hocKy->dang_mo_dang_ky) {
                        $errors[] = "Dòng {$rowNum}: Không thể mở đăng ký lớp học phần này vì học kỳ chưa mở đăng ký";
                        continue;
                    }

                    // Update hoặc tạo lớp học phần (dựa vào ma_lop_hp)
                    LopHocPhan::updateOrCreate(
                        ['ma_lop_hp' => $maLopHp],
                        [
                            'ten_lop_hp' => $tenLopHp,
                            'mon_hoc_id' => $monHoc->id,
                            'hoc_ky_id' => $hocKy->id,
                            'nhom_lop' => $nhomLop,
                            'suc_chua' => $sucChua,
                            'so_luong_toi_thieu' => $soLuongToiThieu,
                            'hinh_thuc' => $hinhThuc,
                            'link_online' => $linkOnline,
                            'ngay_bat_dau' => $ngayBatDauParsed,
                            'ngay_ket_thuc' => $ngayKetThucParsed,
                            'trang_thai_lop' => $trangThaiLop,
                            'ghi_chu' => $ghiChu,
                            // Không update so_luong_dang_ky khi import để tránh ghi đè số lượng thực tế
                        ]
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng {$rowNum}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Đã import thành công {$imported} lớp học phần (đã tạo mới hoặc cập nhật).";
            if (!empty($errors)) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " và " . (count($errors) - 5) . " lỗi khác.";
                }
            }

            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with($imported > 0 ? 'success' : 'error', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi import: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple resources from storage.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));

        if (empty($ids)) {
            return back()->with('error', 'Không có lớp học phần nào được chọn!');
        }

        DB::beginTransaction();
        try {
            $deleted = 0;
            $errors = [];
            $skipped = 0;

            foreach ($ids as $id) {
                try {
                    $lopHocPhan = LopHocPhan::find($id);
                    if (!$lopHocPhan) {
                        $errors[] = "Lớp học phần ID {$id} không tồn tại";
                        continue;
                    }

                    // Kiểm tra xem lớp đã có sinh viên đăng ký chưa
                    $soLuongThucTe = LopHocPhanSinhVien::where('lop_hoc_phan_id', $lopHocPhan->id)
                        ->whereIn('trang_thai', ['da_xep_lop', 'dang_hoc'])
                        ->count();

                    if ($soLuongThucTe > 0) {
                        $skipped++;
                        $errors[] = "Lớp học phần {$lopHocPhan->ma_lop_hp} (ID: {$id}) đã có {$soLuongThucTe} sinh viên đăng ký. Không thể xóa!";
                        continue;
                    }

                    // Xóa lớp học phần
                    $lopHocPhan->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi khi xóa lớp học phần ID {$id}: " . $e->getMessage();
                    Log::error("Lỗi xóa lớp học phần ID {$id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Đã xóa thành công {$deleted} lớp học phần.";
            if ($skipped > 0) {
                $message .= " Bỏ qua {$skipped} lớp học phần do có sinh viên đăng ký.";
            }
            if (count($errors) > 0) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 3));
            }

            return redirect()->route('dao-tao.lop-hoc-phan.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa nhiều lớp học phần: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Copy cấu hình đầu điểm từ môn học sang lớp học phần
     */
    private function copyCauHinhDauDiemTuMonHoc($lopHocPhanId, $monHocId)
    {
        // Lấy cấu hình mặc định của môn học
        $cauHinhMacDinhs = CauHinhDauDiemMacDinh::where('mon_hoc_id', $monHocId)->get();

        if ($cauHinhMacDinhs->isEmpty()) {
            // Nếu môn học chưa có cấu hình mặc định, tạo cấu hình mặc định
            $cauHinhMacDinh = [
                ['ten_dau_diem' => 'Chuyên cần', 'ty_le' => 10, 'so_cot' => 1],
                ['ten_dau_diem' => 'Giữa kỳ', 'ty_le' => 30, 'so_cot' => 1],
                ['ten_dau_diem' => 'Cuối kỳ', 'ty_le' => 60, 'so_cot' => 1],
            ];

            foreach ($cauHinhMacDinh as $cauHinh) {
                CauHinhDauDiem::create([
                    'lop_hoc_phan_id' => $lopHocPhanId,
                    'ten_dau_diem' => $cauHinh['ten_dau_diem'],
                    'ty_le' => $cauHinh['ty_le'],
                    'so_cot' => $cauHinh['so_cot'],
                ]);
            }
        } else {
            // Copy từ cấu hình mặc định
            foreach ($cauHinhMacDinhs as $cauHinhMacDinh) {
                CauHinhDauDiem::create([
                    'lop_hoc_phan_id' => $lopHocPhanId,
                    'ten_dau_diem' => $cauHinhMacDinh->ten_dau_diem,
                    'ty_le' => $cauHinhMacDinh->ty_le,
                    'so_cot' => $cauHinhMacDinh->so_cot,
                ]);
            }
        }
    }
}
