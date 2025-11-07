@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết lớp ' . $lop->ma_lop)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết lớp {{ $lop->ma_lop }}</h3>
                    <p class="text-subtitle text-muted">Thống kê và thông tin lớp chủ nhiệm</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.lop-chu-nhiem.index') }}">Lớp chủ
                                    nhiệm</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $lop->ma_lop }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin lớp -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Mã lớp:</strong></td>
                                    <td>{{ $lop->ma_lop }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tên lớp:</strong></td>
                                    <td>{{ $lop->ten_lop }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Khóa học:</strong></td>
                                    <td>{{ $lop->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Ngành:</strong></td>
                                    <td>{{ $lop->nganh->ten_nganh ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sĩ số:</strong></td>
                                    <td>{{ $lop->si_so }}</td>
                                </tr>
                                <tr>
                                    <td><strong>GVCN:</strong></td>
                                    <td>{{ $giangVien->ho_ten }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['tong_sinh_vien'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="iconly-boldUser"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Nam</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['nam'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon red">
                                        <i class="iconly-boldUser"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Nữ</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['nu'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon green">
                                        <i class="iconly-boldTick-Square"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Đang học</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['dang_hoc'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Phân bố trạng thái học tập -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Phân bố trạng thái học tập</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartTrangThai"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Phân bố giới tính -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Phân bố giới tính</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartGioiTinh"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phân bố chuyên ngành -->
            @if ($phanBoChuyenNganh->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Phân bố chuyên ngành (Từ năm 3)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Chuyên ngành</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($phanBoChuyenNganh as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['ten_chuyen_nganh'] }}</td>
                                            <td class="text-center">{{ $item['so_luong'] }}</td>
                                            <td class="text-center">
                                                {{ number_format(($item['so_luong'] / $thongKe['tong_sinh_vien']) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('giangvien.lop-chu-nhiem.sinh-vien', $lop->id) }}" class="btn btn-primary">
                            <i class="bi bi-list-ul"></i> Xem danh sách sinh viên
                        </a>
                        <a href="{{ route('giangvien.lop-chu-nhiem.export-excel', $lop->id) }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                        </a>
                        <a href="{{ route('giangvien.lop-chu-nhiem.export-pdf', $lop->id) }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                        </a>
                        <a href="{{ route('giangvien.lop-chu-nhiem.index') }}" class="btn btn-secondary ms-auto">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Trạng thái học tập
        const ctxTrangThai = document.getElementById('chartTrangThai').getContext('2d');
        new Chart(ctxTrangThai, {
            type: 'doughnut',
            data: {
                labels: ['Đang học', 'Bảo lưu', 'Thôi học', 'Tốt nghiệp'],
                datasets: [{
                    data: [
                        {{ $thongKe['dang_hoc'] }},
                        {{ $thongKe['bao_luu'] }},
                        {{ $thongKe['thoi_hoc'] }},
                        {{ $thongKe['tot_nghiep'] }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#17a2b8'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Chart Giới tính
        const ctxGioiTinh = document.getElementById('chartGioiTinh').getContext('2d');
        new Chart(ctxGioiTinh, {
            type: 'pie',
            data: {
                labels: ['Nam', 'Nữ', 'Khác'],
                datasets: [{
                    data: [
                        {{ $thongKe['nam'] }},
                        {{ $thongKe['nu'] }},
                        {{ $thongKe['khac'] }}
                    ],
                    backgroundColor: [
                        '#007bff',
                        '#dc3545',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
