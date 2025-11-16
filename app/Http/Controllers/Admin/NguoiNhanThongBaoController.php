<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\NguoiNhanThongBao;
use Illuminate\Http\Request;

class NguoiNhanThongBaoController extends Controller
{
    /**
     * Display a listing of recipients across all notifications
     */
    public function index(Request $request)
    {
        $query = NguoiNhanThongBao::with(['thongBao', 'nguoiNhan']);

        // Filter by notification
        if ($request->filled('thong_bao_id')) {
            $query->where('thong_bao_id', $request->thong_bao_id);
        }

        // Filter by read status
        if ($request->filled('da_doc')) {
            $query->where('da_doc', $request->da_doc == '1');
        }

        // Filter by email sent status
        if ($request->filled('da_gui_email')) {
            $query->where('da_gui_email', $request->da_gui_email == '1');
        }

        // Search by recipient name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nguoiNhan', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $nguoiNhans = $query->orderBy('created_at', 'desc')->paginate(20);
        $thongBaos = ThongBao::orderBy('created_at', 'desc')->get();

        return view('admin.nguoi-nhan-thong-bao.index', compact('nguoiNhans', 'thongBaos'));
    }

    /**
     * Display statistics about notification recipients
     */
    public function statistics()
    {
        $tongNguoiNhan = NguoiNhanThongBao::count();
        $daDoc = NguoiNhanThongBao::where('da_doc', true)->count();
        $chuaDoc = NguoiNhanThongBao::where('da_doc', false)->count();
        $daGuiEmail = NguoiNhanThongBao::where('da_gui_email', true)->count();

        $thongKeTheoThongBao = ThongBao::withCount([
            'nguoiNhan',
            'nguoiNhan as nguoi_nhan_da_doc_count' => function($query) {
                $query->where('da_doc', true);
            },
            'nguoiNhan as nguoi_nhan_da_gui_email_count' => function($query) {
                $query->where('da_gui_email', true);
            }
        ])->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.nguoi-nhan-thong-bao.statistics', compact(
            'tongNguoiNhan',
            'daDoc',
            'chuaDoc',
            'daGuiEmail',
            'thongKeTheoThongBao'
        ));
    }

    /**
     * Show the specified notification recipient
     */
    public function show($id)
    {
        $nguoiNhan = NguoiNhanThongBao::with(['thongBao', 'nguoiNhan'])->findOrFail($id);

        return view('admin.nguoi-nhan-thong-bao.show', compact('nguoiNhan'));
    }

    /**
     * Mark as read (bulk action)
     */
    public function markAsRead(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có người nhận nào được chọn');
        }

        NguoiNhanThongBao::whereIn('id', $ids)->update([
            'da_doc' => true,
            'ngay_doc' => now()
        ]);

        return redirect()->back()->with('success', 'Đã đánh dấu ' . count($ids) . ' người nhận là đã đọc');
    }

    /**
     * Resend email (bulk action)
     */
    public function resendEmail(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có người nhận nào được chọn');
        }

        // TODO: Implement email resend logic via queue
        
        return redirect()->back()->with('success', 'Đang gửi lại email cho ' . count($ids) . ' người nhận');
    }

    /**
     * Delete recipient records (bulk action)
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có người nhận nào được chọn');
        }

        NguoiNhanThongBao::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Đã xóa ' . count($ids) . ' bản ghi người nhận');
    }
}
