<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ChucNang;

/**
 * Middleware kiểm tra quyền động dựa trên route name
 * 
 * Cách hoạt động:
 * 1. Lấy route name của request hiện tại
 * 2. Tìm chức năng tương ứng trong bảng chuc_nang
 * 3. Lấy quyền được gắn với chức năng đó
 * 4. Kiểm tra user có quyền đó không
 * 
 * Sử dụng trong route:
 * Route::middleware(['auth', 'dynamic.permission'])->group(...)
 */
class DynamicPermission
{
    /**
     * Cache để tránh query nhiều lần
     */
    protected static array $cache = [];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Nếu chưa đăng nhập
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        $routeName = $request->route()?->getName();

        // Nếu route không có tên, cho qua
        if (!$routeName) {
            return $next($request);
        }

        // Tìm chức năng
        $chucNang = $this->getChucNang($routeName);

        // Nếu chức năng không tồn tại hoặc không yêu cầu quyền, cho qua
        if (!$chucNang || !$chucNang->yeu_cau_quyen) {
            return $next($request);
        }

        // Nếu chức năng chưa gắn quyền, cho qua (hoặc có thể chặn)
        if (!$chucNang->quyen_id) {
            // Uncomment dòng dưới nếu muốn chặn chức năng chưa gắn quyền
            // abort(403, 'Chức năng này chưa được cấu hình quyền');
            return $next($request);
        }

        // Lấy mã quyền
        $maQuyen = $chucNang->quyen?->ma_quyen;

        if (!$maQuyen) {
            return $next($request);
        }

        // Kiểm tra user có quyền không
        if (!$user->hasPermission($maQuyen)) {
            abort(403, "Bạn không có quyền truy cập chức năng này (yêu cầu: {$maQuyen})");
        }

        return $next($request);
    }

    /**
     * Lấy chức năng từ cache hoặc database
     */
    protected function getChucNang(string $routeName): ?ChucNang
    {
        if (!isset(self::$cache[$routeName])) {
            self::$cache[$routeName] = ChucNang::with('quyen')
                ->where('route_name', $routeName)
                ->first();
        }

        return self::$cache[$routeName];
    }

    /**
     * Xóa cache (gọi khi cập nhật quyền)
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
