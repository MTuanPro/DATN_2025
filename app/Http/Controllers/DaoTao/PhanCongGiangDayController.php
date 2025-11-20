<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LopHocPhan;
use App\Models\PhanCongGiangDay;
use App\Models\GiangVien;
use App\Models\DaoTao\Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanCongGiangDayController extends Controller
{
    /**
     * Hiển thị form phân công giảng viên cho lớp học phần
     */
    public function index(Request $request, $lopHocPhanId)
    {
        $lopHocPhan = LopHocPhan::with(['monHoc', 'hocKy', 'lopHocPhanGiangVien.giangVien'])->findOrFail($lopHocPhanId);
        
        // Lấy danh sách Khoa để filter
        $khoas = Khoa::orderBy('ten_khoa')->get();
        
        // Query giảng viên với filters
        $query = GiangVien::with(['khoa', 'trinhDo']);
        
        // Filter theo Khoa
        if ($request->filled('khoa_id')) {
            $query->where('khoa_id', $request->khoa_id);
        }
        
        // Tìm kiếm theo tên hoặc mã giảng viên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ho_ten', 'LIKE', "%{$search}%")
                  ->orWhere('ma_giang_vien', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter theo chuyên môn
        if ($request->filled('chuyen_mon')) {
            $query->where('chuyen_mon', 'LIKE', "%{$request->chuyen_mon}%");
        }
        
        $giangViens = $query->orderBy('ho_ten')->get();

        return view('daotao.phan-cong-giang-day.index', compact('lopHocPhan', 'giangViens', 'khoas'));
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

        // Kiểm tra trùng lịch giảng viên
        $lopHocPhan = LopHocPhan::with(['lichHocCoDinhs', 'monHoc'])->find($lopHocPhanId);
        $giangVien = GiangVien::find($validated['giang_vien_id']);
        
        if ($lopHocPhan && $lopHocPhan->lichHocCoDinhs->isNotEmpty()) {
            $conflictMessages = [];
            
            foreach ($lopHocPhan->lichHocCoDinhs as $lichLop) {
                // Kiểm tra xung đột với lịch của giảng viên
                $conflict = \App\Models\LichHocCoDinh::where('giang_vien_id', $validated['giang_vien_id'])
                    ->where('id', '!=', $lichLop->id) // Loại trừ chính lịch này
                    ->where('thu_trong_tuan', $lichLop->thu_trong_tuan)
                    ->where(function ($q) use ($lichLop) {
                        $q->where(function ($q2) use ($lichLop) {
                            $q2->where('tiet_ket_thuc', '>=', $lichLop->tiet_bat_dau)
                               ->where('tiet_bat_dau', '<=', $lichLop->tiet_ket_thuc);
                        });
                    })
                    ->with(['lopHocPhan.monHoc'])
                    ->first();
                
                if ($conflict) {
                    $thuText = [
                        2 => 'Thứ 2',
                        3 => 'Thứ 3',
                        4 => 'Thứ 4',
                        5 => 'Thứ 5',
                        6 => 'Thứ 6',
                        7 => 'Thứ 7',
                        8 => 'Chủ nhật'
                    ];
                    
                    $conflictMessages[] = sprintf(
                        '%s, tiết %d-%d: Trùng với lớp "%s" (Môn: %s)',
                        $thuText[$lichLop->thu_trong_tuan] ?? 'Thứ ' . $lichLop->thu_trong_tuan,
                        $lichLop->tiet_bat_dau,
                        $lichLop->tiet_ket_thuc,
                        $conflict->lopHocPhan->ma_lop_hp ?? 'N/A',
                        $conflict->lopHocPhan->monHoc->ten_mon ?? 'N/A'
                    );
                }
            }
            
            if (!empty($conflictMessages) && !$request->has('force_assign')) {
                $errorMessage = sprintf(
                    'Giảng viên "%s" đã có lịch dạy trùng với lớp "%s" (%s):<br><ul><li>%s</li></ul><small class="text-muted">Nếu vẫn muốn phân công, vui lòng xác nhận bên dưới.</small>',
                    $giangVien->ho_ten,
                    $lopHocPhan->ma_lop_hp,
                    $lopHocPhan->monHoc->ten_mon,
                    implode('</li><li>', $conflictMessages)
                );
                
                return redirect()->back()
                    ->withInput()
                    ->with('warning', $errorMessage)
                    ->with('show_force_assign', true)
                    ->with('conflict_data', [
                        'giang_vien_id' => $validated['giang_vien_id'],
                        'vai_tro' => $validated['vai_tro'],
                        'ghi_chu' => $validated['ghi_chu']
                    ]);
            }
        }

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
