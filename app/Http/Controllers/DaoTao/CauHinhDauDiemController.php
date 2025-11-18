<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\CauHinhDauDiem;
use App\Services\CauHinhDauDiemService;
use Illuminate\Http\Request;

class CauHinhDauDiemController extends Controller
{
    protected $cauHinhDauDiemService;

    public function __construct(CauHinhDauDiemService $cauHinhDauDiemService)
    {
        $this->cauHinhDauDiemService = $cauHinhDauDiemService;
    }

    /**
     * Hiển thị danh sách lớp học phần để cấu hình đầu điểm
     */
    public function listLopHocPhan(Request $request)
    {
        $query = LopHocPhan::with(['monHoc', 'hocKy']);

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

        return view('daotao.cau-hinh-dau-diem.list-lop-hoc-phan', compact('lopHocPhans', 'hocKys'));
    }

    /**
     * Hiển thị danh sách cấu hình đầu điểm của lớp học phần
     */
    public function index($lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'cauHinhDauDiem'])->findOrFail($lopHocPhanId);
        $summary = $this->cauHinhDauDiemService->getSummary($lopHocPhanId);
        $tyLeConLai = $summary['remaining_percentage'];

        return view('daotao.cau-hinh-dau-diem.index', compact('lopHocPhan', 'summary', 'tyLeConLai'));
    }

    /**
     * Thêm cấu hình đầu điểm
     * Nếu số cột > 1, tự động tạo nhiều đầu điểm với số đếm (VD: quiz 1, quiz 2, ...)
     */
    public function store(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:50',
            'ty_le' => 'required|numeric|min:0.01|max:100',
            'so_cot' => 'required|integer|min:1|max:20',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_dau_diem.required' => 'Tên đầu điểm là bắt buộc',
            'ty_le.required' => 'Tỷ lệ % là bắt buộc',
            'ty_le.min' => 'Tỷ lệ % phải lớn hơn 0',
            'ty_le.max' => 'Tỷ lệ % không được vượt quá 100',
            'so_cot.required' => 'Số cột điểm là bắt buộc',
            'so_cot.min' => 'Số cột điểm phải lớn hơn 0',
            'so_cot.max' => 'Số cột điểm không được vượt quá 20',
        ]);

        // Sử dụng Service để validate
        $validation = $this->cauHinhDauDiemService->validateCauHinh(
            $lopHocPhanId,
            $validated['ten_dau_diem'],
            $validated['ty_le'],
            $validated['so_cot']
        );

        if (!$validation['passed']) {
            return redirect()->back()
                ->withErrors($validation['errors'])
                ->withInput();
        }

        // Nếu số cột = 1, tạo 1 đầu điểm bình thường
        if ($validated['so_cot'] == 1) {
            CauHinhDauDiem::create([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ten_dau_diem' => $validated['ten_dau_diem'],
                'ty_le' => $validated['ty_le'],
                'so_cot' => 1,
                'ghi_chu' => $validated['ghi_chu'] ?? null,
            ]);

            return redirect()->back()
                ->with('success', 'Thêm cấu hình đầu điểm thành công!');
        }

        // Nếu số cột > 1, tạo nhiều đầu điểm với số đếm
        for ($i = 1; $i <= $validated['so_cot']; $i++) {
            CauHinhDauDiem::create([
                'lop_hoc_phan_id' => $lopHocPhanId,
                'ten_dau_diem' => $validated['ten_dau_diem'] . ' ' . $i,
                'ty_le' => $validated['ty_le'],
                'so_cot' => 1, // Mỗi đầu điểm chỉ có 1 cột
                'ghi_chu' => $validated['ghi_chu'] ?? null,
            ]);
        }

        return redirect()->back()
            ->with('success', "Thêm thành công {$validated['so_cot']} đầu điểm!");
    }

    /**
     * Cập nhật cấu hình đầu điểm
     */
    public function update(Request $request, $id)
    {
        $cauHinh = CauHinhDauDiem::findOrFail($id);

        $validated = $request->validate([
            'ten_dau_diem' => 'required|string|max:50',
            'ma_dau_diem' => 'required|string|max:20',
            'ty_le' => 'required|numeric|min:0.01|max:100',
            'so_cot' => 'required|integer|min:1|max:10',
            'thu_tu_hien_thi' => 'nullable|integer|min:0',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_dau_diem.required' => 'Tên đầu điểm là bắt buộc',
            'ma_dau_diem.required' => 'Mã đầu điểm là bắt buộc',
            'ty_le.required' => 'Tỷ lệ % là bắt buộc',
            'ty_le.min' => 'Tỷ lệ % phải lớn hơn 0',
            'ty_le.max' => 'Tỷ lệ % không được vượt quá 100',
            'so_cot.required' => 'Số cột điểm là bắt buộc',
            'so_cot.min' => 'Số cột điểm phải lớn hơn 0',
            'so_cot.max' => 'Số cột điểm không được vượt quá 10',
        ]);

        // Kiểm tra mã đầu điểm đã tồn tại chưa (trừ bản ghi hiện tại)
        $exists = CauHinhDauDiem::where('lop_hoc_phan_id', $cauHinh->lop_hoc_phan_id)
            ->where('ma_dau_diem', $validated['ma_dau_diem'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Mã đầu điểm đã tồn tại trong lớp học phần này!');
        }

        // Kiểm tra tổng tỷ lệ có vượt quá 100% không
        if (!CauHinhDauDiem::kiemTraTongTyLe($cauHinh->lop_hoc_phan_id, $validated['ty_le'], $id)) {
            $tyLeConLai = CauHinhDauDiem::getTyLeConLai($cauHinh->lop_hoc_phan_id, $id);
            return redirect()->back()
                ->with('error', "Tổng tỷ lệ % vượt quá 100%! Còn lại: {$tyLeConLai}%");
        }

        $cauHinh->update($validated);

        return redirect()->back()
            ->with('success', 'Cập nhật cấu hình đầu điểm thành công!');
    }

    /**
     * Xóa cấu hình đầu điểm
     */
    public function destroy($id)
    {
        $cauHinh = CauHinhDauDiem::findOrFail($id);

        // TODO: Kiểm tra xem đã có điểm nhập cho đầu điểm này chưa
        // Nếu có thì không cho xóa

        $cauHinh->delete();

        return redirect()->back()
            ->with('success', 'Xóa cấu hình đầu điểm thành công!');
    }

    /**
     * API: Lấy tỷ lệ % còn lại
     */
    public function getTyLeConLai($lopHocPhanId)
    {
        $tyLeConLai = CauHinhDauDiem::getTyLeConLai($lopHocPhanId);
        return response()->json(['ty_le_con_lai' => $tyLeConLai]);
    }
}
