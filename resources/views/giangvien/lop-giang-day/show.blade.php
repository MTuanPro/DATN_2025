@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Chi tiết lớp học phần</h3>
                <p class="text-subtitle text-muted">
                    <strong>{{ $lopHocPhan->ma_lop_hp }}</strong> - {{ $lopHocPhan->ten_lop_hp }}
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(!isset($lopHocPhan->da_ket_thuc) || !$lopHocPhan->da_ket_thuc)
                    <a href="{{ route('giangvien.nhap-diem.show', $lopHocPhan->id) }}" 
                       class="btn btn-success">
                        <i class="bi bi-pencil-square"></i> Nhập điểm
                    </a>
                @endif
            <a href="{{ route('giangvien.lop-giang-day.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="classTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" 
                            id="info-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#info" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-info-circle"></i> Thông tin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="results-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#results" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-trophy"></i> Kết quả học tập
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="classTabContent">
                <!-- Tab Thông tin -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <!-- Thông tin tổng quan -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0">{{ $sinhViens->count() }}</h5>
                                    <p class="text-muted mb-0">Sinh viên</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-check text-success" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0">{{ $lopHocPhan->suc_chua }}</h5>
                                    <p class="text-muted mb-0">Sức chứa</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-book text-info" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0">{{ $lopHocPhan->monHoc->so_tin_chi ?? 0 }}</h5>
                                    <p class="text-muted mb-0">Tín chỉ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2 mb-0">
                                        @if($lopHocPhan->trang_thai_lop == 'mo_dang_ky')
                                            <span class="badge bg-warning">Mở đăng ký</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'dang_hoc')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'ket_thuc')
                                            <span class="badge bg-secondary">Kết thúc</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'da_khoa_diem')
                                            <span class="badge bg-danger">Đã khóa điểm</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $lopHocPhan->trang_thai_lop }}</span>
                                        @endif
                                    </h5>
                                    <p class="text-muted mb-0 mt-1">Trạng thái</p>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Thông tin lớp học phần -->
            <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Thông tin lớp học phần
                            </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                            <th style="width: 40%;" class="text-muted">Mã lớp HP:</th>
                                            <td><strong class="text-primary">{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Tên lớp HP:</th>
                                    <td>{{ $lopHocPhan->ten_lop_hp }}</td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Môn học:</th>
                                    <td>
                                                <strong>{{ $lopHocPhan->monHoc->ma_mon ?? '' }}</strong> - {{ $lopHocPhan->monHoc->ten_mon ?? 'N/A' }}<br>
                                                <span class="badge bg-info">{{ $lopHocPhan->monHoc->so_tin_chi ?? 0 }} tín chỉ</span>
                                    </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Học kỳ:</th>
                                            <td>
                                                <i class="bi bi-calendar3"></i> {{ $lopHocPhan->hocKy->ten_hoc_ky }}<br>
                                                <small class="text-muted">{{ $lopHocPhan->hocKy->nam_hoc }}</small>
                                            </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Vai trò của bạn:</th>
                                    <td>
                                        @if($phanCong->vai_tro == 'giang_vien_chinh')
                                                    <span class="badge bg-primary"><i class="bi bi-star-fill"></i> Giảng viên chính</span>
                                        @elseif($phanCong->vai_tro == 'giang_vien_phu')
                                                    <span class="badge bg-info"><i class="bi bi-person"></i> Giảng viên phụ</span>
                                        @elseif($phanCong->vai_tro == 'tro_giang')
                                                    <span class="badge bg-secondary"><i class="bi bi-person-check"></i> Trợ giảng</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                            <th style="width: 40%;" class="text-muted">Nhóm lớp:</th>
                                            <td>
                                                @if($lopHocPhan->nhom_lop)
                                                    <span class="badge bg-secondary">{{ $lopHocPhan->nhom_lop }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Sức chứa:</th>
                                            <td>
                                                <i class="bi bi-people"></i> {{ $lopHocPhan->suc_chua }} sinh viên
                                            </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Số lượng đăng ký:</th>
                                    <td>
                                                <strong class="text-primary">{{ $lopHocPhan->so_luong_dang_ky }}</strong> / {{ $lopHocPhan->suc_chua }}
                                                @php
                                                    $tyLeDangKy = $lopHocPhan->suc_chua > 0 ? round(($lopHocPhan->so_luong_dang_ky / $lopHocPhan->suc_chua) * 100, 1) : 0;
                                                @endphp
                                                <span class="badge {{ $tyLeDangKy >= 100 ? 'bg-danger' : ($tyLeDangKy >= 80 ? 'bg-warning' : 'bg-success') }} ms-2">
                                                    {{ $tyLeDangKy }}%
                                                </span>
                                        @if($lopHocPhan->so_luong_dang_ky >= $lopHocPhan->suc_chua)
                                            <span class="badge bg-danger ms-2">Đầy</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Hình thức:</th>
                                    <td>
                                        @if($lopHocPhan->hinh_thuc == 'truc_tiep')
                                                    <span class="badge bg-success"><i class="bi bi-building"></i> Trực tiếp</span>
                                        @elseif($lopHocPhan->hinh_thuc == 'online')
                                                    <span class="badge bg-info"><i class="bi bi-camera-video"></i> Online</span>
                                        @elseif($lopHocPhan->hinh_thuc == 'hybrid')
                                                    <span class="badge bg-warning"><i class="bi bi-laptop"></i> Hybrid</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                        @endif
                                        @if($lopHocPhan->link_online)
                                                    <br><small><a href="{{ $lopHocPhan->link_online }}" target="_blank" class="text-primary">
                                                        <i class="bi bi-link-45deg"></i> {{ $lopHocPhan->link_online }}
                                                    </a></small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Ngày bắt đầu:</th>
                                            <td>
                                                @if($lopHocPhan->ngay_bat_dau)
                                                    <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                            <th class="text-muted">Ngày kết thúc:</th>
                                    <td>
                                                @if($lopHocPhan->ngay_ket_thuc)
                                                    <i class="bi bi-calendar-x"></i> {{ \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Giảng viên phụ trách -->
            <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-person-badge"></i> Giảng viên phụ trách
                            </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                                    <thead class="table-light">
                                <tr>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                            <th>Số điện thoại</th>
                                    <th>Vai trò</th>
                                </tr>
                            </thead>
                            <tbody>
                                        @forelse($lopHocPhan->lopHocPhanGiangVien as $pc)
                                    <tr>
                                                <td><strong>{{ $pc->giangVien->ma_giang_vien }}</strong></td>
                                                <td>
                                                    <i class="bi bi-person-circle"></i> {{ $pc->giangVien->ho_ten }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-envelope"></i> 
                                                    <a href="mailto:{{ $pc->giangVien->email }}">{{ $pc->giangVien->email }}</a>
                                                </td>
                                                <td>
                                                    @if($pc->giangVien->so_dien_thoai)
                                                        <i class="bi bi-telephone"></i> {{ $pc->giangVien->so_dien_thoai }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                        <td>
                                            @if($pc->vai_tro == 'giang_vien_chinh')
                                                        <span class="badge bg-primary"><i class="bi bi-star-fill"></i> GV Chính</span>
                                            @elseif($pc->vai_tro == 'giang_vien_phu')
                                                        <span class="badge bg-info"><i class="bi bi-person"></i> GV Phụ</span>
                                            @elseif($pc->vai_tro == 'tro_giang')
                                                        <span class="badge bg-secondary"><i class="bi bi-person-check"></i> Trợ giảng</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ $pc->vai_tro }}</span>
                                            @endif
                                        </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Chưa có giảng viên được phân công</td>
                                    </tr>
                                        @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lịch học cố định -->
            @if($lichHocCoDinh->isNotEmpty())
                <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h4 class="card-title mb-0">
                                    <i class="bi bi-calendar-week"></i> Lịch học cố định
                                </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                        <thead class="table-light">
                                    <tr>
                                        <th>Thứ</th>
                                                <th>Tiết</th>
                                                <th>Giờ học</th>
                                        <th>Phòng học</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichHocCoDinh as $lich)
                                        <tr>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            @php
                                                                $thuMap = [2 => 'Thứ 2', 3 => 'Thứ 3', 4 => 'Thứ 4', 5 => 'Thứ 5', 6 => 'Thứ 6', 7 => 'Thứ 7', 8 => 'Chủ nhật'];
                                                                $thu = $thuMap[$lich->thu_trong_tuan] ?? 'Thứ ' . $lich->thu_trong_tuan;
                                                            @endphp
                                                            {{ $thu }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            Tiết {{ $lich->tiet_bat_dau }}-{{ $lich->tiet_ket_thuc }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($lich->gio_bat_dau && $lich->gio_ket_thuc)
                                                            <i class="bi bi-clock"></i> 
                                                            {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lich->phongHoc)
                                                            <i class="bi bi-building"></i> {{ $lich->phongHoc->ten_phong }}
                                                        @else
                                                            <span class="text-muted">Chưa phân phòng</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lich->link_online)
                                                            <a href="{{ $lich->link_online }}" target="_blank" class="text-primary">
                                                                <i class="bi bi-link-45deg"></i> Link online
                                                            </a>
                                                        @else
                                                            <span class="text-muted">{{ $lich->ghi_chu ?? '-' }}</span>
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

            <!-- Danh sách sinh viên -->
            <div class="card">
                        <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    <i class="bi bi-people"></i> Danh sách sinh viên 
                                    <span class="badge bg-light text-dark">{{ $sinhViens->count() }}</span>
                                </h4>
                        <div>
                            <a href="{{ route('giangvien.lop-giang-day.export-students', $lopHocPhan->id) }}" 
                               class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                            </a>
                            <a href="{{ route('giangvien.lop-giang-day.export-students-pdf', $lopHocPhan->id) }}" 
                               class="btn btn-danger btn-sm"
                               target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                            @if($sinhViens->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                <tr>
                                                <th style="width: 4%;">#</th>
                                                <th style="width: 9%;">Mã SV</th>
                                                <th style="width: 18%;">Họ tên</th>
                                                <th style="width: 18%;">Email</th>
                                                <th style="width: 11%;">Số điện thoại</th>
                                                <th style="width: 11%;">Lớp hành chính</th>
                                                <th style="width: 9%;">Trạng thái</th>
                                                <th style="width: 10%;">Ngày đăng ký</th>
                                                <th style="width: 10%;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                            @foreach($sinhViens as $index => $lhpsv)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                                    <td><strong class="text-primary">{{ $lhpsv->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                                    <td>
                                                        <a href="mailto:{{ $lhpsv->sinhVien->email }}">
                                                            <i class="bi bi-envelope"></i> {{ $lhpsv->sinhVien->email }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if($lhpsv->sinhVien->so_dien_thoai)
                                                            <i class="bi bi-telephone"></i> {{ $lhpsv->sinhVien->so_dien_thoai }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lhpsv->sinhVien->lopHanhChinh)
                                                            <span class="badge bg-secondary">{{ $lhpsv->sinhVien->lopHanhChinh->ma_lop }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                        <td>
                                            @if($lhpsv->trang_thai == 'da_xep_lop')
                                                            <span class="badge bg-info"><i class="bi bi-check-circle"></i> Đã xếp lớp</span>
                                            @elseif($lhpsv->trang_thai == 'dang_hoc')
                                                            <span class="badge bg-success"><i class="bi bi-person-check"></i> Đang học</span>
                                            @elseif($lhpsv->trang_thai == 'da_hoan_thanh')
                                                            <span class="badge bg-primary"><i class="bi bi-trophy"></i> Đã hoàn thành</span>
                                            @elseif($lhpsv->trang_thai == 'bo_hoc')
                                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Bỏ học</span>
                                            @elseif($lhpsv->trang_thai == 'huy_dang_ky')
                                                            <span class="badge bg-secondary"><i class="bi bi-x-octagon"></i> Hủy đăng ký</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lhpsv->ngay_dang_ky)
                                                            <small>
                                                                <i class="bi bi-calendar"></i> {{ $lhpsv->ngay_dang_ky->format('d/m/Y') }}<br>
                                                                <i class="bi bi-clock"></i> {{ $lhpsv->ngay_dang_ky->format('H:i') }}
                                                            </small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info btn-view-student" 
                                                                data-lop-hoc-phan-id="{{ $lopHocPhan->id }}"
                                                                data-sinh-vien-id="{{ $lhpsv->sinh_vien_id }}"
                                                                title="Xem chi tiết">
                                                            <i class="bi bi-eye"></i> Chi tiết
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">Chưa có sinh viên nào trong lớp.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Kết quả học tập -->
                <div class="tab-pane fade" id="results" role="tabpanel">
                    @if(isset($cauHinhs) && $cauHinhs->isNotEmpty() && isset($danhSachSinhVienKetQua))
                        <!-- Thống kê tổng quan -->
                        @if(isset($thongKe))
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon purple mb-2">
                                                    <i class="bi bi-people-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                                    <h3 class="font-extrabold mb-0">{{ $thongKe['tong_sv'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon green mb-2">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Qua môn</h6>
                                                    <h3 class="font-extrabold mb-0">{{ $thongKe['sv_qua_mon'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-danger">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon red mb-2">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Trượt</h6>
                                                    <h3 class="font-extrabold mb-0">{{ $thongKe['sv_truot'] }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon blue mb-2">
                                                    <i class="bi bi-graph-up"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="text-muted font-semibold">Điểm TB</h6>
                                                    <h3 class="font-extrabold mb-0">{{ number_format($thongKe['diem_trung_binh'], 2) }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Bảng điểm -->
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="bi bi-table"></i> Bảng điểm tổng kết
                                    </h4>
                                    <div>
                                        <button class="btn btn-light btn-sm" onclick="xuatDanhSachThi()">
                                            <i class="bi bi-file-earmark-text"></i> Xuất danh sách thi
                                        </button>
                                        <a href="{{ route('giangvien.ket-qua-hoc-tap.export-excel', $lopHocPhan->id) }}" 
                                           class="btn btn-light btn-sm">
                                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                                        </a>
                                        <a href="{{ route('giangvien.ket-qua-hoc-tap.export-pdf', $lopHocPhan->id) }}" 
                                           class="btn btn-light btn-sm"
                                           target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th rowspan="2" class="align-middle text-center">STT</th>
                                                <th rowspan="2" class="align-middle">Mã SV</th>
                                                <th rowspan="2" class="align-middle">Họ tên</th>
                                                <th rowspan="2" class="align-middle text-center">Điểm danh</th>
                                                @foreach($cauHinhs as $ch)
                                                    <th colspan="{{ $ch->so_cot }}" class="text-center">
                                                        {{ $ch->ten_dau_diem }}<br>
                                                        <small class="text-muted">({{ $ch->ty_le }}%)</small>
                                                    </th>
                                                @endforeach
                                                <th rowspan="2" class="align-middle text-center">Hệ 10</th>
                                                <th rowspan="2" class="align-middle text-center">Hệ 4</th>
                                                <th rowspan="2" class="align-middle text-center">Chữ</th>
                                                <th rowspan="2" class="align-middle text-center">Kết quả</th>
                                            </tr>
                                            <tr>
                                                @foreach($cauHinhs as $ch)
                                                    @for($i = 1; $i <= $ch->so_cot; $i++)
                                                        <th class="text-center">
                                                            @if($ch->so_cot > 1) Cột {{ $i }} @else - @endif
                                                        </th>
                                                    @endfor
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($danhSachSinhVienKetQua as $index => $lhpsv)
                                                @php
                                                    // Tính tỷ lệ điểm danh
                                                    $tongBuoi = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function($q) use ($lopHocPhan) {
                                                            $q->where('lop_hoc_phan_id', $lopHocPhan->id);
                                                        })
                                                        ->distinct('lich_hoc_chi_tiet_id')
                                                        ->count('lich_hoc_chi_tiet_id');
                                                    
                                                    $buoiCoMat = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                                                        ->where('trang_thai', 'co_mat')
                                                        ->count();
                                                    
                                                    $tyLeDiemDanh = $tongBuoi > 0 ? round(($buoiCoMat / $tongBuoi) * 100, 1) : 0;
                                                    
                                                    // Lấy điểm CC (Chuyên cần)
                                                    $cauHinhCC = $cauHinhs->firstWhere('loai_dau_diem', 'chuyen_can');
                                                    $diemCC = null;
                                                    if ($cauHinhCC) {
                                                        $diemCCRecord = $lhpsv->danh_sach_diem->where('cau_hinh_id', $cauHinhCC->id)->first();
                                                        if ($diemCCRecord) {
                                                            // Chuyển điểm hệ 10 sang hệ 4
                                                            $diemCC = $diemCCRecord->diem_so;
                                                            if ($diemCC >= 9.0) $diemCC = 4.0;
                                                            elseif ($diemCC >= 8.5) $diemCC = 3.5;
                                                            elseif ($diemCC >= 8.0) $diemCC = 3.0;
                                                            elseif ($diemCC >= 7.0) $diemCC = 2.5;
                                                            elseif ($diemCC >= 6.5) $diemCC = 2.0;
                                                            elseif ($diemCC >= 5.5) $diemCC = 1.5;
                                                            elseif ($diemCC >= 5.0) $diemCC = 1.0;
                                                            else $diemCC = 0;
                                                        }
                                                    }
                                                    
                                                    // Kiểm tra điều kiện: điểm danh >= 80% VÀ điểm CC >= 2/4
                                                    $duDieuKienDiemDanh = $tyLeDiemDanh >= 80;
                                                    $duDieuKienDiemCC = $diemCC !== null && $diemCC >= 2.0;
                                                    $duDieuKienThi = $duDieuKienDiemDanh && $duDieuKienDiemCC;
                                                @endphp
                                                <tr class="{{ !$duDieuKienThi && $tongBuoi > 0 ? 'table-danger' : '' }}">
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td><strong class="text-primary">{{ $lhpsv->sinhVien->ma_sinh_vien }}</strong></td>
                                                    <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                                    <td class="text-center">
                                                        @if($tongBuoi > 0)
                                                            <span class="badge {{ $duDieuKienDiemDanh ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $tyLeDiemDanh }}% ({{ $buoiCoMat }}/{{ $tongBuoi }})
                                                            </span>
                                                            @if(!$duDieuKienThi)
                                                                <br><small class="text-danger fw-bold">
                                                                    @if(!$duDieuKienDiemDanh)
                                                                        Điểm danh < 80%
                                                                    @endif
                                                                    @if(!$duDieuKienDiemCC)
                                                                        {{ !$duDieuKienDiemDanh ? ', ' : '' }}Điểm CC < 2/4
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    
                                                    @foreach($cauHinhs as $ch)
                                                        @for($cot = 1; $cot <= $ch->so_cot; $cot++)
                                                            @php
                                                                $diem = $lhpsv->danh_sach_diem->where('cau_hinh_id', $ch->id)->where('cot_diem', $cot)->first();
                                                            @endphp
                                                            <td class="text-center">
                                                                @if($diem)
                                                                    <strong>{{ number_format($diem->diem_so, 2) }}</strong>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    @endforeach
                                                    
                                                    <td class="text-center">
                                                        @if($lhpsv->diem_tong_ket)
                                                            <strong class="text-primary">{{ number_format($lhpsv->diem_tong_ket, 2) }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($lhpsv->diem_he_4)
                                                            {{ number_format($lhpsv->diem_he_4, 2) }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($lhpsv->diem_chu)
                                                            <span class="badge bg-info">{{ $lhpsv->diem_chu }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($lhpsv->diem_tong_ket)
                                                            @if($lhpsv->diem_tong_ket >= 4)
                                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Qua môn</span>
                                                            @else
                                                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Trượt</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">Chưa có</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                                    <td colspan="20" class="text-center text-muted py-4">
                                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                        <p class="mt-2 mb-0">Chưa có dữ liệu điểm</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Lớp học phần chưa có cấu hình đầu điểm hoặc chưa có dữ liệu điểm.</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Chi tiết sinh viên -->
    <div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="studentDetailModalLabel">
                        <i class="bi bi-person-circle"></i> Chi tiết sinh viên
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="studentDetailContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Đang tải dữ liệu...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto switch to results tab if hash is #results
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#results') {
                const resultsTab = document.getElementById('results-tab');
                if (resultsTab) {
                    const tab = new bootstrap.Tab(resultsTab);
                    tab.show();
                }
            }

            // Event listener cho nút xem chi tiết sinh viên
            document.querySelectorAll('.btn-view-student').forEach(button => {
                button.addEventListener('click', function() {
                    const lopHocPhanId = this.getAttribute('data-lop-hoc-phan-id');
                    const sinhVienId = this.getAttribute('data-sinh-vien-id');
                    showStudentDetail(lopHocPhanId, sinhVienId);
                });
            });
        });

        // Hiển thị chi tiết sinh viên
        function showStudentDetail(lopHocPhanId, sinhVienId) {
            const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
            const contentDiv = document.getElementById('studentDetailContent');
            
            // Reset content
            contentDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Đang tải dữ liệu...</p>
                </div>
            `;
            
            modal.show();
            
            // Load dữ liệu
            fetch(`{{ url('giang-vien/lop-giang-day') }}/${lopHocPhanId}/sinh-vien/${sinhVienId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderStudentDetail(data);
                    } else {
                        contentDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> Không thể tải dữ liệu sinh viên.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra khi tải dữ liệu.
                        </div>
                    `;
                });
        }

        // Render chi tiết sinh viên
        function renderStudentDetail(data) {
            const sv = data.sinhVien;
            const thongKe = data.thongKe;
            const monHocs = data.monHocs;
            const monHocsTheoHocKy = data.monHocsTheoHocKy;
            
            let html = `
                <!-- Thông tin sinh viên -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Thông tin sinh viên</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 40%;" class="text-muted">Mã sinh viên:</th>
                                        <td><strong class="text-primary">${sv.ma_sinh_vien}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Họ và tên:</th>
                                        <td><strong>${sv.ho_ten}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Email:</th>
                                        <td><i class="bi bi-envelope"></i> <a href="mailto:${sv.email}">${sv.email}</a></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Số điện thoại:</th>
                                        <td><i class="bi bi-telephone"></i> ${sv.so_dien_thoai || '<span class="text-muted">-</span>'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 40%;" class="text-muted">Lớp hành chính:</th>
                                        <td><span class="badge bg-secondary">${sv.lop_hanh_chinh}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Ngày sinh:</th>
                                        <td><i class="bi bi-calendar"></i> ${sv.ngay_sinh}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Giới tính:</th>
                                        <td>${sv.gioi_tinh === 'nam' ? '<i class="bi bi-gender-male text-primary"></i> Nam' : (sv.gioi_tinh === 'nu' ? '<i class="bi bi-gender-female text-danger"></i> Nữ' : '-')}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê học tập -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <i class="bi bi-book text-primary" style="font-size: 2rem;"></i>
                                <h5 class="mt-2 mb-0">${thongKe.tong_mon}</h5>
                                <p class="text-muted mb-0">Tổng môn học</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <h5 class="mt-2 mb-0">${thongKe.so_mon_qua}</h5>
                                <p class="text-muted mb-0">Môn đã qua</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                <h5 class="mt-2 mb-0">${thongKe.so_mon_truot}</h5>
                                <p class="text-muted mb-0">Môn trượt</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                                <h5 class="mt-2 mb-0">${thongKe.gpa_tich_luy ? thongKe.gpa_tich_luy.toFixed(2) : '0.00'}</h5>
                                <p class="text-muted mb-0">GPA tích lũy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng điểm -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-table"></i> Bảng điểm tất cả các môn học</h5>
                    </div>
                    <div class="card-body">
            `;

            // Nhóm theo học kỳ
            Object.keys(monHocsTheoHocKy).forEach(hocKy => {
                const monHocList = monHocsTheoHocKy[hocKy];
                html += `
                    <h6 class="text-primary mb-3"><i class="bi bi-calendar3"></i> ${hocKy}</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">STT</th>
                                    <th style="width: 10%;">Mã lớp HP</th>
                                    <th style="width: 15%;">Mã môn</th>
                                    <th style="width: 25%;">Tên môn học</th>
                                    <th style="width: 8%;" class="text-center">TC</th>
                                    <th style="width: 10%;" class="text-center">Điểm hệ 10</th>
                                    <th style="width: 10%;" class="text-center">Điểm hệ 4</th>
                                    <th style="width: 8%;" class="text-center">Điểm chữ</th>
                                    <th style="width: 9%;" class="text-center">Kết quả</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                monHocList.forEach((mon, index) => {
                    const diemHe10 = mon.diem_he_10 !== null ? parseFloat(mon.diem_he_10).toFixed(2) : '-';
                    const diemHe4 = mon.diem_he_4 !== null ? parseFloat(mon.diem_he_4).toFixed(2) : '-';
                    const diemChu = mon.diem_chu || '-';
                    const quaMon = mon.qua_mon === true;
                    const coDiem = mon.diem_he_10 !== null;
                    
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${mon.ma_lop_hp}</strong></td>
                            <td>${mon.ma_mon}</td>
                            <td>${mon.ten_mon}</td>
                            <td class="text-center"><span class="badge bg-info">${mon.so_tin_chi}</span></td>
                            <td class="text-center">
                                ${coDiem ? `<strong class="text-primary">${diemHe10}</strong>` : '<span class="text-muted">-</span>'}
                            </td>
                            <td class="text-center">
                                ${coDiem ? diemHe4 : '<span class="text-muted">-</span>'}
                            </td>
                            <td class="text-center">
                                ${coDiem ? `<span class="badge bg-info">${diemChu}</span>` : '<span class="text-muted">-</span>'}
                            </td>
                            <td class="text-center">
                                ${coDiem ? (quaMon ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Qua môn</span>' : '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Trượt</span>') : '<span class="badge bg-secondary">Chưa có</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;

            document.getElementById('studentDetailContent').innerHTML = html;
        }

        // Function xuất danh sách thi
        function xuatDanhSachThi() {
            // Tìm bảng điểm tổng kết
            const bangDiem = document.querySelector('.card-body .table-responsive table tbody');
            if (!bangDiem) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không tìm thấy bảng điểm',
                });
                return;
            }
            
            const rows = bangDiem.querySelectorAll('tr:not(.table-danger)');
            
            if (rows.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Không có sinh viên đủ điều kiện',
                    text: 'Không có sinh viên nào đủ điều kiện dự thi (Yêu cầu: Điểm danh >= 80% VÀ Điểm CC >= 2/4)',
                });
                return;
            }
            
            let danhSach = [];
            let stt = 1;
            
            rows.forEach(row => {
                const cells = row.cells;
                if (cells.length > 0) {
                    const maSV = cells[1].textContent.trim();
                    const hoTen = cells[2].textContent.trim();
                    const diemDanh = cells[3].textContent.trim();
                    
                    danhSach.push({
                        stt: stt++,
                        maSV: maSV,
                        hoTen: hoTen,
                        diemDanh: diemDanh
                    });
                }
            });
            
            // Tạo HTML cho bảng danh sách thi
            let html = `
                <div style="padding: 20px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h3>DANH SÁCH SINH VIÊN DỰ THI</h3>
                        <p><strong>Môn:</strong> {{ $lopHocPhan->monHoc->ten_mon }}</p>
                        <p><strong>Lớp:</strong> {{ $lopHocPhan->ma_lop_hp }}</p>
                        <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</p>
                        <p><strong>Tổng sinh viên đủ điều kiện:</strong> ${danhSach.length}</p>
                    </div>
                    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="width: 50px;">STT</th>
                                <th style="width: 120px;">Mã SV</th>
                                <th>Họ và tên</th>
                                <th style="width: 150px;">Điểm danh</th>
                                <th style="width: 150px;">Chữ ký</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            danhSach.forEach(sv => {
                html += `
                    <tr>
                        <td style="text-align: center;">${sv.stt}</td>
                        <td style="text-align: center;"><strong>${sv.maSV}</strong></td>
                        <td>${sv.hoTen}</td>
                        <td style="text-align: center;">${sv.diemDanh}</td>
                        <td></td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                    <div style="margin-top: 30px; text-align: right;">
                        <p><em>Ngày ${new Date().getDate()} tháng ${new Date().getMonth() + 1} năm ${new Date().getFullYear()}</em></p>
                        <p><strong>Giảng viên</strong></p>
                        <p style="margin-top: 60px;"><em>(Ký và ghi rõ họ tên)</em></p>
                    </div>
                </div>
            `;
            
            // Mở cửa sổ mới và in
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Danh sách sinh viên dự thi</title>
                    <style>
                        @media print {
                            body { margin: 20px; }
                        }
                        body { font-family: 'Times New Roman', Times, serif; font-size: 14px; }
                        table { width: 100%; }
                        th, td { padding: 8px; text-align: left; }
                        th { background-color: #f0f0f0; }
                        @page { margin: 2cm; }
                    </style>
                </head>
                <body>
                    ${html}
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    </script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
    @endpush
@endsection
