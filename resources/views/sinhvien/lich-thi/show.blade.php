@extends('layouts.sinhvien')

@section('title', 'Chi tiết Lịch thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Lịch thi</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sinhvien.lich-thi.index') }}">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Cảnh báo nếu thi hôm nay -->
        @if($lichThi->ngay_thi->isToday())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Lưu ý quan trọng!</h4>
            <p><strong>Bạn có lịch thi HÔM NAY!</strong></p>
            <hr>
            <p class="mb-0">
                Thời gian: <strong>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</strong><br>
                Phòng thi: <strong>{{ $lichThi->phongHoc->ten_phong }}</strong>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Thông tin môn thi -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title text-white mb-0">Thông tin môn thi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Môn học:</th>
                                <td><strong class="text-primary fs-5">{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</strong></td>
                            </tr>
                            <tr>
                                <th>Mã môn:</th>
                                <td>{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</td>
                            </tr>
                            <tr>
                                <th>Lớp học phần:</th>
                                <td>{{ $lichThi->lopHocPhan->ma_lop }}</td>
                            </tr>
                            <tr>
                                <th>Số tín chỉ:</th>
                                <td>{{ $lichThi->lopHocPhan->monHoc->so_tin_chi_ly_thuyet + $lichThi->lopHocPhan->monHoc->so_tin_chi_thuc_hanh }}</td>
                            </tr>
                            <tr>
                                <th>Loại thi:</th>
                                <td>
                                    @if($lichThi->loai_thi == 'giua_ky')
                                        <span class="badge bg-info fs-6">Giữa kỳ</span>
                                    @elseif($lichThi->loai_thi == 'cuoi_ky')
                                        <span class="badge bg-danger fs-6">Cuối kỳ</span>
                                    @else
                                        <span class="badge bg-warning fs-6">Thi lại</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Ngày thi:</th>
                                <td>
                                    <strong class="text-danger fs-5">{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $lichThi->ngay_thi->locale('vi')->isoFormat('dddd') }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Giờ thi:</th>
                                <td><strong>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</strong></td>
                            </tr>
                            <tr>
                                <th>Phòng thi:</th>
                                <td>
                                    <strong class="text-primary">{{ $lichThi->phongHoc->ten_phong }}</strong>
                                    @if($lichThi->phongHoc->vi_tri)
                                        <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $lichThi->phongHoc->vi_tri }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Hình thức thi:</th>
                                <td>
                                    @if($lichThi->hinh_thuc == 'offline')
                                        <span class="badge bg-secondary">Thi tại trường</span>
                                    @elseif($lichThi->hinh_thuc == 'online')
                                        <span class="badge bg-primary">Thi trực tuyến</span>
                                    @else
                                        <span class="badge bg-success">Kết hợp</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Thời gian còn lại:</th>
                                <td>
                                    @if($lichThi->ngay_thi < now()->toDateString())
                                        <span class="badge bg-success">Đã thi</span>
                                    @elseif($lichThi->ngay_thi->isToday())
                                        <span class="badge bg-warning fs-6">
                                            <i class="bi bi-exclamation-circle"></i> HÔM NAY
                                        </span>
                                    @else
                                        <span class="badge bg-info fs-6">
                                            Còn {{ $lichThi->ngay_thi->diffInDays(now()) }} ngày
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link thi online -->
        @if($lichThi->link_online)
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-link-45deg"></i> Link thi online
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <strong>Link thi:</strong>
                    <br>
                    <a href="{{ $lichThi->link_online }}" target="_blank" class="btn btn-primary mt-2">
                        <i class="bi bi-box-arrow-up-right"></i> Vào phòng thi
                    </a>
                    <br>
                    <small class="text-muted">{{ $lichThi->link_online }}</small>
                </div>
            </div>
        </div>
        @endif

        <!-- Giám thị -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Giám thị</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl bg-primary me-3">
                                <span class="avatar-content">1</span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $lichThi->giamThi1->ho_ten ?? 'Chưa phân công' }}</h6>
                                @if($lichThi->giamThi1)
                                    <small class="text-muted">{{ $lichThi->giamThi1->ma_giang_vien }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl bg-secondary me-3">
                                <span class="avatar-content">2</span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $lichThi->giamThi2->ho_ten ?? 'Chưa phân công' }}</h6>
                                @if($lichThi->giamThi2)
                                    <small class="text-muted">{{ $lichThi->giamThi2->ma_giang_vien }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ghi chú -->
        @if($lichThi->ghi_chu)
        <div class="card border-warning">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Ghi chú
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $lichThi->ghi_chu }}</p>
            </div>
        </div>
        @endif

        <!-- Lưu ý quan trọng -->
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title text-white mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Lưu ý quan trọng
                </h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Sinh viên phải có mặt trước giờ thi <strong>15 phút</strong></li>
                    <li>Mang theo <strong>thẻ sinh viên</strong> và <strong>CMND/CCCD</strong></li>
                    <li>Không mang tài liệu, điện thoại vào phòng thi (trừ khi được phép)</li>
                    <li>Tắt điện thoại trước khi vào phòng thi</li>
                    <li>Nghiêm túc thực hiện quy chế thi</li>
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
