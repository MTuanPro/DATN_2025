<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VaiTroController;
use App\Http\Controllers\Admin\NhomQuyenController;
use App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Admin\VaiTroQuyenController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DaoTaoController;
use App\Http\Controllers\Admin\ThongBaoController;
use App\Http\Controllers\DaoTao\DashboardController as DaoTaoDashboardController;
use App\Http\Controllers\GiangVien\DashboardController as GiangVienDashboardController;
use App\Http\Controllers\GiangVien\ScheduleController;
use App\Http\Controllers\SinhVien\DashboardController as SinhVienDashboardController;
use App\Http\Controllers\DaoTao\CTDT\ChuongTrinhKhungController;
use App\Http\Controllers\DaoTao\CTDT\ChuyenNganhController;
use App\Http\Controllers\DaoTao\CTDT\KhoaController;
use App\Http\Controllers\DaoTao\CTDT\KhoaHocController;
use App\Http\Controllers\DaoTao\CTDT\NganhController;
use App\Http\Controllers\DaoTao\CTDT\MonHocController;
use App\Http\Controllers\DaoTao\CTDT\MonHocTienQuyetController;
use App\Http\Controllers\DaoTao\DanhMuc\PhongHocController;
use App\Http\Controllers\DaoTao\DanhMuc\TrangThaiHocTapController;
use App\Http\Controllers\DaoTao\DanhMuc\TrinhDoController;
use App\Http\Controllers\DaoTao\HocKyController;
use App\Http\Controllers\DaoTao\GiangVienController as DaoTaoGiangVienController;
use App\Http\Controllers\DaoTao\LopHocPhanController;
use App\Http\Controllers\DaoTao\PhanCongGiangDayController;
use App\Http\Controllers\DaoTao\CauHinhDauDiemController;
use App\Http\Controllers\DaoTao\LichHocCoDinhController;
use App\Http\Controllers\DaoTao\LichHocChiTietController;
use App\Http\Controllers\DaoTao\LichSuSuaDiemController;
use App\Http\Controllers\DaoTao\SinhVienController;
use App\Http\Controllers\DaoTao\XepLopController;
use App\Http\Controllers\SinhVien\DangKyMonHocController;
use App\Http\Controllers\SinhVien\ThoiKhoaBieuController;
use App\Http\Controllers\GiangVien\NhapDiemController;
use App\Http\Controllers\DaoTao\DuyetDiemController;
use App\Http\Controllers\SinhVien\XemDiemController;
use App\Http\Controllers\DaoTao\ThongBaoController as DaoTaoThongBaoController;
use App\Http\Controllers\GiangVien\ThongBaoController as GiangVienThongBaoController;
use App\Http\Controllers\SinhVien\ThongBaoController as SinhVienThongBaoController;
use App\Http\Controllers\Admin\AiChatbotKnowledgeBaseController;
use App\Http\Controllers\Admin\AiChatbotConversationController;
use App\Http\Controllers\Admin\AiChatbotFeedbackController;
use App\Http\Controllers\SinhVien\ChatbotController;



// Route trang chủ - redirect to dashboard nếu đã login, ngược lại về login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $roles = $user->vaiTro()->pluck('ma_vai_tro')->toArray();
        if (in_array('admin', $roles)) {
            return redirect()->route('admin.dashboard');
        }
        if (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)) {
            return redirect()->route('dao-tao.dashboard');
        }
        if (in_array('giang_vien', $roles)) {
            return redirect()->route('giangvien.dashboard');
        }
        if (in_array('sinh_vien', $roles)) {
            return redirect()->route('sinh-vien.dashboard');
        }
    }
    return redirect()->route('login');
});

// ========== Auth Routes (Không cần đăng nhập) ==========
Route::middleware(['guest', 'prevent.back'])->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot.password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetEmail'])->name('forgot.password.post');
});

// Reset Password - Không cần middleware guest (cho phép cả đã login và chưa login)
Route::get('/reset-password/{token}', [AdminUserController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [AdminUserController::class, 'processReset'])->name('password.reset.process');

// Logout (Cần đăng nhập) - Hỗ trợ cả GET và POST
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ========== Profile & Settings Routes (All roles) ==========
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
});

// ========== Email Verification Routes ==========
Route::get('/email/verify/{token}', [AdminUserController::class, 'showVerifyForm'])->name('verification.form');
Route::post('/email/verify', [AdminUserController::class, 'processVerify'])->name('verification.process');

