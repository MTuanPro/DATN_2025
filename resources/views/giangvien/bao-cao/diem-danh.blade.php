@extends('layouts.layout-giangvien')

@section('title', 'Báo cáo điểm danh')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Báo cáo điểm danh</h3>
                <p class="text-subtitle text-muted">Thống kê tỷ lệ có mặt, vắng, đi trễ theo từng lớp</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.bao-cao.index') }}">Báo cáo</a></li>
                        <li class="breadcrumb-item active">Điểm danh</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('giangvien.bao-cao.diem-danh') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Học kỳ</label>
                    <select name="hoc_ky_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($hocKys as $hk)
                        <option value="{{ $hk->id }}" {{ $hocKyId == $hk->id ? 'selected' : '' }}>
                            {{ $hk->ten_hoc_ky }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lớp học phần</label>
                    <select name="lop_hoc_phan_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($allLopHocPhans as $lhp)
                        <option value="{{ $lhp->id }}" {{ $lopHocPhanId == $lhp->id ? 'selected' : '' }}>
                            {{ $lhp->ma_lop_hp }} - {{ $lhp->monHoc->ten_mon ?? '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                    <a href="{{ route('giangvien.bao-cao.diem-danh') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Xuất
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('giangvien.bao-cao.export-excel', ['loai' => 'diem-danh'] + request()->all()) }}">
                                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('giangvien.bao-cao.export-pdf', ['loai' => 'diem-danh'] + request()->all()) }}">
                                    <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng thống kê -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Thống kê điểm danh</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã lớp</th>
                            <th>Môn học</th>
                            <th class="text-center">Tổng</th>
                            <th class="text-center">Có mặt</th>
                            <th class="text-center">Vắng</th>
                            <th class="text-center">Đi trễ</th>
                            <th class="text-center">Nghỉ phép</th>
                            <th class="text-center">Tỷ lệ có mặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $tongCoMat = 0; $tongVang = 0; $tongDiTre = 0; $tongNghiPhep = 0; $tongTong = 0; @endphp
                        @forelse($thongKe as $index => $item)
                        @php
                            $tongCoMat += $item['co_mat'];
                            $tongVang += $item['vang'];
                            $tongDiTre += $item['di_tre'];
                            $tongNghiPhep += $item['nghi_phep'];
                            $tongTong += $item['tong'];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item['lop']->ma_lop_hp }}</strong></td>
                            <td>{{ $item['lop']->monHoc->ten_mon ?? '' }}</td>
                            <td class="text-center">{{ $item['tong'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $item['co_mat'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $item['vang'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">{{ $item['di_tre'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $item['nghi_phep'] }}</span>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $item['ty_le_co_mat'] >= 80 ? 'success' : ($item['ty_le_co_mat'] >= 60 ? 'warning' : 'danger') }}">
                                    {{ $item['ty_le_co_mat'] }}%
                                </strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có dữ liệu</td>
                        </tr>
                        @endforelse
                        @if(count($thongKe) > 0)
                        <tr class="table-info fw-bold">
                            <td colspan="3" class="text-end">TỔNG CỘNG:</td>
                            <td class="text-center">{{ $tongTong }}</td>
                            <td class="text-center">{{ $tongCoMat }}</td>
                            <td class="text-center">{{ $tongVang }}</td>
                            <td class="text-center">{{ $tongDiTre }}</td>
                            <td class="text-center">{{ $tongNghiPhep }}</td>
                            <td class="text-center">
                                @php
                                    $tyLeTong = $tongTong > 0 ? round(($tongCoMat / $tongTong) * 100, 2) : 0;
                                @endphp
                                {{ $tyLeTong }}%
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
