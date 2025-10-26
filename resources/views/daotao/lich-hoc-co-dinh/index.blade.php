@extends('layouts.layout-daotao')

@section('title', 'Lịch học cố định')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học cố định</h3>
                    <p class="text-subtitle text-muted">
                        {{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch học cố định</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách lịch học cố định</h5>
                        <a href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh.create', $lopHocPhan) }}"
                            class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm lịch học
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($lichHocs->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có lịch học cố định nào.
                            <a href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh.create', $lopHocPhan) }}">Thêm mới</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Thứ</th>
                                        <th>Tiết</th>
                                        <th>Giờ học</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th>Hình thức</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichHocs as $lichHoc)
                                        <tr>
                                            <td><strong>{{ $lichHoc->ten_thu }}</strong></td>
                                            <td>{{ $lichHoc->tiet_bat_dau }} - {{ $lichHoc->tiet_ket_thuc }}</td>
                                            <td>{{ Carbon\Carbon::parse($lichHoc->gio_bat_dau)->format('H:i') }} -
                                                {{ Carbon\Carbon::parse($lichHoc->gio_ket_thuc)->format('H:i') }}</td>
                                            <td>{{ $lichHoc->phongHoc->ten_phong ?? '-' }}</td>
                                            <td>{{ $lichHoc->giangVien->ho_ten ?? '-' }}</td>
                                            <td>
                                                @if ($lichHoc->hinh_thuc == 'offline')
                                                    <span class="badge bg-primary">Offline</span>
                                                @elseif($lichHoc->hinh_thuc == 'online')
                                                    <span class="badge bg-success">Online</span>
                                                @else
                                                    <span class="badge bg-info">Hybrid</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dao-tao.lich-co-dinh.edit', $lichHoc) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('dao-tao.lich-co-dinh.destroy', $lichHoc) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
