@extends('layouts.layout-daotao')

@section('title', 'Quản lý Trình độ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Trình độ</h3>
                    <p class="text-subtitle text-muted">Danh sách trình độ đào tạo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Trình độ</li>
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
                            <h5 class="card-title">Danh sách Trình độ</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('dao-tao.trinh-do.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Trình độ
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tìm kiếm -->
                    <form method="GET" action="{{ route('dao-tao.trinh-do.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tìm kiếm theo tên trình độ..." value="{{ request('keyword') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                    @if (request('keyword'))
                                        <a href="{{ route('dao-tao.trinh-do.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Tên Trình độ</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trinhDos as $trinhDo)
                                    <tr>
                                        <td>{{ $loop->iteration + ($trinhDos->currentPage() - 1) * $trinhDos->perPage() }}
                                        </td>
                                        <td><strong>{{ $trinhDo->ten_trinh_do }}</strong></td>
                                        <td>{{ $trinhDo->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.trinh-do.edit', $trinhDo->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Sửa
                                                </a>
                                                <form action="{{ route('dao-tao.trinh-do.destroy', $trinhDo->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa trình độ này?');"
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
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $trinhDos->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