// ========== Admin Routes ==========
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
    Route::get('/users/{user}/login-history', [AdminUserController::class, 'loginHistory'])->name('users.login-history');
    Route::post('/users/{user}/force-logout', [AdminUserController::class, 'forceLogout'])->name('users.force-logout');

    // Role Management (Member 2)
    Route::resource('vai-tro', VaiTroController::class);

    // Permission Group Management (Member 3)
    Route::resource('nhom-quyen', NhomQuyenController::class);

    // Permission Management (Member 3)
    Route::resource('quyen', QuyenController::class);

    // Map Vai trò - Quyền (Member 4)
    Route::get('/vai-tro-quyen', [VaiTroQuyenController::class, 'index'])->name('vai-tro-quyen.index');
    Route::put('/vai-tro-quyen/update-matrix', [VaiTroQuyenController::class, 'updateMatrix'])->name('vai-tro-quyen.update-matrix');
    Route::put('/vai-tro-quyen/{vaiTro}', [VaiTroQuyenController::class, 'update'])->name('vai-tro-quyen.update');
    Route::post('/vai-tro-quyen/{vaiTro}/attach/{quyen}', [VaiTroQuyenController::class, 'attachPermission'])->name('vai-tro-quyen.attach');
    Route::delete('/vai-tro-quyen/{vaiTro}/detach/{quyen}', [VaiTroQuyenController::class, 'detachPermission'])->name('vai-tro-quyen.detach');

    // Thong Bao Management
    Route::get('thong-bao/{thongBao}/download', [ThongBaoController::class, 'download'])->name('thong-bao.download');
    Route::resource('thong-bao', ThongBaoController::class);
    
    // Nguoi Nhan Thong Bao Management
    Route::prefix('nguoi-nhan-thong-bao')->name('nguoi-nhan-thong-bao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'show'])->name('show');
        Route::post('/mark-as-read', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/resend-email', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'resendEmail'])->name('resend-email');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\NguoiNhanThongBaoController::class, 'bulkDelete'])->name('bulk-delete');
    });
    
    // Mau Thong Bao Tu Dong Management
    Route::resource('mau-thong-bao', \App\Http\Controllers\Admin\MauThongBaoTuDongController::class);
    Route::patch('mau-thong-bao/{mauThongBao}/toggle', [\App\Http\Controllers\Admin\MauThongBaoTuDongController::class, 'toggleActivation'])->name('mau-thong-bao.toggle');

    // Reports & Statistics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
        Route::get('/permissions', [App\Http\Controllers\Admin\ReportController::class, 'permissions'])->name('permissions');
        Route::get('/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // PHASE 12: AI Chatbot Management
    // Backwards-compatible aliases for older route names introduced during refactors.
    // These are lightweight redirects that preserve old named routes (so compiled views
    // or external links that still reference them won't throw RouteNotFound exceptions).
    Route::get('ai-chatbot/compat/chatbot-conversation-create', function () {
        return redirect()->route('admin.ai-chatbot.conversation.index');
    })->name('chatbot.conversation.create'); // yields admin.chatbot.conversation.create

    Route::get('ai-chatbot/compat/chatbot-feedback', function () {
        return redirect()->route('admin.ai-chatbot.feedback.index');
    })->name('ai-chatbot.chatbot.feedback'); // yields admin.ai-chatbot.chatbot.feedback

    Route::prefix('ai-chatbot')->name('ai-chatbot.')->group(function () {
        // Knowledge Base
        Route::prefix('knowledge-base')->name('knowledge-base.')->group(function () {
            Route::get('/', [AiChatbotKnowledgeBaseController::class, 'index'])->name('index');
            Route::get('/create', [AiChatbotKnowledgeBaseController::class, 'create'])->name('create');
            Route::get('/statistics/overview', [AiChatbotKnowledgeBaseController::class, 'statistics'])->name('statistics');
            Route::get('/import/form', [AiChatbotKnowledgeBaseController::class, 'importForm'])->name('import.form');
            Route::post('/import', [AiChatbotKnowledgeBaseController::class, 'import'])->name('import');
            Route::get('/export', [AiChatbotKnowledgeBaseController::class, 'export'])->name('export');
            Route::post('/', [AiChatbotKnowledgeBaseController::class, 'store'])->name('store');
            Route::get('/{knowledgeBase}', [AiChatbotKnowledgeBaseController::class, 'show'])->name('show');
            Route::get('/{knowledgeBase}/edit', [AiChatbotKnowledgeBaseController::class, 'edit'])->name('edit');
            Route::put('/{knowledgeBase}', [AiChatbotKnowledgeBaseController::class, 'update'])->name('update');
            Route::delete('/{knowledgeBase}', [AiChatbotKnowledgeBaseController::class, 'destroy'])->name('destroy');
            Route::post('/{knowledgeBase}/toggle-activate', [AiChatbotKnowledgeBaseController::class, 'toggleActivate'])->name('toggle-activate');
        });

        // Conversations
        Route::prefix('conversation')->name('conversation.')->group(function () {
            Route::get('/', [AiChatbotConversationController::class, 'index'])->name('index');
            Route::get('/{conversation}', [AiChatbotConversationController::class, 'show'])->name('show');
            Route::post('/{conversation}/close', [AiChatbotConversationController::class, 'close'])->name('close');
            Route::post('/{conversation}/reopen', [AiChatbotConversationController::class, 'reopen'])->name('reopen');
            Route::delete('/{conversation}', [AiChatbotConversationController::class, 'destroy'])->name('destroy');
        });

        // Feedback
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [AiChatbotFeedbackController::class, 'index'])->name('index');
            Route::get('/analytics', [AiChatbotFeedbackController::class, 'analytics'])->name('analytics');
            Route::get('/{feedback}', [AiChatbotFeedbackController::class, 'show'])->name('show');
            Route::delete('/{feedback}', [AiChatbotFeedbackController::class, 'destroy'])->name('destroy');
        });
    });
});

