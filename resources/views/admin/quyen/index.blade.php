@extends('layouts.layout-admin')

@section('title', 'Quản lý Quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Quyền</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả quyền trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Quyền</li>
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
                            <h5 class="card-title">Danh sách Quyền</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.quyen.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm quyền
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.quyen.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo mã, tên hoặc mô tả..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-5">
                                <select name="nhom_quyen_id" class="form-select">
                                    <option value="">-- Tất cả nhóm quyền --</option>
                                    @foreach ($nhomQuyens as $nhomQuyen)
                                        <option value="{{ $nhomQuyen->id }}"
                                            {{ request('nhom_quyen_id') == $nhomQuyen->id ? 'selected' : '' }}>
                                            {{ $nhomQuyen->ten_nhom }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <th>Mã quyền</th>
                                    <th>Tên quyền</th>
                                    <th>Nhóm quyền</th>
                                    <th>Mô tả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quyens as $quyen)
                                    <tr>
                                        <td><code>{{ $quyen->ma_quyen }}</code></td>
                                        <td><strong>{{ $quyen->ten_quyen }}</strong></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $quyen->nhomQuyen->ten_nhom }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($quyen->mo_ta, 40) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.quyen.edit', $quyen) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.quyen.destroy', $quyen) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa quyền này?');">
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
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">Không có quyền nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $quyens->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


