<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\CauHinhDauDiem;
use Illuminate\Http\Request;

class CauHinhDauDiemController extends Controller
{
    /**
     * Hiển thị danh sách cấu hình đầu điểm của lớp học phần
     */
    public function index($lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'cauHinhDauDiem'])->findOrFail($lopHocPhanId);
        $tongTyLe = CauHinhDauDiem::getTongTyLe($lopHocPhanId);
        $tyLeConLai = 100 - $tongTyLe;

        return view('daotao.cau-hinh-dau-diem.index', compact('lopHocPhan', 'tongTyLe', 'tyLeConLai'));
    }

    /**
     * Thêm cấu hình đầu điểm
     */
    public function store(Request $request, $lopHocPhanId)
    {
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

        // Kiểm tra mã đầu điểm đã tồn tại chưa
        $exists = CauHinhDauDiem::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('ma_dau_diem', $validated['ma_dau_diem'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Mã đầu điểm đã tồn tại trong lớp học phần này!');
        }

        // Kiểm tra tổng tỷ lệ có vượt quá 100% không
        if (!CauHinhDauDiem::kiemTraTongTyLe($lopHocPhanId, $validated['ty_le'])) {
            $tyLeConLai = CauHinhDauDiem::getTyLeConLai($lopHocPhanId);
            return redirect()->back()
                ->with('error', "Tổng tỷ lệ % vượt quá 100%! Còn lại: {$tyLeConLai}%");
        }

        CauHinhDauDiem::create([
            'lop_hoc_phan_id' => $lopHocPhanId,
            'ten_dau_diem' => $validated['ten_dau_diem'],
            'ma_dau_diem' => $validated['ma_dau_diem'],
            'ty_le' => $validated['ty_le'],
            'so_cot' => $validated['so_cot'],
            'thu_tu_hien_thi' => $validated['thu_tu_hien_thi'] ?? 0,
            'ghi_chu' => $validated['ghi_chu'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Thêm cấu hình đầu điểm thành công!');
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
