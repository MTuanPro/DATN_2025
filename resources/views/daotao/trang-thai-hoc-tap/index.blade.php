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
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Trạng thái Học tập</li>
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
                            <h5 class="card-title">Danh sách Trạng thái</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('dao-tao.trang-thai-hoc-tap.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Trạng thái
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tìm kiếm -->
                    <form method="GET" action="{{ route('dao-tao.trang-thai-hoc-tap.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tìm kiếm theo tên hoặc mô tả..." value="{{ request('keyword') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                    @if (request('keyword'))
                                        <a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle"></i> Xóa lọc
                                        </a>
                                    @endif
                                </div>
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
                                    <th>Tên Trạng thái</th>
                                    <th>Mô tả</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trangThais as $trangThai)
                                    <tr>
                                        <td>{{ $loop->iteration + ($trangThais->currentPage() - 1) * $trangThais->perPage() }}
                                        </td>
                                        <td><strong>{{ $trangThai->ten_trang_thai }}</strong></td>
                                        <td>{{ $trangThai->mo_ta ?? '-' }}</td>
                                        <td>{{ $trangThai->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.trang-thai-hoc-tap.edit', $trangThai->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Sửa
                                                </a>
                                                <form
                                                    action="{{ route('dao-tao.trang-thai-hoc-tap.destroy', $trangThai->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa trạng thái này?');"
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
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $trangThais->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
