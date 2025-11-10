<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\Quyen;
use App\Models\Admin;
use App\Models\DaoTao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardControllerBackup extends Controller
{
    public function index()
    {
        // Thống kê tài khoản
        $totalUsers = User::count();
        $activeUsers = User::where('trang_thai', 'hoat_dong')->count();
        $lockedUsers = User::where('trang_thai', 'khoa')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();

        // Thống kê theo vai trò
        $usersByRole = User::select('tai_khoan_vai_tro.vai_tro_id', 'vai_tro.ten_vai_tro', DB::raw('count(*) as total'))
            ->join('tai_khoan_vai_tro', 'users.id', '=', 'tai_khoan_vai_tro.tai_khoan_id')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->groupBy('tai_khoan_vai_tro.vai_tro_id', 'vai_tro.ten_vai_tro')
            ->get();

        // Thống kê phân quyền
        $totalRoles = VaiTro::count();
        $totalPermissions = Quyen::count();

        // Thống kê nhân sự hệ thống
        $totalAdmins = Admin::count();
        $totalDaoTao = DaoTao::count();

        // Người dùng đăng nhập gần đây
        $recentLogins = User::whereNotNull('lan_dang_nhap_cuoi')
            ->orderBy('lan_dang_nhap_cuoi', 'desc')
            ->limit(5)
            ->get();

        // Người dùng mới tạo trong 7 ngày
        $newUsersThisWeek = User::where('created_at', '>=', now()->subDays(7))->count();

        // Thống kê theo ngày (7 ngày gần đây)
        $userCreationStats = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'lockedUsers' => $lockedUsers,
            'unverifiedUsers' => $unverifiedUsers,
            'usersByRole' => $usersByRole,
            'totalRoles' => $totalRoles,
            'totalPermissions' => $totalPermissions,
            'totalAdmins' => $totalAdmins,
            'totalDaoTao' => $totalDaoTao,
            'recentLogins' => $recentLogins,
            'newUsersThisWeek' => $newUsersThisWeek,
            'userCreationStats' => $userCreationStats,
        ];

        return view('admin.dashboard', $data);
    }
}
