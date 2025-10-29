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

class ReportController extends Controller
{
    /**
     * Báo cáo tổng quan hệ thống
     */
    public function index(Request $request)
    {
        // Lọc theo thời gian
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Thống kê người dùng
        $totalUsers = User::count();
        $usersInPeriod = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeUsers = User::where('trang_thai', 'hoat_dong')->count();
        $lockedUsers = User::where('trang_thai', 'khoa')->count();

        // Thống kê theo vai trò
        $usersByRole = User::select('tai_khoan_vai_tro.vai_tro_id', 'vai_tro.ten_vai_tro', 'vai_tro.ma_vai_tro', DB::raw('count(*) as total'))
            ->join('tai_khoan_vai_tro', 'users.id', '=', 'tai_khoan_vai_tro.tai_khoan_id')
            ->join('vai_tro', 'tai_khoan_vai_tro.vai_tro_id', '=', 'vai_tro.id')
            ->groupBy('tai_khoan_vai_tro.vai_tro_id', 'vai_tro.ten_vai_tro', 'vai_tro.ma_vai_tro')
            ->get();

        // Thống kê người dùng theo tháng
        $usersByMonth = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Thống kê đăng nhập theo ngày
        $loginsByDay = User::select(
            DB::raw('DATE(lan_dang_nhap_cuoi) as date'),
            DB::raw('count(*) as total')
        )
            ->whereNotNull('lan_dang_nhap_cuoi')
            ->whereBetween('lan_dang_nhap_cuoi', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Thống kê email verification
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();

        // Top 10 người dùng đăng nhập gần nhất
        $recentUsers = User::whereNotNull('lan_dang_nhap_cuoi')
            ->orderBy('lan_dang_nhap_cuoi', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalUsers',
            'usersInPeriod',
            'activeUsers',
            'lockedUsers',
            'usersByRole',
            'usersByMonth',
            'loginsByDay',
            'verifiedUsers',
            'unverifiedUsers',
            'recentUsers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Báo cáo chi tiết người dùng
     */
    public function users(Request $request)
    {
        $query = User::with('vaiTro');

        // Lọc theo vai trò
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('vaiTro', function ($q) use ($request) {
                $q->where('vai_tro.id', $request->role);
            });
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('trang_thai', $request->status);
        }

        // Lọc theo thời gian tạo
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('created_at', '<=', $request->end_date);
        }

        $users = $query->paginate(20);
        $roles = VaiTro::all();

        return view('admin.reports.users', compact('users', 'roles'));
    }

    /**
     * Báo cáo phân quyền
     */
    public function permissions()
    {
        $roles = VaiTro::with(['quyen.nhomQuyen'])->get();
        $totalRoles = $roles->count();
        $totalPermissions = Quyen::count();

        // Thống kê quyền theo nhóm
        $permissionsByGroup = Quyen::select('nhom_quyen.ten_nhom', DB::raw('count(*) as total'))
            ->join('nhom_quyen', 'quyen.nhom_quyen_id', '=', 'nhom_quyen.id')
            ->groupBy('nhom_quyen.ten_nhom')
            ->get();

        return view('admin.reports.permissions', compact(
            'roles',
            'totalRoles',
            'totalPermissions',
            'permissionsByGroup'
        ));
    }

    /**
     * Export báo cáo Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export
        return back()->with('info', 'Chức năng export đang phát triển');
    }
}
