<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Nếu đã đăng nhập, chuyển hướng về dashboard theo vai trò
                $user = Auth::guard($guard)->user();
                return $this->redirectToDashboard($user);
            }
        }

        return $next($request);
    }

    /**
     * Chuyển hướng về dashboard tương ứng với vai trò
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

        // Nếu không có vai trò nào, về trang login
        Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được gán vai trò');
    }
}
