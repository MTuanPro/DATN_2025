<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * 
     * Middleware kiểm tra quyền chi tiết của user
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Mã quyền cần kiểm tra (VD: user.create, user.edit)
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // Nếu chưa đăng nhập
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        // Lấy tất cả quyền của user qua các vai trò
        $userPermissions = $user->vaiTro()
            ->with('quyens')
            ->get()
            ->pluck('quyens')
            ->flatten()
            ->pluck('ma_quyen')
            ->unique()
            ->toArray();

        // Kiểm tra quyền
        if (!in_array($permission, $userPermissions)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này');
        }

        return $next($request);
    }
}
