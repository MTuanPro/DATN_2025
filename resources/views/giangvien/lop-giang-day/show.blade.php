@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Chi tiết lớp học phần</h3>
                <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->ten_lop_hp }}</p>
            </div>
            <a href="{{ route('giangvien.lop-giang-day.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin lớp học phần -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Mã lớp HP:</th>
                                    <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp HP:</th>
                                    <td>{{ $lopHocPhan->ten_lop_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>
                                        {{ $lopHocPhan->monHoc->ma_mon ?? '' }} - {{ $lopHocPhan->monHoc->ten_mon ?? 'N/A' }}<br>
                                        <small class="text-muted">
                                            {{ $lopHocPhan->monHoc->so_tin_chi ?? 0 }} TC
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Học kỳ:</th>
                                    <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }} ({{ $lopHocPhan->hocKy->nam_hoc }})</td>
                                </tr>
                                <tr>
                                    <th>Vai trò của bạn:</th>
                                    <td>
                                        @if($phanCong->vai_tro == 'giang_vien_chinh')
                                            <span class="badge bg-primary">Giảng viên chính</span>
                                        @elseif($phanCong->vai_tro == 'giang_vien_phu')
                                            <span class="badge bg-info">Giảng viên phụ</span>
                                        @elseif($phanCong->vai_tro == 'tro_giang')
                                            <span class="badge bg-secondary">Trợ giảng</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Nhóm lớp:</th>
                                    <td>{{ $lopHocPhan->nhom_lop ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sức chứa:</th>
                                    <td>{{ $lopHocPhan->suc_chua }} sinh viên</td>
                                </tr>
                                <tr>
                                    <th>Số lượng đăng ký:</th>
                                    <td>
                                        <strong>{{ $lopHocPhan->so_luong_dang_ky }}</strong> / {{ $lopHocPhan->suc_chua }}
                                        @if($lopHocPhan->so_luong_dang_ky >= $lopHocPhan->suc_chua)
                                            <span class="badge bg-danger ms-2">Đầy</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Hình thức:</th>
                                    <td>
                                        @if($lopHocPhan->hinh_thuc == 'truc_tiep')
                                            <span class="badge bg-success">Trực tiếp</span>
                                        @elseif($lopHocPhan->hinh_thuc == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @elseif($lopHocPhan->hinh_thuc == 'hybrid')
                                            <span class="badge bg-warning">Hybrid</span>
                                        @endif
                                        @if($lopHocPhan->link_online)
                                            <br><small><a href="{{ $lopHocPhan->link_online }}" target="_blank">{{ $lopHocPhan->link_online }}</a></small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @if($lopHocPhan->trang_thai_lop == 'mo_dang_ky')
                                            <span class="badge bg-warning">Mở đăng ký</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'dang_hoc')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'ket_thuc')
                                            <span class="badge bg-secondary">Kết thúc</span>
                                        @elseif($lopHocPhan->trang_thai_lop == 'huy')
                                            <span class="badge bg-danger">Hủy</span>
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
                <div class="card-header">
                    <h4 class="card-title">Giảng viên phụ trách</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Vai trò</th>
                                    <th>Phân công</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lopHocPhan->lopHocPhanGiangVien as $pc)
                                    <tr>
                                        <td>{{ $pc->giangVien->ma_giang_vien }}</td>
                                        <td>{{ $pc->giangVien->ho_ten }}</td>
                                        <td>{{ $pc->giangVien->email }}</td>
                                        <td>
                                            @if($pc->vai_tro == 'giang_vien_chinh')
                                                <span class="badge bg-primary">GV Chính</span>
                                            @elseif($pc->vai_tro == 'giang_vien_phu')
                                                <span class="badge bg-info">GV Phụ</span>
                                            @elseif($pc->vai_tro == 'tro_giang')
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            @endif
                                        </td>
                                        <td>{{ $pc->phan_cong_giang_day ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lịch học cố định -->
            @if($lichHocCoDinh->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Lịch học cố định</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Thứ</th>
                                        <th>Tiết bắt đầu</th>
                                        <th>Số tiết</th>
                                        <th>Phòng học</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichHocCoDinh as $lich)
                                        <tr>
                                            <td>Thứ {{ $lich->thu }}</td>
                                            <td>Tiết {{ $lich->tiet_bat_dau }}</td>
                                            <td>{{ $lich->so_tiet }} tiết</td>
                                            <td>{{ $lich->phong_hoc_id }}</td>
                                            <td>{{ $lich->ghi_chu ?? '-' }}</td>
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
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Danh sách sinh viên ({{ $sinhViens->count() }})</h4>
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
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Lớp hành chính</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đăng ký</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sinhViens as $index => $lhpsv)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $lhpsv->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                        <td>{{ $lhpsv->sinhVien->email }}</td>
                                        <td>{{ $lhpsv->sinhVien->so_dien_thoai ?? 'N/A' }}</td>
                                        <td>{{ $lhpsv->sinhVien->lopHanhChinh->ma_lop ?? 'N/A' }}</td>
                                        <td>
                                            @if($lhpsv->trang_thai == 'da_xep_lop')
                                                <span class="badge bg-info">Đã xếp lớp</span>
                                            @elseif($lhpsv->trang_thai == 'dang_hoc')
                                                <span class="badge bg-success">Đang học</span>
                                            @elseif($lhpsv->trang_thai == 'da_hoan_thanh')
                                                <span class="badge bg-primary">Đã hoàn thành</span>
                                            @elseif($lhpsv->trang_thai == 'bo_hoc')
                                                <span class="badge bg-danger">Bỏ học</span>
                                            @elseif($lhpsv->trang_thai == 'huy_dang_ky')
                                                <span class="badge bg-secondary">Hủy đăng ký</span>
                                            @endif
                                        </td>
                                        <td>{{ $lhpsv->ngay_dang_ky ? $lhpsv->ngay_dang_ky->format('d/m/Y H:i') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Chưa có sinh viên nào trong lớp.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
