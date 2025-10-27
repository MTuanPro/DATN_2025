@extends('layouts.layout-daotao')

@section('title', 'Chi tiết Lớp hành chính')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Lớp hành chính</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hanh-chinh.index') }}">Lớp hành
                                    chính</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $lopHanhChinh->ten_lop }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Mã lớp:</th>
                                    <td><strong>{{ $lopHanhChinh->ma_lop }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp:</th>
                                    <td>{{ $lopHanhChinh->ten_lop }}</td>
                                </tr>
                                <tr>
                                    <th>Khóa học:</th>
                                    <td>
                                        @if ($lopHanhChinh->khoaHoc)
                                            <span class="badge bg-primary">{{ $lopHanhChinh->khoaHoc->ten_khoa_hoc }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngành:</th>
                                    <td>
                                        @if ($lopHanhChinh->nganh)
                                            {{ $lopHanhChinh->nganh->ma_nganh }} - {{ $lopHanhChinh->nganh->ten_nganh }}
                                            @if ($lopHanhChinh->nganh->khoa)
                                                <br><small class="text-muted">Khoa:
                                                    {{ $lopHanhChinh->nganh->khoa->ten_khoa }}</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>GVCN:</th>
                                    <td>
                                        @if ($lopHanhChinh->giangVienChuNhiem)
                                            {{ $lopHanhChinh->giangVienChuNhiem->ho_ten }}
                                            <br><small
                                                class="text-muted">{{ $lopHanhChinh->giangVienChuNhiem->email }}</small>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><span class="badge bg-info fs-6">{{ $lopHanhChinh->si_so }} sinh viên</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Danh sách sinh viên -->
                    @if ($lopHanhChinh->sinhVien->count() > 0)
                        <hr>
                        <h6 class="mt-3 mb-3">Danh sách sinh viên ({{ $lopHanhChinh->sinhVien->count() }})</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>MSSV</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Giới tính</th>
                                        <th>SĐT</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lopHanhChinh->sinhVien as $index => $sv)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $sv->ma_sinh_vien }}</strong></td>
                                            <td>{{ $sv->ho_ten }}</td>
                                            <td>{{ $sv->email }}</td>
                                            <td>
                                                @if ($sv->gioi_tinh == 'nam')
                                                    <span class="badge bg-info">Nam</span>
                                                @elseif($sv->gioi_tinh == 'nu')
                                                    <span class="badge bg-pink">Nữ</span>
                                                @else
                                                    <span class="badge bg-secondary">Khác</span>
                                                @endif
                                            </td>
                                            <td>{{ $sv->so_dien_thoai }}</td>
                                            <td>
                                                @if ($sv->trangThaiHocTap)
                                                    <span
                                                        class="badge bg-success">{{ $sv->trangThaiHocTap->ten_trang_thai }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> Lớp chưa có sinh viên nào.
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('dao-tao.lop-hanh-chinh.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('dao-tao.lop-hanh-chinh.edit', $lopHanhChinh->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Sửa
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