// ========== Đào tạo Routes (Trưởng phòng & Nhân viên) ==========
Route::middleware(['auth', 'role:truong_phong_dt,nhan_vien_dt'])->prefix('dao-tao')->name('dao-tao.')->group(function () {
    Route::get('/dashboard', [DaoTaoDashboardController::class, 'index'])->name('dashboard');

    // Quản lý Ca học
    Route::resource('ca-hoc', \App\Http\Controllers\DaoTao\CaHocController::class);
    Route::post('ca-hoc/{caHoc}/toggle-status', [\App\Http\Controllers\DaoTao\CaHocController::class, 'toggleStatus'])->name('ca-hoc.toggle-status');

    // PHASE 1: Danh mục
    Route::resource('khoa', KhoaController::class);
    Route::resource('nganh', NganhController::class);
    Route::resource('chuyen-nganh', ChuyenNganhController::class);
    Route::resource('khoa-hoc', KhoaHocController::class);
    Route::resource('trinh-do', TrinhDoController::class);
    Route::resource('trang-thai-hoc-tap', TrangThaiHocTapController::class);
    Route::resource('phong-hoc', PhongHocController::class);

    // Môn học và môn tiên quyết
    Route::resource('mon-hoc', MonHocController::class);
    Route::get('mon-hoc/{monHoc}/tien-quyet', [MonHocController::class, 'tienQuyet'])->name('mon-hoc.tien-quyet');
    Route::post('mon-hoc/{monHoc}/tien-quyet', [MonHocController::class, 'storeTienQuyet'])->name('mon-hoc.tien-quyet.store');
    Route::put('mon-hoc/{monHoc}/tien-quyet/{tienQuyet}', [MonHocTienQuyetController::class, 'update'])->name('mon-hoc.tien-quyet.update');
    Route::delete('mon-hoc/{monHoc}/tien-quyet/{tienQuyet}', [MonHocController::class, 'destroyTienQuyet'])->name('mon-hoc.tien-quyet.destroy');

    Route::resource('monhoctienquyet', MonHocTienQuyetController::class);
    Route::resource('chuong-trinh-khung', ChuongTrinhKhungController::class);
    Route::get('chuong-trinh-khung/thong-ke/{chuyenNganhId}', [ChuongTrinhKhungController::class, 'thongKe'])->name('chuong-trinh-khung.thong-ke');

    // PHASE 2: Học kỳ và Giảng viên
    Route::resource('hoc-ky', HocKyController::class);
    Route::post('hoc-ky/{hocKy}/set-hien-tai', [HocKyController::class, 'setHienTai'])
        ->name('hoc-ky.set-hien-tai');
    Route::post('hoc-ky/{hocKy}/mo-dang-ky', [HocKyController::class, 'moDangKy'])
        ->name('hoc-ky.mo-dang-ky');

    Route::resource('giang-vien', DaoTaoGiangVienController::class);
    Route::delete('giang-vien-destroy-multiple', [DaoTaoGiangVienController::class, 'destroyMultiple'])->name('giang-vien.destroy-multiple');
    Route::get('giang-vien-import', [DaoTaoGiangVienController::class, 'showImportForm'])
        ->name('giang-vien.show-import-form');
    Route::post('giang-vien-import', [DaoTaoGiangVienController::class, 'import'])
        ->name('giang-vien.import');
    Route::get('giang-vien-template', [DaoTaoGiangVienController::class, 'downloadTemplate'])
        ->name('giang-vien.download-template');

    // PHASE 3: Sinh viên
    Route::resource('sinh-vien', SinhVienController::class);
    Route::delete('sinh-vien-destroy-multiple', [SinhVienController::class, 'destroyMultiple'])->name('sinh-vien.destroy-multiple');
    Route::get('sinh-vien-import', [SinhVienController::class, 'showImportForm'])->name('sinh-vien.show-import-form');
    Route::post('sinh-vien-import', [SinhVienController::class, 'import'])->name('sinh-vien.import');
    Route::get('sinh-vien-template', [SinhVienController::class, 'downloadTemplate'])->name('sinh-vien.download-template');

    // PHASE 4: Lớp học phần & Phân công
    Route::resource('lop-hoc-phan', LopHocPhanController::class);
    Route::delete('lop-hoc-phan-destroy-multiple', [LopHocPhanController::class, 'destroyMultiple'])->name('lop-hoc-phan.destroy-multiple');
    Route::post('lop-hoc-phan/sync-so-luong', [LopHocPhanController::class, 'syncSoLuongDangKy'])->name('lop-hoc-phan.sync-so-luong');
    Route::get('lop-hoc-phan-import', [LopHocPhanController::class, 'showImportForm'])->name('lop-hoc-phan.show-import-form');
    Route::post('lop-hoc-phan-import', [LopHocPhanController::class, 'import'])->name('lop-hoc-phan.import');
    Route::get('lop-hoc-phan-template', [LopHocPhanController::class, 'downloadTemplate'])->name('lop-hoc-phan.download-template');

    // Phân công giảng dạy
    Route::get('lop-hoc-phan/{lopHocPhan}/phan-cong', [PhanCongGiangDayController::class, 'index'])->name('lop-hoc-phan.phan-cong');
    Route::post('lop-hoc-phan/{lopHocPhan}/phan-cong', [PhanCongGiangDayController::class, 'store'])->name('lop-hoc-phan.phan-cong.store');
    Route::put('phan-cong/{phanCong}', [PhanCongGiangDayController::class, 'update'])->name('phan-cong.update');
    Route::delete('phan-cong/{phanCong}', [PhanCongGiangDayController::class, 'destroy'])->name('phan-cong.destroy');

    // Cấu hình đầu điểm mặc định cho môn học
    Route::get('mon-hoc/{monHoc}/cau-hinh-diem', [MonHocController::class, 'cauHinhDiem'])->name('mon-hoc.cau-hinh-diem');
    Route::post('mon-hoc/{monHoc}/cau-hinh-diem', [MonHocController::class, 'storeCauHinhDiem'])->name('mon-hoc.cau-hinh-diem.store');
    Route::put('mon-hoc/{monHoc}/cau-hinh-diem/{cauHinhMacDinh}', [MonHocController::class, 'updateCauHinhDiem'])->name('mon-hoc.cau-hinh-diem.update');
    Route::delete('mon-hoc/{monHoc}/cau-hinh-diem/{cauHinhMacDinh}', [MonHocController::class, 'destroyCauHinhDiem'])->name('mon-hoc.cau-hinh-diem.destroy');
    
    // Cấu hình đầu điểm cho lớp học phần (từ môn học)
    Route::get('lop-hoc-phan/{lopHocPhan}/cau-hinh-diem', [CauHinhDauDiemController::class, 'index'])->name('lop-hoc-phan.cau-hinh-diem');
    Route::post('lop-hoc-phan/{lopHocPhan}/cau-hinh-diem', [CauHinhDauDiemController::class, 'store'])->name('lop-hoc-phan.cau-hinh-diem.store');
    Route::put('cau-hinh-diem/{cauHinhDiem}', [CauHinhDauDiemController::class, 'update'])->name('cau-hinh-diem.update');
    Route::delete('cau-hinh-diem/{cauHinhDiem}', [CauHinhDauDiemController::class, 'destroy'])->name('cau-hinh-diem.destroy');
    Route::get('lop-hoc-phan/{lopHocPhan}/ty-le-con-lai', [CauHinhDauDiemController::class, 'getTyLeConLai'])->name('lop-hoc-phan.ty-le-con-lai');

    // Lịch học cố định
    Route::get('lop-hoc-phan/{lopHocPhan}/lich-co-dinh', [LichHocCoDinhController::class, 'index'])->name('lop-hoc-phan.lich-co-dinh');
    Route::get('lop-hoc-phan/{lopHocPhan}/lich-co-dinh/create', [LichHocCoDinhController::class, 'create'])->name('lop-hoc-phan.lich-co-dinh.create');
    Route::post('lop-hoc-phan/{lopHocPhan}/lich-co-dinh', [LichHocCoDinhController::class, 'store'])->name('lop-hoc-phan.lich-co-dinh.store');
    Route::get('lich-co-dinh/{lichCoDinh}/edit', [LichHocCoDinhController::class, 'edit'])->name('lich-co-dinh.edit');
    Route::put('lich-co-dinh/{lichCoDinh}', [LichHocCoDinhController::class, 'update'])->name('lich-co-dinh.update');
    Route::delete('lich-co-dinh/{lichCoDinh}', [LichHocCoDinhController::class, 'destroy'])->name('lich-co-dinh.destroy');
    Route::post('lich-co-dinh/check-phong-conflict', [LichHocCoDinhController::class, 'checkPhongConflict'])->name('lich-co-dinh.check-phong-conflict');
    Route::post('lich-co-dinh/check-giang-vien-conflict', [LichHocCoDinhController::class, 'checkGiangVienConflict'])->name('lich-co-dinh.check-giang-vien-conflict');

    // Lịch học chi tiết
    Route::get('lop-hoc-phan/{lopHocPhan}/lich-chi-tiet', [LichHocChiTietController::class, 'index'])->name('lop-hoc-phan.lich-chi-tiet');
    
    // Thời khóa biểu
    Route::prefix('thoi-khoa-bieu')->name('thoi-khoa-bieu.')->group(function () {
        Route::get('/lich-theo-phong', [\App\Http\Controllers\DaoTao\ThoiKhoaBieuController::class, 'lichTheoPhong'])->name('lich-theo-phong');
        Route::get('/lich-theo-giang-vien', [\App\Http\Controllers\DaoTao\ThoiKhoaBieuController::class, 'lichTheoGiangVien'])->name('lich-theo-giang-vien');
    });
    Route::post('lop-hoc-phan/{lopHocPhan}/lich-chi-tiet/generate', [LichHocChiTietController::class, 'generate'])->name('lop-hoc-phan.lich-chi-tiet.generate');
    Route::get('lop-hoc-phan/{lopHocPhan}/lich-chi-tiet/create', [LichHocChiTietController::class, 'create'])->name('lop-hoc-phan.lich-chi-tiet.create');
    Route::post('lop-hoc-phan/{lopHocPhan}/lich-chi-tiet', [LichHocChiTietController::class, 'store'])->name('lop-hoc-phan.lich-chi-tiet.store');
    Route::get('lich-chi-tiet/{lichChiTiet}/edit', [LichHocChiTietController::class, 'edit'])->name('lich-chi-tiet.edit');
    Route::put('lich-chi-tiet/{lichChiTiet}', [LichHocChiTietController::class, 'update'])->name('lich-chi-tiet.update');
    Route::post('lich-chi-tiet/{lichChiTiet}/cancel', [LichHocChiTietController::class, 'cancel'])->name('lich-chi-tiet.cancel');
    Route::delete('lich-chi-tiet/{lichChiTiet}', [LichHocChiTietController::class, 'destroy'])->name('lich-chi-tiet.destroy');

    // PHASE 5: Xếp lớp tự động
    Route::prefix('xep-lop')->name('xep-lop.')->group(function () {
        Route::get('/', [XepLopController::class, 'index'])->name('index');
        Route::post('/auto-assign', [XepLopController::class, 'autoAssign'])->name('auto-assign');
        Route::post('/manual-assign', [XepLopController::class, 'manualAssign'])->name('manual-assign');
        Route::get('/waiting-list', [XepLopController::class, 'waitingList'])->name('waiting-list');
        Route::get('/danh-sach-lop/{lopHocPhan}', [XepLopController::class, 'danhSachLop'])->name('danh-sach-lop');
        Route::get('/lop-hoc-phan-by-mon/{monHoc}', [XepLopController::class, 'getLopHocPhanByMonHoc'])->name('lop-hoc-phan-by-mon');
        Route::delete('/xoa-khoi-lop/{lhpsv}', [XepLopController::class, 'xoaKhoiLop'])->name('xoa-khoi-lop');
    });
    // PHASE 7: Duyệt điểm
    Route::prefix('duyet-diem')->name('duyet-diem.')->group(function () {
        Route::get('/', [DuyetDiemController::class, 'index'])->name('index');
        Route::get('/quan-ly-gui-diem', [DuyetDiemController::class, 'quanLyGuiDiem'])->name('quan-ly-gui-diem');
        Route::post('/{lopHocPhan}/cap-nhat-trang-thai-gui-diem', [DuyetDiemController::class, 'capNhatTrangThaiGuiDiem'])->name('cap-nhat-trang-thai-gui-diem');
        Route::post('/cap-nhat-trang-thai-gui-diem-hang-loat', [DuyetDiemController::class, 'capNhatTrangThaiGuiDiemHangLoat'])->name('cap-nhat-trang-thai-gui-diem-hang-loat');
        Route::post('/{lopHocPhan}/cho-phep-sua-diem-sau-duyet', [DuyetDiemController::class, 'choPhepSuaDiemSauDuyet'])->name('cho-phep-sua-diem-sau-duyet');
        Route::get('/{lopHocPhan}', [DuyetDiemController::class, 'show'])->name('show');
        Route::post('/{lopHocPhan}/duyet', [DuyetDiemController::class, 'duyetDiem'])->name('duyet');
        Route::post('/{lopHocPhan}/sua-diem', [DuyetDiemController::class, 'suaDiem'])->name('sua-diem');
        Route::post('/{lopHocPhan}/luu-tat-ca-diem', [DuyetDiemController::class, 'luuTatCaDiem'])->name('luu-tat-ca-diem');
    });


    // PHASE 7.5: Quản lý Lịch thi
    Route::get('lich-thi', [\App\Http\Controllers\DaoTao\LichThiController::class, 'index'])->name('lich-thi.index');
    Route::get('lich-thi/create', [\App\Http\Controllers\DaoTao\LichThiController::class, 'create'])->name('lich-thi.create');
    Route::post('lich-thi', [\App\Http\Controllers\DaoTao\LichThiController::class, 'store'])->name('lich-thi.store');
    Route::get('lich-thi/{lichThi}', [\App\Http\Controllers\DaoTao\LichThiController::class, 'show'])->name('lich-thi.show');
    Route::get('lich-thi/{lichThi}/edit', [\App\Http\Controllers\DaoTao\LichThiController::class, 'edit'])->name('lich-thi.edit');
    Route::put('lich-thi/{lichThi}', [\App\Http\Controllers\DaoTao\LichThiController::class, 'update'])->name('lich-thi.update');
    Route::delete('lich-thi/{lichThi}', [\App\Http\Controllers\DaoTao\LichThiController::class, 'destroy'])->name('lich-thi.destroy');
    Route::get('lich-thi-import', [\App\Http\Controllers\DaoTao\LichThiController::class, 'showImportForm'])->name('lich-thi.show-import-form');
    Route::post('lich-thi-import', [\App\Http\Controllers\DaoTao\LichThiController::class, 'import'])->name('lich-thi.import');
    Route::get('lich-thi-template', [\App\Http\Controllers\DaoTao\LichThiController::class, 'downloadTemplate'])->name('lich-thi.download-template');
    Route::get('lich-thi/{lichThi}/phan-phong', [\App\Http\Controllers\DaoTao\LichThiController::class, 'phanPhong'])->name('lich-thi.phan-phong');
    Route::post('lich-thi/{lichThi}/cap-nhat-phong', [\App\Http\Controllers\DaoTao\LichThiController::class, 'capNhatPhong'])->name('lich-thi.cap-nhat-phong');
    Route::get('lich-thi/{lichThi}/danh-sach-sinh-vien', [\App\Http\Controllers\DaoTao\LichThiController::class, 'danhSachSinhVien'])->name('lich-thi.danh-sach-sinh-vien');
    Route::post('lich-thi/{lichThi}/gui-thong-bao', [\App\Http\Controllers\DaoTao\LichThiController::class, 'guiThongBao'])->name('lich-thi.gui-thong-bao');
    Route::get('lich-thi-export', [\App\Http\Controllers\DaoTao\LichThiController::class, 'export'])->name('lich-thi.export');
    Route::get('lich-thi/{lichThi}/download-de-thi', [\App\Http\Controllers\DaoTao\LichThiController::class, 'downloadDeThi'])->name('lich-thi.download-de-thi');
    Route::get('lich-thi/{lichThi}/download-dap-an', [\App\Http\Controllers\DaoTao\LichThiController::class, 'downloadDapAn'])->name('lich-thi.download-dap-an');

    // PHASE 8.5: Cảnh báo Học vụ
    Route::prefix('canh-bao-hoc-vu')->name('canh-bao-hoc-vu.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'store'])->name('store');
        Route::get('/{canhBaoHocVu}', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'show'])->name('show');
        Route::get('/{canhBaoHocVu}/edit', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'edit'])->name('edit');
        Route::put('/{canhBaoHocVu}', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'update'])->name('update');
        Route::delete('/{canhBaoHocVu}', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'destroy'])->name('destroy');
        Route::post('/tu-dong-phat-hien', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'tuDongPhatHien'])->name('tu-dong-phat-hien');
        Route::get('/export', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'export'])->name('export');
        Route::post('/{canhBaoHocVu}/xu-ly', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'xuLy'])->name('xu-ly');
        Route::post('/{canhBaoHocVu}/gui-email', [\App\Http\Controllers\DaoTao\CanhBaoHocVuController::class, 'guiEmailCanhBao'])->name('gui-email');
    });

    // PHASE 10: Quản lý Thông báo (CRUD full)
    Route::resource('thong-bao', DaoTaoThongBaoController::class);

    // PHASE 10: Mẫu thông báo tự động
    Route::resource('mau-thong-bao', \App\Http\Controllers\DaoTao\MauThongBaoTuDongController::class);
    Route::patch('mau-thong-bao/{mauThongBao}/toggle', [\App\Http\Controllers\DaoTao\MauThongBaoTuDongController::class, 'toggleActivation'])->name('mau-thong-bao.toggle');

    // PHASE 8: Quản lý Học phí
    // Cấu hình học phí
    Route::prefix('hoc-phi')->name('hoc-phi.')->group(function () {
        Route::resource('cau-hinh', \App\Http\Controllers\DaoTao\CauHinhHocPhiController::class);
        Route::get('cau-hinh/current', [\App\Http\Controllers\DaoTao\CauHinhHocPhiController::class, 'getCurrent'])->name('cau-hinh.current');

        // Quản lý học phí
        Route::get('/', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'statistics'])->name('statistics');
        Route::get('/overdue', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'overdue'])->name('overdue');
        Route::get('/export', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'update'])->name('update');
        Route::get('/{id}/payment', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'payment'])->name('payment');
        Route::post('/{id}/payment', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'storePayment'])->name('storePayment');
        Route::get('/bien-lai/{lichSuId}', [\App\Http\Controllers\DaoTao\HocPhiController::class, 'viewBienLai'])->name('bien-lai');
    });

    // Lịch sử sửa điểm
    Route::prefix('lich-su-sua-diem')->name('lich-su-sua-diem.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DaoTao\LichSuSuaDiemController::class, 'index'])->name('index');
        Route::get('/{lopHocPhanId}', [\App\Http\Controllers\DaoTao\LichSuSuaDiemController::class, 'show'])->name('show');
    });

    // Báo cáo đào tạo
    Route::prefix('bao-cao')->name('bao-cao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'index'])->name('index');
        Route::get('/sinh-vien', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'sinhVien'])->name('sinh-vien');
        Route::get('/ket-qua', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'ketQua'])->name('ket-qua');
        Route::get('/diem-danh', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'diemDanh'])->name('diem-danh');
        Route::get('/hoc-phi', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'hocPhi'])->name('hoc-phi');
        Route::get('/dang-ky', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'dangKy'])->name('dang-ky');
        Route::get('/xep-lop', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'xepLop'])->name('xep-lop');
        Route::get('/tai-giang-vien', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'taiGiangVien'])->name('tai-giang-vien');
        Route::get('/phong-hoc', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'phongHoc'])->name('phong-hoc');
        Route::get('/canh-bao', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'canhBao'])->name('canh-bao');
        
        // Export routes
        Route::get('/export-excel', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [\App\Http\Controllers\DaoTao\BaoCaoController::class, 'exportPdf'])->name('export-pdf');
    });
});

