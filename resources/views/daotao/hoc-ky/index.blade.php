@extends('layouts.layout-daotao')

@section('title', 'Danh sách Học kỳ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Học kỳ</h3>
                    <p class="text-subtitle text-muted">Quản lý học kỳ trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học kỳ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách Học kỳ</h5>
                        <a href="{{ route('dao-tao.hoc-ky.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm Học kỳ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Bộ lọc --}}
                    <form action="{{ route('dao-tao.hoc-ky.index') }}" method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm theo tên học kỳ hoặc năm học..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Bảng danh sách --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên học kỳ</th>
                                    <th>Năm học</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Thời gian đăng ký</th>
                                    <th class="text-center">Hiện tại</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hocKys as $index => $hocKy)
                                    <tr class="{{ $hocKy->la_hoc_ky_hien_tai ? 'table-success' : '' }}">
                                        <td>{{ $hocKys->firstItem() + $index }}</td>
                                        <td><strong>{{ $hocKy->ten_hoc_ky }}</strong></td>
                                        <td>{{ $hocKy->nam_hoc }}</td>
                                        <td>{{ $hocKy->ngay_bat_dau->format('d/m/Y') }}</td>
                                        <td>{{ $hocKy->ngay_ket_thuc->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($hocKy->ngay_bat_dau_dang_ky && $hocKy->ngay_ket_thuc_dang_ky)
                                                <div>
                                                    {{ $hocKy->ngay_bat_dau_dang_ky->format('d/m/Y') }}
                                                    -
                                                    {{ $hocKy->ngay_ket_thuc_dang_ky->format('d/m/Y') }}
                                                </div>
                                                @php
                                                    $now = now();
                                                    $batDau = $hocKy->ngay_bat_dau_dang_ky;
                                                    $ketThuc = $hocKy->ngay_ket_thuc_dang_ky;
                                                @endphp
                                                @if ($now < $batDau)
                                                    <small class="text-warning">Chưa mở</small>
                                                @elseif ($now > $ketThuc)
                                                    <small class="text-danger">Đã đóng</small>
                                                @else
                                                    <small class="text-success fw-bold">✓ Đang mở</small>
                                                @endif
                                            @else
                                                <small class="text-muted">Chưa thiết lập</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($hocKy->la_hoc_ky_hien_tai)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Hiện tại
                                                </span>
                                                <br>
                                                @if ($hocKy->ngay_bat_dau_dang_ky && $hocKy->ngay_ket_thuc_dang_ky)
                                                    <form action="{{ route('dao-tao.hoc-ky.mo-dang-ky', $hocKy->id) }}"
                                                        method="POST" class="d-inline mt-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info">
                                                            <i class="bi bi-unlock"></i> Mở đăng ký
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <form action="{{ route('dao-tao.hoc-ky.set-hien-tai', $hocKy->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Đặt làm học kỳ hiện tại?')">
                                                        Đặt hiện tại
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.hoc-ky.edit', $hocKy->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if (!$hocKy->la_hoc_ky_hien_tai)
                                                    <form action="{{ route('dao-tao.hoc-ky.destroy', $hocKy->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa học kỳ này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có học kỳ nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-3">
                        {{ $hocKys->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
