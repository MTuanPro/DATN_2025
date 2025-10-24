@extends('layouts.layout-admin')

@section('title', 'Quản lý Vai trò')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Vai trò</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả vai trò trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Vai trò</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Danh sách Vai trò</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.vai-tro.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm vai trò
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.vai-tro.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo mã, tên hoặc mô tả..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Mã vai trò</th>
                                    <th>Tên vai trò</th>
                                    <th>Mô tả</th>
                                    <th>Ưu tiên</th>
                                    <th>Số người dùng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vaiTros as $vaiTro)
                                    <tr>
                                        <td><code>{{ $vaiTro->ma_vai_tro }}</code></td>
                                        <td><strong>{{ $vaiTro->ten_vai_tro }}</strong></td>
                                        <td>{{ Str::limit($vaiTro->mo_ta, 50) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $vaiTro->muc_do_uu_tien }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $vaiTro->users_count }} người</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.vai-tro.edit', $vaiTro) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.vai-tro.destroy', $vaiTro) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">Không có vai trò nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $vaiTros->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
