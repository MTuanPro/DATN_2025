<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\DaoTao\DashboardController as DaoTaoDashboardController;
use App\Http\Controllers\GiangVien\DashboardController as GiangVienDashboardController;
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
use App\Http\Controllers\DaoTao\LopHanhChinhController;
use App\Http\Controllers\DaoTao\SinhVienController;
use App\Http\Controllers\DaoTao\XepLopController;
use App\Http\Controllers\SinhVien\DangKyMonHocController;
use App\Http\Controllers\SinhVien\ThoiKhoaBieuController;


// Debug route (temporary)
Route::get('/debug/check-auth', [App\Http\Controllers\DebugController::class, 'checkAuth'])->name('debug.check-auth');

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
            return redirect()->route('sinhvien.dashboard');
        }
    }
    return redirect()->route('login');
});

// ========== Auth Routes (Không cần đăng nhập) ==========
Route::middleware('guest')->group(function () {
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

// Logout (Cần đăng nhập)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

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

    // Admin Management (Member 5)
    Route::resource('admin', AdminController::class);
    Route::post('/admin/{admin}/assign-user', [AdminController::class, 'assignUser'])->name('admin.assign-user');
    Route::post('/admin/{admin}/unassign-user', [AdminController::class, 'unassignUser'])->name('admin.unassign-user');

    // Dao Tao Management (Member 5)
    Route::resource('dao-tao', DaoTaoController::class);
    Route::post('/dao-tao/{daoTao}/assign-user', [DaoTaoController::class, 'assignUser'])->name('dao-tao.assign-user');
    Route::post('/dao-tao/{daoTao}/unassign-user', [DaoTaoController::class, 'unassignUser'])->name('dao-tao.unassign-user');
});

// ========== Đào tạo Routes (Trưởng phòng & Nhân viên) ==========
Route::middleware(['auth', 'role:truong_phong_dt,nhan_vien_dt'])->prefix('dao-tao')->name('dao-tao.')->group(function () {
    Route::get('/dashboard', [DaoTaoDashboardController::class, 'index'])->name('dashboard');


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
    Route::get('giang-vien-import', [DaoTaoGiangVienController::class, 'showImportForm'])
        ->name('giang-vien.show-import-form');
    Route::post('giang-vien-import', [DaoTaoGiangVienController::class, 'import'])
        ->name('giang-vien.import');
    Route::get('giang-vien-template', [DaoTaoGiangVienController::class, 'downloadTemplate'])
        ->name('giang-vien.download-template');

    // PHASE 3: Lớp hành chính và Sinh viên  
    Route::get('lop-hanh-chinh', [LopHanhChinhController::class, 'index'])->name('lop-hanh-chinh.index');
    Route::get('lop-hanh-chinh/create', [LopHanhChinhController::class, 'create'])->name('lop-hanh-chinh.create');
    Route::post('lop-hanh-chinh', [LopHanhChinhController::class, 'store'])->name('lop-hanh-chinh.store');
    Route::get('lop-hanh-chinh/{lop_hanh_chinh}', [LopHanhChinhController::class, 'show'])->name('lop-hanh-chinh.show');
    Route::get('lop-hanh-chinh/{lop_hanh_chinh}/edit', [LopHanhChinhController::class, 'edit'])->name('lop-hanh-chinh.edit');
    Route::put('lop-hanh-chinh/{lop_hanh_chinh}', [LopHanhChinhController::class, 'update'])->name('lop-hanh-chinh.update');
    Route::delete('lop-hanh-chinh/{lop_hanh_chinh}', [LopHanhChinhController::class, 'destroy'])->name('lop-hanh-chinh.destroy');

    Route::resource('sinh-vien', SinhVienController::class);
    Route::get('sinh-vien-import', [SinhVienController::class, 'showImportForm'])->name('sinh-vien.show-import-form');
    Route::post('sinh-vien-import', [SinhVienController::class, 'import'])->name('sinh-vien.import');
    Route::get('sinh-vien-template', [SinhVienController::class, 'downloadTemplate'])->name('sinh-vien.download-template');

    // PHASE 4: Lớp học phần & Phân công
    Route::resource('lop-hoc-phan', LopHocPhanController::class);

    // Phân công giảng dạy
    Route::get('lop-hoc-phan/{lopHocPhan}/phan-cong', [PhanCongGiangDayController::class, 'index'])->name('lop-hoc-phan.phan-cong');
    Route::post('lop-hoc-phan/{lopHocPhan}/phan-cong', [PhanCongGiangDayController::class, 'store'])->name('lop-hoc-phan.phan-cong.store');
    Route::put('phan-cong/{phanCong}', [PhanCongGiangDayController::class, 'update'])->name('phan-cong.update');
    Route::delete('phan-cong/{phanCong}', [PhanCongGiangDayController::class, 'destroy'])->name('phan-cong.destroy');

    // Cấu hình đầu điểm
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
});

// ========== Giảng viên Routes ==========
Route::middleware(['auth', 'role:giang_vien'])->prefix('giang-vien')->name('giangvien.')->group(function () {
    Route::get('/dashboard', [GiangVienDashboardController::class, 'index'])->name('dashboard');
    // Thêm các route giảng viên khác ở đây
});

// ========== Sinh viên Routes ==========
Route::middleware(['auth', 'role:sinh_vien'])->prefix('sinh-vien')->name('sinhvien.')->group(function () {
    Route::get('/dashboard', [SinhVienDashboardController::class, 'index'])->name('dashboard');

    // PHASE 5: Đăng ký môn học
    Route::middleware('sinhvien.check')->prefix('dang-ky-mon-hoc')->name('dang-ky-mon-hoc.')->group(function () {
    Route::get('/', [DangKyMonHocController::class, 'index'])->name('index');
    Route::get('/create', [DangKyMonHocController::class, 'create'])->name('create');
    Route::post('/', [DangKyMonHocController::class, 'store'])->name('store');
        Route::delete('/{dangKy}', [DangKyMonHocController::class, 'destroy'])->name('destroy');
        Route::get('/my-registrations', [DangKyMonHocController::class, 'myRegistrations'])->name('my-registrations');
    });

    // PHASE 5: Thời khóa biểu cá nhân
    Route::middleware('sinhvien.check')->prefix('thoi-khoa-bieu')->name('thoi-khoa-bieu.')->group(function () {
        Route::get('/', [ThoiKhoaBieuController::class, 'index'])->name('index');
        Route::get('/chi-tiet', [ThoiKhoaBieuController::class, 'chiTiet'])->name('chi-tiet');
        Route::get('/export-pdf', [ThoiKhoaBieuController::class, 'exportPDF'])->name('export-pdf');
    });
});
