@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết lớp học phần</h3>
                    <p class="text-subtitle text-muted">
                        {{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }} - {{ $lopHocPhanSinhVien->lopHocPhan->ten_lop_hp }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.lop-hoc-phan.index') }}">Lớp học phần</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Nút hành động --}}
            <div class="mb-3">
                <a href="{{ route('sinh-vien.lop-hoc-phan.lich-su-diem-danh', $lopHocPhanSinhVien->id) }}" class="btn btn-primary">
                    <i class="bi bi-calendar-check"></i> Xem lịch sử điểm danh
                </a>
                <a href="{{ route('sinh-vien.lop-hoc-phan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            {{-- Thông tin lớp học phần --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> Thông tin lớp học phần
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Mã lớp HP:</th>
                                    <td><strong>{{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp HP:</th>
                                    <td>{{ $lopHocPhanSinhVien->lopHocPhan->ten_lop_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>
                                        <strong>{{ $lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Số tín chỉ:</th>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi }} TC
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Học kỳ:</th>
                                    <td>
                                        {{ $lopHocPhanSinhVien->lopHocPhan->hocKy->ten_hoc_ky }}
                                        <br>
                                        <small class="text-muted">{{ $lopHocPhanSinhVien->lopHocPhan->hocKy->nam_hoc }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Giảng viên:</th>
                                    <td>
                                        @if($lopHocPhanSinhVien->lopHocPhan->giangVienChinh && $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien)
                                            {{ $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien->ho_ten }}
                                            <br>
                                            <small class="text-muted">{{ $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien->email }}</small>
                                        @else
                                            <span class="text-muted">Chưa phân công</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hình thức:</th>
                                    <td>
                                        @if($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'offline')
                                            <span class="badge bg-success">Trực tiếp</span>
                                        @elseif($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @elseif($lopHocPhanSinhVien->lopHocPhan->hinh_thuc == 'hybrid')
                                            <span class="badge bg-warning">Hybrid</span>
                                        @endif
                                        @if($lopHocPhanSinhVien->lopHocPhan->link_online)
                                            <br>
                                            <a href="{{ $lopHocPhanSinhVien->lopHocPhan->link_online }}" target="_blank" class="text-primary">
                                                <i class="bi bi-link-45deg"></i> Link lớp học
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if($lopHocPhanSinhVien->trang_thai == 'da_xep_lop')
                                            <span class="badge bg-info">Đã xếp lớp</span>
                                        @elseif($lopHocPhanSinhVien->trang_thai == 'dang_hoc')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($lopHocPhanSinhVien->trang_thai == 'da_hoan_thanh')
                                            <span class="badge bg-primary">Đã hoàn thành</span>
                                        @elseif($lopHocPhanSinhVien->trang_thai == 'bo_hoc')
                                            <span class="badge bg-danger">Bỏ học</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $lopHocPhanSinhVien->trang_thai }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày đăng ký:</th>
                                    <td>{{ $lopHocPhanSinhVien->ngay_dang_ky ? $lopHocPhanSinhVien->ngay_dang_ky->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày xếp lớp:</th>
                                    <td>{{ $lopHocPhanSinhVien->ngay_xep_lop ? $lopHocPhanSinhVien->ngay_xep_lop->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kết quả học tập --}}
            @if($ketQuaHocTap)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy"></i> Kết quả học tập
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm hệ 10</h6>
                                <h2 class="text-primary mb-0">{{ number_format($ketQuaHocTap->diem_he_10, 2) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm hệ 4</h6>
                                <h2 class="text-info mb-0">{{ number_format($ketQuaHocTap->diem_he_4, 2) }}</h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Điểm chữ</h6>
                                <h2 class="text-success mb-0">{{ $ketQuaHocTap->diem_chu }}</h2>
                                @if($ketQuaHocTap->qua_mon)
                                    <span class="badge bg-success mt-2">Qua môn</span>
                                @else
                                    <span class="badge bg-danger mt-2">Không qua môn</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Lịch học cố định --}}
            @if($lichHocCoDinh->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-calendar-week"></i> Lịch học cố định
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th>Tiết</th>
                                    <th>Giờ học</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lichHocCoDinh as $lich)
                                    <tr>
                                        <td>
                                            <strong>{{ $lich->ten_thu ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            Tiết {{ $lich->tiet_bat_dau }}
                                            @if($lich->tiet_ket_thuc != $lich->tiet_bat_dau)
                                                - {{ $lich->tiet_ket_thuc }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($lich->gio_bat_dau && $lich->gio_ket_thuc)
                                                {{ $lich->gio_bat_dau->format('H:i') }} - {{ $lich->gio_ket_thuc->format('H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            {{ $lich->phongHoc->ten_phong ?? 'TBA' }}
                                        </td>
                                        <td>
                                            {{ $lich->giangVien->ho_ten ?? 'TBA' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Thông tin thời gian --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-clock"></i> Thời gian học
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Ngày bắt đầu:</th>
                                    <td>{{ $lopHocPhanSinhVien->lopHocPhan->ngay_bat_dau ? \Carbon\Carbon::parse($lopHocPhanSinhVien->lopHocPhan->ngay_bat_dau)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày kết thúc:</th>
                                    <td>{{ $lopHocPhanSinhVien->lopHocPhan->ngay_ket_thuc ? \Carbon\Carbon::parse($lopHocPhanSinhVien->lopHocPhan->ngay_ket_thuc)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($lopHocPhanSinhVien->lopHocPhan->ghi_chu)
                            <div>
                                <strong>Ghi chú:</strong>
                                <p class="text-muted">{{ $lopHocPhanSinhVien->lopHocPhan->ghi_chu }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nút thao tác --}}
            <div class="d-flex justify-content-between gap-2">
                <a href="{{ route('sinh-vien.lop-hoc-phan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <div class="btn-group">
                    <a href="{{ route('sinh-vien.diem.show', $lopHocPhanSinhVien->lopHocPhan->id) }}" class="btn btn-primary">
                        <i class="bi bi-clipboard-check"></i> Xem điểm
                    </a>
                    <a href="{{ route('sinh-vien.thoi-khoa-bieu.index', ['hoc_ky_id' => $lopHocPhanSinhVien->lopHocPhan->hoc_ky_id]) }}" class="btn btn-info">
                        <i class="bi bi-calendar-week"></i> Thời khóa biểu
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection

