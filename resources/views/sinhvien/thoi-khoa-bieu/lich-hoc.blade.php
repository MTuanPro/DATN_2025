@extends('layouts.layout-sinhvien')

@section('title', 'Lịch học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học</h3>
                    <p class="text-subtitle text-muted">
                        @if ($hocKy)
                            Học kỳ: {{ $hocKy->ten_hoc_ky . ' - ' . $hocKy->nam_hoc }}
                        @else
                            Chưa có học kỳ
                        @endif
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.thoi-khoa-bieu.index') }}">Thời khóa biểu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('sinh-vien.thoi-khoa-bieu.lich-hoc') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Học kỳ
                        </label>
                        <select name="hoc_ky_id" class="form-select form-select-lg">
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}" {{ $hocKy && $hocKy->id == $hk->id ? 'selected' : '' }}>
                                    {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clock-history"></i> Thời gian
                        </label>
                        <select name="thoi_gian" class="form-select form-select-lg" onchange="this.form.submit()">
                            <optgroup label="Tương lai">
                                <option value="7_ngay_toi" {{ $thoiGianFilter == '7_ngay_toi' ? 'selected' : '' }}>7 ngày tới</option>
                                <option value="14_ngay_toi" {{ $thoiGianFilter == '14_ngay_toi' ? 'selected' : '' }}>14 ngày tới</option>
                                <option value="30_ngay_toi" {{ $thoiGianFilter == '30_ngay_toi' ? 'selected' : '' }}>30 ngày tới</option>
                                <option value="60_ngay_toi" {{ $thoiGianFilter == '60_ngay_toi' ? 'selected' : '' }}>60 ngày tới</option>
                                <option value="90_ngay_toi" {{ $thoiGianFilter == '90_ngay_toi' ? 'selected' : '' }}>90 ngày tới</option>
                            </optgroup>
                            <optgroup label="Quá khứ">
                                <option value="7_ngay_truoc" {{ $thoiGianFilter == '7_ngay_truoc' ? 'selected' : '' }}>7 ngày trước</option>
                                <option value="14_ngay_truoc" {{ $thoiGianFilter == '14_ngay_truoc' ? 'selected' : '' }}>14 ngày trước</option>
                                <option value="30_ngay_truoc" {{ $thoiGianFilter == '30_ngay_truoc' ? 'selected' : '' }}>30 ngày trước</option>
                                <option value="60_ngay_truoc" {{ $thoiGianFilter == '60_ngay_truoc' ? 'selected' : '' }}>60 ngày trước</option>
                                <option value="90_ngay_truoc" {{ $thoiGianFilter == '90_ngay_truoc' ? 'selected' : '' }}>90 ngày trước</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search me-1"></i> Xem lịch học
                        </button>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('sinh-vien.lop-hoc-phan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại Lớp của tôi
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if (!$hocKy)
            <div class="alert alert-warning">
                <h4 class="alert-heading">Thông báo</h4>
                <p>Không tìm thấy học kỳ hiện tại.</p>
            </div>
        @else
            <!-- Bảng lịch học -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3"></i> Danh sách lịch học
                        @if (isset($startDate) && isset($endDate))
                            <small class="ms-2">({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</small>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($lichHocList->isEmpty())
                        <div class="alert alert-info m-4 mb-0">
                            <i class="bi bi-info-circle"></i> Không có lịch học trong khoảng thời gian đã chọn.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th width="150">Thứ</th>
                                        <th width="120">Ngày</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th width="150" class="text-center">Thời gian</th>
                                        <th width="100" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichHocList as $index => $lich)
                                        @php
                                            $ngayHoc = \Carbon\Carbon::parse($lich->ngay_hoc);
                                            $thuTrongTuan = $ngayHoc->dayOfWeek; // 0 = CN, 1 = T2, ..., 6 = T7
                                            $thuNames = [
                                                0 => 'Chủ nhật',
                                                1 => 'Thứ Hai',
                                                2 => 'Thứ Ba',
                                                3 => 'Thứ Tư',
                                                4 => 'Thứ Năm',
                                                5 => 'Thứ Sáu',
                                                6 => 'Thứ Bảy',
                                            ];
                                            $tenThu = $thuNames[$thuTrongTuan] ?? 'N/A';
                                            $lopHocPhan = $lich->lopHocPhan;
                                            $monHoc = $lopHocPhan ? $lopHocPhan->monHoc : null;
                                        @endphp
                                        <tr>
                                            <td class="text-center align-middle">{{ $lichHocList->firstItem() + $index }}</td>
                                            <td class="align-middle">
                                                <span class="badge bg-info">{{ $tenThu }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <strong>{{ $ngayHoc->format('d/m/Y') }}</strong>
                                            </td>
                                            <td class="align-middle">
                                                @if ($monHoc)
                                                    <code class="bg-light px-2 py-1 rounded">{{ $monHoc->ma_mon }}</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($monHoc)
                                                    <strong class="text-primary">{{ $monHoc->ten_mon }}</strong>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($lich->phongHoc)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-geo-alt"></i> {{ $lich->phongHoc->ten_phong }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Chưa xếp</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($lich->giangVien)
                                                    <i class="bi bi-person-fill text-primary"></i> {{ $lich->giangVien->ho_ten }}
                                                @else
                                                    <span class="text-muted">Chưa phân công</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($lich->gio_bat_dau && $lich->gio_ket_thuc)
                                                    <span class="badge bg-primary">
                                                        {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                    </span>
                                                @elseif ($lich->caHoc)
                                                    <span class="badge bg-primary">
                                                        {{ \Carbon\Carbon::parse($lich->caHoc->gio_bat_dau)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($lich->caHoc->gio_ket_thuc)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($lopHocPhan)
                                                    <a href="{{ route('sinh-vien.lop-hoc-phan.show', $lopHocPhan->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted">
                                        Đang xem {{ $lichHocList->firstItem() ?? 0 }} đến {{ $lichHocList->lastItem() ?? 0 }} 
                                        trong tổng số {{ $lichHocList->total() }} mục
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div class="btn-group">
                                            @if ($lichHocList->onFirstPage())
                                                <button class="btn btn-sm btn-outline-secondary" disabled>Trước</button>
                                            @else
                                                <a href="{{ $lichHocList->previousPageUrl() }}" class="btn btn-sm btn-outline-primary">Trước</a>
                                            @endif
                                            
                                            @php
                                                $currentPage = $lichHocList->currentPage();
                                                $lastPage = $lichHocList->lastPage();
                                                $startPage = max(1, $currentPage - 2);
                                                $endPage = min($lastPage, $currentPage + 2);
                                            @endphp
                                            
                                            @if ($startPage > 1)
                                                <a href="{{ $lichHocList->url(1) }}" class="btn btn-sm btn-outline-primary">1</a>
                                                @if ($startPage > 2)
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>...</button>
                                                @endif
                                            @endif
                                            
                                            @for ($page = $startPage; $page <= $endPage; $page++)
                                                @if ($page == $currentPage)
                                                    <button class="btn btn-sm btn-primary">{{ $page }}</button>
                                                @else
                                                    <a href="{{ $lichHocList->url($page) }}" class="btn btn-sm btn-outline-primary">{{ $page }}</a>
                                                @endif
                                            @endfor
                                            
                                            @if ($endPage < $lastPage)
                                                @if ($endPage < $lastPage - 1)
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>...</button>
                                                @endif
                                                <a href="{{ $lichHocList->url($lastPage) }}" class="btn btn-sm btn-outline-primary">{{ $lastPage }}</a>
                                            @endif
                                            
                                            @if ($lichHocList->hasMorePages())
                                                <a href="{{ $lichHocList->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Tiếp</a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>Tiếp</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

