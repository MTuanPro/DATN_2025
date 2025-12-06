@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết lịch sử nhập điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết lịch sử nhập điểm</h3>
                    <p class="text-subtitle text-muted">Lịch sử nhập điểm của lớp học phần: {{ $lopHocPhan->ma_lop_hp }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.nhap-diem.index') }}">Nhập điểm</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.lich-su-nhap-diem.index') }}">Lịch sử nhập điểm</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Mã lớp HP:</th>
                                    <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tên lớp HP:</th>
                                    <td>{{ $lopHocPhan->ten_lop_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>{{ $lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Học kỳ:</th>
                                    <td>{{ $lopHocPhan->hocKy->ten_hoc_ky ?? 'N/A' }} - {{ $lopHocPhan->hocKy->nam_hoc ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày bắt đầu:</th>
                                    <td>{{ $lopHocPhan->ngay_bat_dau ? \Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày kết thúc:</th>
                                    <td>{{ $lopHocPhan->ngay_ket_thuc ? \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.lich-su-nhap-diem.show', $lopHocPhan->id) }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="loai_thao_tac">Loại thao tác</label>
                                    <select name="loai_thao_tac" id="loai_thao_tac" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="them" {{ request('loai_thao_tac') == 'them' ? 'selected' : '' }}>Thêm</option>
                                        <option value="sua" {{ request('loai_thao_tac') == 'sua' ? 'selected' : '' }}>Sửa</option>
                                        <option value="xoa" {{ request('loai_thao_tac') == 'xoa' ? 'selected' : '' }}>Xóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tu_ngay">Từ ngày</label>
                                    <input type="date" name="tu_ngay" id="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="den_ngay">Đến ngày</label>
                                    <input type="date" name="den_ngay" id="den_ngay" class="form-control" value="{{ request('den_ngay') }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('giangvien.lich-su-nhap-diem.show', $lopHocPhan->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử nhập điểm</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($lichSu->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Không có lịch sử nhập điểm nào cho lớp học phần này.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Thời gian</th>
                                        <th>Mã SV</th>
                                        <th>Tên sinh viên</th>
                                        <th>Đầu điểm</th>
                                        <th>Cột điểm</th>
                                        <th>Điểm cũ</th>
                                        <th>Điểm mới</th>
                                        <th>Loại thao tác</th>
                                        <th>Lý do</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichSu as $index => $ls)
                                        @php
                                            $sinhVien = $ls->lopHocPhanSinhVien->sinhVien ?? null;
                                            $cauHinh = $ls->cauHinh ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $lichSu->firstItem() + $index }}</td>
                                            <td>
                                                <small>{{ $ls->created_at->format('d/m/Y H:i:s') }}</small>
                                            </td>
                                            <td><strong>{{ $sinhVien->ma_sinh_vien ?? 'N/A' }}</strong></td>
                                            <td>{{ $sinhVien->ho_ten ?? 'N/A' }}</td>
                                            <td>{{ $cauHinh->ten_dau_diem ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $ls->cot_diem }}</td>
                                            <td class="text-center">
                                                @if($ls->diem_cu !== null)
                                                    <span class="badge bg-secondary">{{ number_format($ls->diem_cu, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($ls->diem_moi !== null)
                                                    <span class="badge bg-primary">{{ number_format($ls->diem_moi, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->loai_thao_tac == 'them')
                                                    <span class="badge bg-success">Thêm</span>
                                                @elseif($ls->loai_thao_tac == 'sua')
                                                    <span class="badge bg-warning">Sửa</span>
                                                @elseif($ls->loai_thao_tac == 'xoa')
                                                    <span class="badge bg-danger">Xóa</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->ly_do)
                                                    <small class="text-muted" title="{{ $ls->ly_do }}">{{ Str::limit($ls->ly_do, 50) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $lichSu->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <a href="{{ route('giangvien.lich-su-nhap-diem.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <a href="{{ route('giangvien.nhap-diem.show', $lopHocPhan->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Nhập điểm
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection

