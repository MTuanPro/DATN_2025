@extends('layouts.layout-sinhvien')

@section('title', 'Điều kiện tốt nghiệp')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Điều kiện tốt nghiệp</h3>
                    <p class="text-subtitle text-muted">Kiểm tra điều kiện tốt nghiệp của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}">Chương trình đào tạo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Điều kiện tốt nghiệp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin sinh viên -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Thông tin sinh viên</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Sinh viên:</strong></td>
                                    <td>{{ $sinhVien->ho_ten }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mã sinh viên:</strong></td>
                                    <td>{{ $sinhVien->ma_sinh_vien }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lớp:</strong></td>
                                    <td>{{ $sinhVien->lopHanhChinh->ten_lop }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Khoa:</strong></td>
                                    <td>{{ $chuyenNganh->nganh->khoa->ten_khoa }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngành:</strong></td>
                                    <td>{{ $chuyenNganh->nganh->ten_nganh }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Chuyên ngành:</strong></td>
                                    <td>{{ $chuyenNganh->ten_chuyen_nganh }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kết quả tổng quan -->
            <div class="card">
                <div class="card-header {{ $dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bg-success' : 'bg-warning' }} text-white">
                    <h5 class="mb-0">
                        <i class="bi {{ $dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                        Kết quả tổng quan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h4 class="mb-3">
                                @if($dieuKienTotNghiep['tong_quat']['du_dieu_kien'])
                                    <span class="text-success">
                                        <i class="bi bi-check-circle-fill"></i> Bạn đã đủ điều kiện tốt nghiệp!
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Bạn chưa đủ điều kiện tốt nghiệp
                                    </span>
                                @endif
                            </h4>
                            <p class="lead">
                                Đã đạt <strong>{{ $dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] }}/{{ $dieuKienTotNghiep['tong_quat']['tong_dieu_kien'] }}</strong> điều kiện
                            </p>
                            <div class="progress mb-3" style="height: 30px;">
                                <div class="progress-bar {{ $dieuKienTotNghiep['tong_quat']['du_dieu_kien'] ? 'bg-success' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ ($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] / $dieuKienTotNghiep['tong_quat']['tong_dieu_kien']) * 100 }}%"
                                     aria-valuenow="{{ $dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $dieuKienTotNghiep['tong_quat']['tong_dieu_kien'] }}">
                                    {{ number_format(($dieuKienTotNghiep['tong_quat']['so_dieu_kien_dat'] / $dieuKienTotNghiep['tong_quat']['tong_dieu_kien']) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết từng điều kiện -->
            <div class="row">
                <!-- Điều kiện 1: Tín chỉ tích lũy -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bg-success' : 'bg-warning' }} text-white">
                            <h5 class="mb-0">
                                <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                                1. Tín chỉ tích lũy
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4>{{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['yeu_cau'] }} TC</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Đã đạt:</p>
                                    <h4 class="{{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'text-success' : 'text-warning' }}">
                                        {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['da_dat'] }} TC
                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'] ? 'bg-success' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['phan_tram'] }}%">
                                    {{ number_format($dieuKienTotNghiep['dieu_kien']['tin_chi']['phan_tram'], 1) }}%
                                </div>
                            </div>
                            @if(!$dieuKienTotNghiep['dieu_kien']['tin_chi']['dat'])
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Còn thiếu: <strong>{{ $dieuKienTotNghiep['dieu_kien']['tin_chi']['con_thieu'] }} tín chỉ</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 2: Môn bắt buộc -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bg-success' : 'bg-warning' }} text-white">
                            <h5 class="mb-0">
                                <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                                2. Môn học bắt buộc
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4>{{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['yeu_cau'] }} TC</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Đã đạt:</p>
                                    <h4 class="{{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'text-success' : 'text-warning' }}">
                                        {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['da_dat'] }} TC
                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'] ? 'bg-success' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['phan_tram'] }}%">
                                    {{ number_format($dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['phan_tram'], 1) }}%
                                </div>
                            </div>
                            @if(!$dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['dat'])
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Còn thiếu: <strong>{{ $dieuKienTotNghiep['dieu_kien']['mon_bat_buoc']['con_thieu'] }} tín chỉ bắt buộc</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 3: Điểm trung bình -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'border-success' : 'border-warning' }}">
                        <div class="card-header {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bg-success' : 'bg-warning' }} text-white">
                            <h5 class="mb-0">
                                <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                                3. Điểm trung bình tích lũy
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Yêu cầu:</p>
                                    <h4>≥ {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['yeu_cau'] }}</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-1">Hiện tại:</p>
                                    <h4 class="{{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'text-success' : 'text-warning' }}">
                                        {{ number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['da_dat'], 2) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar {{ $dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'] ? 'bg-success' : 'bg-warning' }}" 
                                     role="progressbar" 
                                     style="width: {{ min($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['phan_tram'], 100) }}%">
                                    {{ number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['phan_tram'], 1) }}%
                                </div>
                            </div>
                            @if(!$dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['dat'])
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Cần cải thiện thêm: <strong>{{ number_format($dieuKienTotNghiep['dieu_kien']['diem_trung_binh']['con_thieu'], 2) }} điểm</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Điều kiện 4: Không có môn nợ -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100 {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'border-success' : 'border-danger' }}">
                        <div class="card-header {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'bg-success' : 'bg-danger' }} text-white">
                            <h5 class="mb-0">
                                <i class="bi {{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'] ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                                4. Không còn môn nợ (bắt buộc)
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['dat'])
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <strong>Tuyệt vời!</strong> Bạn đã hoàn thành tất cả môn học bắt buộc.
                                </div>
                            @else
                                <div class="alert alert-danger mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Còn <strong>{{ $dieuKienTotNghiep['dieu_kien']['khong_no_mon']['so_mon_no'] }} môn</strong> chưa đạt hoặc chưa học
                                </div>
                                
                                @if(count($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach']) > 0)
                                    <h6 class="mb-3">Danh sách môn cần hoàn thành:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã môn</th>
                                                    <th>Tên môn</th>
                                                    <th>TC</th>
                                                    <th>HK</th>
                                                    <th>Trạng thái</th>
                                                    <th>Điểm</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dieuKienTotNghiep['dieu_kien']['khong_no_mon']['danh_sach'] as $mon)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td><strong>{{ $mon['ma_mon'] }}</strong></td>
                                                        <td>{{ $mon['ten_mon'] }}</td>
                                                        <td>{{ $mon['so_tin_chi'] }}</td>
                                                        <td>{{ $mon['hoc_ky_goi_y'] }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $mon['trang_thai'] == 'Chưa học' ? 'secondary' : 'warning' }}">
                                                                {{ $mon['trang_thai'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($mon['diem'])
                                                                <span class="text-danger">{{ number_format($mon['diem'], 1) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ghi chú quan trọng</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Điều kiện tốt nghiệp này là <strong>tham khảo</strong>, điều kiện chính thức do phòng Đào tạo quyết định.</li>
                        <li>Điểm trung bình tích lũy yêu cầu tối thiểu: <strong>5.0/10</strong></li>
                        <li>Tất cả môn học bắt buộc phải <strong>đạt điểm ≥ 4.0</strong></li>
                        <li>Ngoài điều kiện học tập, sinh viên cần hoàn thành các yêu cầu về:
                            <ul>
                                <li>Học phí (không còn nợ)</li>
                                <li>Chứng chỉ ngoại ngữ, tin học (theo quy định)</li>
                                <li>Giáo dục thể chất, giáo dục quốc phòng</li>
                            </ul>
                        </li>
                        <li>Liên hệ phòng Đào tạo để biết thêm chi tiết: <strong>daotao@university.edu.vn</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="text-center mt-4">
                <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại CTĐT
                </a>
                <a href="{{ route('sinh-vien.diem.bang-diem') }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-text me-2"></i>Xem bảng điểm
                </a>
            </div>
        </section>
    </div>
@endsection

