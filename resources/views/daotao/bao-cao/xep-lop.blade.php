@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Xếp lớp')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Xếp lớp</h3>
                    <p class="text-subtitle text-muted">Thống kê xếp lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Xếp lớp</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.xep-lop') }}">
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
                                <a href="{{ route('dao-tao.bao-cao.xep-lop') }}" class="btn btn-secondary">
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
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldCategory"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng lớp HP</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_lop']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Đủ sĩ số</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['du_si_so']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Thiếu sĩ số</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['thieu_si_so']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Tỷ lệ thành công</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['ty_le_thanh_cong'], 1) }}%</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Assignment Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết xếp lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Lớp HP</th>
                                <th>Môn học</th>
                                <th>Giảng viên</th>
                                <th>Sĩ số tối thiểu</th>
                                <th>Sĩ số tối đa</th>
                                <th>Đã xếp</th>
                                <th>Tỷ lệ lấp đầy</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classAssignments as $class)
                                <tr>
                                    <td><strong>{{ $class->ma_lop_hp }}</strong></td>
                                    <td>{{ $class->ten_mon }}</td>
                                    <td>{{ $class->ten_giang_vien }}</td>
                                    <td>{{ $class->si_so_toi_thieu ?? 10 }}</td>
                                    <td>{{ $class->si_so_toi_da }}</td>
                                    <td><span class="badge bg-primary">{{ $class->da_xep }}</span></td>
                                    <td>
                                        @php
                                            $tyLe = $class->si_so_toi_da > 0 ? ($class->da_xep / $class->si_so_toi_da * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe >= 90 ? 'bg-danger' : ($tyLe >= 50 ? 'bg-success' : 'bg-warning') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $tyLe }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $siSoToiThieu = $class->si_so_toi_thieu ?? 10;
                                        @endphp
                                        @if($class->da_xep >= $siSoToiThieu)
                                            <span class="badge bg-success">Đủ sĩ số</span>
                                        @else
                                            <span class="badge bg-danger">Thiếu {{ $siSoToiThieu - $class->da_xep }} SV</span>
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
