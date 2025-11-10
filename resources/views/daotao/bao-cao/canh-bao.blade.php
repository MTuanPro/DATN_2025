@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Cảnh báo học vụ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Cảnh báo học vụ</h3>
                    <p class="text-subtitle text-muted">Thống kê cảnh báo học vụ sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cảnh báo học vụ</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.canh-bao') }}">
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
                                <label>Loại cảnh báo</label>
                                <select name="loai_canh_bao" class="form-control">
                                    <option value="">Tất cả</option>
                                    <option value="hoc_vu" {{ request('loai_canh_bao') == 'hoc_vu' ? 'selected' : '' }}>Học vụ</option>
                                    <option value="diem_danh" {{ request('loai_canh_bao') == 'diem_danh' ? 'selected' : '' }}>Điểm danh</option>
                                    <option value="ky_luat" {{ request('loai_canh_bao') == 'ky_luat' ? 'selected' : '' }}>Kỷ luật</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="{{ route('dao-tao.bao-cao.canh-bao') }}" class="btn btn-secondary">
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
                                    <i class="iconly-boldDanger"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng cảnh báo</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_canh_bao']) }}</h6>
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
                                    <i class="iconly-boldInfo-Circle"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Học vụ</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['hoc_vu']) }}</h6>
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
                                <div class="stats-icon orange mb-2">
                                    <i class="iconly-boldCalendar"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Điểm danh</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['diem_danh']) }}</h6>
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
                                    <i class="iconly-boldShield-Done"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Kỷ luật</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['ky_luat']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết cảnh báo học vụ</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Học kỳ</th>
                                <th>Loại cảnh báo</th>
                                <th>Mức độ</th>
                                <th>Lý do</th>
                                <th>Ngày gửi</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warningData as $warning)
                                <tr>
                                    <td><strong>{{ $warning->ma_sinh_vien }}</strong></td>
                                    <td>{{ $warning->ho_ten }}</td>
                                    <td>{{ $warning->lop ?? 'N/A' }}</td>
                                    <td>{{ $warning->ten_hoc_ky }}</td>
                                    <td>
                                        @if($warning->loai_canh_bao == 'hoc_vu')
                                            <span class="badge bg-danger">Học vụ</span>
                                        @elseif($warning->loai_canh_bao == 'diem_danh')
                                            <span class="badge bg-warning">Điểm danh</span>
                                        @elseif($warning->loai_canh_bao == 'ky_luat')
                                            <span class="badge bg-info">Kỷ luật</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $warning->loai_canh_bao }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($warning->muc_do == 'cao')
                                            <span class="badge bg-danger">Cao</span>
                                        @elseif($warning->muc_do == 'trung_binh')
                                            <span class="badge bg-warning">Trung bình</span>
                                        @else
                                            <span class="badge bg-info">Thấp</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($warning->ly_do, 50) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($warning->ngay_gui)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($warning->trang_thai == 'da_xu_ly')
                                            <span class="badge bg-success">Đã xử lý</span>
                                        @elseif($warning->trang_thai == 'dang_xu_ly')
                                            <span class="badge bg-warning">Đang xử lý</span>
                                        @else
                                            <span class="badge bg-danger">Chưa xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có dữ liệu cảnh báo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $warningData->links() }}
                </div>
            </div>
        </div>

        <!-- Statistics by Type and Severity -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Thống kê theo loại cảnh báo</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loại</th>
                                        <th>Số lượng</th>
                                        <th>Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statsByType as $stat)
                                        <tr>
                                            <td>
                                                @if($stat->loai_canh_bao == 'hoc_vu')
                                                    <span class="badge bg-danger">Học vụ</span>
                                                @elseif($stat->loai_canh_bao == 'diem_danh')
                                                    <span class="badge bg-warning">Điểm danh</span>
                                                @else
                                                    <span class="badge bg-info">Kỷ luật</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ number_format($stat->count) }}</strong></td>
                                            <td>
                                                @php
                                                    $tyLe = $statistics['total_canh_bao'] > 0 ? ($stat->count / $statistics['total_canh_bao'] * 100) : 0;
                                                @endphp
                                                {{ number_format($tyLe, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Thống kê theo mức độ</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mức độ</th>
                                        <th>Số lượng</th>
                                        <th>Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statsBySeverity as $stat)
                                        <tr>
                                            <td>
                                                @if($stat->muc_do == 'cao')
                                                    <span class="badge bg-danger">Cao</span>
                                                @elseif($stat->muc_do == 'trung_binh')
                                                    <span class="badge bg-warning">Trung bình</span>
                                                @else
                                                    <span class="badge bg-info">Thấp</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ number_format($stat->count) }}</strong></td>
                                            <td>
                                                @php
                                                    $tyLe = $statistics['total_canh_bao'] > 0 ? ($stat->count / $statistics['total_canh_bao'] * 100) : 0;
                                                @endphp
                                                {{ number_format($tyLe, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
