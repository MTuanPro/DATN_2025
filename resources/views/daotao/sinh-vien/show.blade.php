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
            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> {{ $sinhVien->ho_ten }} - {{ $sinhVien->ma_sinh_vien }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('dao-tao.sinh-vien.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('dao-tao.sinh-vien.edit', $sinhVien->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Sửa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                                <i class="bi bi-info-circle"></i> Thông tin cơ bản
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab">
                                <i class="bi bi-clipboard-data"></i> Điểm số
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="bi bi-clock-history"></i> Lịch sử học tập
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="studentTabsContent">
                        <!-- Tab 1: Thông tin cơ bản -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="bi bi-person-badge"></i> Thông tin cá nhân</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">MSSV:</th>
                                            <td><strong class="text-primary">{{ $sinhVien->ma_sinh_vien }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Họ tên:</th>
                                            <td>{{ $sinhVien->ho_ten }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><i class="bi bi-envelope"></i> {{ $sinhVien->email }}</td>
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
                                                    <span class="badge bg-danger">Nữ</span>
                                                @else
                                                    <span class="badge bg-secondary">Khác</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>SĐT:</th>
                                            <td><i class="bi bi-telephone"></i> {{ $sinhVien->so_dien_thoai }}</td>
                                        </tr>
                                        <tr>
                                            <th>CCCD:</th>
                                            <td>{{ $sinhVien->can_cuoc_cong_dan }}</td>
                                        </tr>
                                        @if($sinhVien->so_nha_duong || $sinhVien->phuong_xa || $sinhVien->quan_huyen || $sinhVien->tinh_thanh)
                                        <tr>
                                            <th>Địa chỉ:</th>
                                            <td>
                                                {{ $sinhVien->so_nha_duong }}
                                                @if($sinhVien->phuong_xa), {{ $sinhVien->phuong_xa }}@endif
                                                @if($sinhVien->quan_huyen), {{ $sinhVien->quan_huyen }}@endif
                                                @if($sinhVien->tinh_thanh), {{ $sinhVien->tinh_thanh }}@endif
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="bi bi-mortarboard"></i> Thông tin học vụ</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Khóa học:</th>
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
                                                        <br><small class="text-muted">Khoa: {{ $sinhVien->nganh->khoa->ten_khoa }}</small>
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
                                                    <span class="badge bg-success">{{ $sinhVien->trangThaiHocTap->ten_trang_thai }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($sinhVien->giangVienChuNhiem)
                                        <tr>
                                            <th>GVCN:</th>
                                            <td>{{ $sinhVien->giangVienChuNhiem->ho_ten }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Điểm số -->
                        <div class="tab-pane fade" id="grades" role="tabpanel">
                            <!-- Thống kê tổng quan -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">GPA Tích lũy</h5>
                                            <h2 class="mb-0">{{ number_format($gpaTichLuy, 2) }}</h2>
                                            <small>Hệ 4.0</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Tín chỉ đã đạt</h5>
                                            <h2 class="mb-0">{{ $tongTinChiDat }}</h2>
                                            <small>Tín chỉ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Tín chỉ đã học</h5>
                                            <h2 class="mb-0">{{ $tongTinChiHoc }}</h2>
                                            <small>Tín chỉ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Tỷ lệ đạt</h5>
                                            <h2 class="mb-0">
                                                {{ $tongTinChiHoc > 0 ? number_format(($tongTinChiDat / $tongTinChiHoc) * 100, 1) : 0 }}%
                                            </h2>
                                            <small>Đạt / Tổng</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng điểm theo học kỳ -->
                            @if($bangDiems->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Bảng điểm theo học kỳ</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Học kỳ</th>
                                                    <th>Tín chỉ đăng ký</th>
                                                    <th>Tín chỉ đạt</th>
                                                    <th>Điểm TB (Hệ 10)</th>
                                                    <th>Điểm TB (Hệ 4)</th>
                                                    <th>Xếp loại</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($bangDiems as $bd)
                                                <tr>
                                                    <td>
                                                        @if($bd->hocKy)
                                                            <strong>{{ $bd->hocKy->ten_hoc_ky }}</strong>
                                                            <br><small class="text-muted">{{ $bd->hocKy->nam_hoc }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $bd->tong_tin_chi_dang_ky }}</td>
                                                    <td class="text-center">{{ $bd->tong_tin_chi_dat }}</td>
                                                    <td class="text-center"><strong>{{ number_format($bd->diem_trung_binh_he_10, 2) }}</strong></td>
                                                    <td class="text-center"><strong>{{ number_format($bd->diem_trung_binh_he_4, 2) }}</strong></td>
                                                    <td>
                                                        @php
                                                            $badgeClass = 'secondary';
                                                            if($bd->xep_loai_hoc_tap == 'xuat_sac') $badgeClass = 'success';
                                                            elseif($bd->xep_loai_hoc_tap == 'gioi') $badgeClass = 'info';
                                                            elseif($bd->xep_loai_hoc_tap == 'kha') $badgeClass = 'primary';
                                                            elseif($bd->xep_loai_hoc_tap == 'trung_binh') $badgeClass = 'warning';
                                                            elseif(in_array($bd->xep_loai_hoc_tap, ['yeu', 'kem'])) $badgeClass = 'danger';
                                                        @endphp
                                                        <span class="badge bg-{{ $badgeClass }}">
                                                            {{ ucfirst(str_replace('_', ' ', $bd->xep_loai_hoc_tap)) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Chi tiết điểm từng môn -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-list-check"></i> Chi tiết điểm từng môn học</h6>
                                </div>
                                <div class="card-body">
                                    @if($monHocsTheoHocKy->count() > 0)
                                        @foreach($monHocsTheoHocKy as $hocKyId => $monHocs)
                                            @php
                                                $hocKy = $monHocs->first()->lopHocPhan->hocKy ?? null;
                                            @endphp
                                            <div class="mb-4">
                                                <h6 class="text-primary mb-3">
                                                    <i class="bi bi-calendar3"></i> 
                                                    @if($hocKy)
                                                        {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                                    @else
                                                        Chưa xác định
                                                    @endif
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>STT</th>
                                                                <th>Mã môn</th>
                                                                <th>Tên môn học</th>
                                                                <th>Tín chỉ</th>
                                                                <th>Điểm hệ 10</th>
                                                                <th>Điểm hệ 4</th>
                                                                <th>Điểm chữ</th>
                                                                <th>Kết quả</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($monHocs as $index => $lhpsv)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $lhpsv->lopHocPhan->monHoc->ma_mon_hoc ?? '-' }}</td>
                                                                <td>{{ $lhpsv->lopHocPhan->monHoc->ten_mon_hoc ?? '-' }}</td>
                                                                <td class="text-center">{{ $lhpsv->lopHocPhan->monHoc->so_tin_chi ?? '-' }}</td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_10 !== null)
                                                                        <strong>{{ number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) }}</strong>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_4 !== null)
                                                                        <strong>{{ number_format($lhpsv->ketQuaHocTap->diem_he_4, 2) }}</strong>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_chu)
                                                                        <span class="badge bg-{{ $lhpsv->ketQuaHocTap->diem_chu_badge }}">
                                                                            {{ $lhpsv->ketQuaHocTap->diem_chu }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap)
                                                                        @if($lhpsv->ketQuaHocTap->qua_mon)
                                                                            <span class="badge bg-success">Đạt</span>
                                                                        @else
                                                                            <span class="badge bg-danger">Không đạt</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="bi bi-info-circle"></i> Sinh viên chưa có điểm số nào.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Lịch sử học tập -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <!-- Các môn đang học -->
                            @if($lopDangHoc->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-book-half"></i> Các môn đang học ({{ $lopDangHoc->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã môn</th>
                                                    <th>Tên môn học</th>
                                                    <th>Tín chỉ</th>
                                                    <th>Học kỳ</th>
                                                    <th>Giảng viên</th>
                                                    <th>Trạng thái</th>
                                                    <th>Ngày đăng ký</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lopDangHoc as $index => $lhpsv)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $lhpsv->lopHocPhan->monHoc->ma_mon_hoc ?? '-' }}</td>
                                                    <td>{{ $lhpsv->lopHocPhan->monHoc->ten_mon_hoc ?? '-' }}</td>
                                                    <td class="text-center">{{ $lhpsv->lopHocPhan->monHoc->so_tin_chi ?? '-' }}</td>
                                                    <td>
                                                        @if($lhpsv->lopHocPhan->hocKy)
                                                            {{ $lhpsv->lopHocPhan->hocKy->ten_hoc_ky }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lhpsv->lopHocPhan->giangVien)
                                                            {{ $lhpsv->lopHocPhan->giangVien->ho_ten }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $lhpsv->trang_thai)) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($lhpsv->ngay_dang_ky)
                                                            {{ $lhpsv->ngay_dang_ky->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Lịch sử các môn đã học -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử các môn đã học</h6>
                                </div>
                                <div class="card-body">
                                    @if($monHocsTheoHocKy->count() > 0)
                                        @foreach($monHocsTheoHocKy as $hocKyId => $monHocs)
                                            @php
                                                $hocKy = $monHocs->first()->lopHocPhan->hocKy ?? null;
                                            @endphp
                                            <div class="mb-4">
                                                <h6 class="text-primary mb-3">
                                                    <i class="bi bi-calendar3"></i> 
                                                    @if($hocKy)
                                                        {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                                    @else
                                                        Chưa xác định
                                                    @endif
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>STT</th>
                                                                <th>Mã môn</th>
                                                                <th>Tên môn học</th>
                                                                <th>Tín chỉ</th>
                                                                <th>Giảng viên</th>
                                                                <th>Điểm</th>
                                                                <th>Kết quả</th>
                                                                <th>Ngày đăng ký</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($monHocs as $index => $lhpsv)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $lhpsv->lopHocPhan->monHoc->ma_mon_hoc ?? '-' }}</td>
                                                                <td>{{ $lhpsv->lopHocPhan->monHoc->ten_mon_hoc ?? '-' }}</td>
                                                                <td class="text-center">{{ $lhpsv->lopHocPhan->monHoc->so_tin_chi ?? '-' }}</td>
                                                                <td>
                                                                    @if($lhpsv->lopHocPhan->giangVien)
                                                                        {{ $lhpsv->lopHocPhan->giangVien->ho_ten }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_10 !== null)
                                                                        <strong>{{ number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) }}</strong>
                                                                        <br><small class="text-muted">({{ $lhpsv->ketQuaHocTap->diem_chu ?? '-' }})</small>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($lhpsv->ketQuaHocTap)
                                                                        @if($lhpsv->ketQuaHocTap->qua_mon)
                                                                            <span class="badge bg-success">Đạt</span>
                                                                        @else
                                                                            <span class="badge bg-danger">Không đạt</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($lhpsv->ngay_dang_ky)
                                                                        {{ $lhpsv->ngay_dang_ky->format('d/m/Y') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="bi bi-info-circle"></i> Sinh viên chưa có lịch sử học tập nào.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
