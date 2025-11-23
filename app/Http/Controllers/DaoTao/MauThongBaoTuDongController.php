<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\MauThongBaoTuDong;
use Illuminate\Http\Request;

class MauThongBaoTuDongController extends Controller
{
    /**
     * Hiển thị danh sách tất cả mẫu thông báo tự động
     * 
     * Lấy danh sách các mẫu thông báo được sắp xếp theo loại thông báo
     * để hiển thị trên trang quản lý
     * 
     * @return \Illuminate\View\View Trang danh sách mẫu thông báo
     * @throws \Exception Nếu có lỗi khi truy vấn database
     */
    public function index()
    {
        $mauThongBaos = MauThongBaoTuDong::orderBy('loai_thong_bao')->get();

        return view('daotao.mau-thong-bao.index', compact('mauThongBaos'));
    }

    /**
     * Hiển thị form tạo mẫu thông báo tự động mới
     * 
     * Lấy danh sách các loại thông báo có sẵn để người dùng chọn
     * khi tạo mẫu mới
     * 
     * @return \Illuminate\View\View Form tạo mẫu thông báo
     * @throws \Exception Nếu có lỗi khi tải options
     */
    public function create()
    {
        $loaiThongBaoOptions = MauThongBaoTuDong::getLoaiThongBaoOptions();

        return view('daotao.mau-thong-bao.create', compact('loaiThongBaoOptions'));
    }

    /**
     * Lưu mẫu thông báo tự động mới vào database
     * 
     * Validate dữ liệu đầu vào và tạo bản ghi mới trong bảng mau_thong_bao_tu_dong
     * 
     * @param Request $request Chứa dữ liệu:
     *                         - loai_thong_bao: Loại thông báo (unique)
     *                         - tieu_de_mau: Tiêu đề mẫu (max 255 ký tự)
     *                         - noi_dung_mau: Nội dung thông báo
     *                         - doi_tuong_mac_dinh: Đối tượng nhận (nullable)
     *                         - muc_do_uu_tien: binh_thuong|quan_trong|rat_quan_trong
     *                         - gui_email_mac_dinh: boolean
     *                         - gui_sms_mac_dinh: boolean
     *                         - kich_hoat: boolean
     *                         - ghi_chu: Ghi chú (nullable)
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách với thông báo thành công
     * @throws \Illuminate\Validation\ValidationException Nếu dữ liệu không hợp lệ
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

        return redirect()->route('dao-tao.mau-thong-bao.index')
            ->with('success', 'Tạo mẫu thông báo tự động thành công');
    }

    /**
     * Hiển thị chi tiết một mẫu thông báo tự động
     * 
     * @param MauThongBaoTuDong $mauThongBao Instance mẫu thông báo cần xem
     * @return \Illuminate\View\View Trang chi tiết mẫu thông báo
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy mẫu thông báo
     */
    public function show(MauThongBaoTuDong $mauThongBao)
    {
        return view('daotao.mau-thong-bao.show', compact('mauThongBao'));
    }

    /**
     * Hiển thị form chỉnh sửa mẫu thông báo tự động
     * 
     * Lấy thông tin mẫu thông báo hiện tại và danh sách loại thông báo
     * để hiển thị trên form chỉnh sửa
     * 
     * @param MauThongBaoTuDong $mauThongBao Instance mẫu thông báo cần sửa
     * @return \Illuminate\View\View Form chỉnh sửa mẫu thông báo
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
     */
    public function edit(MauThongBaoTuDong $mauThongBao)
    {
        $loaiThongBaoOptions = MauThongBaoTuDong::getLoaiThongBaoOptions();

        return view('daotao.mau-thong-bao.edit', compact('mauThongBao', 'loaiThongBaoOptions'));
    }

    /**
     * Cập nhật thông tin mẫu thông báo tự động
     * 
     * Validate và cập nhật thông tin mẫu thông báo trong database
     * 
     * @param Request $request Chứa dữ liệu cập nhật (tương tự store)
     * @param MauThongBaoTuDong $mauThongBao Instance mẫu thông báo cần cập nhật
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách với thông báo thành công
     * @throws \Illuminate\Validation\ValidationException Nếu dữ liệu không hợp lệ
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
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

        return redirect()->route('dao-tao.mau-thong-bao.index')
            ->with('success', 'Cập nhật mẫu thông báo tự động thành công');
    }

    /**
     * Xóa mẫu thông báo tự động khỏi database
     * 
     * Thực hiện soft delete hoặc hard delete tùy theo cấu hình model
     * 
     * @param MauThongBaoTuDong $mauThongBao Instance mẫu thông báo cần xóa
     * @return \Illuminate\Http\RedirectResponse Redirect về trang danh sách với thông báo thành công
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
     * @throws \Exception Nếu có lỗi khi xóa (ví dụ: constraint violation)
     */
    public function destroy(MauThongBaoTuDong $mauThongBao)
    {
        $mauThongBao->delete();

        return redirect()->route('dao-tao.mau-thong-bao.index')
            ->with('success', 'Xóa mẫu thông báo tự động thành công');
    }

    /**
     * Bật/tắt trạng thái kích hoạt của mẫu thông báo
     * 
     * Đảo ngược trạng thái kich_hoat (true <-> false) của mẫu thông báo.
     * Chỉ các mẫu được kích hoạt mới được sử dụng để gửi thông báo tự động.
     * 
     * @param MauThongBaoTuDong $mauThongBao Instance mẫu thông báo cần bật/tắt
     * @return \Illuminate\Http\RedirectResponse Redirect về trang trước với thông báo trạng thái mới
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy
     * @throws \Exception Nếu có lỗi khi cập nhật
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
