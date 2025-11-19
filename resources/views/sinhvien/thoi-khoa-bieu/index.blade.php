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
                                <input type="hidden" name="thoi_gian" value="{{ $thoiGianFilter ?? '' }}" id="thoiGianInput">
                            </form>
                        </div>
                        <div class="col-md-8 mb-3 mb-md-0">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-eye"></i> Chế độ xem
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="view_mode_radio" id="view_co_dinh" 
                                               value="co_dinh" {{ ($viewMode ?? 'co_dinh') == 'co_dinh' && empty($thoiGianFilter ?? null) ? 'checked' : '' }}
                                               onchange="changeViewMode('co_dinh', '')">
                                        <label class="btn btn-outline-primary" for="view_co_dinh">
                                            <i class="bi bi-arrow-repeat"></i> Lịch cố định
                                        </label>

                                        <input type="radio" class="btn-check" name="view_mode_radio" id="view_full" 
                                               value="full" {{ ($viewMode ?? 'co_dinh') == 'full' && empty($thoiGianFilter ?? null) ? 'checked' : '' }}
                                               onchange="changeViewMode('full', '')">
                                        <label class="btn btn-outline-primary" for="view_full">
                                            <i class="bi bi-calendar-range"></i> Toàn bộ học kỳ
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-clock-history"></i> Lọc theo thời gian
                                    </label>
                                    <select name="thoi_gian" class="form-select form-select-lg" id="thoiGianSelect" onchange="changeViewMode('full', this.value)">
                                        <option value="">-- Chọn khoảng thời gian --</option>
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
                        @if ($thoiGianFilter && isset($startDate) && isset($endDate))
                            <small class="ms-2">({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</small>
                        @endif
                    </h5>
                    @if ($thoiGianFilter && isset($thoiKhoaBieuTheoNgay) && !empty($thoiKhoaBieuTheoNgay))
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="toggleViewMode" 
                                   onchange="toggleTableView()" checked>
                            <label class="form-check-label text-white" for="toggleViewMode">
                                Hiển thị theo ngày
                            </label>
                        </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if ($thoiGianFilter && isset($thoiKhoaBieuTheoNgay) && !empty($thoiKhoaBieuTheoNgay))
                        {{-- Hiển thị theo ngày --}}
                        <div id="viewTheoNgay" style="display: block;">
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-bordered table-hover mb-0" style="font-size: 0.8rem;">
                                    <thead class="table-dark sticky-top">
                                        <tr class="text-center">
                                            <th style="width: 120px; position: sticky; left: 0; background: #212529; z-index: 10;">
                                                <i class="bi bi-clock-history"></i> Ca học</th>
                                            @php
                                                $currentDate = $startDate->copy();
                                                $ngayHienThi = [];
                                                while ($currentDate <= $endDate) {
                                                    $ngayHienThi[] = $currentDate->copy();
                                                    $currentDate->addDay();
                                                }
                                            @endphp
                                            @foreach ($ngayHienThi as $ngay)
                                                <th style="min-width: 120px;" class="text-center">
                                                    <div>{{ $ngay->format('d/m') }}</div>
                                                    <small class="text-muted">{{ $ngay->locale('vi')->dayName }}</small>
                                                </th>
                                            @endforeach
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
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            {{ \Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                                @foreach ($ngayHienThi as $ngay)
                                                    @php
                                                        $ngayStr = $ngay->format('Y-m-d');
                                                        $lichs = $thoiKhoaBieuTheoNgay[$ngayStr][$caHoc->id] ?? [];
                                                    @endphp
                                                    <td class="align-middle p-2" style="min-height: 80px;">
                                                        @if (!empty($lichs))
                                                            @foreach ($lichs as $lich)
                                                                @php
                                                                    $lopHocPhan = $lich->lopHocPhan;
                                                                    // Bỏ qua nếu không có lớp học phần hoặc môn học
                                                                    if (!$lopHocPhan || !$lopHocPhan->monHoc) {
                                                                        continue;
                                                                    }
                                                                    $loaiLop = $lopHocPhan->loai_lop ?? null;
                                                                @endphp
                                                                <div class="mb-2 p-2 rounded" 
                                                                     style="{{ $loaiLop == 'ly_thuyet' ? 'background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 3px solid #2196F3;' : 'background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-left: 3px solid #FF9800;' }}">
                                                                    <div class="d-flex align-items-start mb-1">
                                                                        <span class="badge {{ $loaiLop == 'ly_thuyet' ? 'bg-primary' : 'bg-warning text-dark' }} me-1" style="font-size: 0.65rem;">
                                                                            {{ $loaiLop == 'ly_thuyet' ? 'LT' : 'TH' }}
                                                                        </span>
                                                                        <strong class="flex-grow-1" style="font-size: 0.75rem; line-height: 1.2;">
                                                                            {{ $lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                                                                        </strong>
                                                                    </div>
                                                                    <div class="text-muted" style="font-size: 0.65rem;">
                                                                        <div class="mb-1">
                                                                            <code class="bg-white px-1 rounded" style="font-size: 0.65rem;">
                                                                                {{ $lopHocPhan->monHoc->ma_mon ?? 'N/A' }}
                                                                            </code>
                                                                        </div>
                                                                        <div class="mb-1">
                                                                            <i class="bi bi-door-closed text-success"></i>
                                                                            {{ $lich->phongHoc->ten_phong ?? 'TBA' }}
                                                                        </div>
                                                                        <div class="mb-1">
                                                                            <i class="bi bi-person-fill text-primary"></i>
                                                                            {{ $lich->giangVien->ho_ten ?? 'TBA' }}
                                                                        </div>
                                                                        <div class="text-primary fw-bold">
                                                                            <i class="bi bi-clock-fill"></i>
                                                                            @if($lich->gio_bat_dau)
                                                                                {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }}
                                                                                - {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                                            @elseif($lich->caHoc)
                                                                                {{ \Carbon\Carbon::parse($lich->caHoc->gio_bat_dau)->format('H:i') }}
                                                                                - {{ \Carbon\Carbon::parse($lich->caHoc->gio_ket_thuc)->format('H:i') }}
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-center text-muted" style="font-size: 0.7rem;">-</div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="{{ count($ngayHienThi) + 1 }}" class="text-center text-muted py-4">
                                                    <i class="bi bi-info-circle"></i> Chưa có ca học nào được thiết lập
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Hiển thị theo thứ trong tuần (mặc định) --}}
                    <div id="viewTheoThu" style="display: {{ ($thoiGianFilter && isset($thoiKhoaBieuTheoNgay) && !empty($thoiKhoaBieuTheoNgay)) ? 'none' : 'block' }};">
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
        function changeViewMode(mode, thoiGian) {
            document.getElementById('viewModeInput').value = mode;
            if (thoiGian !== undefined && thoiGian !== '') {
                document.getElementById('thoiGianInput').value = thoiGian;
                // Nếu chọn filter thời gian, tự động chuyển sang chế độ full
                document.getElementById('viewModeInput').value = 'full';
            } else if (thoiGian === '') {
                // Nếu bỏ chọn filter thời gian, xóa giá trị
                document.getElementById('thoiGianInput').value = '';
            }
            document.getElementById('filterForm').submit();
        }
        
        // Khi chọn filter thời gian, tự động chuyển sang chế độ full
        document.getElementById('thoiGianSelect').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('view_full').checked = true;
            }
        });
        
        // Toggle giữa hiển thị theo thứ và theo ngày
        function toggleTableView() {
            const toggle = document.getElementById('toggleViewMode');
            const viewTheoThu = document.getElementById('viewTheoThu');
            const viewTheoNgay = document.getElementById('viewTheoNgay');
            
            if (toggle && toggle.checked) {
                if (viewTheoThu) viewTheoThu.style.display = 'none';
                if (viewTheoNgay) viewTheoNgay.style.display = 'block';
            } else {
                if (viewTheoThu) viewTheoThu.style.display = 'block';
                if (viewTheoNgay) viewTheoNgay.style.display = 'none';
            }
        }
    </script>
    @endpush
@endsection
