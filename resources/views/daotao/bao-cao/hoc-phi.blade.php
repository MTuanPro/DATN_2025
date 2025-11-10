@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Học phí</h3>
                    <p class="text-subtitle text-muted">Thống kê thu và nợ học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học phí</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('dao-tao.bao-cao.hoc-phi') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="{{ route('dao-tao.bao-cao.hoc-phi') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                                </a>
                                <button type="button" class="btn btn-success float-end">
                                    <i class="bi bi-file-excel"></i> Xuất Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldWallet"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng học phí</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_hoc_phi']) }} VNĐ</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldCheck"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Đã đóng</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['da_dong']) }} VNĐ</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldDanger"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Còn nợ</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['con_no']) }} VNĐ</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldChart"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tỷ lệ thu</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['ty_le_thu'], 1) }}%</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tổng quan thu học phí</h5>
            </div>
            <div class="card-body">
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $statistics['ty_le_thu'] }}%">
                        {{ number_format($statistics['ty_le_thu'], 1) }}% đã thu
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        Đã thu: <strong>{{ number_format($statistics['da_dong']) }} VNĐ</strong> / 
                        Tổng: <strong>{{ number_format($statistics['total_hoc_phi']) }} VNĐ</strong>
                    </small>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết học phí theo sinh viên</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Học kỳ</th>
                                <th>Tổng học phí</th>
                                <th>Đã đóng</th>
                                <th>Còn nợ</th>
                                <th>Tỷ lệ đóng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hocPhiData as $hp)
                                <tr>
                                    <td><strong>{{ $hp->ma_sinh_vien }}</strong></td>
                                    <td>{{ $hp->ho_ten }}</td>
                                    <td>{{ $hp->ten_hoc_ky }}</td>
                                    <td>{{ number_format($hp->tong_so_tien) }} VNĐ</td>
                                    <td><span class="text-success">{{ number_format($hp->so_tien_da_dong) }} VNĐ</span></td>
                                    <td><span class="text-danger">{{ number_format($hp->so_tien_con_lai) }} VNĐ</span></td>
                                    <td>
                                        @php
                                            $tyLe = $hp->tong_so_tien > 0 ? ($hp->so_tien_da_dong / $hp->tong_so_tien * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe >= 100 ? 'bg-success' : ($tyLe >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $tyLe }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($hp->so_tien_con_lai == 0)
                                            <span class="badge bg-success">Đã hoàn thành</span>
                                        @elseif($tyLe >= 50)
                                            <span class="badge bg-warning">Đang đóng</span>
                                        @else
                                            <span class="badge bg-danger">Nợ nhiều</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistics by Khoa -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thống kê theo khóa học</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Khóa học</th>
                                <th>SL sinh viên</th>
                                <th>Tổng học phí</th>
                                <th>Đã thu</th>
                                <th>Còn nợ</th>
                                <th>Tỷ lệ thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsByKhoa as $stat)
                                <tr>
                                    <td><strong>{{ $stat->ten_khoa_hoc }}</strong></td>
                                    <td>{{ number_format($stat->so_luong_sv) }}</td>
                                    <td>{{ number_format($stat->total) }} VNĐ</td>
                                    <td class="text-success">{{ number_format($stat->da_dong) }} VNĐ</td>
                                    <td class="text-danger">{{ number_format($stat->con_no) }} VNĐ</td>
                                    <td>
                                        @php
                                            $tyLe = $stat->total > 0 ? ($stat->da_dong / $stat->total * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $tyLe >= 80 ? 'success' : ($tyLe >= 50 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $tyLe }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
