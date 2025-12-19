@extends('layouts.layout-daotao')

@section('title', 'Danh Sách Sinh Viên Dự Thi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Danh Sách Sinh Viên Dự Thi</h4>
            <p class="text-muted mb-0">
                {{ $lichThi->lopHocPhan->monHoc->ten_mon_hoc }} - 
                {{ $lichThi->lopHocPhan->ma_lop }}
            </p>
        </div>
        <div>
            <a href="{{ route('dao-tao.lich-thi.phan-phong', $lichThi) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> In danh sách
            </button>
        </div>
    </div>

    <!-- Thông tin lịch thi -->
    <div class="card mb-4 print-show">
        <div class="card-body">
            <div class="text-center mb-3">
                <h5>DANH SÁCH SINH VIÊN DỰ THI</h5>
                <h6>{{ $lichThi->lopHocPhan->monHoc->ten_mon_hoc }}</h6>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Lớp học phần:</strong> {{ $lichThi->lopHocPhan->ma_lop }}</p>
                    <p><strong>Học kỳ:</strong> {{ $lichThi->lopHocPhan->hocKy->ten_hoc_ky }}</p>
                    <p><strong>Ngày thi:</strong> {{ \Carbon\Carbon::parse($lichThi->ngay_thi)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Thời gian:</strong> 
                        {{ \Carbon\Carbon::parse($lichThi->gio_bat_dau)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($lichThi->gio_ket_thuc)->format('H:i') }}
                    </p>
                    <p><strong>Phòng thi:</strong> {{ $lichThi->phongThi->ten_phong ?? 'Chưa xác định' }}</p>
                    <p><strong>Hình thức:</strong> 
                        {{ $lichThi->hinh_thuc === 'tai_truong' ? 'Thi tại trường' : 'Thi trực tuyến' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4 print-hide">
        <div class="card-body">
            <form method="GET" action="{{ route('dao-tao.lich-thi.danh-sach-sinh-vien', $lichThi) }}">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Lọc theo phòng thi:</label>
                        <select name="phong_thi_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả phòng --</option>
                            @foreach($phongHocs as $phong)
                                <option value="{{ $phong->id }}" 
                                    {{ request('phong_thi_id') == $phong->id ? 'selected' : '' }}>
                                    {{ $phong->ten_phong }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="{{ route('dao-tao.lich-thi.danh-sach-sinh-vien', $lichThi) }}" 
                               class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row mb-4 print-show">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $sinhViens->count() }}</h3>
                    <small>Tổng số sinh viên</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $sinhViens->where('trang_thai', 'du_thi')->count() }}</h3>
                    <small>Dự thi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $sinhViens->where('trang_thai', 'vang_co_phep')->count() }}</h3>
                    <small>Vắng có phép</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $sinhViens->where('trang_thai', 'vang_khong_phep')->count() }}</h3>
                    <small>Vắng không phép</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sinh viên -->
    <div class="card">
        <div class="card-body">
            @if($sinhViens->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> Không có sinh viên nào.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">STT</th>
                                <th width="100" class="text-center">Số báo danh</th>
                                <th width="120">Mã sinh viên</th>
                                <th>Họ và tên</th>
                                <th width="150">Lớp hành chính</th>
                                <th width="150">Phòng thi</th>
                                <th width="120" class="text-center print-hide">Trạng thái</th>
                                <th width="150" class="text-center print-show">Chữ ký</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sinhViens as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center"><strong>{{ $item->so_bao_danh }}</strong></td>
                                    <td>{{ $item->sinhVien->ma_sinh_vien }}</td>
                                    <td>{{ $item->sinhVien->ho_ten }}</td>
                                    <td>{{ $item->sinhVien->nganh->ten_nganh ?? 'N/A' ?? 'N/A' }}</td>
                                    <td>{{ $item->phongThi->ten_phong ?? 'Chưa xác định' }}</td>
                                    <td class="text-center print-hide">
                                        @if($item->trang_thai === 'du_thi')
                                            <span class="badge bg-success">Dự thi</span>
                                        @elseif($item->trang_thai === 'vang_co_phep')
                                            <span class="badge bg-warning text-dark">Vắng có phép</span>
                                        @else
                                            <span class="badge bg-danger">Vắng không phép</span>
                                        @endif
                                    </td>
                                    <td class="print-show" style="height: 40px;"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Chữ ký giám thị (chỉ hiện khi in) -->
                <div class="print-show mt-5">
                    <div class="row">
                        <div class="col-6 text-center">
                            <p><strong>Giám thị 1</strong></p>
                            <p class="mt-5">(Ký và ghi rõ họ tên)</p>
                        </div>
                        <div class="col-6 text-center">
                            <p><strong>Giám thị 2</strong></p>
                            <p class="mt-5">(Ký và ghi rõ họ tên)</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    /* Ẩn các thành phần không cần in */
    .print-hide,
    .sidebar,
    .navbar,
    .btn,
    .card-header,
    .breadcrumb,
    nav {
        display: none !important;
    }

    /* Hiện các thành phần chỉ in */
    .print-show {
        display: block !important;
    }

    /* Điều chỉnh layout */
    body {
        margin: 0;
        padding: 15px;
    }

    .container-fluid {
        padding: 0;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    /* Điều chỉnh bảng */
    table {
        font-size: 12px;
    }

    /* Đảm bảo in trên một trang */
    @page {
        size: A4 portrait;
        margin: 1cm;
    }
}

/* Ẩn mặc định các phần tử chỉ hiện khi in */
.print-show {
    display: none;
}

@media print {
    .print-show {
        display: block !important;
    }
}
</style>
@endpush
@endsection
