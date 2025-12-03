@extends('layouts.layout-sinhvien')

@section('title', 'Điều kiện tốt nghiệp')

@section('content')
<div class="page-heading">
    <h3>Điều kiện tốt nghiệp</h3>
</div>

<div class="page-content">
    <section class="section">
        <!-- Thông tin sinh viên -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="row small">
                    <div class="col-md-3 mb-1">
                        <span class="text-muted">Sinh viên:</span>
                        <strong class="ms-2">{{ $sinhVien->ho_ten }}</strong>
                    </div>
                    <div class="col-md-3 mb-1">
                        <span class="text-muted">Mã SV:</span>
                        <strong class="ms-2">{{ $sinhVien->ma_sinh_vien }}</strong>
                    </div>
                    <div class="col-md-3 mb-1">
                        <span class="text-muted">Khoa:</span>
                        <strong class="ms-2">{{ $chuyenNganh->nganh->khoa->ten_khoa }}</strong>
                    </div>
                    <div class="col-md-3 mb-1">
                        <span class="text-muted">Chuyên ngành:</span>
                        <strong class="ms-2">{{ $chuyenNganh->ten_chuyen_nganh }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kết quả tổng quan -->
        <div class="card mb-4">
            <div class="card-body text-center py-3">
                @if($dieuKienTotNghiep['tong_quat']['du_dieu_kien'])
                    <h5 class="text-success mb-1">
                        <i class="bi bi-check-circle-fill"></i> Đủ điều kiện tốt nghiệp
                    </h5>
                @else
                    <h5 class="text-warning mb-1">
                        <i class="bi bi-exclamation-triangle-fill"></i> Chưa đủ điều kiện tốt nghiệp
                    </h5>
                @endif
                <small class="text-muted">Đạt {{ $dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] }}/{{ $dieuKienTotNghiep['tong_quat']['tong_dieu_kien'] }} điều kiện</small>
            </div>
        </div>

        <!-- Chi tiết điều kiện -->
        <div class="row g-3 mb-3">
            <!-- Tín chỉ tích lũy -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-3 {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'border-success' : 'border-secondary' }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-secondary' }} fs-4 me-2"></i>
                            <h6 class="mb-0 small">Tín chỉ tích lũy</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <small class="text-muted d-block">Đã đạt</small>
                                <h5 class="mb-0">{{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['da_dat'] }}</h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Yêu cầu</small>
                                <h5 class="mb-0">{{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['yeu_cau'] }}</h5>
                            </div>
                        </div>
                        @if(!$dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'])
                            <small class="text-muted d-block mt-2">Thiếu {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['con_thieu'] }} TC</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tín chỉ bắt buộc -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-3 {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'border-success' : 'border-secondary' }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-secondary' }} fs-4 me-2"></i>
                            <h6 class="mb-0 small">Tín chỉ bắt buộc</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <small class="text-muted d-block">Đã đạt</small>
                                <h5 class="mb-0">{{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['da_dat'] }}</h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Yêu cầu</small>
                                <h5 class="mb-0">{{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['yeu_cau'] }}</h5>
                            </div>
                        </div>
                        @if(!$dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'])
                            <small class="text-muted d-block mt-2">Thiếu {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['con_thieu'] }} TC</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Điểm trung bình -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-3 {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'border-success' : 'border-secondary' }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-secondary' }} fs-4 me-2"></i>
                            <h6 class="mb-0 small">Điểm TB tích lũy</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <small class="text-muted d-block">Hiện tại</small>
                                <h5 class="mb-0">{{ number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['da_dat'], 2) }}</h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Yêu cầu</small>
                                <h5 class="mb-0">≥ {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['yeu_cau'] }}</h5>
                            </div>
                        </div>
                        @if(!$dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'])
                            <small class="text-muted d-block mt-2">Cần {{ number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['con_thieu'], 2) }} điểm</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Môn học bắt buộc -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-start border-3 {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'border-success' : 'border-danger' }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-4 me-2"></i>
                            <h6 class="mb-0 small">Môn học bắt buộc</h6>
                        </div>
                        @if($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'])
                            <p class="text-muted small mb-0">Đã hoàn thành</p>
                        @else
                            <p class="text-danger small mb-0">Còn {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['so_mon_no'] }} môn chưa đạt</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách môn nợ (nếu có) -->
        @if(!$dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] && count($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach']) > 0)
        <div class="card mb-3">
            <div class="card-header py-2">
                <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Môn học cần hoàn thành ({{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['so_mon_no'] }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mã môn</th>
                                <th>Tên môn học</th>
                                <th class="text-center">TC</th>
                                <th class="text-center pe-3">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach'] as $mon)
                            <tr>
                                <td class="ps-3"><code class="small">{{ $mon['ma_mon'] }}</code></td>
                                <td>{{ $mon['ten_mon'] }}</td>
                                <td class="text-center">{{ $mon['so_tin_chi'] }}</td>
                                <td class="text-center pe-3">
                                    <span class="badge bg-{{ $mon['trang_thai'] == 'Chưa học' ? 'secondary' : 'warning' }} small">
                                        {{ $mon['trang_thai'] }}
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

        <!-- Lưu ý -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <p class="mb-2 small"><strong><i class="bi bi-info-circle me-1"></i>Lưu ý:</strong></p>
                <ul class="mb-0 small text-muted ps-3">
                    <li>Kết quả mang tính tham khảo, quyết định chính thức do phòng Đào tạo công bố</li>
                    <li>Điểm TB tích lũy tối thiểu ≥ 5.0/10, môn bắt buộc ≥ 4.0</li>
                    <li>Cần hoàn thành: Học phí, chứng chỉ ngoại ngữ/tin học, GDTC, GDQP</li>
                </ul>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="text-center">
            <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
            <a href="{{ route('sinh-vien.diem.bang-diem') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-file-earmark-text me-1"></i>Xem bảng điểm
            </a>
        </div>
    </section>
</div>
@endsection

