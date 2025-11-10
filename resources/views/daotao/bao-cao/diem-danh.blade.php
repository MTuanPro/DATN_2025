@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Điểm danh')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Điểm danh</h3>
                    <p class="text-subtitle text-muted">Tỷ lệ vắng mặt theo lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Điểm danh</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.diem-danh') }}">
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
                                <a href="{{ route('dao-tao.bao-cao.diem-danh') }}" class="btn btn-secondary">
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

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldCalendar"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng buổi học</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_buoi']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldCheck"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Có mặt</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['co_mat']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldClose-Square"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Vắng mặt</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['vang_mat']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thống kê điểm danh theo lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Lớp HP</th>
                                <th>Môn học</th>
                                <th>Giảng viên</th>
                                <th>SL sinh viên</th>
                                <th>Tổng buổi</th>
                                <th>Có mặt</th>
                                <th>Vắng</th>
                                <th>Tỷ lệ vắng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceData as $data)
                                <tr>
                                    <td><strong>{{ $data->ma_lop_hp }}</strong></td>
                                    <td>{{ $data->ten_mon }}</td>
                                    <td>{{ $data->ten_giang_vien }}</td>
                                    <td>{{ number_format($data->so_luong_sv) }}</td>
                                    <td>{{ number_format($data->total_buoi) }}</td>
                                    <td><span class="badge bg-success">{{ number_format($data->co_mat) }}</span></td>
                                    <td><span class="badge bg-danger">{{ number_format($data->vang_mat) }}</span></td>
                                    <td>
                                        @php
                                            $total = $data->co_mat + $data->vang_mat;
                                            $tyLeVang = $total > 0 ? ($data->vang_mat / $total * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLeVang >= 20 ? 'bg-danger' : ($tyLeVang >= 10 ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $tyLeVang }}%">
                                                {{ number_format($tyLeVang, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($tyLeVang >= 20)
                                            <span class="badge bg-danger">Cảnh báo</span>
                                        @elseif($tyLeVang >= 10)
                                            <span class="badge bg-warning">Theo dõi</span>
                                        @else
                                            <span class="badge bg-success">Tốt</span>
                                        @endif
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
