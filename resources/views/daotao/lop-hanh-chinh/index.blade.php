@extends('layouts.layout-daotao')

@section('title', 'Danh sách Lớp hành chính')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Lớp hành chính</h3>
                    <p class="text-subtitle text-muted">Quản lý lớp hành chính - PHASE 3</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lớp hành chính</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filters & Actions -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('dao-tao.lop-hanh-chinh.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tìm kiếm mã lớp, tên lớp..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="khoa_hoc_id" class="form-select">
                                        <option value="">-- Tất cả khóa học --</option>
                                        @foreach ($khoaHocs as $khoaHoc)
                                            <option value="{{ $khoaHoc->id }}"
                                                {{ request('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                                                {{ $khoaHoc->ten_khoa_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="nganh_id" class="form-select">
                                        <option value="">-- Tất cả ngành --</option>
                                        @foreach ($nganhs as $nganh)
                                            <option value="{{ $nganh->id }}"
                                                {{ request('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                                {{ $nganh->ma_nganh }} - {{ $nganh->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('dao-tao.lop-hanh-chinh.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Thêm lớp mới
                            </a>
                        </div>
                    </div>

                    <!-- Bảng dữ liệu -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Khóa học</th>
                                    <th>Ngành</th>
                                    <th>GVCN</th>
                                    <th>Sĩ số</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lopHanhChinh as $index => $lop)
                                    <tr>
                                        <td>{{ $lopHanhChinh->firstItem() + $index }}</td>
                                        <td><strong>{{ $lop->ma_lop }}</strong></td>
                                        <td>{{ $lop->ten_lop }}</td>
                                        <td>
                                            @if ($lop->khoaHoc)
                                                <span class="badge bg-primary">{{ $lop->khoaHoc->ten_khoa_hoc }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lop->nganh)
                                                {{ $lop->nganh->ma_nganh }} - {{ $lop->nganh->ten_nganh }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lop->giangVienChuNhiem)
                                                {{ $lop->giangVienChuNhiem->ho_ten }}
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $lop->si_so }} SV</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.lop-hanh-chinh.show', $lop->id) }}"
                                                    class="btn btn-sm btn-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.lop-hanh-chinh.edit', $lop->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.lop-hanh-chinh.destroy', $lop->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa lớp này?')">
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
                                        <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $lopHanhChinh->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
