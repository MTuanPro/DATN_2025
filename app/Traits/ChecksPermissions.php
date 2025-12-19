<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait để kiểm tra quyền trong Controller
 * 
 * Sử dụng trong Controller:
 * use App\Traits\ChecksPermissions;
 * 
 * class MyController extends Controller
 * {
 *     use ChecksPermissions;
 *     
 *     public function destroy($id)
 *     {
 *         $this->authorizePermission('thong_bao.xoa');
 *         // ... code xóa
 *     }
 * }
 */
trait ChecksPermissions
{
    /**
     * Kiểm tra và authorize quyền
     * Nếu không có quyền sẽ abort 403
     * 
     * @param string $permission Mã quyền (VD: khoa.them, thong_bao.xoa)
     * @param string|null $message Thông báo lỗi tùy chỉnh
     */
    protected function authorizePermission(string $permission, ?string $message = null): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Vui lòng đăng nhập');
        }

        if (!$user->hasPermission($permission)) {
            abort(403, $message ?? "Bạn không có quyền thực hiện chức năng này (yêu cầu: {$permission})");
        }
    }

    /**
     * Kiểm tra user có quyền hay không (không abort)
     * 
     * @param string $permission Mã quyền
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        return $user && $user->hasPermission($permission);
    }

    /**
     * Kiểm tra user có một trong các quyền hay không
     * 
     * @param array $permissions Mảng mã quyền
     * @return bool
     */
    protected function hasAnyPermission(array $permissions): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyPermission($permissions);
    }

    /**
     * Authorize một trong các quyền
     * 
     * @param array $permissions Mảng mã quyền
     * @param string|null $message Thông báo lỗi
     */
    protected function authorizeAnyPermission(array $permissions, ?string $message = null): void
    {
        if (!$this->hasAnyPermission($permissions)) {
            abort(403, $message ?? 'Bạn không có quyền thực hiện chức năng này');
        }
    }
}
