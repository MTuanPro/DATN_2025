<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiNhanThongBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo chưa đọc cho user hiện tại
     */
    public function index()
    {
        $userId = Auth::id();

        $notifications = NguoiNhanThongBao::with(['thongBao' => function ($query) {
            $query->select('id', 'tieu_de', 'noi_dung', 'loai_thong_bao', 'muc_do_quan_trong', 'created_at');
        }])
            ->where('nguoi_nhan_id', $userId)
            ->whereHas('thongBao', function ($query) {
                $query->congKhai()->dangHienThi();
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $chuaDocCount = NguoiNhanThongBao::where('nguoi_nhan_id', $userId)
            ->where('da_xem', false)
            ->whereHas('thongBao', function ($query) {
                $query->congKhai()->dangHienThi();
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $chuaDocCount,
            ],
        ]);
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|exists:nguoi_nhan_thong_bao,id',
        ]);

        $notification = NguoiNhanThongBao::where('id', $validated['notification_id'])
            ->where('nguoi_nhan_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->danhDauDaXem();

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đã đọc',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thông báo',
        ], 404);
    }

    /**
     * Đánh dấu tất cả đã đọc
     */
    public function markAllAsRead()
    {
        NguoiNhanThongBao::where('nguoi_nhan_id', Auth::id())
            ->where('da_xem', false)
            ->update([
                'da_xem' => true,
                'thoi_gian_xem' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả đã đọc',
        ]);
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     */
    public function unreadCount()
    {
        $count = NguoiNhanThongBao::where('nguoi_nhan_id', Auth::id())
            ->where('da_xem', false)
            ->whereHas('thongBao', function ($query) {
                $query->congKhai()->dangHienThi();
            })
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }
}
