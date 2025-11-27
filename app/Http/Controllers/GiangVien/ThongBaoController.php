<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of notifications for the lecturer
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = NguoiNhanThongBao::with(['thongBao.nguoiGui'])
            ->where('nguoi_nhan_id', $userId)
            ->whereHas('thongBao', function ($q) {
                $q->where('trang_thai', 'cong_khai')
                    ->where(function ($subQ) {
                        $subQ->whereNull('hien_thi_tu_ngay')
                            ->orWhere('hien_thi_tu_ngay', '<=', now());
                    })
                    ->where(function ($subQ) {
                        $subQ->whereNull('ngay_het_han')
                            ->orWhere('ngay_het_han', '>=', now());
                    });
            })
            ->orderByDesc(function ($q) {
                $q->selectRaw('thong_bao.ghim_dau_trang')
                    ->from('thong_bao')
                    ->whereColumn('thong_bao.id', 'nguoi_nhan_thong_bao.thong_bao_id');
            })
            ->orderBy('created_at', 'desc');

        // Filter theo loại
        if ($request->filled('loai_thong_bao')) {
            $query->whereHas('thongBao', function ($q) use ($request) {
                $q->where('loai_thong_bao', $request->loai_thong_bao);
            });
        }

        // Filter đã đọc/chưa đọc
        if ($request->filled('trang_thai_doc')) {
            $query->where('da_doc', $request->trang_thai_doc == 'da_doc' ? true : false);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('thongBao', function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                    ->orWhere('noi_dung', 'like', "%{$search}%");
            });
        }

        $thongBaos = $query->paginate(20);

        // Đếm số thông báo chưa đọc
        $chuaDocCount = NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->whereHas('thongBao', function ($q) {
                $q->where('trang_thai', 'cong_khai');
            })
            ->count();

        return view('giangvien.thong-bao.index', compact('thongBaos', 'chuaDocCount'));
    }

    /**
     * Display the specified notification
     */
    public function show($id)
    {
        $userId = Auth::id();

        $nguoiNhanThongBao = NguoiNhanThongBao::with(['thongBao.nguoiGui'])
            ->where('nguoi_nhan_id', $userId)
            ->where('thong_bao_id', $id)
            ->firstOrFail();

        $thongBao = $nguoiNhanThongBao->thongBao;

        // Đánh dấu đã đọc
        if (!$nguoiNhanThongBao->da_doc) {
            $nguoiNhanThongBao->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);
        }

        // Tăng lượt xem
        $thongBao->increment('so_luot_xem');

        // Đổi tên biến để khớp với view
        $nguoiNhan = $nguoiNhanThongBao;

        return view('giangvien.thong-bao.show', compact('thongBao', 'nguoiNhan'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $userId = Auth::id();

        NguoiNhanThongBao::where('thong_bao_id', $id)
            ->where('nguoi_nhan_id', $userId)
            ->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);

        return back()->with('success', 'Đã đánh dấu đã đọc!');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();

        NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);

        return redirect()->route('giangvien.thong-bao.index')
            ->with('success', 'Đã đánh dấu tất cả là đã đọc!');
    }

    /**
     * Get unread count (for API/AJAX)
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();

        $count = NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_doc', false)
            ->whereHas('thongBao', function ($q) {
                $q->where('trang_thai', 'cong_khai');
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
