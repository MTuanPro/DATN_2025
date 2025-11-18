@extends('layouts.layout-giangvien')

@section('title', 'Xuất danh sách sinh viên đi thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Xuất danh sách sinh viên đi thi</h3>
                <p class="text-subtitle text-muted">Danh sách sinh viên đủ điều kiện đi thi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.lich-thi.index') }}">Lịch thi</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.lich-thi.show', $lichThi) }}">Chi tiết</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Xuất danh sách</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Thông tin lịch thi -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin lịch thi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Lớp học phần:</th>
                                <td><strong>{{ $lichThi->lopHocPhan->ma_lop_hp }}</strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td>{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</td>
                            </tr>
                            <tr>
                                <th>Loại thi:</th>
                                <td>
                                    @if($lichThi->loai_thi == 'giua_ky')
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    @elseif($lichThi->loai_thi == 'cuoi_ky')
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    @else
                                        <span class="badge bg-warning">Thi lại</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Ngày thi:</th>
                                <td><strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Giờ thi:</th>
                                <td>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</td>
                            </tr>
                            <tr>
                                <th>Tổng số buổi học:</th>
                                <td><strong>{{ $tongBuoiHoc }}</strong> buổi</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Điều kiện đi thi -->
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> Điều kiện đi thi:</h5>
            <ul class="mb-0">
                <li>Tỷ lệ có mặt phải đạt tối thiểu <strong>75%</strong> (không vắng quá 25% số buổi học)</li>
                <li>Điểm trung bình các đầu điểm phải đạt tối thiểu <strong>5.0 điểm</strong></li>
            </ul>
            <p class="mb-0 mt-2"><strong>Lưu ý:</strong> Sinh viên không đạt một trong hai điều kiện trên sẽ <span class="text-danger"><strong>KHÔNG ĐƯỢC ĐI THI</strong></span>.</p>
        </div>

        <!-- Danh sách sinh viên -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Danh sách sinh viên ({{ count($danhSachSinhVien) }} sinh viên)
                </h5>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-primary">
                        <i class="bi bi-printer"></i> In danh sách
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(empty($danhSachSinhVien))
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có sinh viên nào trong lớp học phần.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Lớp hành chính</th>
                                    <th>Chuyên cần</th>
                                    <th>Điểm TB</th>
                                    <th>Điều kiện</th>
                                    <th>Lý do</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($danhSachSinhVien as $index => $item)
                                <tr class="{{ $item['khong_duoc_di_thi'] ? 'table-danger' : 'table-success' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $item['sinh_vien']->ma_sinh_vien }}</strong></td>
                                    <td>{{ $item['sinh_vien']->ho_ten }}</td>
                                    <td>{{ $item['lop_hanh_chinh']->ten_lop ?? 'N/A' }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $item['ty_le_co_mat'] }}%</strong>
                                            <small class="text-muted d-block">
                                                (Có mặt: {{ $item['co_mat'] }}/{{ $item['tong_buoi_hoc'] }})
                                            </small>
                                        </div>
                                        @if($item['khong_dat_chuyen_can'])
                                            <span class="badge bg-danger">Không đạt</span>
                                        @else
                                            <span class="badge bg-success">Đạt</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['diem_trung_binh'] !== null)
                                            <strong>{{ number_format($item['diem_trung_binh'], 2) }}</strong>
                                            @if($item['khong_dat_diem'])
                                                <span class="badge bg-danger">Không đạt</span>
                                            @else
                                                <span class="badge bg-success">Đạt</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa có điểm</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['khong_duoc_di_thi'])
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> KHÔNG ĐƯỢC ĐI THI
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> ĐƯỢC ĐI THI
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['khong_duoc_di_thi'])
                                            <small class="text-danger">{{ $item['ly_do'] }}</small>
                                        @else
                                            <small class="text-success">Đủ điều kiện</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Thống kê -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Được đi thi</h5>
                                    <h2 class="mb-0">{{ count(array_filter($danhSachSinhVien, fn($sv) => !$sv['khong_duoc_di_thi'])) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Không được đi thi</h5>
                                    <h2 class="mb-0">{{ count(array_filter($danhSachSinhVien, fn($sv) => $sv['khong_duoc_di_thi'])) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Tổng số</h5>
                                    <h2 class="mb-0">{{ count($danhSachSinhVien) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
    @media print {
        .page-heading, .card-header .btn, nav {
            display: none !important;
        }
        .table-danger {
            background-color: #f8d7da !important;
        }
        .table-success {
            background-color: #d1e7dd !important;
        }
    }
</style>
@endpush
@endsection

