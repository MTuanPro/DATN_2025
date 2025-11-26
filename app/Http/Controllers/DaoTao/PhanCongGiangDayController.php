<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;
use App\Models\GiangVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanCongGiangDayController extends Controller
{
    /**
     * Hiển thị form phân công giảng viên cho lớp học phần
     */
    public function index($lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien'])->findOrFail($lopHocPhanId);

        // Chỉ lấy những giảng viên có thể dạy môn học này
        $giangViens = GiangVien::whereHas('monHocs', function ($query) use ($lopHocPhan) {
            $query->where('mon_hoc.id', $lopHocPhan->mon_hoc_id);
        })->orderBy('ho_ten')->get();

        return view('daotao.phan-cong-giang-day.index', compact('lopHocPhan', 'giangViens'));
    }

    /**
     * Phân công giảng viên
     */
    public function store(Request $request, $lopHocPhanId)
    {
        $validated = $request->validate([
            'giang_vien_id' => 'required|exists:giang_vien,id',
            'vai_tro' => 'required|in:giang_vien_chinh,giang_vien_phu,tro_giang',
            'ghi_chu' => 'nullable|string',
        ], [
            'giang_vien_id.required' => 'Giảng viên là bắt buộc',
            'giang_vien_id.exists' => 'Giảng viên không tồn tại',
            'vai_tro.required' => 'Vai trò là bắt buộc',
            'vai_tro.in' => 'Vai trò không hợp lệ',
        ]);

        // Kiểm tra đã phân công giảng viên này với vai trò này chưa
        $existingPhanCong = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
            ->where('giang_vien_id', $validated['giang_vien_id'])
            ->where('vai_tro', $validated['vai_tro'])
            ->first();

        if ($existingPhanCong) {
            return redirect()->back()
                ->with('error', 'Giảng viên này đã được phân công với vai trò này rồi!');
        }

        // Kiểm tra nếu phân công giảng viên chính
        if ($validated['vai_tro'] === 'giang_vien_chinh') {
            $hasGiangVienChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $lopHocPhanId)
                ->where('vai_tro', 'giang_vien_chinh')
                ->exists();

            if ($hasGiangVienChinh) {
                return redirect()->back()
                    ->with('error', 'Lớp học phần đã có giảng viên chính rồi!');
            }
        }

        // TODO: Kiểm tra trùng lịch giảng viên (cần implement sau khi có bảng lich_hoc_co_dinh)

        // Lấy ID của nhân viên đào tạo từ user đang đăng nhập
        $daoTaoId = Auth::user()->daoTao ? Auth::user()->daoTao->id : null;

        PhanCongGiangDay::create([
            'lop_hoc_phan_id' => $lopHocPhanId,
            'giang_vien_id' => $validated['giang_vien_id'],
            'vai_tro' => $validated['vai_tro'],
            'nguoi_phan_cong_id' => $daoTaoId,
            'ngay_phan_cong' => now(),
            'ghi_chu' => $validated['ghi_chu'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Phân công giảng viên thành công!');
    }

    /**
     * Cập nhật phân công
     */
    public function update(Request $request, $id)
    {
        $phanCong = PhanCongGiangDay::findOrFail($id);

        $validated = $request->validate([
            'vai_tro' => 'required|in:giang_vien_chinh,giang_vien_phu,tro_giang',
            'ghi_chu' => 'nullable|string',
        ], [
            'vai_tro.required' => 'Vai trò là bắt buộc',
            'vai_tro.in' => 'Vai trò không hợp lệ',
        ]);

        // Kiểm tra nếu thay đổi thành giảng viên chính
        if ($validated['vai_tro'] === 'giang_vien_chinh' && $phanCong->vai_tro !== 'giang_vien_chinh') {
            $hasGiangVienChinh = PhanCongGiangDay::where('lop_hoc_phan_id', $phanCong->lop_hoc_phan_id)
                ->where('vai_tro', 'giang_vien_chinh')
                ->where('id', '!=', $id)
                ->exists();

            if ($hasGiangVienChinh) {
                return redirect()->back()
                    ->with('error', 'Lớp học phần đã có giảng viên chính rồi!');
            }
        }

        $phanCong->update($validated);

        return redirect()->back()
            ->with('success', 'Cập nhật phân công thành công!');
    }

    /**
     * Xóa phân công
     */
    public function destroy($id)
    {
        $phanCong = PhanCongGiangDay::findOrFail($id);
        $phanCong->delete();

        return redirect()->back()
            ->with('success', 'Xóa phân công thành công!');
    }
}
