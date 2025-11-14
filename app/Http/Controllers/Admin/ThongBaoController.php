<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ThongBao::with('nguoiGui')->orderBy('created_at', 'desc');

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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                    ->orWhere('noi_dung', 'like', "%{$search}%");
            });
        }

        $thongBaos = $query->paginate(15);

        return view('admin.thong-bao.index', compact('thongBaos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.thong-bao.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        try {
            $validated = $request->validate([
                'tieu_de' => 'required|string|max:255',
                'noi_dung' => 'required|string',
                'loai_thong_bao' => 'required|in:tin_tuc,thong_bao_chung,tin_gap,lich_hoc,lich_thi,hoc_phi,diem,dang_ky_mon',
                'muc_do_quan_trong' => 'required|in:rat_quan_trong,quan_trong,binh_thuong',
                'doi_tuong' => 'required|in:all,sinh_vien,giang_vien,dao_tao,admin',
                'ghim_dau_trang' => 'boolean',
                'gui_email' => 'boolean',
                'hien_thi_tu_ngay' => 'nullable|date',
                'ngay_het_han' => 'nullable|date|after_or_equal:hien_thi_tu_ngay',
                'anh_dai_dien' => 'nullable|image|max:2048',
                'file_dinh_kem' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
            ]);

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

            return redirect()->route('admin.thong-bao.index')
                ->with('success', 'Tạo thông báo thành công! Đang gửi đến người nhận...');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Vui lòng kiểm tra lại thông tin!');
        } catch (\Exception $e) {
            \Log::error('Lỗi tạo thông báo: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ThongBao $thongBao)
    {
        $thongBao->load('nguoiGui', 'nguoiNhan.nguoiNhan');
        $thongBao->tangLuotXem();

        // Đánh dấu thông báo đã đọc cho user hiện tại
        if (Auth::check()) {
            $nguoiNhan = NguoiNhanThongBao::where('thong_bao_id', $thongBao->id)
                ->where('nguoi_nhan_id', Auth::id())
                ->first();

            if ($nguoiNhan && !$nguoiNhan->da_doc) {
                $nguoiNhan->danhDauDaDoc();
            }
        }

        // Determine layout based on user role
        $roles = Auth::user()->vaiTro()->pluck('ma_vai_tro')->toArray();
        $layout = 'layout-admin'; // default

        if (in_array('admin', $roles)) {
            $layout = 'layout-admin';
        } elseif (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)) {
            $layout = 'layout-daotao';
        } elseif (in_array('giang_vien', $roles)) {
            $layout = 'layout-giangvien';
        } elseif (in_array('sinh_vien', $roles)) {
            $layout = 'layout-sinhvien';
        }

        return view('thong-bao-show', compact('thongBao', 'layout'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThongBao $thongBao)
    {
        return view('admin.thong-bao.edit', compact('thongBao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThongBao $thongBao)
    {
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'loai_thong_bao' => 'required|in:tin_tuc,thong_bao_chung,tin_gap,lich_hoc,lich_thi,hoc_phi,diem,dang_ky_mon',
            'muc_do_quan_trong' => 'required|in:rat_quan_trong,quan_trong,binh_thuong',
            'doi_tuong' => 'required|in:all,sinh_vien,giang_vien,dao_tao,admin',
            'ghim_dau_trang' => 'boolean',
            'gui_email' => 'boolean',
            'hien_thi_tu_ngay' => 'nullable|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:hien_thi_tu_ngay',
            'anh_dai_dien' => 'nullable|image|max:2048',
            'file_dinh_kem' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
            'trang_thai' => 'required|in:cong_khai,nhap,da_xoa',
        ]);

        // Xử lý checkbox xóa ảnh
        if ($request->has('xoa_anh') && $thongBao->anh_dai_dien) {
            Storage::disk('public')->delete($thongBao->anh_dai_dien);
            $validated['anh_dai_dien'] = null;
        }

        // Xử lý checkbox xóa file
        if ($request->has('xoa_file') && $thongBao->file_dinh_kem) {
            Storage::disk('public')->delete($thongBao->file_dinh_kem);
            $validated['file_dinh_kem'] = null;
        }

        // Upload ảnh đại diện mới
        if ($request->hasFile('anh_dai_dien')) {
            // Xóa ảnh cũ nếu có
            if ($thongBao->anh_dai_dien) {
                Storage::disk('public')->delete($thongBao->anh_dai_dien);
            }
            $validated['anh_dai_dien'] = $request->file('anh_dai_dien')->store('thong-bao/images', 'public');
        }

        // Upload file đính kèm mới
        if ($request->hasFile('file_dinh_kem')) {
            // Xóa file cũ nếu có
            if ($thongBao->file_dinh_kem) {
                Storage::disk('public')->delete($thongBao->file_dinh_kem);
            }
            $validated['file_dinh_kem'] = $request->file('file_dinh_kem')->store('thong-bao/files', 'public');
        }

        $thongBao->update($validated);

        // Cập nhật người nhận nếu đối tượng thay đổi
        if ($request->doi_tuong !== $thongBao->getOriginal('doi_tuong')) {
            $thongBao->nguoiNhan()->delete();
            $this->taoNguoiNhan($thongBao);
        }

        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Cập nhật thông báo thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThongBao $thongBao)
    {
        // Xóa file nếu có
        if ($thongBao->anh_dai_dien) {
            Storage::disk('public')->delete($thongBao->anh_dai_dien);
        }
        if ($thongBao->file_dinh_kem) {
            Storage::disk('public')->delete($thongBao->file_dinh_kem);
        }

        $thongBao->delete();

        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Xóa thông báo thành công!');
    }

    /**
     * Download file đính kèm
     */
    public function download(ThongBao $thongBao)
    {
        if (!$thongBao->file_dinh_kem || !Storage::disk('public')->exists($thongBao->file_dinh_kem)) {
            abort(404, 'File không tồn tại');
        }

        $filePath = storage_path('app/public/' . $thongBao->file_dinh_kem);
        $fileName = basename($thongBao->file_dinh_kem);

        return response()->download($filePath, $fileName);
    }

    /**
     * Tạo bản ghi người nhận dựa vào đối tượng
     */
    private function taoNguoiNhan(ThongBao $thongBao)
    {
        $nguoiNhanIds = [];

        switch ($thongBao->doi_tuong) {
            case 'all':
                // Gửi cho tất cả user
                $nguoiNhanIds = User::pluck('id')->toArray();
                break;

            case 'sinh_vien':
                // Gửi cho tất cả sinh viên
                $nguoiNhanIds = User::whereHas('taiKhoanVaiTro.vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'sinh_vien');
                })->pluck('id')->toArray();
                break;

            case 'giang_vien':
                // Gửi cho tất cả giảng viên
                $nguoiNhanIds = User::whereHas('taiKhoanVaiTro.vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'giang_vien');
                })->pluck('id')->toArray();
                break;

            case 'dao_tao':
                // Gửi cho nhân viên đào tạo
                $nguoiNhanIds = User::whereHas('taiKhoanVaiTro.vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'dao_tao');
                })->pluck('id')->toArray();
                break;

            case 'admin':
                // Gửi cho admin
                $nguoiNhanIds = User::whereHas('taiKhoanVaiTro.vaiTro', function ($query) {
                    $query->where('ma_vai_tro', 'admin');
                })->pluck('id')->toArray();
                break;
        }

        // Tạo bản ghi người nhận
        foreach ($nguoiNhanIds as $nguoiNhanId) {
            NguoiNhanThongBao::create([
                'thong_bao_id' => $thongBao->id,
                'nguoi_nhan_id' => $nguoiNhanId,
                'da_doc' => false,
            ]);
        }
    }
}
