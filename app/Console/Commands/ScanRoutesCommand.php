<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use App\Models\ChucNang;

/**
 * Command quét tất cả routes và lưu vào bảng chuc_nang
 * 
 * Sử dụng: php artisan permission:scan-routes
 */
class ScanRoutesCommand extends Command
{
    protected $signature = 'permission:scan-routes {--fresh : Xóa tất cả và quét lại}';
    protected $description = 'Quét tất cả routes và lưu vào bảng chức năng';

    /**
     * Mapping prefix → actor
     */
    protected array $prefixActorMap = [
        'admin' => 'admin',
        'dao-tao' => 'dao_tao',
        'giangvien' => 'giang_vien',
        'sinh-vien' => 'sinh_vien',
    ];

    /**
     * Các route cần bỏ qua
     */
    protected array $excludePatterns = [
        'sanctum.*',
        'ignition.*',
        'debugbar.*',
        'livewire.*',
        '*.index', // Có thể bỏ qua nếu index không cần quyền
    ];

    /**
     * Mapping method → action name
     */
    protected array $methodActionMap = [
        'GET' => 'xem',
        'POST' => 'them',
        'PUT' => 'sua',
        'PATCH' => 'sua',
        'DELETE' => 'xoa',
    ];

    public function handle()
    {
        if ($this->option('fresh')) {
            ChucNang::truncate();
            $this->info('Đã xóa tất cả chức năng cũ.');
        }

        $routes = Route::getRoutes();
        $count = 0;
        $skipped = 0;

        foreach ($routes as $route) {
            $routeName = $route->getName();
            $uri = $route->uri();
            $methods = $route->methods();
            $method = $methods[0] ?? 'GET';

            // Bỏ qua route không có tên
            if (!$routeName) {
                $skipped++;
                continue;
            }

            // Bỏ qua HEAD method
            if ($method === 'HEAD') {
                continue;
            }

            // Bỏ qua các route trong danh sách exclude
            if ($this->shouldExclude($routeName)) {
                $skipped++;
                continue;
            }

            // Xác định actor từ prefix
            $actor = $this->detectActor($uri, $routeName);
            if (!$actor) {
                $skipped++;
                continue;
            }

            // Tạo tên chức năng từ route name
            $tenChucNang = $this->generateFunctionName($routeName, $method);
            $nhom = $this->detectGroup($routeName);

            // Lưu hoặc cập nhật
            ChucNang::updateOrCreate(
                ['route_name' => $routeName],
                [
                    'ten_chuc_nang' => $tenChucNang,
                    'nhom' => $nhom,
                    'actor' => $actor,
                    'method' => $method,
                    'uri' => $uri,
                    'yeu_cau_quyen' => $this->requiresPermission($method, $routeName),
                ]
            );

            $count++;
            $this->line("  ✓ {$routeName} ({$method}) → {$actor}");
        }

        $this->newLine();
        $this->info("Đã quét xong: {$count} chức năng, bỏ qua {$skipped} route.");
        $this->info("Chạy 'php artisan permission:auto-assign' để tự động gắn quyền.");
    }

    /**
     * Kiểm tra route có nên bỏ qua không
     */
    protected function shouldExclude(string $routeName): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Xác định actor từ URI hoặc route name
     */
    protected function detectActor(string $uri, string $routeName): ?string
    {
        foreach ($this->prefixActorMap as $prefix => $actor) {
            if (str_starts_with($uri, $prefix) || str_starts_with($routeName, $prefix)) {
                return $actor;
            }
        }
        return null;
    }

    /**
     * Tạo tên chức năng từ route name
     * VD: dao-tao.khoa.store → Thêm khoa
     */
    protected function generateFunctionName(string $routeName, string $method): string
    {
        $parts = explode('.', $routeName);
        $action = end($parts);

        // Lấy resource name (khoa, sinh-vien, ...)
        $resource = count($parts) >= 2 ? $parts[count($parts) - 2] : '';
        $resource = str_replace('-', ' ', $resource);
        $resource = ucfirst($resource);

        // Map action sang tiếng Việt
        $actionMap = [
            'index' => 'Danh sách',
            'create' => 'Form thêm',
            'store' => 'Thêm',
            'show' => 'Chi tiết',
            'edit' => 'Form sửa',
            'update' => 'Cập nhật',
            'destroy' => 'Xóa',
        ];

        $actionName = $actionMap[$action] ?? ucfirst($action);

        return "{$actionName} {$resource}";
    }

    /**
     * Xác định nhóm từ route name
     * VD: dao-tao.khoa.store → Quản lý Khoa
     */
    protected function detectGroup(string $routeName): string
    {
        $parts = explode('.', $routeName);

        if (count($parts) >= 2) {
            $resource = $parts[1]; // khoa, sinh-vien, ...
            $resource = str_replace('-', ' ', $resource);
            return 'Quản lý ' . ucfirst($resource);
        }

        return 'Khác';
    }

    /**
     * Xác định route có cần kiểm tra quyền không
     * Thường GET index không cần, POST/PUT/DELETE cần
     */
    protected function requiresPermission(string $method, string $routeName): bool
    {
        // Dashboard không cần quyền chi tiết
        if (str_contains($routeName, 'dashboard')) {
            return false;
        }

        // GET thường không cần (có thể bỏ comment nếu muốn)
        // if ($method === 'GET') {
        //     return false;
        // }

        return true;
    }
}
