@extends('layouts.layout-daotao')

@section('title', 'Quản lý Trạng thái Học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Trạng thái Học tập</h3>
                    <p class="text-subtitle text-muted">Danh sách trạng thái học tập sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Trạng thái Học tập</li>
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
                    <form method="GET" action="{{ route('dao-tao.trang-thai-hoc-tap.index') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tên trạng thái, Mô tả..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary icon icon-left">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}"
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
                    <h5 class="card-title mb-0">Danh sách Trạng thái</h5>
                    <a href="{{ route('dao-tao.trang-thai-hoc-tap.create') }}" class="btn btn-primary icon icon-left">
                        <i class="bi bi-plus-circle"></i> Thêm Trạng thái
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên Trạng thái</th>
                                    <th>Mô tả</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trangThais as $index => $trangThai)
                                    <tr>
                                        <td>{{ $trangThais->firstItem() + $index }}</td>
                                        <td><strong>{{ $trangThai->ten_trang_thai }}</strong></td>
                                        <td>{{ $trangThai->mo_ta ?? '-' }}</td>
                                        <td>{{ $trangThai->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.trang-thai-hoc-tap.edit', $trangThai->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form
                                                    action="{{ route('dao-tao.trang-thai-hoc-tap.destroy', $trangThai->id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa trạng thái này?');">
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
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $trangThais->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
