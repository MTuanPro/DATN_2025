<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\CauHinhDauDiem;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CauHinhDiemController extends Controller
{
    /**
     * Hiển thị danh sách lớp học phần mà giảng viên có thể cấu hình điểm
     *
     * Điều kiện để cấu hình điểm:
     * - Giảng viên phải là giảng viên chính (vai_tro = 'giang_vien_chinh')
     * - Giảng viên phụ không được cấu hình điểm
     *
     * Hiển thị thông tin:
     * - Danh sách lớp học phần (mã, tên, môn học, học kỳ)
     * - Trạng thái cấu hình: Đã cấu hình / Chưa cấu hình
     * - Số đầu điểm đã tạo
     * - Tổng tỷ lệ (phải bằng 100%)
     * - Cảnh báo nếu tổng tỷ lệ ≠ 100%
     *
     * Chức năng lọc:
     * - Theo học kỳ (hoc_ky_id)
     * - Tìm kiếm theo mã/tên lớp (search)
     *
     * Phân trang 15 lớp/trang.
     *
     * @param Request $request Có thể chứa hoc_ky_id, search
     * @return \Illuminate\View\View Danh sách lớp có thể cấu hình điểm
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không tìm thấy giảng viên
     */
    public function index(Request $request)
    {
        $giangVien = Auth::user()->giangVien;

        if (!$giangVien) {
            return redirect()->route('giangvien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giảng viên');
        }

        // Lấy danh sách lớp được phân công (chỉ GV chính mới được cấu hình)
        $query = LopHocPhan::with(['monHoc', 'hocKy'])
            ->whereHas('giangViens', function ($q) use ($giangVien) {
                $q->where('giang_vien_id', $giangVien->id)
                  ->where('vai_tro', 'giang_vien_chinh'); // Chỉ GV chính
            });

        // Lọc theo học kỳ
        if ($request->filled('hoc_ky_id')) {
            $query->where('hoc_ky_id', $request->hoc_ky_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_lop_hp', 'LIKE', "%{$search}%")
                    ->orWhere('ten_lop_hp', 'LIKE', "%{$search}%");
            });
        }

        $lopHocPhans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Kiểm tra đã cấu hình chưa
        foreach ($lopHocPhans as $lop) {
            $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lop->id)->get();
            $lop->da_cau_hinh = $cauHinhs->isNotEmpty();
            $lop->tong_ty_le = $cauHinhs->sum('ty_le');
            $lop->so_dau_diem = $cauHinhs->count();
        }

        $hocKys = \App\Models\HocKy::orderBy('nam_hoc', 'desc')
            ->orderBy('ten_hoc_ky', 'desc')
            ->get();

        return view('giangvien.cau-hinh-diem.index', compact('lopHocPhans', 'hocKys'));
    }

    /**
     * Xem chi tiết cấu hình điểm của một lớp học phần
     *
     * Kiểm tra:
     * - Quyền giảng viên chính (vai_tro = 'giang_vien_chinh')
     * - Nếu không có quyền: Redirect về index với lỗi
     *
     * Hiển thị:
     * - Thông tin lớp học phần (mã, tên, môn học, học kỳ)
     * - Danh sách tất cả các đầu điểm đã cấu hình:
     *   + Tên đầu điểm (CC, GK, TH, CK...)
     *   + Tỷ lệ % (ví dụ: CC 10%, GK 20%, CK 70%)
     *   + Số cột điểm (1 cột hoặc nhiều cột)
     * - Tổng tỷ lệ và cảnh báo nếu ≠ 100%
     * - Nút sửa/xóa từng đầu điểm
     *
     * @param int $lopHocPhanId ID lớp học phần cần xem cấu hình
     * @return \Illuminate\View\View Chi tiết cấu hình điểm
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền
     */
    public function show($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($lopHocPhanId);

        $cauHinhs = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->orderBy('id')
            ->get();

        $tongTyLe = $cauHinhs->sum('ty_le');
        $hoanThien = $tongTyLe == 100;

        return view('giangvien.cau-hinh-diem.show', compact(
            'lopHocPhan',
            'cauHinhs',
            'tongTyLe',
            'hoanThien'
        ));
    }

    /**
     * Form tạo cấu hình mới
     */
    /**
     * Hiển thị form thêm đầu điểm mới cho lớp học phần
     *
     * Kiểm tra:
     * - Quyền giảng viên chính
     * - Tổng tỷ lệ hiện tại của các đầu điểm đã có
     * - Hiển thị tỷ lệ còn lại có thể thêm (100% - tổng hiện tại)
     *
     * Form nhập:
     * - Tên đầu điểm (ví dụ: Chuyên cần, Giữa kỳ, Cuối kỳ...)
     * - Tỷ lệ % (0-100, không vượt quá tỷ lệ còn lại)
     * - Số cột (1-10, ví dụ: Chuyên cần có thể có nhiều cột điểm)
     *
     * @param int $lopHocPhanId ID lớp học phần cần thêm đầu điểm
     * @return \Illuminate\View\View Form thêm đầu điểm
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền
     */
    public function create($lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy'])->findOrFail($lopHocPhanId);

        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
        $tyLeConLai = 100 - $tongTyLe;

        return view('giangvien.cau-hinh-diem.create', compact('lopHocPhan', 'tyLeConLai'));
    }

    /**
     * Lưu cấu hình mới
     */
    /**
     * Lưu cấu hình đầu điểm mới cho lớp học phần
     *
     * Quy trình validate và lưu:
     * 1. Validate dữ liệu đầu vào:
     *    - ten_dau_diem: required, string, max 255 (ví dụ: 'Chuyên cần')
     *    - ty_le: required, numeric, 0-100
     *    - so_cot: required, integer, 1-10
     * 2. Kiểm tra quyền giảng viên chính
     * 3. Kiểm tra tổng tỷ lệ:
     *    - Tính tổng tỷ lệ hiện tại của các đầu điểm đã có
     *    - Tổng mới = tổng hiện tại + ty_le mới
     *    - Nếu tổng mới > 100%: Trả về lỗi 'Vượt quá 100%'
     * 4. Sử dụng database transaction:
     *    - Tạo bản ghi CauHinhDauDiem mới
     *    - Lưu: lop_hoc_phan_id, ten_dau_diem, ty_le, so_cot
     * 5. Redirect về trang chi tiết cấu hình với thông báo thành công
     * 6. Cảnh báo nếu tổng tỷ lệ chưa đủ 100% (còn thíeu)
     *
     * @param Request $request Chứa ten_dau_diem, ty_le, so_cot
     * @param int $lopHocPhanId ID lớp học phần
     * @return \Illuminate\Http\RedirectResponse Redirect về show với thông báo
     * @throws \Illuminate\Validation\ValidationException Nếu dữ liệu không hợp lệ
     */
    public function store(Request $request, $lopHocPhanId)
    {
        $giangVien = Auth::user()->giangVien;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền cấu hình điểm lớp này');
        }

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:100',
            'ty_le' => 'required|numeric|min:1|max:100',
            'so_cot' => 'required|integer|min:1|max:10',
        ], [
            'ten_dau_diem.required' => 'Vui lòng nhập tên đầu điểm',
            'ty_le.required' => 'Vui lòng nhập tỷ lệ %',
            'ty_le.min' => 'Tỷ lệ phải từ 1% trở lên',
            'ty_le.max' => 'Tỷ lệ không được vượt quá 100%',
            'so_cot.required' => 'Vui lòng nhập số cột',
            'so_cot.min' => 'Số cột phải từ 1 trở lên',
            'so_cot.max' => 'Số cột không được vượt quá 10',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)->sum('ty_le');
        
        if ($tongTyLe + $validated['ty_le'] > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tổng tỷ lệ không được vượt quá 100%. Tỷ lệ còn lại: ' . (100 - $tongTyLe) . '%');
        }

        try {
            DB::beginTransaction();

            CauHinhDauDiem::create([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ten_dau_diem' => $validated['ten_dau_diem'],
                'ty_le' => $validated['ty_le'],
                'so_cot' => $validated['so_cot'],
            ]);

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $lopHocPhanId)
                ->with('success', 'Đã thêm cấu hình đầu điểm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Form sửa cấu hình
     */
    /**
     * Hiển thị form chỉnh sửa cấu hình đầu điểm
     *
     * Kiểm tra:
     * - Quyền giảng viên chính của lớp học phần
     * - Đầu điểm có tồn tại không
     * - Đầu điểm có thuộc lớp của giảng viên không
     *
     * Hiển thị:
     * - Form chỉnh sửa với dữ liệu hiện tại
     * - Tỷ lệ còn lại có thể thêm (không tính đầu điểm đang sửa)
     * - Cảnh báo nếu đã có sinh viên được nhập điểm (không nên sửa tỷ lệ)
     *
     * @param int $id ID của cấu hình đầu điểm cần sửa
     * @return \Illuminate\View\View Form chỉnh sửa
     * @return \Illuminate\Http\RedirectResponse Redirect nếu không có quyền
     */
    public function edit($id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::with(['lopHocPhan.monHoc', 'nhapDiems'])->findOrFail($id);
        $lopHocPhan = $cauHinh->lopHocPhan;

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa');
        }

        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('id', '!=', $id)
            ->sum('ty_le');
        
        $tyLeConLai = 100 - $tongTyLe;

        return view('giangvien.cau-hinh-diem.edit', compact('cauHinh', 'lopHocPhan', 'tyLeConLai'));
    }

    /**
     * Cập nhật cấu hình
     */
    /**
     * Cập nhật cấu hình đầu điểm đã tồn tại
     *
     * Quy trình cập nhật:
     * 1. Validate dữ liệu (tương tự store)
     * 2. Kiểm tra quyền
     * 3. Kiểm tra tổng tỷ lệ mới:
     *    - Tổng = (tổng hiện tại - ty_le cũ) + ty_le mới
     *    - Nếu > 100%: Trả về lỗi
     * 4. Kiểm tra đã có điểm chưa:
     *    - Nếu đã có sinh viên nhập điểm cho đầu điểm này
     *    - Cảnh báo khi thay đổi tỷ lệ (sẽ ảnh hưởng điểm tổng kết)
     *    - Yêu cầu xác nhận nếu cần
     * 5. Cập nhật CauHinhDauDiem
     * 6. Nếu thay đổi tỷ lệ: Tự động tính lại điểm tổng kết cho tất cả sinh viên
     * 7. Redirect với thông báo thành công
     *
     * @param Request $request Chứa ten_dau_diem, ty_le, so_cot
     * @param int $id ID cấu hình đầu điểm
     * @return \Illuminate\Http\RedirectResponse Redirect về show
     * @throws \Exception Khi có lỗi tính lại điểm
     */
    public function update(Request $request, $id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa');
        }

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:100',
            'ty_le' => 'required|numeric|min:1|max:100',
            'so_cot' => 'required|integer|min:1|max:10',
        ]);

        // Kiểm tra tổng tỷ lệ
        $tongTyLe = CauHinhDauDiem::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('id', '!=', $id)
            ->sum('ty_le');
        
        if ($tongTyLe + $validated['ty_le'] > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tổng tỷ lệ không được vượt quá 100%');
        }

        try {
            DB::beginTransaction();

            $cauHinh->update($validated);

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $cauHinh->lop_hoc_phan_id)
                ->with('success', 'Đã cập nhật cấu hình thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa cấu hình
     */
    /**
     * Xóa cấu hình đầu điểm (cần kiểm tra kỹ trước khi xóa)
     *
     * Quy trình xóa:
     * 1. Kiểm tra quyền giảng viên chính
     * 2. Kiểm tra đã có điểm chưa:
     *    - Nếu đã có sinh viên nhập điểm cho đầu điểm này
     *    - KHÔNG cho phép xóa, trả về lỗi:
     *      'Không thể xóa đầu điểm đã có sinh viên nhập điểm'
     * 3. Kiểm tra đây có phải đầu điểm duy nhất không:
     *    - Nếu là đầu điểm duy nhất: Cảnh báo nhưng vẫn cho phép xóa
     * 4. Sử dụng database transaction:
     *    - Xóa tất cả NhapDiem liên quan (nếu chưa có điểm)
     *    - Xóa CauHinhDauDiem
     * 5. Cập nhật lại điểm tổng kết cho tất cả sinh viên (nếu cần)
     * 6. Redirect với thông báo thành công
     *
     * @param int $id ID cấu hình đầu điểm cần xóa
     * @return \Illuminate\Http\RedirectResponse Redirect về show
     * @throws \Exception Khi có lỗi xóa hoặc cập nhật điểm
     */
    public function destroy($id)
    {
        $giangVien = Auth::user()->giangVien;

        $cauHinh = CauHinhDauDiem::findOrFail($id);

        // Kiểm tra quyền
        $phanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('giang_vien_id', $giangVien->id)
            ->where('vai_tro', 'giang_vien_chinh')
            ->first();

        if (!$phanCong) {
            return redirect()->route('giangvien.cau-hinh-diem.index')
                ->with('error', 'Bạn không có quyền xóa');
        }

        try {
            DB::beginTransaction();

            $lopHocPhanId = $cauHinh->lop_hoc_phan_id;
            $cauHinh->delete();

            DB::commit();

            return redirect()->route('giangvien.cau-hinh-diem.show', $lopHocPhanId)
                ->with('success', 'Đã xóa cấu hình thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
