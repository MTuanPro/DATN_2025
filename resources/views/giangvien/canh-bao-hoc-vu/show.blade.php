@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết cảnh báo học vụ - Giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết cảnh báo học vụ</h3>
                    <p class="text-subtitle text-muted">Thông tin chi tiết cảnh báo học vụ sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('giangvien.canh-bao-hoc-vu.index') }}">Cảnh báo học vụ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Info -->
        <section class="section">
            <div class="row">
                <!-- Left Column - Warning Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title text-white mb-0">
                                <i class="bi bi-exclamation-triangle-fill"></i> Thông tin cảnh báo
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Loại cảnh báo:</label>
                                    <div>
                                        @if ($canhBao->loai == 'diem_thap')
                                            <span class="badge bg-warning fs-6">Điểm thấp</span>
                                        @elseif($canhBao->loai == 'vang_nhieu')
                                            <span class="badge bg-info fs-6">Vắng nhiều</span>
                                        @elseif($canhBao->loai == 'no_hoc_phi')
                                            <span class="badge bg-danger fs-6">Nợ học phí</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">Học kỳ liên tiếp</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mức độ:</label>
                                    <div>
                                        @if ($canhBao->muc_do == 'canh_cao')
                                            <span class="badge bg-warning fs-6">Cảnh cáo</span>
                                        @elseif($canhBao->muc_do == 'dinh_chi')
                                            <span class="badge bg-danger fs-6">Đình chỉ</span>
                                        @else
                                            <span class="badge bg-dark fs-6">Buộc thôi học</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Trạng thái:</label>
                                    <div>
                                        @if ($canhBao->trang_thai == 'chua_xu_ly')
                                            <span class="badge bg-secondary fs-6">Chưa xử lý</span>
                                        @elseif($canhBao->trang_thai == 'dang_xu_ly')
                                            <span class="badge bg-primary fs-6">Đang xử lý</span>
                                        @else
                                            <span class="badge bg-success fs-6">Đã xử lý</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ngày cảnh báo:</label>
                                    <div>
                                        {{ $canhBao->ngay_canh_bao ? $canhBao->ngay_canh_bao->format('d/m/Y H:i') : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Lý do cảnh báo:</label>
                                <div class="alert alert-light">
                                    {{ $canhBao->ly_do }}
                                </div>
                            </div>

                            @if ($canhBao->ghi_chu)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú xử lý:</label>
                                    <div class="alert alert-info">
                                        {{ $canhBao->ghi_chu }}
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Người tạo:</label>
                                    <div>{{ $canhBao->nguoiTao->name ?? 'Hệ thống' }}</div>
                                </div>
                                @if ($canhBao->nguoiXuLy)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Người xử lý:</label>
                                        <div>{{ $canhBao->nguoiXuLy->name }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- History -->
                    @if ($lichSuCanhBao->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="bi bi-clock-history"></i> Lịch sử cảnh báo sinh viên
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Loại</th>
                                                <th>Mức độ</th>
                                                <th>Trạng thái</th>
                                                <th>Lý do</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lichSuCanhBao as $ls)
                                                <tr>
                                                    <td>{{ $ls->ngay_canh_bao ? $ls->ngay_canh_bao->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if ($ls->loai == 'diem_thap')
                                                            <span class="badge bg-warning">Điểm thấp</span>
                                                        @elseif($ls->loai == 'vang_nhieu')
                                                            <span class="badge bg-info">Vắng nhiều</span>
                                                        @elseif($ls->loai == 'no_hoc_phi')
                                                            <span class="badge bg-danger">Nợ HP</span>
                                                        @else
                                                            <span class="badge bg-secondary">HK liên tiếp</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ls->muc_do == 'canh_cao')
                                                            <span class="badge bg-warning">Cảnh cáo</span>
                                                        @elseif($ls->muc_do == 'dinh_chi')
                                                            <span class="badge bg-danger">Đình chỉ</span>
                                                        @else
                                                            <span class="badge bg-dark">Thôi học</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ls->trang_thai == 'da_xu_ly')
                                                            <span class="badge bg-success">Đã xử lý</span>
                                                        @else
                                                            <span class="badge bg-secondary">Chưa xử lý</span>
                                                        @endif
                                                    </td>
                                                    <td><small>{{ Str::limit($ls->ly_do, 40) }}</small></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Student Info -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title text-white mb-0">
                                <i class="bi bi-person-circle"></i> Thông tin sinh viên
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="avatar avatar-xl bg-primary text-white mb-2">
                                    <span class="avatar-content">{{ strtoupper(substr($canhBao->sinhVien->user->name ?? 'N', 0, 2)) }}</span>
                                </div>
                                <h5 class="mb-1">{{ $canhBao->sinhVien->user->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">{{ $canhBao->sinhVien->ma_sinh_vien ?? 'N/A' }}</p>
                            </div>

                            <hr>

                            <div class="mb-2">
                                <small class="text-muted">Lớp hành chính:</small>
                                <div class="fw-bold">{{ $canhBao->sinhVien->lopHanhChinh->ten_lop ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Email:</small>
                                <div>{{ $canhBao->sinhVien->user->email ?? 'N/A' }}</div>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">Số điện thoại:</small>
                                <div>{{ $canhBao->sinhVien->so_dien_thoai ?? 'N/A' }}</div>
                            </div>

                            <hr>

                            @if ($canhBao->sinhVien->ketQuaHocTaps->isNotEmpty())
                                @php
                                    $latestKQHT = $canhBao->sinhVien->ketQuaHocTaps->sortByDesc('created_at')->first();
                                @endphp
                                <div class="mb-2">
                                    <small class="text-muted">GPA hiện tại:</small>
                                    <div class="fw-bold {{ $latestKQHT->gpa_hoc_ky < 2.0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($latestKQHT->gpa_hoc_ky, 2) }}
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">Tổng tín chỉ tích lũy:</small>
                                    <div>{{ $latestKQHT->tong_tin_chi_tich_luy ?? 0 }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('giangvien.canh-bao-hoc-vu.index') }}" class="btn btn-secondary btn-block">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
