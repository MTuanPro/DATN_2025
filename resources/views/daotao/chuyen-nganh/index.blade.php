@extends('layouts.layout-daotao')

@section('title', 'Danh sách Chuyên ngành')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Chuyên ngành</h3>
                    <p class="text-subtitle text-muted">Danh sách chuyên ngành trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chuyên ngành</li>
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
                    <form action="{{ route('dao-tao.chuyen-nganh.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Mã, Tên chuyên ngành..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Theo ID</label>
                                    <select name="nganh_id" class="form-select">
                                        <option value="">-- Tất cả ngành --</option>
                                        @foreach ($nganhs as $nganh)
                                            <option value="{{ $nganh->id }}"
                                                {{ request('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                                {{ $nganh->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sắp xếp</label>
                                    <select name="sort" class="form-select">
                                        <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Theo ID
                                        </option>
                                        <option value="ten_chuyen_nganh"
                                            {{ request('sort') == 'ten_chuyen_nganh' ? 'selected' : '' }}>Theo Tên</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Thứ tự</label>
                                    <select name="direction" class="form-select">
                                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng
                                            dần
                                        </option>
                                        <option value="desc"
                                            {{ request('direction', 'asc') == 'desc' ? 'selected' : '' }}>
                                            Giảm dần</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary icon icon-left">
                                            <i class="bi bi-search"></i> Tìm
                                        </button>
                                        <a href="{{ route('dao-tao.chuyen-nganh.index') }}"
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
                    <h5 class="card-title mb-0">Danh sách Chuyên ngành</h5>
                    <a href="{{ route('dao-tao.chuyen-nganh.create') }}" class="btn btn-primary icon icon-left">
                        <i class="bi bi-plus-circle"></i> Thêm Chuyên ngành
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã Chuyên ngành</th>
                                    <th>Tên Chuyên ngành</th>
                                    <th>Ngành</th>
                                    <th>Khoa</th>
                                    <th>Tổng TC tối thiểu</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chuyenNganhs as $index => $cn)
                                    <tr>
                                        <td>{{ $chuyenNganhs->firstItem() + $index }}</td>
                                        <td><strong>{{ $cn->ma_chuyen_nganh }}</strong></td>
                                        <td>{{ $cn->ten_chuyen_nganh }}</td>
                                        <td>{{ $cn->nganh->ten_nganh ?? '-' }}</td>
                                        <td>{{ $cn->nganh->khoa->ten_khoa ?? '-' }}</td>
                                        <td class="text-center">{{ $cn->tong_tin_chi_toi_thieu ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.chuyen-nganh.edit', $cn->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.chuyen-nganh.destroy', $cn->id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa chuyên ngành này?')">
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
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $chuyenNganhs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
