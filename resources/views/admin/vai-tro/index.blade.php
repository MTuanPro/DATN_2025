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

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-check me-2"></i>Danh sách Vai trò
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.vai-tro.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Thêm Vai trò
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Search Form --}}
                    <form action="{{ route('admin.vai-tro.index') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo mã vai trò, tên vai trò, mô tả..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã vai trò</th>
                                    <th>Tên vai trò</th>
                                    <th>Mô tả</th>
                                    <th>Ưu tiên</th>
                                    <th>Số user</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vaiTros as $vaiTro)
                                    <tr>
                                        <td>{{ $vaiTro->id }}</td>
                                        <td>
                                            <code class="badge bg-secondary">{{ $vaiTro->ma_vai_tro }}</code>
                                        </td>
                                        <td>
                                            <strong>{{ $vaiTro->ten_vai_tro }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $vaiTro->mo_ta ?? 'Chưa có mô tả' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $vaiTro->muc_do_uu_tien }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $vaiTro->users_count }} người</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.vai-tro.edit', $vaiTro->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('admin.vai-tro.destroy', $vaiTro->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Không có vai trò nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Hiển thị {{ $vaiTros->firstItem() ?? 0 }} - {{ $vaiTros->lastItem() ?? 0 }}
                            trong tổng số {{ $vaiTros->total() }} vai trò
                        </div>
                        <div>
                            {{ $vaiTros->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
