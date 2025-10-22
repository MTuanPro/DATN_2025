<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DaoTao\DashboardController as DaoTaoDashboardController;
use App\Http\Controllers\GiangVien\DashboardController as GiangVienDashboardController;
use App\Http\Controllers\SinhVien\DashboardController as SinhVienDashboardController;

// Route trang chủ - redirect to login
Route::get('/', function () {
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
