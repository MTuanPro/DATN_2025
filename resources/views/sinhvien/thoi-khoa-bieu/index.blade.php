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
                        <div class="col-md-4 mb-3 mb-md-0">
                            <form method="GET" action="{{ route('sinh-vien.thoi-khoa-bieu.index') }}" id="filterForm">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-funnel"></i> Chọn học kỳ
                                </label>
                                <select name="hoc_ky_id" class="form-select form-select-lg" onchange="document.getElementById('filterForm').submit()">
                                    @foreach ($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ $hocKy->id == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="view_mode" value="{{ $viewMode ?? 'co_dinh' }}" id="viewModeInput">
                                <input type="hidden" name="mon_hoc_id" value="{{ $monHocFilter ?? '' }}" id="monHocInput">
                            </form>
                            <a href="{{ route('sinh-vien.lop-hoc-phan.index') }}" class="btn btn-secondary w-100 mt-2">
                                <i class="bi bi-arrow-left"></i> Quay lại Lớp của tôi
                            </a>
                        </div>
                        <div class="col-md-8 mb-3 mb-md-0">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-eye"></i> Chế độ xem
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="view_mode_radio" id="view_co_dinh" 
                                               value="co_dinh" {{ ($viewMode ?? 'co_dinh') == 'co_dinh' ? 'checked' : '' }}
                                               onchange="changeViewMode('co_dinh', '')">
                                        <label class="btn btn-outline-primary" for="view_co_dinh">
                                            <i class="bi bi-arrow-repeat"></i> Lịch cố định
                                        </label>

                                        <input type="radio" class="btn-check" name="view_mode_radio" id="view_full" 
                                               value="full" {{ ($viewMode ?? 'co_dinh') == 'full' ? 'checked' : '' }}
                                               onchange="changeViewMode('full', '')">
                                        <label class="btn btn-outline-primary" for="view_full">
                                            <i class="bi bi-calendar-range"></i> Toàn bộ học kỳ
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-book"></i> Lọc theo môn học
                                    </label>
                                    <select name="mon_hoc_id" class="form-select form-select-lg" id="monHocSelect" onchange="changeViewMode('full', this.value)">
                                        <option value="">-- Tất cả môn học --</option>
                                        @foreach($lopHocPhanSinhViens as $lopSV)
                                            @if($lopSV->lopHocPhan && $lopSV->lopHocPhan->monHoc)
                                                <option value="{{ $lopSV->lopHocPhan->monHoc->id }}" {{ (request('mon_hoc_id') == $lopSV->lopHocPhan->monHoc->id) ? 'selected' : '' }}>
                                                    {{ $lopSV->lopHocPhan->monHoc->ma_mon }} - {{ $lopSV->lopHocPhan->monHoc->ten_mon }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <a href="{{ route('sinh-vien.thoi-khoa-bieu.lich-hoc', ['hoc_ky_id' => $hocKy->id]) }}" class="btn btn-primary btn-lg">
                                    <i class="bi bi-calendar-week me-1"></i> Xem lịch học
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
                                <strong>{{ $trung['thu'] }}, {{ $trung['ca_hoc'] ?? 'Ca học' }}:</strong>
                                {{ $trung['mon_1'] }} và {{ $trung['mon_2'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bảng thời khóa biểu -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3"></i> Thời khóa biểu tổng quan
                        @if ($monHocFilter && isset($monHocSelected))
                            <small class="ms-2">({{ $monHocSelected->ma_mon }} - {{ $monHocSelected->ten_mon }})</small>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    {{-- Hiển thị theo thứ trong tuần --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="table-dark">
                                    <tr class="text-center">
                                        <th style="width: 120px; position: sticky; left: 0; background: #212529; z-index: 10;">
                                            <i class="bi bi-clock-history"></i> Ca học</th>
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
                                    @if(isset($caHocs) && $caHocs->count() > 0)
                                        @foreach ($caHocs as $caHoc)
                                        <tr>
                                            <td class="text-center align-middle fw-bold bg-light"
                                                style="position: sticky; left: 0; z-index: 5;">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span class="badge bg-primary mb-1">{{ $caHoc->ten_ca }}</span>
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="bi bi-clock"></i> 
                                                            {{ \Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i') }}
                                                        </small>
                                                    </div>
                                            </td>
                                            @for ($thu = 2; $thu <= 8; $thu++)
                                                @php
                                                        $lich = $thoiKhoaBieu[$thu][$caHoc->id] ?? null;
                                                @endphp

                                                    @if($lich)
                                                        <td class="align-middle p-0"
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
                                                                @if(isset($lich['is_full']) && $lich['is_full'] && isset($lich['lich_list']))
                                                                    {{-- Hiển thị danh sách các ngày học --}}
                                                                    <div class="mb-2">
                                                                        <i class="bi bi-calendar-event text-info"></i>
                                                                        <small>
                                                                            @foreach($lich['lich_list'] as $lichItem)
                                                                                {{ \Carbon\Carbon::parse($lichItem->ngay_hoc)->format('d/m') }}
                                                                                @if(!$loop->last), @endif
                                                                            @endforeach
                                                                        </small>
                                                                    </div>
                                                                @endif
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
                                                                    @if(isset($lich['ca_hoc']))
                                                                        <div class="mb-1">
                                                                            <i class="bi bi-clock-history text-info"></i>
                                                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                                                {{ $lich['ca_hoc']->ten_ca }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                <div class="text-primary fw-bold">
                                                                        <i class="bi bi-clock-fill"></i> 
                                                                        @if($lich['gio_bat_dau'])
                                                                            {{ \Carbon\Carbon::parse($lich['gio_bat_dau'])->format('H:i') }}
                                                                            - {{ \Carbon\Carbon::parse($lich['gio_ket_thuc'])->format('H:i') }}
                                                                        @else
                                                                            {{ \Carbon\Carbon::parse($lich['ca_hoc']->gio_bat_dau ?? '07:00')->format('H:i') }}
                                                                            - {{ \Carbon\Carbon::parse($lich['ca_hoc']->gio_ket_thuc ?? '08:50')->format('H:i') }}
                                                                        @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="bg-light bg-opacity-25"></td>
                                                @endif
                                            @endfor
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="bi bi-info-circle"></i> Chưa có ca học nào được thiết lập
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>

            <!-- Danh sách tất cả các buổi học (khi chọn "Toàn bộ kỳ học") -->
            @if($viewMode === 'full' && isset($lichHocChiTietFull) && $lichHocChiTietFull->count() > 0)
                <div class="card shadow-sm mb-4 mt-4">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-check"></i> Danh sách tất cả các buổi học trong học kỳ
                            <span class="badge bg-light text-dark ms-2">{{ $lichHocChiTietFull->count() }} buổi</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th width="120">Thứ</th>
                                        <th width="120">Ngày</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th>Ca</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th width="150" class="text-center">Thời gian</th>
                                        <th width="100" class="text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichHocChiTietFull as $index => $lich)
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
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
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
                                                @if ($lich->caHoc)
                                                    <span class="badge bg-primary">{{ $lich->caHoc->ten_ca }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
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
                                                @if ($lich->trang_thai == 'chua_day')
                                                    <span class="badge bg-secondary">Chưa dạy</span>
                                                @elseif($lich->trang_thai == 'dang_day')
                                                    <span class="badge bg-info">Đang dạy</span>
                                                @elseif($lich->trang_thai == 'da_day')
                                                    <span class="badge bg-success">Đã dạy</span>
                                                @else
                                                    <span class="badge bg-danger">Hủy</span>
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
                                        @if(!$lopSV->lopHocPhan || !$lopSV->lopHocPhan->monHoc)
                                            @continue
                                        @endif
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>{{ $lopSV->lopHocPhan->monHoc->ma_mon ?? 'N/A' }}</code></td>
                                            <td>{{ $lopSV->lopHocPhan->monHoc->ten_mon ?? 'Môn học đã bị xóa' }}</td>
                                            <td>{{ $lopSV->lopHocPhan->monHoc->so_tin_chi ?? 0 }}</td>
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
                                            <strong>{{ $lopHocPhanSinhViens->sum(fn($item) => $item->lopHocPhan->monHoc->so_tin_chi ?? 0) }}
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

    @push('scripts')
    <script>
        function changeViewMode(mode, monHocId) {
            document.getElementById('viewModeInput').value = mode;
            if (monHocId !== undefined && monHocId !== '') {
                document.getElementById('monHocInput').value = monHocId;
                // Nếu chọn filter môn học, tự động chuyển sang chế độ full
                document.getElementById('viewModeInput').value = 'full';
            } else if (monHocId === '') {
                // Nếu bỏ chọn filter môn học, xóa giá trị
                document.getElementById('monHocInput').value = '';
            }
            document.getElementById('filterForm').submit();
        }
        
        // Xử lý khi chọn môn học
        document.getElementById('monHocSelect')?.addEventListener('change', function() {
            changeViewMode('full', this.value);
        });
    </script>
    @endpush
@endsection
