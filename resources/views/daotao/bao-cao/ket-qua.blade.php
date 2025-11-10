@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Kết quả học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Kết quả học tập</h3>
                    <p class="text-subtitle text-muted">Phân bố điểm, GPA, tỷ lệ qua môn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kết quả học tập</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.ket-qua') }}">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Khóa học</label>
                                <select name="khoa_hoc_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($khoaHocs as $kh)
                                        <option value="{{ $kh->id }}" {{ request('khoa_hoc_id') == $kh->id ? 'selected' : '' }}>
                                            {{ $kh->ten_khoa_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="{{ route('dao-tao.bao-cao.ket-qua') }}" class="btn btn-secondary">
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
                                    <i class="iconly-boldDocument"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng kết quả</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_ket_qua']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Qua môn</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['qua_mon']) }}</h6>
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
                                    <i class="iconly-boldClose-Square"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Trượt môn</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['truot_mon']) }}</h6>
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
                                    <i class="iconly-boldStar"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">GPA trung bình</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['avg_gpa'], 2) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Phân bố điểm chữ</h5>
                    </div>
                    <div class="card-body">
                        <div id="gradeDistributionChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">GPA theo khóa học</h5>
                    </div>
                    <div class="card-body">
                        <div id="gpaByKhoaChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết kết quả học tập</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th>Lớp HP</th>
                                <th>SL sinh viên</th>
                                <th>Điểm TB</th>
                                <th>Qua môn</th>
                                <th>Trượt môn</th>
                                <th>Tỷ lệ qua</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailedResults as $result)
                                <tr>
                                    <td><strong>{{ $result->ten_mon }}</strong></td>
                                    <td>{{ $result->ma_lop_hp }}</td>
                                    <td>{{ number_format($result->total) }}</td>
                                    <td>{{ number_format($result->avg_diem, 2) }}</td>
                                    <td><span class="badge bg-success">{{ $result->qua_mon }}</span></td>
                                    <td><span class="badge bg-danger">{{ $result->truot_mon }}</span></td>
                                    <td>
                                        @php
                                            $tyLe = $result->total > 0 ? ($result->qua_mon / $result->total * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe >= 80 ? 'bg-success' : ($tyLe >= 50 ? 'bg-warning' : 'bg-danger') }}" 
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

@push('scripts')
<script>
    // Grade Distribution Chart
    var gradeDistOptions = {
        series: @json(array_values($gradeDistribution->pluck('count')->toArray())),
        labels: @json($gradeDistribution->pluck('diem_chu')->toArray()),
        chart: {
            type: 'donut',
            height: 350
        },
        colors: ['#28a745', '#20c997', '#17a2b8', '#ffc107', '#fd7e14', '#dc3545', '#6c757d'],
        legend: {
            position: 'bottom'
        }
    };
    var gradeDistChart = new ApexCharts(document.querySelector("#gradeDistributionChart"), gradeDistOptions);
    gradeDistChart.render();

    // GPA by Khoa Chart
    var gpaOptions = {
        series: [{
            name: 'GPA trung bình',
            data: @json($gpaByKhoa->pluck('avg_gpa')->toArray())
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2);
            }
        },
        xaxis: {
            categories: @json($gpaByKhoa->pluck('ten_khoa_hoc')->toArray())
        },
        yaxis: {
            title: {
                text: 'GPA'
            },
            min: 0,
            max: 4
        },
        colors: ['#435ebe']
    };
    var gpaChart = new ApexCharts(document.querySelector("#gpaByKhoaChart"), gpaOptions);
    gpaChart.render();
</script>
@endpush
