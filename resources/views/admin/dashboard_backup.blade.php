@extends('layouts.layout-admin')

@section('title', 'Admin Dashboard (Backup)')

@section('content')
    <div class="page-heading">
        <h3>Dashboard - Quản trị viên (Backup)</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                {{-- Thống kê tổng quan --}}
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Tổng tài khoản</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalUsers }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Đang hoạt động</h6>
                                        <h6 class="font-extrabold mb-0">{{ $activeUsers }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="bi bi-lock-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Tài khoản khóa</h6>
                                        <h6 class="font-extrabold mb-0">{{ $lockedUsers }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Chưa xác thực email</h6>
                                        <h6 class="font-extrabold mb-0">{{ $unverifiedUsers }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thống kê phân quyền --}}
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="bi bi-shield-fill-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Vai trò</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalRoles }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Quyền</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalPermissions }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Admin</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalAdmins }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="bi bi-person-workspace"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Phòng Đào tạo</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalDaoTao }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Biểu đồ và bảng (backup) --}}
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Thống kê người dùng theo vai trò</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Vai trò</th>
                                                <th>Số lượng</th>
                                                <th>Tỷ lệ</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($usersByRole as $role)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $role->ten_vai_tro }}</span>
                                                    </td>
                                                    <td class="font-semibold">{{ $role->total }}</td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ $totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0 }}%">
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="text-muted small">{{ $totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0 }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> Hoạt động
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Thống kê người dùng mới --}}
                        <div class="card">
                            <div class="card-header">
                                <h4>Người dùng mới trong 7 ngày</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <h2 class="mb-0 me-3">{{ $newUsersThisWeek }}</h2>
                                    <span class="badge bg-success">
                                        <i class="bi bi-arrow-up"></i> Người dùng mới
                                    </span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            @foreach ($userCreationStats as $stat)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}
                                                    </td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-primary"
                                                                style="width: {{ $newUsersThisWeek > 0 ? round(($stat->total / $newUsersThisWeek) * 100) : 0 }}%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ $stat->total }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        {{-- Đăng nhập gần đây --}}
                        <div class="card">
                            <div class="card-header">
                                <h4>Đăng nhập gần đây</h4>
                            </div>
                            <div class="card-content pb-4">
                                @forelse($recentLogins as $user)
                                    <div class="recent-message d-flex px-4 py-3">
                                        <div class="avatar avatar-lg">
                                            <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                        </div>
                                        <div class="name ms-3">
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <p class="text-muted mb-0 small">{{ $user->email }}</p>
                                            <p class="text-muted mb-0 small">
                                                <i class="bi bi-clock"></i>
                                                {{ $user->lan_dang_nhap_cuoi ? \Carbon\Carbon::parse($user->lan_dang_nhap_cuoi)->diffForHumans() : 'Chưa đăng nhập' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-3 text-center text-muted">
                                        Chưa có hoạt động đăng nhập
                                    </div>
                                @endforelse
                                <div class="px-4">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="btn btn-sm btn-block btn-outline-primary">
                                        <i class="bi bi-arrow-right-circle"></i> Xem tất cả
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Thông tin hệ thống --}}
                        <div class="card">
                            <div class="card-header">
                                <h4>Thông tin hệ thống</h4>
                            </div>
                            <div class="card-content pb-4">
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-primary">
                                        <i class="bi bi-info-circle text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Phiên bản</h6>
                                        <p class="text-muted mb-0">S-MIS v1.0.0</p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-success">
                                        <i class="bi bi-code-square text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Laravel</h6>
                                        <p class="text-muted mb-0">{{ app()->version() }}</p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-warning">
                                        <i class="bi bi-filetype-php text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">PHP Version</h6>
                                        <p class="text-muted mb-0">{{ phpversion() }}</p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-info">
                                        <i class="bi bi-calendar-check text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Ngày triển khai</h6>
                                        <p class="text-muted mb-0">21/10/2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
