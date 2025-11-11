@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Sinh viên')

@push('styles')
    <!-- Add custom styles if needed -->
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/export-report.js') }}"></script>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Sinh viên</h3>
                    <p class="text-subtitle text-muted">Thống kê sinh viên theo khoa, ngành, khóa học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sinh viên</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.sinh-vien') }}" id="filterForm" data-report-type="sinh-vien">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Khoa</label>
                                <select name="khoa_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($khoas as $khoa)
                                        <option value="{{ $khoa->id }}" {{ request('khoa_id') == $khoa->id ? 'selected' : '' }}>
                                            {{ $khoa->ten_khoa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ngành</label>
                                <select name="nganh_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($nganhs as $nganh)
                                        <option value="{{ $nganh->id }}" {{ request('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                            {{ $nganh->ten_nganh }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Khóa học</label>
                                <select name="khoa_hoc_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($khoaHocs as $khoaHoc)
                                        <option value="{{ $khoaHoc->id }}" {{ request('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                                            {{ $khoaHoc->ten_khoa_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Lớp hành chính</label>
                                <input type="text" name="lop" class="form-control" value="{{ request('lop') }}" placeholder="Nhập tên lớp">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter"></i> Lọc
                            </button>
                            <a href="{{ route('dao-tao.bao-cao.sinh-vien') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                            </a>
                            
                            <!-- Export Buttons Component -->
                            <div class="float-end">
                                <x-export-buttons report-type="sinh-vien" />
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
                                    <i class="iconly-boldUser"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Đang học</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['hoc']) }}</h6>
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
                                    <i class="iconly-boldPause"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Bảo lưu</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['bao_luu']) }}</h6>
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
                                <h6 class="text-muted font-semibold">Thôi học</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['thoi_hoc']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Danh sách sinh viên ({{ $sinhViens->total() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Ngày sinh</th>
                                <th>Khoa</th>
                                <th>Ngành</th>
                                <th>Khóa học</th>
                                <th>Lớp</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sinhViens as $sv)
                                <tr>
                                    <td><strong>{{ $sv->ma_sinh_vien }}</strong></td>
                                    <td>{{ $sv->ho_ten }}</td>
                                    <td>{{ $sv->email }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sv->ngay_sinh)->format('d/m/Y') }}</td>
                                    <td>{{ $sv->chuyenNganh->nganh->khoa->ten_khoa ?? 'N/A' }}</td>
                                    <td>{{ $sv->chuyenNganh->nganh->ten_nganh ?? 'N/A' }}</td>
                                    <td>{{ $sv->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</td>
                                    <td>{{ $sv->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $trangThai = $sv->trangThaiHocTap->ten_trang_thai ?? 'N/A';
                                        @endphp
                                        @if($trangThai == 'Đang học')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($trangThai == 'Bảo lưu')
                                            <span class="badge bg-warning">Bảo lưu</span>
                                        @elseif($trangThai == 'Thôi học')
                                            <span class="badge bg-danger">Thôi học</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $trangThai }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $sinhViens->links() }}
                </div>
            </div>
        </div>

        <!-- Statistics by Khoa -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thống kê theo Khoa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Khoa</th>
                                <th>Tổng SV</th>
                                <th>Đang học</th>
                                <th>Bảo lưu</th>
                                <th>Thôi học</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsByKhoa as $stat)
                                <tr>
                                    <td>{{ $stat->ten_khoa }}</td>
                                    <td><strong>{{ number_format($stat->total) }}</strong></td>
                                    <td>{{ number_format($stat->dang_hoc) }}</td>
                                    <td>{{ number_format($stat->bao_luu) }}</td>
                                    <td>{{ number_format($stat->thoi_hoc) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
