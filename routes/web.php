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


// Route trang chủ - redirect to dashboard nếu đã login, ngược lại về login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $roles = $user->vaiTro()->pluck('ma_vai_tro')->toArray();

        if (in_array('admin', $roles)) {
            return redirect()->route('admin.dashboard');
        }
        if (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)) {
            return redirect()->route('daotao.dashboard');
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
Route::prefix('dao-tao')->name('dao-tao.')->group(function () {
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

    // Chương trình khung
    Route::resource('chuong-trinh-khung', ChuongTrinhKhungController::class);
    Route::get('chuong-trinh-khung/thong-ke/{chuyenNganhId}', [ChuongTrinhKhungController::class, 'thongKe'])->name('chuong-trinh-khung.thong-ke');
});


// ========== Đào tạo Routes (Trưởng phòng & Nhân viên) ==========
Route::middleware(['auth', 'role:truong_phong_dt,nhan_vien_dt'])->prefix('dao-tao')->name('daotao.')->group(function () {
    Route::get('/dashboard', [DaoTaoDashboardController::class, 'index'])->name('dashboard');

    // Thêm các route đào tạo khác ở đây
});

// ========== Giảng viên Routes ==========
Route::middleware(['auth', 'role:giang_vien'])->prefix('giang-vien')->name('giangvien.')->group(function () {
    Route::get('/dashboard', [GiangVienDashboardController::class, 'index'])->name('dashboard');

    // Thêm các route giảng viên khác ở đây
});

// ========== Sinh viên Routes ==========
Route::middleware(['auth', 'role:sinh_vien'])->prefix('sinh-vien')->name('sinhvien.')->group(function () {
    Route::get('/dashboard', [SinhVienDashboardController::class, 'index'])->name('dashboard');

    // Thêm các route sinh viên khác ở đây
});
