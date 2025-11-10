@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Đăng ký môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Thống kê đăng ký môn học theo học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Đăng ký môn học</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.dang-ky') }}">
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
                                <a href="{{ route('dao-tao.bao-cao.dang-ky') }}" class="btn btn-secondary">
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
                                    <i class="iconly-boldDocument"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng đăng ký</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_dang_ky']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Thành công</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['thanh_cong']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Hủy</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['huy']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Courses -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Top môn học đăng ký nhiều nhất</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Môn học</th>
                                <th>Mã môn</th>
                                <th>Số tín chỉ</th>
                                <th>Lượt đăng ký</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCourses as $index => $course)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>{{ $course->ten_mon }}</td>
                                    <td>{{ $course->ma_mon }}</td>
                                    <td>{{ $course->so_tin_chi }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($course->so_luot_dang_ky) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Registration by Class -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Đăng ký theo lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Lớp HP</th>
                                <th>Môn học</th>
                                <th>Giảng viên</th>
                                <th>Sĩ số tối đa</th>
                                <th>Đã đăng ký</th>
                                <th>Còn trống</th>
                                <th>Tỷ lệ lấp đầy</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrationByClass as $class)
                                <tr>
                                    <td><strong>{{ $class->ma_lop_hp }}</strong></td>
                                    <td>{{ $class->ten_mon }}</td>
                                    <td>{{ $class->ten_giang_vien }}</td>
                                    <td>{{ $class->si_so_toi_da }}</td>
                                    <td><span class="badge bg-success">{{ $class->da_dang_ky }}</span></td>
                                    <td><span class="badge bg-warning">{{ $class->si_so_toi_da - $class->da_dang_ky }}</span></td>
                                    <td>
                                        @php
                                            $tyLe = $class->si_so_toi_da > 0 ? ($class->da_dang_ky / $class->si_so_toi_da * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe >= 90 ? 'bg-danger' : ($tyLe >= 70 ? 'bg-success' : 'bg-warning') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $tyLe }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($tyLe >= 100)
                                            <span class="badge bg-danger">Đầy</span>
                                        @elseif($tyLe >= 90)
                                            <span class="badge bg-warning">Sắp đầy</span>
                                        @elseif($tyLe >= 50)
                                            <span class="badge bg-success">Còn chỗ</span>
                                        @else
                                            <span class="badge bg-secondary">Ít người</span>
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
