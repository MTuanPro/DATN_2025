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
        // Nếu đã đăng nhập, redirect về dashboard tương ứng
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

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
            'password' => 'required',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
        ]);

        // Lấy thông tin đăng nhập
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Kiểm tra user tồn tại và có password
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ])->withInput($request->only('email'));
        }

        // Kiểm tra password không null
        if (empty($user->password)) {
            return back()->withErrors([
                'email' => 'Tài khoản chưa được thiết lập mật khẩu. Vui lòng liên hệ quản trị viên.',
            ])->withInput($request->only('email'));
        }

        // Kiểm tra trạng thái tài khoản TRƯỚC KHI đăng nhập
        if ($user->trang_thai === 'khoa') {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ])->withInput($request->only('email'));
        }

        if ($user->trang_thai === 'ngung_hoat_dong') {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị ngừng hoạt động. Vui lòng liên hệ quản trị viên.',
            ])->withInput($request->only('email'));
        }

        // Kiểm tra email đã xác thực chưa
        if (empty($user->email_verified_at)) {
            return back()->withErrors([
                'email' => 'Email của bạn chưa được xác thực. Vui lòng xác thực email trước khi đăng nhập.',
            ])->withInput($request->only('email'));
        }

        // Kiểm tra đăng nhập
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

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
        // Lấy vai trò có ưu tiên cao nhất của user (theo muc_do_uu_tien)
        $vaiTro = $user->vaiTro()->orderBy('muc_do_uu_tien', 'desc')->first();

        if (!$vaiTro) {
            // Nếu không có vai trò nào, logout
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được gán vai trò');
        }

        // Redirect dựa trên actor của vai trò
        return $this->redirectToActorDashboard($vaiTro->actor);
    }

    /**
     * Redirect đến dashboard tương ứng với actor
     */
    protected function redirectToActorDashboard($actor)
    {
        switch ($actor) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'dao_tao':
                return redirect()->route('dao-tao.dashboard');
            case 'giang_vien':
                return redirect()->route('giangvien.dashboard');
            case 'sinh_vien':
                return redirect()->route('sinh-vien.dashboard');
            default:
                // Mặc định redirect về admin nếu actor không xác định
                return redirect()->route('admin.dashboard');
        }
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
