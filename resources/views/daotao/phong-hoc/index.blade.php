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
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Phòng học</li>
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
                            <h5 class="card-title">Danh sách Phòng học</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('dao-tao.phong-hoc.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Phòng học
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tìm kiếm và lọc -->
                    <form method="GET" action="{{ route('dao-tao.phong-hoc.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tìm kiếm mã phòng, tên phòng, vị trí..."
                                        value="{{ request('keyword') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i> Tìm
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="loai_phong" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Tất cả loại phòng --</option>
                                    <option value="Lý thuyết" {{ request('loai_phong') == 'Lý thuyết' ? 'selected' : '' }}>
                                        Lý thuyết</option>
                                    <option value="Thực hành" {{ request('loai_phong') == 'Thực hành' ? 'selected' : '' }}>
                                        Thực hành</option>
                                    <option value="Phòng máy" {{ request('loai_phong') == 'Phòng máy' ? 'selected' : '' }}>
                                        Phòng máy</option>
                                    <option value="Hội trường"
                                        {{ request('loai_phong') == 'Hội trường' ? 'selected' : '' }}>Hội trường</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="Hoạt động" {{ request('trang_thai') == 'Hoạt động' ? 'selected' : '' }}>
                                        Hoạt động</option>
                                    <option value="Bảo trì" {{ request('trang_thai') == 'Bảo trì' ? 'selected' : '' }}>Bảo
                                        trì</option>
                                    <option value="Không sử dụng"
                                        {{ request('trang_thai') == 'Không sử dụng' ? 'selected' : '' }}>Không sử dụng
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                @if (request('keyword') || request('loai_phong') || request('trang_thai'))
                                    <a href="{{ route('dao-tao.phong-hoc.index') }}"
                                        class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Bảng dữ liệu -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã Phòng</th>
                                    <th>Tên Phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Vị trí</th>
                                    <th>Loại phòng</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phongHocs as $phongHoc)
                                    <tr>
                                        <td>{{ $loop->iteration + ($phongHocs->currentPage() - 1) * $phongHocs->perPage() }}
                                        </td>
                                        <td><strong>{{ $phongHoc->ma_phong }}</strong></td>
                                        <td>{{ $phongHoc->ten_phong }}</td>
                                        <td>{{ $phongHoc->suc_chua ?? '-' }}</td>
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
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.phong-hoc.edit', $phongHoc->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Sửa
                                                </a>
                                                <form action="{{ route('dao-tao.phong-hoc.destroy', $phongHoc->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng học này?');"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $phongHocs->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
