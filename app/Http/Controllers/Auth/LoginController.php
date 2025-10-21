<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        // Lấy thông tin đăng nhập
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Kiểm tra đăng nhập
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Kiểm tra trạng thái tài khoản
            $user = Auth::user();
            if ($user->trang_thai === 'khoa') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ])->withInput($request->only('email'));
            }

            // Cập nhật thời gian đăng nhập cuối
            $user->update([
                'lan_dang_nhap_cuoi' => now(),
            ]);

            // Điều hướng theo vai trò
            return $this->redirectToDashboard($user);
        }

        // Đăng nhập thất bại
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->withInput($request->only('email'));
    }

    /**
     * Điều hướng đến dashboard tương ứng với vai trò
     */
    protected function redirectToDashboard($user)
    {
        // Lấy vai trò của user
        $roles = $user->vaiTro()->pluck('ma_vai_tro')->toArray();

        // Ưu tiên theo thứ tự: admin -> dao_tao -> giang_vien -> sinh_vien
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

        // Nếu không có vai trò nào, logout
        Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được gán vai trò');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đăng xuất thành công');
    }
}
