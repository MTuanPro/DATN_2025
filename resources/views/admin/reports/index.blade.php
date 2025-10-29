@extends('layouts.layout-admin')

@section('title', 'Báo cáo & Thống kê')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo & Thống kê</h3>
                    <p class="text-subtitle text-muted">Tổng quan hệ thống quản lý</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Báo cáo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        {{-- Bộ lọc --}}
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-filter"></i> Lọc
                                        </button>
                                        <a href="{{ route('admin.reports.export') }}" class="btn btn-success">
                                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- Thống kê tổng quan --}}
        <section class="row">
            <div class="col-12">
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
                                        <h6 class="text-muted font-semibold">Tổng người dùng</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalUsers) }}</h6>
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
                                            <i class="bi bi-person-plus-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Mới trong kỳ</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($usersInPeriod) }}</h6>
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
                                        <h6 class="font-extrabold mb-0">{{ number_format($activeUsers) }}</h6>
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
                                        <h6 class="text-muted font-semibold">Bị khóa</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($lockedUsers) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Biểu đồ và bảng --}}
        <section class="row">
            <div class="col-12 col-lg-8">
                {{-- Thống kê theo vai trò --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Phân bố người dùng theo vai trò</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vai trò</th>
                                        <th>Mã vai trò</th>
                                        <th>Số lượng</th>
                                        <th>Tỷ lệ %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usersByRole as $index => $role)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="badge bg-primary">{{ $role->ten_vai_tro }}</span></td>
                                            <td><code>{{ $role->ma_vai_tro }}</code></td>
                                            <td class="font-semibold">{{ number_format($role->total) }}</td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0 }}%">
                                                    </div>
                                                </div>
                                                <span
                                                    class="small">{{ $totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0 }}%</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Tổng cộng:</td>
                                        <td>{{ number_format($totalUsers) }}</td>
                                        <td>100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Thống kê đăng nhập --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Hoạt động đăng nhập theo ngày</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Số lượt đăng nhập</th>
                                        <th>Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loginsByDay as $login)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($login->date)->format('d/m/Y') }}</td>
                                            <td><strong>{{ $login->total }}</strong></td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-info"
                                                        style="width: {{ $loginsByDay->max('total') > 0 ? round(($login->total / $loginsByDay->max('total')) * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Chưa có dữ liệu đăng nhập
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                {{-- Xác thực Email --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Xác thực Email</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Đã xác thực</span>
                                <span class="badge bg-success">{{ number_format($verifiedUsers) }}</span>
                            </div>
                            <div class="progress progress-sm mb-3">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100) : 0 }}%">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Chưa xác thực</span>
                                <span class="badge bg-warning">{{ number_format($unverifiedUsers) }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning"
                                    style="width: {{ $totalUsers > 0 ? round(($unverifiedUsers / $totalUsers) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top đăng nhập gần đây --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Đăng nhập gần đây</h4>
                    </div>
                    <div class="card-content pb-4">
                        @forelse($recentUsers as $user)
                            <div class="recent-message d-flex px-4 py-2">
                                <div class="avatar avatar-md">
                                    <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                </div>
                                <div class="name ms-3">
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <p class="text-muted mb-0 small">{{ $user->email }}</p>
                                    <p class="text-muted mb-0 small">
                                        <i class="bi bi-clock"></i>
                                        {{ \Carbon\Carbon::parse($user->lan_dang_nhap_cuoi)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-3 text-center text-muted">
                                Chưa có hoạt động
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Báo cáo chi tiết</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-primary">
                                <i class="bi bi-people"></i> Báo cáo Người dùng
                            </a>
                            <a href="{{ route('admin.reports.permissions') }}" class="btn btn-outline-success">
                                <i class="bi bi-shield-check"></i> Báo cáo Phân quyền
                            </a>
                            <a href="{{ route('admin.reports.export') }}" class="btn btn-outline-info">
                                <i class="bi bi-download"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
