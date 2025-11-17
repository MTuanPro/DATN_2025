<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\CaHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CaHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $caHocList = CaHoc::orderBy('thu_tu')->get();
        
        return view('daotao.ca-hoc.index', compact('caHocList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daotao.ca-hoc.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_ca' => 'required|string|max:50',
            'thu_tu' => 'required|integer|between:1,20|unique:ca_hoc,thu_tu',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_ca.required' => 'Tên ca học là bắt buộc',
            'ten_ca.max' => 'Tên ca học không được vượt quá 50 ký tự',
            'thu_tu.required' => 'Thứ tự ca học là bắt buộc',
            'thu_tu.unique' => 'Thứ tự ca học đã tồn tại',
            'thu_tu.between' => 'Thứ tự ca học phải từ 1 đến 20',
            'gio_bat_dau.required' => 'Giờ bắt đầu là bắt buộc',
            'gio_bat_dau.date_format' => 'Giờ bắt đầu phải đúng định dạng HH:MM',
            'gio_ket_thuc.required' => 'Giờ kết thúc là bắt buộc',
            'gio_ket_thuc.date_format' => 'Giờ kết thúc phải đúng định dạng HH:MM',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra xung đột thời gian với các ca học khác
        if (CaHoc::kiemTraXungDotThoiGian($request->gio_bat_dau, $request->gio_ket_thuc)) {
            $caHocTrung = CaHoc::getCaHocTrungThoiGian($request->gio_bat_dau, $request->gio_ket_thuc);
            $danhSachTrung = $caHocTrung->pluck('ten_ca')->join(', ');
            
            return redirect()->back()
                ->withErrors([
                    'gio_bat_dau' => "Khoảng thời gian này trùng với ca học khác: {$danhSachTrung}. Vui lòng chọn thời gian khác."
                ])
                ->withInput();
        }

        try {
            CaHoc::create([
                'ten_ca' => $request->ten_ca,
                'thu_tu' => $request->thu_tu,
                'gio_bat_dau' => $request->gio_bat_dau,
                'gio_ket_thuc' => $request->gio_ket_thuc,
                'trang_thai' => $request->has('trang_thai') ? true : false,
                'ghi_chu' => $request->ghi_chu,
            ]);

            return redirect()->route('dao-tao.ca-hoc.index')
                ->with('success', 'Thêm ca học thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $caHoc = CaHoc::findOrFail($id);
        return view('daotao.ca-hoc.show', compact('caHoc'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $caHoc = CaHoc::findOrFail($id);
        return view('daotao.ca-hoc.edit', compact('caHoc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $caHoc = CaHoc::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ten_ca' => 'required|string|max:50',
            'thu_tu' => 'required|integer|between:1,20|unique:ca_hoc,thu_tu,' . $id,
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'ghi_chu' => 'nullable|string',
        ], [
            'ten_ca.required' => 'Tên ca học là bắt buộc',
            'ten_ca.max' => 'Tên ca học không được vượt quá 50 ký tự',
            'thu_tu.required' => 'Thứ tự ca học là bắt buộc',
            'thu_tu.unique' => 'Thứ tự ca học đã tồn tại',
            'thu_tu.between' => 'Thứ tự ca học phải từ 1 đến 20',
            'gio_bat_dau.required' => 'Giờ bắt đầu là bắt buộc',
            'gio_bat_dau.date_format' => 'Giờ bắt đầu phải đúng định dạng HH:MM',
            'gio_ket_thuc.required' => 'Giờ kết thúc là bắt buộc',
            'gio_ket_thuc.date_format' => 'Giờ kết thúc phải đúng định dạng HH:MM',
            'gio_ket_thuc.after' => 'Giờ kết thúc phải sau giờ bắt đầu',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra xung đột thời gian với các ca học khác (loại trừ ca học hiện tại)
        if (CaHoc::kiemTraXungDotThoiGian($request->gio_bat_dau, $request->gio_ket_thuc, $id)) {
            $caHocTrung = CaHoc::getCaHocTrungThoiGian($request->gio_bat_dau, $request->gio_ket_thuc, $id);
            $danhSachTrung = $caHocTrung->pluck('ten_ca')->join(', ');
            
            return redirect()->back()
                ->withErrors([
                    'gio_bat_dau' => "Khoảng thời gian này trùng với ca học khác: {$danhSachTrung}. Vui lòng chọn thời gian khác."
                ])
                ->withInput();
        }

        try {
            $caHoc->update([
                'ten_ca' => $request->ten_ca,
                'thu_tu' => $request->thu_tu,
                'gio_bat_dau' => $request->gio_bat_dau,
                'gio_ket_thuc' => $request->gio_ket_thuc,
                'trang_thai' => $request->has('trang_thai') ? true : false,
                'ghi_chu' => $request->ghi_chu,
            ]);

            return redirect()->route('dao-tao.ca-hoc.index')
                ->with('success', 'Cập nhật ca học thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $caHoc = CaHoc::findOrFail($id);
            $caHoc->delete();

            return redirect()->route('dao-tao.ca-hoc.index')
                ->with('success', 'Xóa ca học thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status of ca hoc
     */
    public function toggleStatus(string $id)
    {
        try {
            $caHoc = CaHoc::findOrFail($id);
            $caHoc->trang_thai = !$caHoc->trang_thai;
            $caHoc->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $caHoc->trang_thai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
