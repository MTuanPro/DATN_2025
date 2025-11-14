@extends('layouts.layout-giangvien')

@section('title', 'Phân tích điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Phân tích điểm</h3>
                    <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.ket-qua-hoc-tap.index') }}">Kết quả học
                                    tập</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Phân tích</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thống kê tổng quan -->
        <section class="section">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Tổng SV</h6>
                                    <h3 class="font-extrabold mb-0">{{ $thongKe['tong_sv'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Qua môn</h6>
                                    <h3 class="font-extrabold mb-0 text-success">{{ $thongKe['sv_qua_mon'] }}</h3>
                                    <small class="text-muted">
                                        {{ $thongKe['tong_sv'] > 0 ? round(($thongKe['sv_qua_mon'] / $thongKe['tong_sv']) * 100, 2) : 0 }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-x-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Trượt môn</h6>
                                    <h3 class="font-extrabold mb-0 text-danger">{{ $thongKe['sv_truot'] }}</h3>
                                    <small class="text-muted">
                                        {{ $thongKe['tong_sv'] > 0 ? round(($thongKe['sv_truot'] / $thongKe['tong_sv']) * 100, 2) : 0 }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Điểm TB</h6>
                                    <h3 class="font-extrabold mb-0 text-primary">
                                        {{ number_format($thongKe['diem_trung_binh'], 2) }}
                                    </h3>
                                    <small class="text-muted">Hệ 10</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Chi tiết điểm -->
        <section class="section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Điểm cao nhất & thấp nhất</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h6 class="text-muted mb-2">Điểm cao nhất</h6>
                                    <h2 class="text-success mb-0">{{ number_format($thongKe['diem_cao_nhat'], 2) }}</h2>
                                    <small class="text-muted">Hệ 10</small>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted mb-2">Điểm thấp nhất</h6>
                                    <h2 class="text-danger mb-0">{{ number_format($thongKe['diem_thap_nhat'], 2) }}</h2>
                                    <small class="text-muted">Hệ 10</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tỷ lệ qua/trượt</h4>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3" style="height: 30px;">
                                @php
                                    $tyLeQua = $thongKe['tong_sv'] > 0 ? ($thongKe['sv_qua_mon'] / $thongKe['tong_sv']) * 100 : 0;
                                    $tyLeTruot = 100 - $tyLeQua;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tyLeQua }}%"
                                    aria-valuenow="{{ $tyLeQua }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($tyLeQua, 1) }}%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $tyLeTruot }}%"
                                    aria-valuenow="{{ $tyLeTruot }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($tyLeTruot, 1) }}%
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <span class="badge bg-success">Qua môn: {{ $thongKe['sv_qua_mon'] }} SV</span>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-danger">Trượt: {{ $thongKe['sv_truot'] }} SV</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Phân bố điểm chữ -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân bố điểm theo loại (A, B+, B, C+, C, D+, D, F)</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach (['A' => 'success', 'B+' => 'primary', 'B' => 'primary', 'C+' => 'info', 'C' => 'info', 'D+' => 'warning', 'D' => 'warning', 'F' => 'danger'] as $loai => $color)
                            <div class="col-md-3 col-6 mb-4">
                                <div class="card border-{{ $color }}">
                                    <div class="card-body text-center">
                                        <h6 class="text-muted mb-2">Loại {{ $loai }}</h6>
                                        <h2 class="text-{{ $color }} mb-0">
                                            {{ $phanBoDiemChu[$loai] ?? 0 }}
                                        </h2>
                                        <small class="text-muted">sinh viên</small>
                                        @if ($thongKe['tong_sv'] > 0)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    {{ number_format((($phanBoDiemChu[$loai] ?? 0) / $thongKe['tong_sv']) * 100, 1) }}%
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Phân bố theo khoảng điểm -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân bố theo khoảng điểm</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartPhanBoDiem" height="80"></canvas>
                </div>
            </div>
        </section>

        <!-- Nút quay lại -->
        <section class="section">
            <a href="{{ route('giangvien.ket-qua-hoc-tap.show', $lopHocPhan->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại bảng điểm
            </a>
            <a href="{{ route('giangvien.ket-qua-hoc-tap.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Danh sách lớp
            </a>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartPhanBoDiem');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($phanBoKhoang)) !!},
                datasets: [{
                    label: 'Số sinh viên',
                    data: {!! json_encode(array_values($phanBoKhoang)) !!},
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(108, 117, 125, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(255, 152, 0, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(220, 53, 69, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Biểu đồ phân bố điểm'
                    }
                }
            }
        });
    </script>
@endpush
