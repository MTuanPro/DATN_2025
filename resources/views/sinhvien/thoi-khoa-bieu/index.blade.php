@extends('layouts.layout-sinhvien')

@section('title', 'Thời khóa biểu cá nhân')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thời khóa biểu cá nhân</h3>
                    <p class="text-subtitle text-muted">
                        Học kỳ: {{ $hocKy ? $hocKy->ten_hoc_ky . ' - ' . $hocKy->nam_hoc : 'N/A' }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thời khóa biểu</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (!$hocKy)
            <div class="alert alert-warning">
                <h4 class="alert-heading">Thông báo</h4>
                <p>{{ $message ?? 'Không tìm thấy học kỳ hiện tại.' }}</p>
            </div>
        @else
            <!-- Lọc học kỳ và xuất PDF -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <form method="GET" action="{{ route('sinh-vien.thoi-khoa-bieu.index') }}">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-funnel"></i> Chọn học kỳ
                                </label>
                                <select name="hoc_ky_id" class="form-select form-select-lg" onchange="this.form.submit()">
                                    @foreach ($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ $hocKy->id == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <a href="{{ route('sinh-vien.thoi-khoa-bieu.chi-tiet') }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-calendar-week me-1"></i> Xem chi tiết theo tuần
                                </a>
                                <a href="{{ route('sinh-vien.thoi-khoa-bieu.export-pdf', ['hoc_ky_id' => $hocKy->id]) }}"
                                    class="btn btn-danger btn-lg">
                                    <i class="bi bi-file-pdf me-1"></i> Xuất PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cảnh báo đăng ký chưa được xếp lớp -->
            @if (isset($dangKyTam) && $dangKyTam > 0)
                <div class="alert alert-info">
                    <h5 class="alert-heading"><i class="bi bi-clock-history"></i> Thông báo</h5>
                    <p class="mb-0">Bạn có <strong>{{ $dangKyTam }}</strong> môn học đang chờ xếp lớp. Vui lòng đợi hệ thống xếp lớp tự động hoặc liên hệ phòng Đào tạo.</p>
                </div>
            @endif

            <!-- Cảnh báo lớp chưa có lịch học cố định -->
            @if (!empty($lopChuaCoLich))
                <div class="alert alert-warning">
                    <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Thông báo</h5>
                    <p class="mb-2">Các lớp sau chưa có lịch học cố định, vui lòng liên hệ phòng Đào tạo:</p>
                    <ul class="mb-0">
                        @foreach ($lopChuaCoLich as $lop)
                            <li>{{ $lop }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Cảnh báo lớp có lịch nhưng trạng thái không đúng -->
            @if (!empty($lopCoLichNhungTrangThaiSai))
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Cảnh báo</h5>
                    <p class="mb-2">Các lớp sau đã có lịch học cố định nhưng trạng thái không đúng, vui lòng liên hệ phòng Đào tạo:</p>
                    <ul class="mb-0">
                        @foreach ($lopCoLichNhungTrangThaiSai as $lop)
                            <li>
                                <strong>{{ $lop['ma_lop_hp'] }}</strong> - {{ $lop['ten_mon'] }} 
                                (Trạng thái: <span class="badge bg-secondary">{{ $lop['trang_thai'] }}</span>, 
                                Số lịch: {{ $lop['so_lich_co_dinh'] }})
                            </li>
                        @endforeach
                    </ul>
                    <p class="mb-0 mt-2"><small>Lưu ý: Chỉ các lớp có trạng thái "da_xep_lop" hoặc "dang_hoc" mới được hiển thị trong thời khóa biểu.</small></p>
                </div>
            @endif

            <!-- Debug Info -->
            @if (isset($debugInfo) && config('app.debug'))
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">🔍 Thông tin Debug</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tổng lớp đã xếp:</strong> {{ $debugInfo['tong_lop_da_xep'] ?? 0 }}</p>
                                <p class="mb-1"><strong>Tổng lớp đăng ký:</strong> {{ $debugInfo['tong_lop_dang_ky'] ?? 0 }}</p>
                                <p class="mb-1"><strong>Đăng ký tạm chờ xếp:</strong> {{ $debugInfo['dang_ky_tam_cho_xep'] ?? 0 }}</p>
                                <p class="mb-1"><strong>Lớp có lịch:</strong> {{ $debugInfo['lop_co_lich'] ?? 0 }}</p>
                                <p class="mb-1"><strong>Trạng thái học phí:</strong> {{ $debugInfo['hoc_phi_trang_thai'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        @if (isset($debugInfo['chi_tiet']) && !empty($debugInfo['chi_tiet']))
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mã lớp HP</th>
                                            <th>Tên môn</th>
                                            <th>Trạng thái</th>
                                            <th>Số lịch cố định</th>
                                            <th>Có lịch?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($debugInfo['chi_tiet'] as $item)
                                            <tr class="{{ $item['co_lich'] ? 'table-success' : 'table-warning' }}">
                                                <td>{{ $item['ma_lop_hp'] }}</td>
                                                <td>{{ $item['ten_mon'] }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item['trang_thai'] == 'da_xep_lop' || $item['trang_thai'] == 'dang_hoc' ? 'success' : 'secondary' }}">
                                                        {{ $item['trang_thai'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $item['so_lich_co_dinh'] }}</td>
                                                <td>
                                                    @if ($item['co_lich'])
                                                        <span class="badge bg-success">Có</span>
                                                    @else
                                                        <span class="badge bg-warning">Không</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Cảnh báo trùng lịch -->
            @if (!empty($trungLich))
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Cảnh báo trùng lịch!</h5>
                    <ul class="mb-0">
                        @foreach ($trungLich as $trung)
                            <li>
                                <strong>{{ $trung['thu'] }}, Tiết {{ $trung['tiet'] }}:</strong>
                                {{ $trung['mon_1'] }} và {{ $trung['mon_2'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bảng thời khóa biểu -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3"></i> Thời khóa biểu tổng quan
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th style="width: 70px; position: sticky; left: 0; background: #212529; z-index: 10;">
                                        Tiết</th>
                                    <th style="min-width: 150px;">Thứ 2</th>
                                    <th style="min-width: 150px;">Thứ 3</th>
                                    <th style="min-width: 150px;">Thứ 4</th>
                                    <th style="min-width: 150px;">Thứ 5</th>
                                    <th style="min-width: 150px;">Thứ 6</th>
                                    <th style="min-width: 150px;">Thứ 7</th>
                                    <th style="min-width: 150px;">Chủ nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($tiet = 1; $tiet <= 12; $tiet++)
                                    <tr>
                                        <td class="text-center align-middle fw-bold bg-light"
                                            style="position: sticky; left: 0; z-index: 5;">
                                            {{ $tiet }}
                                        </td>
                                        @for ($thu = 2; $thu <= 8; $thu++)
                                            @php
                                                $lich = $thoiKhoaBieu[$thu][$tiet] ?? null;
                                            @endphp

                                            @if ($lich === 'span')
                                                {{-- Cell đã được merge --}}
                                            @elseif($lich)
                                                <td rowspan="{{ $lich['so_tiet'] }}" class="align-middle p-0"
                                                    style="{{ $lich['loai_lop'] == 'ly_thuyet' ? 'background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 4px solid #2196F3;' : 'background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-left: 4px solid #FF9800;' }}">
                                                    <div class="p-3">
                                                        <div class="d-flex align-items-start mb-2">
                                                            <span
                                                                class="badge {{ $lich['loai_lop'] == 'ly_thuyet' ? 'bg-primary' : 'bg-warning text-dark' }} me-2">
                                                                {{ $lich['loai_lop'] == 'ly_thuyet' ? 'LT' : 'TH' }}
                                                            </span>
                                                            <strong class="flex-grow-1"
                                                                style="font-size: 0.9rem;">{{ $lich['mon_hoc'] }}</strong>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.8rem;">
                                                            <div class="mb-1">
                                                                <i class="bi bi-code-square text-secondary"></i>
                                                                <code
                                                                    class="bg-white px-1 rounded">{{ $lich['ma_mon'] }}</code>
                                                            </div>
                                                            <div class="mb-1">
                                                                <i class="bi bi-door-closed text-success"></i>
                                                                {{ $lich['phong'] }}
                                                            </div>
                                                            <div class="mb-1">
                                                                <i class="bi bi-person-fill text-primary"></i>
                                                                {{ $lich['giang_vien'] }}
                                                            </div>
                                                            <div class="text-primary fw-bold">
                                                                <i class="bi bi-clock-fill"></i> {{ $lich['gio_bat_dau'] }}
                                                                - {{ $lich['gio_ket_thuc'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="bg-light bg-opacity-25"></td>
                                            @endif
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Danh sách môn học đã đăng ký -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book-half"></i> Danh sách môn học trong học kỳ
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lopHocPhanSinhViens->isEmpty())
                        <div class="alert alert-info mb-0">
                            Bạn chưa đăng ký môn học nào trong học kỳ này.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th>Tín chỉ</th>
                                        <th>Lớp</th>
                                        <th>Giảng viên</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lopHocPhanSinhViens as $index => $lopSV)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>{{ $lopSV->lopHocPhan->monHoc->ma_mon }}</code></td>
                                            <td>{{ $lopSV->lopHocPhan->monHoc->ten_mon }}</td>
                                            <td>{{ $lopSV->lopHocPhan->monHoc->so_tin_chi }}</td>
                                            <td>{{ $lopSV->lopHocPhan->ma_lop_hoc_phan }}</td>
                                            <td>
                                                @if ($lopSV->lopHocPhan->giangVienChinh)
                                                    {{ $lopSV->lopHocPhan->giangVienChinh->giangVien->ho_ten }}
                                                @else
                                                    <span class="text-muted">Chưa phân công</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $lopSV->trang_thai_badge }}">
                                                    {{ $lopSV->trang_thai_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng tín chỉ:</strong></td>
                                        <td colspan="4">
                                            <strong>{{ $lopHocPhanSinhViens->sum(fn($item) => $item->lopHocPhan->monHoc->so_tin_chi) }}
                                                TC</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
