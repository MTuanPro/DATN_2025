<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Lấy vai trò của user từ bảng tai_khoan_vai_tro
        $userRoles = $user->vaiTro()->pluck('ma_vai_tro')->toArray();

        // Kiểm tra xem user có vai trò được phép không
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        // Nếu không có quyền, chuyển về trang tương ứng với vai trò của user
        return $this->redirectToRoleDashboard($userRoles);
    }

    /**
     * Chuyển hướng về dashboard tương ứng với vai trò
     */
    protected function redirectToRoleDashboard(array $userRoles)
    {
        // Ưu tiên theo thứ tự: admin -> dao_tao -> giang_vien -> sinh_vien
        if (in_array('admin', $userRoles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('truong_phong_dt', $userRoles) || in_array('nhan_vien_dt', $userRoles)) {
            return redirect()->route('daotao.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('giang_vien', $userRoles)) {
            return redirect()->route('giangvien.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('sinh_vien', $userRoles)) {
            return redirect()->route('sinhvien.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        // Nếu không có vai trò nào, logout
        \Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được gán vai trò');
    }
}
