<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\LichThi;
use App\Models\LopHocPhan;
use App\Models\DanhMuc\PhongHoc;
use App\Models\GiangVien;
use App\Models\HocKy;
use App\Http\Requests\StoreLichThiRequest;
use App\Http\Requests\UpdateLichThiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('daotao.lich-thi.create', compact('lopHocPhans', 'phongHocs', 'giangViens', 'hocKys'));
    }

    /**
     * Lưu lịch thi mới
     */
    public function store(StoreLichThiRequest $request)
    {
        try {
            DB::beginTransaction();

            // 1. Kiểm tra lớp học phần hợp lệ
            $lopHocPhan = LopHocPhan::with(['hocKy', 'lopHocPhanSinhViens'])->findOrFail($request->lop_hoc_phan_id);

            // 2. Kiểm tra ngày thi trong phạm vi học kỳ
            $hocKy = $lopHocPhan->hocKy;
            $ngayThi = \Carbon\Carbon::parse($request->ngay_thi);
            
            if ($ngayThi->lt($hocKy->ngay_bat_dau) || $ngayThi->gt($hocKy->ngay_ket_thuc)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ngày thi phải nằm trong phạm vi học kỳ (' . 
                           $hocKy->ngay_bat_dau->format('d/m/Y') . ' - ' . 
                           $hocKy->ngay_ket_thuc->format('d/m/Y') . ')');
            }

            // 3. Kiểm tra trùng lịch thi sinh viên (sinh viên không được thi 2 môn cùng lúc)
            $sinhVienIds = $lopHocPhan->lopHocPhanSinhViens->pluck('sinh_vien_id');
            
            $trungLichSinhVien = LichThi::whereHas('lopHocPhan.lopHocPhanSinhViens', function($q) use ($sinhVienIds) {
                    $q->whereIn('sinh_vien_id', $sinhVienIds);
                })
                ->where('ngay_thi', $request->ngay_thi)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                          });
                })
                ->exists();

            if ($trungLichSinhVien) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Có sinh viên trong lớp đã có lịch thi trùng giờ!');
            }

            // 4. Kiểm tra trùng phòng thi
            if ($request->phong_thi_id) {
                $trungPhong = LichThi::where('phong_thi_id', $request->phong_thi_id)
                    ->where('ngay_thi', $request->ngay_thi)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhere(function ($q) use ($request) {
                                  $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                    ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                              });
                    })
                    ->exists();

                if ($trungPhong) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Phòng thi đã có lịch thi trùng thời gian!');
                }

                // 5. Kiểm tra sức chứa phòng
                $phongHoc = PhongHoc::find($request->phong_thi_id);
                $soSinhVienDuThi = $request->so_sinh_vien_du_thi ?? $lopHocPhan->lopHocPhanSinhViens->count();
                
                if ($soSinhVienDuThi > $phongHoc->suc_chua) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Phòng thi chỉ chứa được ' . $phongHoc->suc_chua . ' sinh viên, không đủ cho ' . $soSinhVienDuThi . ' sinh viên dự thi!');
                }
            }

            // 6. Kiểm tra trùng lịch giám thị
            $giamThiIds = array_filter([$request->giam_thi_1_id, $request->giam_thi_2_id]);
            
            if (!empty($giamThiIds)) {
                $trungGiamThi = LichThi::where('ngay_thi', $request->ngay_thi)
                    ->where(function($q) use ($giamThiIds) {
                        $q->whereIn('giam_thi_1_id', $giamThiIds)
                          ->orWhereIn('giam_thi_2_id', $giamThiIds);
                    })
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhere(function ($q) use ($request) {
                                  $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                    ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                              });
                    })
                    ->exists();

                if ($trungGiamThi) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Giảng viên giám thị đã có lịch coi thi trùng giờ!');
                }
            }

            // 7. Giới hạn số lịch thi theo loại (ví dụ: 1 giữa kỳ, 1 cuối kỳ, tối đa 2 thi lại)
            $soLichThiCungLoai = LichThi::where('lop_hoc_phan_id', $request->lop_hoc_phan_id)
                ->where('loai_thi', $request->loai_thi)
                ->count();

            $gioiHan = [
                'giua_ky' => 1,
                'cuoi_ky' => 1,
                'thi_lai' => 2
            ];

            if ($soLichThiCungLoai >= ($gioiHan[$request->loai_thi] ?? 1)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Lớp học phần đã đạt giới hạn số lần thi ' . 
                           ($request->loai_thi == 'giua_ky' ? 'giữa kỳ' : 
                           ($request->loai_thi == 'cuoi_ky' ? 'cuối kỳ' : 'thi lại')) . '!');
            }

            $data = $request->validated();

            // Xử lý upload file đề thi
            if ($request->hasFile('de_thi_file')) {
                $data['de_thi_file'] = $request->file('de_thi_file')->store('de-thi', 'public');
            }

            // Xử lý upload file đáp án
            if ($request->hasFile('dap_an_file')) {
                $data['dap_an_file'] = $request->file('dap_an_file')->store('dap-an', 'public');
            }

            // Tự động tính số sinh viên dự thi nếu không nhập
            if (!isset($data['so_sinh_vien_du_thi'])) {
                $data['so_sinh_vien_du_thi'] = $lopHocPhan->lopHocPhanSinhViens->count();
            }

            $lichThi = LichThi::create($data);

            DB::commit();

            return redirect()->route('dao-tao.lich-thi.index')
                ->with('success', 'Thêm lịch thi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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
            'lopHocPhan.lopHocPhanSinhViens.sinhVien', 
            'hocKy',
            'phongThi', 
            'giamThi1', 
            'giamThi2'
        ]);
        
        return view('daotao.lich-thi.show', compact('lichThi'));
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

        return view('daotao.lich-thi.edit', compact('lichThi', 'lopHocPhans', 'phongHocs', 'giangViens', 'hocKys'));
    }

    /**
     * Cập nhật lịch thi
     */
    public function update(UpdateLichThiRequest $request, LichThi $lichThi)
    {
        try {
            DB::beginTransaction();

            // 1. Kiểm tra lớp học phần hợp lệ
            $lopHocPhan = LopHocPhan::with(['hocKy', 'lopHocPhanSinhViens'])->findOrFail($request->lop_hoc_phan_id);

            // 2. Kiểm tra ngày thi trong phạm vi học kỳ
            $hocKy = $lopHocPhan->hocKy;
            $ngayThi = \Carbon\Carbon::parse($request->ngay_thi);
            
            if ($ngayThi->lt($hocKy->ngay_bat_dau) || $ngayThi->gt($hocKy->ngay_ket_thuc)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ngày thi phải nằm trong phạm vi học kỳ (' . 
                           $hocKy->ngay_bat_dau->format('d/m/Y') . ' - ' . 
                           $hocKy->ngay_ket_thuc->format('d/m/Y') . ')');
            }

            // 3. Kiểm tra trùng lịch thi sinh viên
            $sinhVienIds = $lopHocPhan->lopHocPhanSinhViens->pluck('sinh_vien_id');
            
            $trungLichSinhVien = LichThi::where('id', '!=', $lichThi->id)
                ->whereHas('lopHocPhan.lopHocPhanSinhViens', function($q) use ($sinhVienIds) {
                    $q->whereIn('sinh_vien_id', $sinhVienIds);
                })
                ->where('ngay_thi', $request->ngay_thi)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                          });
                })
                ->exists();

            if ($trungLichSinhVien) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Có sinh viên trong lớp đã có lịch thi trùng giờ!');
            }

            // 4. Kiểm tra trùng phòng thi (loại trừ bản ghi hiện tại)
            if ($request->phong_thi_id) {
                $trungPhong = LichThi::where('id', '!=', $lichThi->id)
                    ->where('phong_thi_id', $request->phong_thi_id)
                    ->where('ngay_thi', $request->ngay_thi)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhere(function ($q) use ($request) {
                                  $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                    ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                              });
                    })
                    ->exists();

                if ($trungPhong) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Phòng thi đã có lịch thi trùng thời gian!');
                }

                // 5. Kiểm tra sức chứa phòng
                $phongHoc = PhongHoc::find($request->phong_thi_id);
                $soSinhVienDuThi = $request->so_sinh_vien_du_thi ?? $lopHocPhan->lopHocPhanSinhViens->count();
                
                if ($soSinhVienDuThi > $phongHoc->suc_chua) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Phòng thi chỉ chứa được ' . $phongHoc->suc_chua . ' sinh viên, không đủ cho ' . $soSinhVienDuThi . ' sinh viên dự thi!');
                }
            }

            // 6. Kiểm tra trùng lịch giám thị
            $giamThiIds = array_filter([$request->giam_thi_1_id, $request->giam_thi_2_id]);
            
            if (!empty($giamThiIds)) {
                $trungGiamThi = LichThi::where('id', '!=', $lichThi->id)
                    ->where('ngay_thi', $request->ngay_thi)
                    ->where(function($q) use ($giamThiIds) {
                        $q->whereIn('giam_thi_1_id', $giamThiIds)
                          ->orWhereIn('giam_thi_2_id', $giamThiIds);
                    })
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                              ->orWhere(function ($q) use ($request) {
                                  $q->where('gio_bat_dau', '<=', $request->gio_bat_dau)
                                    ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
                              });
                    })
                    ->exists();

                if ($trungGiamThi) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Giảng viên giám thị đã có lịch coi thi trùng giờ!');
                }
            }

            // 7. Giới hạn số lịch thi theo loại (loại trừ bản ghi hiện tại)
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
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Lớp học phần đã đạt giới hạn số lần thi ' . 
                           ($request->loai_thi == 'giua_ky' ? 'giữa kỳ' : 
                           ($request->loai_thi == 'cuoi_ky' ? 'cuối kỳ' : 'thi lại')) . '!');
            }

            $data = $request->validated();

            // Xử lý upload file đề thi
            if ($request->hasFile('de_thi_file')) {
                // Xóa file cũ
                if ($lichThi->de_thi_file) {
                    Storage::disk('public')->delete($lichThi->de_thi_file);
                }
                $data['de_thi_file'] = $request->file('de_thi_file')->store('de-thi', 'public');
            }

            // Xử lý upload file đáp án
            if ($request->hasFile('dap_an_file')) {
                // Xóa file cũ
                if ($lichThi->dap_an_file) {
                    Storage::disk('public')->delete($lichThi->dap_an_file);
                }
                $data['dap_an_file'] = $request->file('dap_an_file')->store('dap-an', 'public');
            }

            // Tự động tính số sinh viên dự thi nếu không nhập
            if (!isset($data['so_sinh_vien_du_thi'])) {
                $data['so_sinh_vien_du_thi'] = $lopHocPhan->lopHocPhanSinhViens->count();
            }

            $lichThi->update($data);

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
}
