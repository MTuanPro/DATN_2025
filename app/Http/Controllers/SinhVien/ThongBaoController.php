<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of notifications for the student
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

        // Filter theo mức độ quan trọng
        if ($request->filled('muc_do')) {
            $query->whereHas('thongBao', function ($q) use ($request) {
                $q->where('muc_do_quan_trong', $request->muc_do);
            });
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

        // Thống kê theo loại
        $thongKe = [
            'tin_gap' => NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->whereHas('thongBao', function ($q) {
                    $q->where('loai_thong_bao', 'tin_gap')
                        ->where('trang_thai', 'cong_khai');
                })
                ->where('da_doc', false)
                ->count(),
            'lich_hoc' => NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->whereHas('thongBao', function ($q) {
                    $q->where('loai_thong_bao', 'lich_hoc')
                        ->where('trang_thai', 'cong_khai');
                })
                ->where('da_doc', false)
                ->count(),
            'lich_thi' => NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->whereHas('thongBao', function ($q) {
                    $q->where('loai_thong_bao', 'lich_thi')
                        ->where('trang_thai', 'cong_khai');
                })
                ->where('da_doc', false)
                ->count(),
            'hoc_phi' => NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->whereHas('thongBao', function ($q) {
                    $q->where('loai_thong_bao', 'hoc_phi')
                        ->where('trang_thai', 'cong_khai');
                })
                ->where('da_doc', false)
                ->count(),
            'diem' => NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
                ->whereHas('thongBao', function ($q) {
                    $q->where('loai_thong_bao', 'diem')
                        ->where('trang_thai', 'cong_khai');
                })
                ->where('da_doc', false)
                ->count(),
        ];

        return view('sinhvien.thong-bao.index', compact('thongBaos', 'chuaDocCount', 'thongKe'));
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

        return view('sinhvien.thong-bao.show', compact('thongBao', 'nguoiNhanThongBao'));
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

        return redirect()->route('sinhvien.thong-bao.index')
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
