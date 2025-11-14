<?php

namespace App\Http\Controllers\DaoTao;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Models\User;
use App\Models\DaoTao\SinhVien;
use App\Models\DaoTao\LopHanhChinh;
use App\Models\LopHocPhan;
use App\Models\GiangVien;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Đào tạo chỉ thấy thông báo của mình tạo hoặc thông báo tự động
        $query = ThongBao::with('nguoiGui')
            ->where(function($q) {
                $q->where('nguoi_gui_id', Auth::id())
                  ->orWhere('loai_nguon', 'tu_dong');
            })
            ->orderBy('ghim_dau_trang', 'desc')
            ->orderBy('ngay_gui', 'desc');

        // Filter theo loại
        if ($request->filled('loai_thong_bao')) {
            $query->where('loai_thong_bao', $request->loai_thong_bao);
        }

        // Filter theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Filter theo đối tượng
        if ($request->filled('doi_tuong')) {
            $query->where('doi_tuong', $request->doi_tuong);
        }

        // Filter theo mức độ quan trọng
        if ($request->filled('muc_do')) {
            $query->where('muc_do_quan_trong', $request->muc_do);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                    ->orWhere('noi_dung', 'like', "%{$search}%");
            });
        }

        $thongBaos = $query->paginate(20);

        return view('daotao.thong-bao.index', compact('thongBaos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy danh sách để chọn đối tượng cụ thể
        $lopHanhChinhs = LopHanhChinh::orderBy('ma_lop')->get();
        $lopHocPhans = LopHocPhan::with('monHoc')->where('trang_thai', 'dang_mo')->orderBy('ma_lop_hoc_phan')->get();

        return view('daotao.thong-bao.create', compact('lopHanhChinhs', 'lopHocPhans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'loai_thong_bao' => 'required|in:tin_tuc,thong_bao_chung,tin_gap,lich_hoc,lich_thi,hoc_phi,diem,dang_ky_mon',
            'muc_do_quan_trong' => 'required|in:rat_quan_trong,quan_trong,binh_thuong',
            'doi_tuong' => 'required|in:all,sinh_vien,giang_vien,lop_hanh_chinh,lop_hoc_phan',
            'doi_tuong_cu_the_id' => 'nullable|integer',
            'ghim_dau_trang' => 'boolean',
            'gui_email' => 'boolean',
            'hien_thi_tu_ngay' => 'nullable|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:hien_thi_tu_ngay',
            'anh_dai_dien' => 'nullable|image|max:2048',
            'file_dinh_kem' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        try {
            // Upload ảnh đại diện
            if ($request->hasFile('anh_dai_dien')) {
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('thong-bao/images', 'public');
            }

            // Upload file đính kèm
            if ($request->hasFile('file_dinh_kem')) {
                $validated['file_dinh_kem'] = $request->file('file_dinh_kem')->store('thong-bao/files', 'public');
            }

            $validated['nguoi_gui_id'] = Auth::id();
            $validated['ngay_gui'] = now();
            $validated['loai_nguon'] = 'thu_cong';
            $validated['trang_thai'] = 'cong_khai';
            $validated['gui_web_notification'] = true;

            // Sử dụng NotificationService để tạo và gửi thông báo
            $thongBao = $notificationService->createNotification($validated, true);

            return redirect()->route('daotao.thong-bao.index')
                ->with('success', 'Đã tạo và gửi thông báo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $thongBao = ThongBao::with(['nguoiGui', 'nguoiNhan.nguoiNhan'])->findOrFail($id);

        // Tăng lượt xem
        $thongBao->increment('so_luot_xem');

        // Đánh dấu đã đọc cho user hiện tại
        if (Auth::check()) {
            $nguoiNhan = NguoiNhanThongBao::where('thong_bao_id', $thongBao->id)
                ->where('nguoi_nhan_id', Auth::id())
                ->first();

            if ($nguoiNhan && !$nguoiNhan->da_doc) {
                $nguoiNhan->danhDauDaDoc();
            }
        }

        // Thống kê người nhận
        $tongNguoiNhan = $thongBao->nguoiNhan->count();
        $daDoc = $thongBao->nguoiNhan->where('da_doc', true)->count();
        $chuaDoc = $thongBao->nguoiNhan->where('da_doc', false)->count();
        $daGuiEmail = $thongBao->nguoiNhan->where('da_gui_email', true)->count();

        // Danh sách người nhận với phân trang và filter
        $query = NguoiNhanThongBao::with('nguoiNhan')
            ->where('thong_bao_id', $thongBao->id);

        if ($request->filled('trang_thai')) {
            $query->where('da_doc', $request->trang_thai == 'da_doc' ? true : false);
        }

        $nguoiNhans = $query->paginate(20);

        return view('daotao.thong-bao.show', compact(
            'thongBao',
            'tongNguoiNhan',
            'daDoc',
            'chuaDoc',
            'daGuiEmail',
            'nguoiNhans'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        
        // Kiểm tra quyền: Đào tạo chỉ sửa thông báo của mình
        if ($thongBao->nguoi_gui_id !== Auth::id() && $thongBao->loai_nguon !== 'tu_dong') {
            abort(403, 'Bạn không có quyền sửa thông báo này');
        }
        
        $lopHanhChinhs = LopHanhChinh::orderBy('ma_lop')->get();
        $lopHocPhans = LopHocPhan::with('monHoc')->where('trang_thai', 'dang_mo')->orderBy('ma_lop_hoc_phan')->get();

        return view('daotao.thong-bao.edit', compact('thongBao', 'lopHanhChinhs', 'lopHocPhans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $thongBao = ThongBao::findOrFail($id);
        
        // Kiểm tra quyền: Đào tạo chỉ sửa thông báo của mình
        if ($thongBao->nguoi_gui_id !== Auth::id() && $thongBao->loai_nguon !== 'tu_dong') {
            abort(403, 'Bạn không có quyền sửa thông báo này');
        }

        // Validation - Đào tạo chỉ được gửi cho sinh viên, giảng viên, lớp
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'loai_thong_bao' => 'required|in:tin_tuc,thong_bao_chung,tin_gap,lich_hoc,lich_thi,hoc_phi,diem,dang_ky_mon',
            'muc_do_quan_trong' => 'required|in:rat_quan_trong,quan_trong,binh_thuong',
            'doi_tuong' => 'required|in:all,sinh_vien,giang_vien,lop_hanh_chinh,lop_hoc_phan', // Không cho phép: admin, dao_tao
            'doi_tuong_cu_the_id' => 'nullable|integer',
            'ghim_dau_trang' => 'boolean',
            'gui_email' => 'boolean',
            'hien_thi_tu_ngay' => 'nullable|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:hien_thi_tu_ngay',
            'anh_dai_dien' => 'nullable|image|max:2048',
            'file_dinh_kem' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        DB::beginTransaction();
        try {
            // Upload ảnh đại diện mới (nếu có)
            if ($request->hasFile('anh_dai_dien')) {
                // Xóa ảnh cũ
                if ($thongBao->anh_dai_dien && Storage::disk('public')->exists($thongBao->anh_dai_dien)) {
                    Storage::disk('public')->delete($thongBao->anh_dai_dien);
                }
                $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('thong-bao/images', 'public');
            }

            // Upload file đính kèm mới (nếu có)
            if ($request->hasFile('file_dinh_kem')) {
                // Xóa file cũ
                if ($thongBao->file_dinh_kem && Storage::disk('public')->exists($thongBao->file_dinh_kem)) {
                    Storage::disk('public')->delete($thongBao->file_dinh_kem);
                }
                $validated['file_dinh_kem'] = $request->file('file_dinh_kem')->store('thong-bao/files', 'public');
            }

            $thongBao->update($validated);

            // Nếu thay đổi đối tượng, cập nhật lại người nhận
            if ($request->has('doi_tuong') && $thongBao->doi_tuong != $validated['doi_tuong']) {
                // Xóa người nhận cũ
                $thongBao->nguoiNhan()->delete();
                // Tạo người nhận mới
                $this->taoNguoiNhan($thongBao);
            }

            DB::commit();

            return redirect()->route('daotao.thong-bao.show', $thongBao->id)
                ->with('success', 'Đã cập nhật thông báo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $thongBao = ThongBao::findOrFail($id);
        
        // Kiểm tra quyền: Đào tạo chỉ xóa thông báo của mình
        if ($thongBao->nguoi_gui_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa thông báo này');
        }

        // Xóa file đính kèm
        if ($thongBao->anh_dai_dien && Storage::disk('public')->exists($thongBao->anh_dai_dien)) {
            Storage::disk('public')->delete($thongBao->anh_dai_dien);
        }
        if ($thongBao->file_dinh_kem && Storage::disk('public')->exists($thongBao->file_dinh_kem)) {
            Storage::disk('public')->delete($thongBao->file_dinh_kem);
        }

        $thongBao->delete();

        return redirect()->route('daotao.thong-bao.index')
            ->with('success', 'Đã xóa thông báo thành công!');
    }

    /**
     * Tạo người nhận thông báo dựa vào đối tượng
     */
    private function taoNguoiNhan(ThongBao $thongBao)
    {
        $nguoiNhanIds = [];

        switch ($thongBao->doi_tuong) {
            case 'all':
                $nguoiNhanIds = User::where('trang_thai', 'hoat_dong')->pluck('id')->toArray();
                break;

            case 'sinh_vien':
                $nguoiNhanIds = User::whereHas('sinhVien')->where('trang_thai', 'hoat_dong')->pluck('id')->toArray();
                break;

            case 'giang_vien':
                $nguoiNhanIds = User::whereHas('giangVien')->where('trang_thai', 'hoat_dong')->pluck('id')->toArray();
                break;

            case 'lop_hanh_chinh':
                if ($thongBao->doi_tuong_cu_the_id) {
                    $nguoiNhanIds = SinhVien::where('lop_hanh_chinh_id', $thongBao->doi_tuong_cu_the_id)
                        ->whereHas('user', function ($q) {
                            $q->where('trang_thai', 'hoat_dong');
                        })
                        ->pluck('user_id')
                        ->toArray();
                }
                break;

            case 'lop_hoc_phan':
                if ($thongBao->doi_tuong_cu_the_id) {
                    $nguoiNhanIds = DB::table('lop_hoc_phan_sinh_vien')
                        ->where('lop_hoc_phan_id', $thongBao->doi_tuong_cu_the_id)
                        ->join('sinh_vien', 'lop_hoc_phan_sinh_vien.sinh_vien_id', '=', 'sinh_vien.id')
                        ->join('users', 'sinh_vien.user_id', '=', 'users.id')
                        ->where('users.trang_thai', 'hoat_dong')
                        ->pluck('users.id')
                        ->toArray();
                }
                break;
        }

        // Tạo bản ghi người nhận
        foreach ($nguoiNhanIds as $userId) {
            NguoiNhanThongBao::create([
                'thong_bao_id' => $thongBao->id,
                'nguoi_nhan_id' => $userId,
                'da_doc' => false,
            ]);
        }
    }
}
