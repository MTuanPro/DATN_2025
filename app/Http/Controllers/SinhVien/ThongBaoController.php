<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ThongBaoController extends Controller
{
    /**
     * Display a listing of notifications for the student
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $now = now();
        
        // Debug: Kiểm tra tổng số thông báo của user
        $totalNguoiNhan = NguoiNhanThongBao::where('nguoi_nhan_id', $userId)->count();
        Log::info('SinhVien ThongBaoController - Tổng số bản ghi NguoiNhanThongBao', [
            'user_id' => $userId,
            'total' => $totalNguoiNhan
        ]);
        
        $query = NguoiNhanThongBao::with(['thongBao.nguoiGui'])
            ->where('nguoi_nhan_id', $userId)
            ->whereHas('thongBao', function ($q) use ($now) {
                $q->where('trang_thai', 'cong_khai')
                    ->where(function ($subQ) use ($now) {
                        $subQ->whereNull('hien_thi_tu_ngay')
                            ->orWhere('hien_thi_tu_ngay', '<=', $now);
                    })
                    ->where(function ($subQ) use ($now) {
                        $subQ->whereNull('ngay_het_han')
                            ->orWhere('ngay_het_han', '>=', $now);
                    });
            });

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

        // Lấy dữ liệu và sort
        $thongBaos = $query->get();
        
        // Debug: Log số lượng thông báo sau khi query
        Log::info('SinhVien ThongBaoController - Số lượng thông báo sau query', [
            'user_id' => $userId,
            'count' => $thongBaos->count(),
            'filters' => [
                'loai_thong_bao' => $request->loai_thong_bao,
                'trang_thai_doc' => $request->trang_thai_doc,
                'muc_do' => $request->muc_do,
                'search' => $request->search,
            ]
        ]);
        
        // Sort: Ưu tiên thông báo ghim, sau đó sắp xếp theo ngày giờ mới nhất
        $thongBaos = $thongBaos->sort(function ($a, $b) {
            if (!$a->thongBao || !$b->thongBao) {
                return 0;
            }
            
            // So sánh ghim_dau_trang trước
            $aGhim = $a->thongBao->ghim_dau_trang ? 1 : 0;
            $bGhim = $b->thongBao->ghim_dau_trang ? 1 : 0;
            
            if ($aGhim !== $bGhim) {
                return $bGhim - $aGhim; // Ghim trước, không ghim sau
            }
            
            // Nếu cùng trạng thái ghim, sắp xếp theo ngày giờ mới nhất
            $aTime = $a->thongBao->ngay_gui ? $a->thongBao->ngay_gui->timestamp : 0;
            $bTime = $b->thongBao->ngay_gui ? $b->thongBao->ngay_gui->timestamp : 0;
            
            return $bTime - $aTime; // Mới nhất trước
        })->values();

        // Paginate thủ công
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $thongBaos->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $thongBaos = new LengthAwarePaginator(
            $currentItems,
            $thongBaos->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        // Đổi tên biến để khớp với view
        $nguoiNhan = $nguoiNhanThongBao;

        return view('sinhvien.thong-bao.show', compact('thongBao', 'nguoiNhan'));
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
