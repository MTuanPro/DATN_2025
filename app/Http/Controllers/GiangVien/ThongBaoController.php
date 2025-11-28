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
     * Hiển thị danh sách tất cả thông báo dành cho giảng viên với filters và search
     *
     * Function này cung cấp dashboard thông báo cho giảng viên, hiển thị các thông báo
     * từ nhà trường, khoa, bộ môn, với khả năng lọc, tìm kiếm, và quản lý
     * trạng thái đọc/chưa đọc.
     *
     * Workflow:
     * 1. Lấy user ID của giảng viên đang đăng nhập (Auth::id())
     * 2. Build base query:
     *    - NguoiNhanThongBao (pivot table giữa User và ThongBao)
     *    - Eager load: thongBao.nguoiGui
     *    - Where nguoi_nhan_id = current user ID
     *    - WhereHas thongBao với conditions:
     *      + trang_thai = 'cong_khai' (chỉ lấy thông báo đã publish)
     *      + hien_thi_tu_ngay: NULL hoặc <= now() (thông báo đã đến thời gian hiển thị)
     *      + ngay_het_han: NULL hoặc >= now() (thông báo chưa hết hạn)
     * 3. Sắp xếp:
     *    - OrderByDesc ghim_dau_trang (thông báo ghim lên đầu)
     *    - OrderBy created_at DESC (mới nhất trước)
     * 4. Apply filters từ request:
     *    a. Lọc theo loại thông báo (loai_thong_bao):
     *       - 'thong_bao_chung': Thông báo chung
     *       - 'lich_thi': Lịch thi
     *       - 'lich_hoc': Lịch học
     *       - 'hoc_vu': Học vụ
     *       - etc.
     *    b. Lọc theo trạng thái đọc (trang_thai_doc):
     *       - 'da_doc': Chỉ lấy thông báo đã đọc
     *       - 'chua_doc': Chỉ lấy thông báo chưa đọc
     *    c. Tìm kiếm text (search):
     *       - Search trong tieu_de
     *       - Search trong noi_dung
     *       - Case-insensitive LIKE %keyword%
     * 5. Paginate kết quả: 20 items per page
     * 6. Đếm số thông báo chưa đọc:
     *    - Count NguoiNhanThongBao where da_doc = false
     *    - Where trang_thai = 'cong_khai'
     * 7. Return view với data
     *
     * Thông tin hiển thị:
     * - Header statistics:
     *   + Tổng số thông báo
     *   + Số thông báo chưa đọc (badge đỏ)
     * - Filter controls:
     *   + Search box (tìm theo tiêu đề, nội dung)
     *   + Dropdown loại thông báo
     *   + Dropdown trạng thái đọc
     *   + Button "Reset filters"
     * - Danh sách thông báo (card/list layout):
     *   + Icon loại thông báo với màu sắc
     *   + Tiêu đề (bold nếu chưa đọc)
     *   + Nội dung tóm tắt (150 chars)
     *   + Người gửi (avatar + tên)
     *   + Thời gian gửi (relative time: "2 giờ trước")
     *   + Badge "Ghim" nếu ghim_dau_trang = true
     *   + Badge "Chưa đọc" nếu da_doc = false
     *   + Nút: "Xem chi tiết", "Đánh dấu đã đọc"
     * - Bulk actions:
     *   + Chọn nhiều thông báo
     *   + "Đánh dấu tất cả là đã đọc"
     * - Pagination: 20 thông báo/trang
     *
     * Tính năng đặc biệt:
     * - Thông báo ghim luôn xuất hiện đầu tiên
     * - Real-time notification count (WebSocket/Pusher)
     * - Mark as read khi click vào thông báo
     * - Auto-hide thông báo hết hạn
     *
     * @param Request $request Chứa filters:
     *   - loai_thong_bao (string|null): Loại thông báo
     *   - trang_thai_doc (string|null): 'da_doc' | 'chua_doc'
     *   - search (string|null): Keyword tìm kiếm
     * @return \Illuminate\View\View View danh sách thông báo với:
     *   - thongBaos: Paginated NguoiNhanThongBao collection
     *   - chuaDocCount: Số thông báo chưa đọc
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
     * Hiển thị nội dung đầy đủ của một thông báo và tự động đánh dấu đã đọc
     *
     * Function này hiển thị chi tiết của một thông báo, bao gồm nội dung HTML,
     * file đính kèm, thông tin người gửi, và tự động đánh dấu là đã đọc
     * khi giảng viên mở xem.
     *
     * Workflow:
     * 1. Lấy user ID của giảng viên hiện tại
     * 2. Tìm bản ghi NguoiNhanThongBao:
     *    - Where nguoi_nhan_id = current user
     *    - Where thong_bao_id = $id (tham số truyền vào)
     *    - Eager load: thongBao.nguoiGui
     *    - FirstOrFail (404 nếu không tìm thấy hoặc không có quyền xem)
     * 3. Lấy thông báo từ relationship
     * 4. Nếu thông báo chưa đọc (da_doc = false):
     *    - Update NguoiNhanThongBao:
     *      + da_doc = true
     *      + ngay_doc = now() (timestamp)
     * 5. Tăng lượt xem của thông báo:
     *    - Increment so_luot_xem column
     *    - Để thống kê số lượng người đã xem
     * 6. Return view với data
     *
     * Thông tin hiển thị:
     * - Header thông báo:
     *   + Tiêu đề (h1, bold)
     *   + Badge loại thông báo (với màu)
     *   + Mức độ ưu tiên (nếu có):
     *     - "Cao": Đỏ
     *     - "Trung bình": Vàng
     *     - "Thấp": Xanh
     * - Metadata:
     *   + Người gửi (avatar + tên + chức danh)
     *   + Thời gian gửi (format: dd/mm/YYYY HH:ii)
     *   + Số lượt xem (icon eye + số)
     * - Nội dung thông báo:
     *   + Hiển thị HTML content (CKEditor output)
     *   + Sanitize XSS nếu cần
     *   + Support images, videos, tables, formatting
     * - File đính kèm (nếu có):
     *   + Danh sách files với icon theo extension
     *   + Tên file + kích thước
     *   + Nút download
     * - Actions:
     *   + "Đánh dấu chưa đọc" (toggle)
     *   + "In thông báo"
     *   + "Chia sẻ"
     *   + "Quay lại danh sách"
     * - Thông báo liên quan (nếu có):
     *   + 3-5 thông báo cùng loại hoặc topic
     *
     * Tính năng đặc biệt:
     * - Auto-mark as read khi mở
     * - Tracking view count
     * - Print-friendly version
     * - Download attachments
     * - Share via email
     * - Responsive cho mobile
     *
     * Side effects:
     * - Update da_doc = true và ngay_doc = now()
     * - Increment so_luot_xem
     * - Có thể trigger event NotificationRead
     *
     * @param int $id ID của thông báo (thong_bao_id)
     * @return \Illuminate\View\View View chi tiết thông báo với:
     *   - thongBao: ThongBao instance với full data
     *   - nguoiNhan: NguoiNhanThongBao instance (pivot data)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Nếu không tìm thấy hoặc không có quyền
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
     * Đánh dấu một thông báo cụ thể là đã đọc (manual action)
     *
     * Function này cho phép giảng viên thủ công đánh dấu một thông báo
     * là đã đọc mà không cần mở xem chi tiết. Hữu ích cho việc
     * quản lý inbox và giảm số thông báo chưa đọc.
     *
     * Workflow:
     * 1. Lấy user ID của giảng viên đang đăng nhập
     * 2. Tìm bản ghi NguoiNhanThongBao:
     *    - Where thong_bao_id = $id
     *    - Where nguoi_nhan_id = current user ID
     * 3. Update trạng thái đọc:
     *    - Set da_doc = true
     *    - Set ngay_doc = now() (timestamp hiện tại)
     * 4. Redirect back với success message
     *
     * Use cases:
     * - Giảng viên đánh dấu nhanh từ danh sách thông báo
     * - Clear notification badge không cần xem chi tiết
     * - Bulk mark multiple notifications (gọi nhiều lần)
     * - Mark đọc từ notification dropdown
     *
     * Response:
     * - Redirect back to previous page
     * - Flash success message: "Đã đánh dấu đã đọc!"
     * - Update notification count realtime (nếu dùng WebSocket)
     *
     * Side effects:
     * - Update database: da_doc = true, ngay_doc = now()
     * - Giảm số unread count (cập nhật badge)
     * - Có thể trigger event/listener để update realtime UI
     *
     * Security:
     * - Chỉ update thông báo thuộc về current user
     * - Where nguoi_nhan_id = Auth::id() để prevent unauthorized access
     *
     * Performance:
     * - Sử dụng update() thay vì find()->update() để tối ưu
     * - Không load full model, chỉ update trực tiếp trong DB
     *
     * Alternative:
     * - Có thể dùng AJAX request trả về JSON thay vì redirect
     * - Thêm parameter toggle để mark as read/unread
     *
     * @param int $id ID của thông báo (thong_bao_id)
     * @return \Illuminate\Http\RedirectResponse Redirect back với success message
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
     * Đánh dấu tất cả thông báo chưa đọc của giảng viên thành đã đọc
     *
     * Function này cho phép giảng viên clear tất cả thông báo chưa đọc
     * bằng một thao tác duy nhất, giúp dọc dẹp inbox nhanh chóng.
     *
     * Workflow:
     * 1. Lấy user ID của giảng viên đang đăng nhập (Auth::id())
     * 2. Query tất cả NguoiNhanThongBao chưa đọc:
     *    - Where nguoi_nhan_id = current user
     *    - Where da_doc = false (chưa đọc)
     * 3. Bulk update tất cả records:
     *    - Set da_doc = true
     *    - Set ngay_doc = now() (cùng timestamp cho tất cả)
     * 4. Redirect về trang danh sách thông báo
     * 5. Hiển thị success message
     *
     * Use cases:
     * - Giảng viên có quá nhiều thông báo chưa đọc
     * - Clear inbox để tập trung vào thông báo mới
     * - Reset notification badge về 0
     * - Bulk action trong quản lý thông báo
     *
     * Response:
     * - Redirect to: route('giangvien.thong-bao.index')
     * - Success message: "Đã đánh dấu tất cả là đã đọc!"
     * - Update notification count về 0
     *
     * Performance considerations:
     * - Sử dụng bulk update() thay vì loop qua từng record
     * - Single query update nhiều rows cùng lúc
     * - Efficient cho 100+ thông báo
     * - Index on (nguoi_nhan_id, da_doc) để tối ưu query
     *
     * Side effects:
     * - Update nhiều rows trong bảng nguoi_nhan_thong_bao
     * - Badge count giảm về 0
     * - Có thể trigger events cho realtime updates
     * - Logs activity (nếu có audit logging)
     *
     * Security:
     * - Chỉ update thông báo thuộc current user
     * - Where nguoi_nhan_id = Auth::id() prevent unauthorized updates
     * - Không ảnh hưởng thông báo của user khác
     *
     * Best practices:
     * - Confirm dialog trước khi thực hiện (optional)
     * - Undo option trong vài giây (advanced)
     * - Show số lượng thông báo đã đánh dấu
     *
     * @return \Illuminate\Http\RedirectResponse Redirect về danh sách thông báo với success message
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
     * API endpoint để lấy số lượng thông báo chưa đọc cho AJAX/realtime updates
     *
     * Function này cung cấp API endpoint trả về JSON chứa số lượng thông báo
     * chưa đọc của giảng viên, phục vụ cho AJAX polling, WebSocket updates,
     * hoặc realtime notification badge counter.
     *
     * Workflow:
     * 1. Lấy user ID của giảng viên đang đăng nhập
     * 2. Count thông báo chưa đọc:
     *    - NguoiNhanThongBao where nguoi_nhan_id = current user
     *    - Where da_doc = false
     *    - WhereHas thongBao where trang_thai = 'cong_khai'
     *      (chỉ tính thông báo đã publish, không tính draft)
     * 3. Return JSON response với count
     *
     * JSON response format:
     * {
     *   "count": 5
     * }
     *
     * Use cases:
     * - AJAX polling mỗi 30 giây để update badge
     * - WebSocket/Pusher realtime updates
     * - Mobile app API
     * - Header notification dropdown
     * - Dashboard widget
     *
     * Frontend integration examples:
     * - jQuery AJAX:
     *   $.get('/giangvien/thong-bao/unread-count', function(data) {
     *     $('#notification-badge').text(data.count);
     *   });
     * - Axios:
     *   axios.get('/giangvien/thong-bao/unread-count')
     *     .then(res => updateBadge(res.data.count));
     * - Fetch API:
     *   fetch('/giangvien/thong-bao/unread-count')
     *     .then(res => res.json())
     *     .then(data => badge.textContent = data.count);
     *
     * Performance:
     * - Chỉ count, không select full data
     * - Fast query với index on (nguoi_nhan_id, da_doc)
     * - Cache-able response (1-2 minutes)
     * - Rate limit: Max 60 requests/minute
     *
     * Security:
     * - Chỉ trả về count của current user
     * - Không lộ thông tin thông báo của user khác
     * - Require authentication (middleware)
     *
     * @return \Illuminate\Http\JsonResponse JSON response:
     *   - count (int): Số lượng thông báo chưa đọc
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
