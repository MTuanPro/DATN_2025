<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VaiTro;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * Middleware này kiểm tra quyền truy cập dựa trên ACTOR của vai trò, không phải ma_vai_tro
     * Ví dụ: role:admin sẽ cho phép tất cả vai trò có actor='admin' (admin, super_admin, sp_admin, etc.)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedActors): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Lấy tất cả actor của user từ vai trò của họ
        $userActors = $user->vaiTro()->pluck('actor')->unique()->toArray();

        // Kiểm tra xem user có actor được phép không
        foreach ($allowedActors as $actor) {
            if (in_array($actor, $userActors)) {
                return $next($request);
            }
        }

        // Nếu không có quyền, chuyển về trang tương ứng với actor của user
        return $this->redirectToActorDashboard($userActors);
    }

    /**
     * Chuyển hướng về dashboard tương ứng với actor
     */
    protected function redirectToActorDashboard(array $userActors)
    {
        // Ưu tiên theo thứ tự: admin -> dao_tao -> giang_vien -> sinh_vien
        if (in_array('admin', $userActors)) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('dao_tao', $userActors)) {
            return redirect()->route('dao-tao.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('giang_vien', $userActors)) {
            return redirect()->route('giangvien.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        if (in_array('sinh_vien', $userActors)) {
            return redirect()->route('sinh-vien.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        // Nếu không có actor nào (vai trò chưa được gán actor), logout
        \Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được gán vai trò hợp lệ');
    }
}
