<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VaiTro;
use App\Models\Quyen;
use App\Models\Admin;
use App\Models\DaoTao;
use App\Models\DaoTao\SinhVien;
use App\Models\GiangVien;
use App\Models\LopHocPhan;
use App\Models\DangKyMonHoc;
use App\Models\KetQuaHocTap;
use App\Models\HocPhiHocKy;
use App\Models\CanhBaoHocVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

        // Additional educational statistics for admin dashboard
        $data['totalStudents'] = SinhVien::count();
        $data['totalLecturers'] = GiangVien::count();
        $data['totalLopHocPhan'] = LopHocPhan::count();

        // Đăng ký môn học theo môn (top 8)
        $registrations = DangKyMonHoc::select('mon_hoc.ten_mon', DB::raw('count(*) as total'))
            ->join('lop_hoc_phan', 'dang_ky_mon_hocs.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
            ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
            ->groupBy('mon_hoc.ten_mon')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $data['registrationLabels'] = $registrations->pluck('ten_mon')->toArray();
        $data['registrationSeries'] = $registrations->pluck('total')->toArray();

        // Phân bố điểm (theo diem_chu từ ket_qua_hoc_tap)
        $gradeStats = KetQuaHocTap::select('diem_chu', DB::raw('count(*) as total'))
            ->groupBy('diem_chu')
            ->get()
            ->pluck('total', 'diem_chu')
            ->toArray();

        // Ensure common grade order
        $gradeOrder = ['A','B+','B','C+','C','D+','D','F'];
        $gradeLabels = [];
        $gradeSeries = [];
        foreach ($gradeOrder as $g) {
            $gradeLabels[] = $g;
            $gradeSeries[] = isset($gradeStats[$g]) ? (int)$gradeStats[$g] : 0;
        }
        $data['gradeLabels'] = $gradeLabels;
        $data['gradeSeries'] = $gradeSeries;

        // Tỷ lệ đỗ / trượt
        $quaMon = KetQuaHocTap::where('qua_mon', true)->count();
        $khongQua = KetQuaHocTap::where('qua_mon', false)->count();
        $data['passFail'] = ['labels' => ['Qua môn', 'Không qua'], 'series' => [$quaMon, $khongQua]];

        // Thống kê học phí tổng quan
        $hocPhiQuery = HocPhiHocKy::query();
        $data['hocPhiTong'] = (clone $hocPhiQuery)->sum('tong_so_tien');
        $data['hocPhiDaThu'] = (clone $hocPhiQuery)->sum('so_tien_da_dong');
        $data['hocPhiConLai'] = (clone $hocPhiQuery)->sum('so_tien_con_lai');

        // Cảnh báo học vụ (chưa xử lý, top 5 gần nhất)
        $data['canhBaoCount'] = CanhBaoHocVu::count();
        $data['canhBaoChuaXuLy'] = CanhBaoHocVu::where('da_xu_ly', false)->count();
        $data['recentWarnings'] = CanhBaoHocVu::with('sinhVien')->orderBy('ngay_canh_bao', 'desc')->limit(5)->get();

        return view('admin.dashboard', $data);
    }
}