// ========== Giảng viên Routes ==========
Route::middleware(['auth', 'role:giang_vien'])->prefix('giang-vien')->name('giangvien.')->group(function () {
    Route::get('/dashboard', [GiangVienDashboardController::class, 'index'])->name('dashboard');

    // Lớp giảng dạy
    Route::get('/lop-giang-day', [App\Http\Controllers\GiangVien\TeachingClassController::class, 'index'])->name('lop-giang-day.index');
    Route::get('/lop-giang-day/{id}', [App\Http\Controllers\GiangVien\TeachingClassController::class, 'show'])->name('lop-giang-day.show');
    Route::get('/lop-giang-day/{lopHocPhanId}/sinh-vien/{sinhVienId}', [App\Http\Controllers\GiangVien\TeachingClassController::class, 'showStudent'])->name('lop-giang-day.show-student');
    Route::get('/lop-giang-day/{id}/export-students', [App\Http\Controllers\GiangVien\TeachingClassController::class, 'exportStudents'])->name('lop-giang-day.export-students');
    Route::get('/lop-giang-day/{id}/export-students-pdf', [App\Http\Controllers\GiangVien\TeachingClassController::class, 'exportStudentsPdf'])->name('lop-giang-day.export-students-pdf');

    // Quản lý buổi học
    Route::get('/buoi-hoc', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'index'])->name('buoi-hoc.index');
    Route::get('/buoi-hoc/lich-su', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'history'])->name('buoi-hoc.history');
    Route::get('/buoi-hoc/{id}/edit', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'edit'])->name('buoi-hoc.edit');
    Route::put('/buoi-hoc/{id}', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'update'])->name('buoi-hoc.update');
    Route::delete('/buoi-hoc/{id}/tai-lieu', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'deleteTaiLieu'])->name('buoi-hoc.delete-tai-lieu');
    Route::get('/buoi-hoc/{id}/tai-lieu', [App\Http\Controllers\GiangVien\TeachingSessionController::class, 'downloadTaiLieu'])->name('buoi-hoc.download-tai-lieu');

    // Điểm danh
    Route::get('/diem-danh', [App\Http\Controllers\GiangVien\AttendanceController::class, 'index'])->name('diem-danh.index');
    Route::get('/diem-danh/bao-cao', [App\Http\Controllers\GiangVien\AttendanceController::class, 'report'])->name('diem-danh.report');
    Route::get('/diem-danh/bao-cao/export-excel', [App\Http\Controllers\GiangVien\AttendanceController::class, 'exportExcel'])->name('diem-danh.export-excel');
    Route::get('/diem-danh/bao-cao/export-pdf', [App\Http\Controllers\GiangVien\AttendanceController::class, 'exportPdf'])->name('diem-danh.export-pdf');
    Route::post('/diem-danh/canh-bao', [App\Http\Controllers\GiangVien\AttendanceController::class, 'checkAndSendWarnings'])->name('diem-danh.canh-bao');
    Route::get('/diem-danh/{id}', [App\Http\Controllers\GiangVien\AttendanceController::class, 'show'])->name('diem-danh.show');
    Route::post('/diem-danh/{id}', [App\Http\Controllers\GiangVien\AttendanceController::class, 'store'])->name('diem-danh.store');

    // Yêu cầu điểm danh bù
    Route::get('/yeu-cau-diem-danh-bu', [App\Http\Controllers\GiangVien\YeuCauDiemDanhBuController::class, 'index'])->name('yeu-cau-diem-danh-bu.index');
    Route::post('/yeu-cau-diem-danh-bu/{id}/duyet', [App\Http\Controllers\GiangVien\YeuCauDiemDanhBuController::class, 'duyet'])->name('yeu-cau-diem-danh-bu.duyet');
    Route::post('/yeu-cau-diem-danh-bu/{id}/tu-choi', [App\Http\Controllers\GiangVien\YeuCauDiemDanhBuController::class, 'tuChoi'])->name('yeu-cau-diem-danh-bu.tu-choi');


    // PHASE 7.5: Lịch thi
    Route::get('lich-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'index'])->name('lich-thi.index');
    Route::get('lich-thi/lich-coi-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'lichCoiThi'])->name('lich-thi.lich-coi-thi');
    Route::get('lich-thi/{lichThi}', [\App\Http\Controllers\GiangVien\LichThiController::class, 'show'])->name('lich-thi.show');
    Route::post('lich-thi/{lichThi}/upload-de-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'uploadDeThi'])->name('lich-thi.upload-de-thi');
    Route::post('lich-thi/{lichThi}/upload-dap-an', [\App\Http\Controllers\GiangVien\LichThiController::class, 'uploadDapAn'])->name('lich-thi.upload-dap-an');
    Route::post('lich-thi/{lichThi}/xac-nhan-coi-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'xacNhanCoiThi'])->name('lich-thi.xac-nhan-coi-thi');
    Route::get('lich-thi/{lichThi}/download-de-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'downloadDeThi'])->name('lich-thi.download-de-thi');
    Route::get('lich-thi/{lichThi}/download-dap-an', [\App\Http\Controllers\GiangVien\LichThiController::class, 'downloadDapAn'])->name('lich-thi.download-dap-an');
    Route::get('lich-thi/{lichThi}/xuat-danh-sach-di-thi', [\App\Http\Controllers\GiangVien\LichThiController::class, 'xuatDanhSachSinhVienDiThi'])->name('lich-thi.xuat-danh-sach-di-thi');
    
    // Xuất danh sách thi
    Route::prefix('xuat-danh-sach-thi')->name('xuat-danh-sach-thi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GiangVien\LichThiController::class, 'indexXuatDanhSachThi'])->name('index');
    });

    // Lịch dạy cá nhân
    Route::get('/lich-day', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/lich-day/export', [ScheduleController::class, 'export'])->name('schedule.export');
    
    // PHASE 8.5: Cảnh báo học vụ
    Route::get('/canh-bao-hoc-vu', [\App\Http\Controllers\GiangVien\CanhBaoHocVuController::class, 'index'])->name('canh-bao-hoc-vu.index');
    Route::get('/canh-bao-hoc-vu/{id}', [\App\Http\Controllers\GiangVien\CanhBaoHocVuController::class, 'show'])->name('canh-bao-hoc-vu.show');
    
    
    // Nhập điểm
    Route::prefix('nhap-diem')->name('nhap-diem.')->group(function () {
        Route::post('/{lopHocPhan}/gui-dao-tao', [NhapDiemController::class, 'guiDiemChoDaoTao'])->name('gui-dao-tao');
        Route::get('/', [NhapDiemController::class, 'index'])->name('index');
        Route::get('/{lopHocPhan}', [NhapDiemController::class, 'show'])->name('show');
        Route::post('/store', [NhapDiemController::class, 'nhapDiem'])->name('store');
        Route::post('/get-diem-tk', [NhapDiemController::class, 'getDiemTK'])->name('get-diem-tk');
        Route::post('/{lopHocPhan}/khoa', [NhapDiemController::class, 'khoaDiem'])->name('khoa');
        Route::post('/{lopHocPhan}/mo-khoa', [NhapDiemController::class, 'moKhoaDiem'])->name('mo-khoa');
        Route::get('/{lopHocPhan}/download-template', [NhapDiemController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/{lopHocPhan}/import-excel', [NhapDiemController::class, 'importExcel'])->name('import-excel');
    });

    // PHASE 8: Xem kết quả học tập
    Route::prefix('ket-qua-hoc-tap')->name('ket-qua-hoc-tap.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GiangVien\KetQuaHocTapController::class, 'index'])->name('index');
        Route::get('/{lopHocPhan}', [\App\Http\Controllers\GiangVien\KetQuaHocTapController::class, 'show'])->name('show');
        Route::get('/{lopHocPhan}/phan-tich', [\App\Http\Controllers\GiangVien\KetQuaHocTapController::class, 'phanTich'])->name('phan-tich');
        Route::get('/{lopHocPhan}/export-excel', [\App\Http\Controllers\GiangVien\KetQuaHocTapController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{lopHocPhan}/export-pdf', [\App\Http\Controllers\GiangVien\KetQuaHocTapController::class, 'exportPdf'])->name('export-pdf');
    });

    // PHASE 10: Thông báo (chỉ xem)
    Route::prefix('thong-bao')->name('thong-bao.')->group(function () {
        Route::get('/', [GiangVienThongBaoController::class, 'index'])->name('index');
        Route::get('/{thongBao}', [GiangVienThongBaoController::class, 'show'])->name('show');
        Route::post('/{thongBao}/mark-read', [GiangVienThongBaoController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [GiangVienThongBaoController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread/count', [GiangVienThongBaoController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Báo cáo giảng dạy cá nhân
    Route::prefix('bao-cao')->name('bao-cao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'index'])->name('index');
        Route::get('/tien-do', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'tienDoGiangDay'])->name('tien-do');
        Route::get('/diem-danh', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'diemDanh'])->name('diem-danh');
        Route::get('/phan-tich-diem', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'phanTichDiem'])->name('phan-tich-diem');
        Route::get('/export-excel', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [\App\Http\Controllers\GiangVien\BaoCaoController::class, 'exportPdf'])->name('export-pdf');
    });
});

// ========== Sinh viên Routes ==========
Route::middleware(['auth', 'role:sinh_vien'])->prefix('sinh-vien')->name('sinh-vien.')->group(function () {
    Route::get('/dashboard', [SinhVienDashboardController::class, 'index'])->name('dashboard');

    // PHASE 5: Đăng ký môn học
    Route::middleware('sinhvien.check')->prefix('dang-ky-mon-hoc')->name('dang-ky-mon-hoc.')->group(function () {
        Route::get('/', [DangKyMonHocController::class, 'index'])->name('index');
        Route::get('/create', [DangKyMonHocController::class, 'create'])->name('create');
        Route::post('/', [DangKyMonHocController::class, 'store'])->name('store');
        Route::delete('/{dangKy}', [DangKyMonHocController::class, 'destroy'])->name('destroy');
        Route::get('/my-registrations', [DangKyMonHocController::class, 'myRegistrations'])->name('my-registrations');
    });

    // PHASE 5: Lớp học phần
    Route::middleware('sinhvien.check')->prefix('lop-hoc-phan')->name('lop-hoc-phan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\LopHocPhanController::class, 'index'])->name('index');
        Route::get('/{id}/lich-su-diem-danh', [\App\Http\Controllers\SinhVien\LopHocPhanController::class, 'lichSuDiemDanh'])->name('lich-su-diem-danh');
        Route::get('/{id}', [\App\Http\Controllers\SinhVien\LopHocPhanController::class, 'show'])->name('show');
    });

    // PHASE 5: Thời khóa biểu cá nhân
    Route::middleware('sinhvien.check')->prefix('thoi-khoa-bieu')->name('thoi-khoa-bieu.')->group(function () {
        Route::get('/', [ThoiKhoaBieuController::class, 'index'])->name('index');
        Route::get('/lich-hoc', [ThoiKhoaBieuController::class, 'lichHoc'])->name('lich-hoc');
        Route::get('/export-pdf', [ThoiKhoaBieuController::class, 'exportPDF'])->name('export-pdf');
    });

    // PHASE 6: Lịch sử điểm danh
    Route::middleware('sinhvien.check')->prefix('diem-danh')->name('diem-danh.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\LopHocPhanController::class, 'tongHopDiemDanh'])->name('index');
        Route::post('/yeu-cau-diem-danh-bu', [\App\Http\Controllers\SinhVien\YeuCauDiemDanhBuController::class, 'store'])->name('yeu-cau-diem-danh-bu.store');
    });

    // PHASE 7: Xem điểm
    Route::middleware('sinhvien.check')->prefix('diem')->name('diem.')->group(function () {
        Route::get('/', [XemDiemController::class, 'index'])->name('index');
        Route::get('/{lopHocPhan}', [XemDiemController::class, 'show'])->name('show');
        Route::get('/bang-diem/tong-hop', [XemDiemController::class, 'bangDiem'])->name('bang-diem');
        Route::get('/bang-diem/export-pdf', [XemDiemController::class, 'exportPDF'])->name('export-pdf');
    });

    // PHASE 7.5: Lịch thi cá nhân
    Route::middleware('sinhvien.check')->prefix('lich-thi')->name('lich-thi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\LichThiController::class, 'index'])->name('index');
        Route::get('/calendar', [\App\Http\Controllers\SinhVien\LichThiController::class, 'calendar'])->name('calendar');
        Route::get('/export-pdf', [\App\Http\Controllers\SinhVien\LichThiController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{lichThi}', [\App\Http\Controllers\SinhVien\LichThiController::class, 'show'])->name('show');
    });

    // PHASE 2: Chương trình đào tạo
    Route::middleware('sinhvien.check')->prefix('chuong-trinh-dao-tao')->name('chuong-trinh-dao-tao.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\ChuongTrinhDaoTaoController::class, 'index'])->name('index');
        Route::get('/dieu-kien-tot-nghiep', [\App\Http\Controllers\SinhVien\ChuongTrinhDaoTaoController::class, 'dieuKienTotNghiep'])->name('dieu-kien-tot-nghiep');
        Route::get('/mon-hoc/{id}', [\App\Http\Controllers\SinhVien\ChuongTrinhDaoTaoController::class, 'chiTietMonHoc'])->name('mon-hoc');
    });

    // PHASE 8.5: Cảnh báo học vụ cá nhân
    Route::middleware('sinhvien.check')->prefix('canh-bao-hoc-vu')->name('canh-bao-hoc-vu.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\CanhBaoHocVuController::class, 'index'])->name('index');
        Route::get('/{canhBaoHocVu}', [\App\Http\Controllers\SinhVien\CanhBaoHocVuController::class, 'show'])->name('show');
    });

    // PHASE 8: Học phí cá nhân
    Route::middleware('sinhvien.check')->prefix('hoc-phi')->name('hoc-phi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'index'])->name('index');
        Route::get('/huong-dan', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'huongDan'])->name('huong-dan');
        Route::get('/{id}', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'show'])->name('show');
        Route::get('/{id}/lich-su', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'lichSu'])->name('lich-su');
        Route::get('/{id}/pdf', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'exportPdf'])->name('pdf');
        
        // PayOS Payment routes
        Route::get('/{id}/payos-payment', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'showPayOSPayment'])->name('payos-payment');
        Route::post('/{id}/payos-check-status', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'checkPayOSStatus'])->name('payos-check-status');
        
        // ZaloPay Payment routes
        Route::get('/{id}/zalopay-payment', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'showZaloPayPayment'])->name('zalopay-payment');
        Route::post('/{id}/zalopay-initiate', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'initiateZaloPayPayment'])->name('zalopay-initiate');
        Route::post('/{id}/zalopay-check-status', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'checkZaloPayStatus'])->name('zalopay-check-status');
    });

    // PHASE 9.5: Xuất dữ liệu (Export Data)
    Route::middleware('sinhvien.check')->prefix('xuat-du-lieu')->name('xuat-du-lieu.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SinhVien\XuatDuLieuController::class, 'index'])->name('index');
        Route::get('/bang-diem/excel', [\App\Http\Controllers\SinhVien\XuatDuLieuController::class, 'xuatBangDiemExcel'])->name('bang-diem.excel');
        Route::get('/bang-diem/pdf', [\App\Http\Controllers\SinhVien\XuatDuLieuController::class, 'xuatBangDiemPdf'])->name('bang-diem.pdf');
        Route::get('/tkb/pdf', [\App\Http\Controllers\SinhVien\XuatDuLieuController::class, 'xuatTKBPdf'])->name('tkb.pdf');
        Route::get('/giay-xac-nhan/pdf', [\App\Http\Controllers\SinhVien\XuatDuLieuController::class, 'giayXacNhanPdf'])->name('giay-xac-nhan.pdf');
    });

    // PHASE 10: Thông báo (chỉ xem)
    Route::middleware('sinhvien.check')->prefix('thong-bao')->name('thong-bao.')->group(function () {
        Route::get('/', [SinhVienThongBaoController::class, 'index'])->name('index');
        Route::get('/{thongBao}', [SinhVienThongBaoController::class, 'show'])->name('show');
        Route::post('/{thongBao}/mark-read', [SinhVienThongBaoController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [SinhVienThongBaoController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread/count', [SinhVienThongBaoController::class, 'getUnreadCount'])->name('unread-count');
    });

    // TRA CỨU
    Route::middleware('sinhvien.check')->prefix('tra-cuu')->name('tra-cuu.')->group(function () {
        Route::get('/hoc-phan', [\App\Http\Controllers\SinhVien\TraCuuController::class, 'traHocPhan'])->name('hoc-phan');
        Route::get('/giang-vien', [\App\Http\Controllers\SinhVien\TraCuuController::class, 'traGiangVien'])->name('giang-vien');
        Route::get('/phong-hoc', [\App\Http\Controllers\SinhVien\TraCuuController::class, 'traPhongHoc'])->name('phong-hoc');
    });

    // PHASE 12: AI Chatbot (Sinh viên)
    // FIX: Thêm rate limiting để tránh spam (30 requests/minute)
    Route::middleware(['sinhvien.check', 'throttle:30,1'])->prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/', [ChatbotController::class, 'index'])->name('index');
        Route::post('/conversation/create', [ChatbotController::class, 'createConversation'])->name('conversation.create');
        Route::post('/message/send', [ChatbotController::class, 'sendMessage'])->name('message.send');
        Route::get('/conversation/{conversationId}/messages', [ChatbotController::class, 'getMessages'])->name('conversation.messages');
        Route::get('/conversation/{conversationId}', [ChatbotController::class, 'loadConversation'])->name('conversation.show');
        Route::delete('/conversation/{conversationId}', [ChatbotController::class, 'deleteConversation'])->name('conversation.delete');
        Route::post('/feedback', [ChatbotController::class, 'submitFeedback'])->name('feedback.submit');
        Route::get('/history', [ChatbotController::class, 'history'])->name('history');
        Route::get('/suggested-questions', [ChatbotController::class, 'getSuggestedQuestions'])->name('suggested-questions');
    });
});

// PayOS Payment Callback (public routes - no auth required - OUTSIDE middleware group)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/payos/callback', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'payOSCallback'])->name('payos.callback');
    Route::get('/payos/cancel', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'payOSCancel'])->name('payos.cancel');
    
    // ZaloPay Payment Callback (public routes - no auth required)
    Route::get('/zalopay/callback', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'zaloPayCallback'])->name('zalopay.callback');
    Route::post('/zalopay/callback', [\App\Http\Controllers\SinhVien\HocPhiController::class, 'zaloPayIpn'])->name('zalopay.ipn');
});

// ZaloPay Redirect Handler (public route)
Route::get('/zalopay-redirect', function (\Illuminate\Http\Request $request) {
    // ZaloPay redirects here after payment
    $returncode = $request->get('returncode');
    $returnmessage = $request->get('returnmessage');
    $apptransid = $request->get('apptransid');
    
    // Redirect to callback with all parameters
    return redirect()->route('payment.zalopay.callback', $request->all());
})->name('zalopay.redirect-handler');
