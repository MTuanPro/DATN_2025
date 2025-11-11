@extends('layouts.layout-daotao')

@section('title', 'Quản lý Phòng học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Phòng học</h3>
                    <p class="text-subtitle text-muted">Danh sách phòng học, phòng thực hành</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Phòng học</li>
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
                    <form method="GET" action="{{ route('dao-tao.phong-hoc.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Mã phòng, Tên phòng, Vị trí..." value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Loại phòng</label>
                                    <select name="loai_phong" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="Lý thuyết"
                                            {{ request('loai_phong') == 'Lý thuyết' ? 'selected' : '' }}>
                                            Lý thuyết</option>
                                        <option value="Thực hành"
                                            {{ request('loai_phong') == 'Thực hành' ? 'selected' : '' }}>
                                            Thực hành</option>
                                        <option value="Phòng máy"
                                            {{ request('loai_phong') == 'Phòng máy' ? 'selected' : '' }}>
                                            Phòng máy</option>
                                        <option value="Hội trường"
                                            {{ request('loai_phong') == 'Hội trường' ? 'selected' : '' }}>
                                            Hội trường</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="Hoạt động"
                                            {{ request('trang_thai') == 'Hoạt động' ? 'selected' : '' }}>
                                            Hoạt động</option>
                                        <option value="Bảo trì" {{ request('trang_thai') == 'Bảo trì' ? 'selected' : '' }}>
                                            Bảo trì</option>
                                        <option value="Không sử dụng"
                                            {{ request('trang_thai') == 'Không sử dụng' ? 'selected' : '' }}>
                                            Không sử dụng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary icon icon-left">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('dao-tao.phong-hoc.index') }}"
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
                    <h5 class="card-title mb-0">Danh sách Phòng học</h5>
                    <a href="{{ route('dao-tao.phong-hoc.create') }}" class="btn btn-primary icon icon-left">
                        <i class="bi bi-plus-circle"></i> Thêm Phòng học
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã Phòng</th>
                                    <th>Tên Phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Vị trí</th>
                                    <th>Loại phòng</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phongHocs as $index => $phongHoc)
                                    <tr>
                                        <td>{{ $phongHocs->firstItem() + $index }}</td>
                                        <td><strong>{{ $phongHoc->ma_phong }}</strong></td>
                                        <td>{{ $phongHoc->ten_phong }}</td>
                                        <td class="text-center">{{ $phongHoc->suc_chua ?? '-' }}</td>
                                        <td>{{ $phongHoc->vi_tri ?? '-' }}</td>
                                        <td>
                                            @if ($phongHoc->loai_phong)
                                                <span class="badge bg-info">{{ $phongHoc->loai_phong }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($phongHoc->trang_thai == 'Hoạt động')
                                                <span class="badge bg-success">{{ $phongHoc->trang_thai }}</span>
                                            @elseif($phongHoc->trang_thai == 'Bảo trì')
                                                <span class="badge bg-warning">{{ $phongHoc->trang_thai }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $phongHoc->trang_thai }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.phong-hoc.edit', $phongHoc->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.phong-hoc.destroy', $phongHoc->id) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng học này?');">
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
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $phongHocs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
