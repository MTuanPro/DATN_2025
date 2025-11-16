<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MauThongBaoTuDong;
use Illuminate\Http\Request;

class MauThongBaoTuDongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mauThongBaos = MauThongBaoTuDong::orderBy('loai_thong_bao')->get();

        return view('admin.mau-thong-bao.index', compact('mauThongBaos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $loaiThongBaoOptions = MauThongBaoTuDong::getLoaiThongBaoOptions();

        return view('admin.mau-thong-bao.create', compact('loaiThongBaoOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'loai_thong_bao' => 'required|string|unique:mau_thong_bao_tu_dong,loai_thong_bao',
            'tieu_de_mau' => 'required|string|max:255',
            'noi_dung_mau' => 'required|string',
            'doi_tuong_mac_dinh' => 'nullable|string',
            'muc_do_uu_tien' => 'required|in:binh_thuong,quan_trong,rat_quan_trong',
            'gui_email_mac_dinh' => 'boolean',
            'gui_sms_mac_dinh' => 'boolean',
            'kich_hoat' => 'boolean',
            'ghi_chu' => 'nullable|string',
        ]);

        $validated['gui_email_mac_dinh'] = $request->has('gui_email_mac_dinh');
        $validated['gui_sms_mac_dinh'] = $request->has('gui_sms_mac_dinh');
        $validated['kich_hoat'] = $request->has('kich_hoat');

        MauThongBaoTuDong::create($validated);

        return redirect()->route('admin.mau-thong-bao.index')
            ->with('success', 'Tạo mẫu thông báo tự động thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(MauThongBaoTuDong $mauThongBao)
    {
        return view('admin.mau-thong-bao.show', compact('mauThongBao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MauThongBaoTuDong $mauThongBao)
    {
        $loaiThongBaoOptions = MauThongBaoTuDong::getLoaiThongBaoOptions();

        return view('admin.mau-thong-bao.edit', compact('mauThongBao', 'loaiThongBaoOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MauThongBaoTuDong $mauThongBao)
    {
        $validated = $request->validate([
            'loai_thong_bao' => 'required|string|unique:mau_thong_bao_tu_dong,loai_thong_bao,' . $mauThongBao->id,
            'tieu_de_mau' => 'required|string|max:255',
            'noi_dung_mau' => 'required|string',
            'doi_tuong_mac_dinh' => 'nullable|string',
            'muc_do_uu_tien' => 'required|in:binh_thuong,quan_trong,rat_quan_trong',
            'gui_email_mac_dinh' => 'boolean',
            'gui_sms_mac_dinh' => 'boolean',
            'kich_hoat' => 'boolean',
            'ghi_chu' => 'nullable|string',
        ]);

        $validated['gui_email_mac_dinh'] = $request->has('gui_email_mac_dinh');
        $validated['gui_sms_mac_dinh'] = $request->has('gui_sms_mac_dinh');
        $validated['kich_hoat'] = $request->has('kich_hoat');

        $mauThongBao->update($validated);

        return redirect()->route('admin.mau-thong-bao.index')
            ->with('success', 'Cập nhật mẫu thông báo tự động thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MauThongBaoTuDong $mauThongBao)
    {
        $mauThongBao->delete();

        return redirect()->route('admin.mau-thong-bao.index')
            ->with('success', 'Xóa mẫu thông báo tự động thành công');
    }

    /**
     * Toggle activation status
     */
    public function toggleActivation(MauThongBaoTuDong $mauThongBao)
    {
        $mauThongBao->update([
            'kich_hoat' => !$mauThongBao->kich_hoat
        ]);

        $status = $mauThongBao->kich_hoat ? 'kích hoạt' : 'tắt';

        return redirect()->back()
            ->with('success', "Đã {$status} mẫu thông báo tự động");
    }
}
