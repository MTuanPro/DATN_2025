@extends('layouts.layout-admin')

@section('title', 'Báo cáo Phân quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Phân quyền</h3>
                    <p class="text-subtitle text-muted">Chi tiết vai trò và quyền hạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Phân quyền</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        {{-- Thống kê tổng quan --}}
        <section class="row">
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="bi bi-shield-fill-check"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Tổng Vai trò</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalRoles }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="bi bi-key-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Tổng Quyền</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalPermissions }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Nhóm quyền</h6>
                                <h6 class="font-extrabold mb-0">{{ $permissionsByGroup->count() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Thống kê quyền theo nhóm --}}
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Phân bố quyền theo nhóm</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nhóm quyền</th>
                                    <th>Số lượng quyền</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissionsByGroup as $index => $group)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge bg-info">{{ $group->ten_nhom }}</span></td>
                                        <td class="font-semibold">{{ $group->total }}</td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary"
                                                    style="width: {{ $totalPermissions > 0 ? round(($group->total / $totalPermissions) * 100, 1) : 0 }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="small">{{ $totalPermissions > 0 ? round(($group->total / $totalPermissions) * 100, 1) : 0 }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        {{-- Chi tiết quyền theo vai trò --}}
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết quyền theo vai trò</h4>
                </div>
                <div class="card-body">
                    @foreach ($roles as $role)
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <span class="badge bg-primary">{{ $role->ten_vai_tro }}</span>
                                <small class="text-muted ms-2">({{ $role->quyen->count() }} quyền)</small>
                            </h5>
                            @if ($role->quyen->count() > 0)
                                <div class="row">
                                    @php
                                        $groupedPermissions = $role->quyen->groupBy('nhomQuyen.ten_nhom');
                                    @endphp
                                    @foreach ($groupedPermissions as $groupName => $permissions)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <i class="bi bi-folder2-open"></i> {{ $groupName }}
                                                    </h6>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($permissions as $permission)
                                                            <li class="mb-1">
                                                                <i class="bi bi-check-circle text-success"></i>
                                                                {{ $permission->ten_quyen }}
                                                                <code class="small">{{ $permission->ma_quyen }}</code>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Vai trò này chưa được gán quyền nào.</p>
                            @endif
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
