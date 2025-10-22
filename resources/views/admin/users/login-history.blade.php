@extends('layouts.layout-admin')

@section('title', 'Lịch sử Đăng nhập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử Đăng nhập</h3>
                    <p class="text-subtitle text-muted">Theo dõi lịch sử đăng nhập của: {{ $user->name }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Tài khoản</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử đăng nhập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Tài khoản</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        @if ($user->trang_thai == 'hoat_dong')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @elseif($user->trang_thai == 'khoa')
                                            <span class="badge bg-danger">Khóa</span>
                                        @else
                                            <span class="badge bg-secondary">Ngừng hoạt động</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Vai trò:</strong></td>
                                    <td>
                                        @foreach ($user->vaiTro as $vaiTro)
                                            <span class="badge bg-info">{{ $vaiTro->ten_vai_tro }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-pencil"></i> Sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Lịch sử Đăng nhập</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Lưu ý:</strong> Tính năng lưu lịch sử đăng nhập chi tiết đang được phát triển.
                                Hiện tại chỉ hiển thị lần đăng nhập cuối cùng.
                            </div>

                            @if ($user->lan_dang_nhap_cuoi)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Thời gian</th>
                                                <th>IP Address</th>
                                                <th>User Agent</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-clock"></i>
                                                    {{ $user->lan_dang_nhap_cuoi->format('d/m/Y H:i:s') }}
                                                    <br>
                                                    <small class="text-muted">
                                                        ({{ $user->lan_dang_nhap_cuoi->diffForHumans() }})
                                                    </small>
                                                </td>
                                                <td>
                                                    <code>N/A</code>
                                                    <br>
                                                    <small class="text-muted">Chưa lưu IP</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">Chưa lưu User Agent</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Thành công
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-3">Người dùng này chưa đăng nhập lần nào</p>
                                </div>
                            @endif

                            <hr>

                            <h5 class="mt-4">TODO: Tính năng cần phát triển</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="bi bi-square text-muted"></i>
                                    Tạo bảng <code>login_history</code> để lưu chi tiết
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-square text-muted"></i>
                                    Lưu IP address, User Agent, Location
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-square text-muted"></i>
                                    Phân trang lịch sử đăng nhập
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-square text-muted"></i>
                                    Lọc theo khoảng thời gian
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-square text-muted"></i>
                                    Phát hiện đăng nhập bất thường
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </section>
    </div>
@endsection
