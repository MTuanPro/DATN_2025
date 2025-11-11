@extends('layouts.layout-daotao')

@section('title', 'Danh sách Ngành')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Ngành</h3>
                    <p class="text-subtitle text-muted">Danh sách ngành trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ngành</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tìm kiếm & Lọc</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.nganh.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Mã ngành, Tên ngành..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sắp xếp</label>
                                    <select name="sort" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Theo ID
                                        </option>
                                        <option value="ten_nganh" {{ request('sort') == 'ten_nganh' ? 'selected' : '' }}>
                                            Theo Tên</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Thứ tự</label>
                                    <select name="direction" class="form-select">
                                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng
                                            dần</option>
                                        <option value="desc"
                                            {{ request('direction', 'asc') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary icon icon-left">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('dao-tao.nganh.index') }}"
                                            class="btn btn-secondary icon icon-left">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách Ngành</h5>
                    <a href="{{ route('dao-tao.nganh.create') }}" class="btn btn-primary icon icon-left">
                        <i class="bi bi-plus-circle"></i> Thêm Ngành
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã Ngành</th>
                                    <th>Tên Ngành</th>
                                    <th>Khoa</th>
                                    <th>Mô tả</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nganhs as $index => $nganh)
                                    <tr>
                                        <td>{{ $nganhs->firstItem() + $index }}</td>
                                        <td><strong>{{ $nganh->ma_nganh }}</strong></td>
                                        <td>{{ $nganh->ten_nganh }}</td>
                                        <td>{{ $nganh->khoa->ten_khoa ?? '-' }}</td>
                                        <td>{{ $nganh->mo_ta ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.nganh.edit', $nganh->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.nganh.destroy', $nganh->id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa ngành này?')">
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
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $nganhs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
