@extends('layouts.layout-admin')

@section('title', 'Quản lý Tài khoản')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Tài khoản</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả tài khoản trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Danh sách Tài khoản</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tạo tài khoản mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo tên hoặc email..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="hoat_dong" {{ request('status') == 'hoat_dong' ? 'selected' : '' }}>Hoạt
                                        động</option>
                                    <option value="khoa" {{ request('status') == 'khoa' ? 'selected' : '' }}>Khóa</option>
                                    <option value="ngung_hoat_dong"
                                        {{ request('status') == 'ngung_hoat_dong' ? 'selected' : '' }}>Ngừng hoạt động
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select">
                                    <option value="">-- Tất cả vai trò --</option>
                                    @foreach ($vaiTros as $vaiTro)
                                        <option value="{{ $vaiTro->ma_vai_tro }}"
                                            {{ request('role') == $vaiTro->ma_vai_tro ? 'selected' : '' }}>
                                            {{ $vaiTro->ten_vai_tro }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Users Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Email verified</th>
                                    <th>Đăng nhập cuối</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->vaiTro as $vaiTro)
                                                <span class="badge bg-info">{{ $vaiTro->ten_vai_tro }}</span>
                                            @endforeach
                                            @if ($user->vaiTro->isEmpty())
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->trang_thai == 'hoat_dong')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @elseif($user->trang_thai == 'khoa')
                                                <span class="badge bg-danger">Khóa</span>
                                            @else
                                                <span class="badge bg-secondary">Ngừng hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->email_verified_at)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <small>{{ $user->email_verified_at->format('d/m/Y') }}</small>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                <small>Chưa xác thực</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->lan_dang_nhap_cuoi)
                                                <small>{{ $user->lan_dang_nhap_cuoi->format('d/m/Y H:i') }}</small>
                                            @else
                                                <small class="text-muted">Chưa đăng nhập</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                @if ($user->id !== Auth::id())
                                                    <button type="button"
                                                        class="btn btn-sm btn-{{ $user->trang_thai == 'hoat_dong' ? 'danger' : 'success' }} toggle-status"
                                                        data-user-id="{{ $user->id }}"
                                                        data-current-status="{{ $user->trang_thai }}"
                                                        title="{{ $user->trang_thai == 'hoat_dong' ? 'Khóa' : 'Mở khóa' }}">
                                                        <i
                                                            class="bi bi-{{ $user->trang_thai == 'hoat_dong' ? 'lock' : 'unlock' }}"></i>
                                                    </button>

                                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Không có tài khoản nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Hiển thị {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}
                            trong tổng số {{ $users->total() }} tài khoản
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Toggle status
                document.querySelectorAll('.toggle-status').forEach(button => {
                    button.addEventListener('click', function() {
                        const userId = this.dataset.userId;
                        const currentStatus = this.dataset.currentStatus;

                        if (confirm(
                                `Bạn có chắc chắn muốn ${currentStatus === 'hoat_dong' ? 'khóa' : 'mở khóa'} tài khoản này?`
                                )) {
                            fetch(`/admin/users/${userId}/toggle-status`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        location.reload();
                                    } else {
                                        alert(data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Có lỗi xảy ra!');
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
