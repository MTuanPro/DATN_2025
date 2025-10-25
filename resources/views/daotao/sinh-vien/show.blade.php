@extends('layouts.layout-daotao')

@section('title', 'Chi tiết Sinh viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Sinh viên</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.sinh-vien.index') }}">Sinh viên</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $sinhVien->ho_ten }} - {{ $sinhVien->ma_sinh_vien }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Thông tin cơ bản</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">MSSV:</th>
                                    <td><strong>{{ $sinhVien->ma_sinh_vien }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Họ tên:</th>
                                    <td>{{ $sinhVien->ho_ten }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $sinhVien->email }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh:</th>
                                    <td>{{ $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Giới tính:</th>
                                    <td>
                                        @if ($sinhVien->gioi_tinh == 'nam')
                                            <span class="badge bg-info">Nam</span>
                                        @elseif($sinhVien->gioi_tinh == 'nu')
                                            <span class="badge bg-pink">Nữ</span>
                                        @else
                                            <span class="badge bg-secondary">Khác</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>SĐT:</th>
                                    <td>{{ $sinhVien->so_dien_thoai }}</td>
                                </tr>
                                <tr>
                                    <th>CCCD:</th>
                                    <td>{{ $sinhVien->can_cuoc_cong_dan }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Thông tin học vụ</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Lớp:</th>
                                    <td>
                                        @if ($sinhVien->lopHanhChinh)
                                            <span class="badge bg-secondary">{{ $sinhVien->lopHanhChinh->ma_lop }}</span>
                                            <br>{{ $sinhVien->lopHanhChinh->ten_lop }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Khóa học:</th>
                                    <td>
                                        @if ($sinhVien->khoaHoc)
                                            <span class="badge bg-primary">{{ $sinhVien->khoaHoc->ten_khoa_hoc }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngành:</th>
                                    <td>
                                        @if ($sinhVien->nganh)
                                            {{ $sinhVien->nganh->ma_nganh }} - {{ $sinhVien->nganh->ten_nganh }}
                                            @if ($sinhVien->nganh->khoa)
                                                <br><small class="text-muted">Khoa:
                                                    {{ $sinhVien->nganh->khoa->ten_khoa }}</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Chuyên ngành:</th>
                                    <td>
                                        @if ($sinhVien->chuyenNganh)
                                            {{ $sinhVien->chuyenNganh->ten_chuyen_nganh }}
                                        @else
                                            <span class="text-muted">Chưa chọn</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kỳ hiện tại:</th>
                                    <td><span class="badge bg-info">Kỳ {{ $sinhVien->ky_hien_tai }}</span></td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if ($sinhVien->trangThaiHocTap)
                                            <span
                                                class="badge bg-success">{{ $sinhVien->trangThaiHocTap->ten_trang_thai }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>GVCN:</th>
                                    <td>
                                        @if ($sinhVien->giangVienChuNhiem)
                                            {{ $sinhVien->giangVienChuNhiem->ho_ten }}
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('dao-tao.sinh-vien.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('dao-tao.sinh-vien.edit', $sinhVien->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Sửa
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
