@extends('layouts.layout-sinhvien')

@section('title', 'Thời khóa biểu chi tiết')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thời khóa biểu chi tiết</h3>
                    <p class="text-subtitle text-muted">Lịch học theo tuần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.thoi-khoa-bieu.index') }}">Thời khóa
                                    biểu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc tuần -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('sinh-vien.thoi-khoa-bieu.chi-tiet') }}"
                    class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Học kỳ
                        </label>
                        <select name="hoc_ky_id" class="form-select form-select-lg" id="selectHocKy">
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}" {{ $selectedHocKy->id == $hk->id ? 'selected' : '' }}>
                                    {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-week"></i> Tuần
                        </label>
                        <input type="number" name="tuan" class="form-control form-control-lg"
                            value="{{ $tuan }}" min="1" max="20" placeholder="Nhập số tuần (1-20)">
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-1"></i> Xem lịch học
                            </button>
                            <a href="{{ route('sinh-vien.thoi-khoa-bieu.index') }}" class="btn btn-secondary btn-lg px-4">
                                <i class="bi bi-arrow-left me-1"></i> TKB tổng quan
                            </a>
                            <a href="{{ route('sinh-vien.thoi-khoa-bieu.export-pdf', ['hoc_ky_id' => $selectedHocKy->id, 'tuan' => $tuan]) }}"
                                class="btn btn-danger btn-lg px-4">
                                <i class="bi bi-file-pdf me-1"></i> Xuất PDF
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thông tin tuần học -->
        <div class="card mb-4 shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white">
                <div class="row text-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-calendar-range fs-3 mb-2"></i>
                            <h6 class="mb-1">Học kỳ</h6>
                            <p class="mb-0 fs-5 fw-bold">{{ $selectedHocKy->ten_hoc_ky }} - {{ $selectedHocKy->nam_hoc }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-calendar-week fs-3 mb-2"></i>
                            <h6 class="mb-1">Tuần hiện tại</h6>
                            <p class="mb-0 fs-5 fw-bold">Tuần {{ $tuan }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded p-3">
                            <i class="bi bi-clock-history fs-3 mb-2"></i>
                            <h6 class="mb-1">Thời gian</h6>
                            @php
                                $startDate = $selectedHocKy->ngay_bat_dau->copy()->addWeeks($tuan - 1);
                                $endDate = $startDate->copy()->addDays(6);
                            @endphp
                            <p class="mb-0 fs-5 fw-bold">{{ $startDate->format('d/m/Y') }} -
                                {{ $endDate->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch học chi tiết theo ngày -->
        @php
            $weekDays = [
                2 => 'Thứ Hai',
                3 => 'Thứ Ba',
                4 => 'Thứ Tư',
                5 => 'Thứ Năm',
                6 => 'Thứ Sáu',
                7 => 'Thứ Bảy',
                8 => 'Chủ Nhật',
            ];
        @endphp

        @foreach ($weekDays as $thu => $tenThu)
            @php
                $lichTrongNgay = $lichHoc->where('thu_trong_tuan', $thu);
            @endphp

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header {{ $lichTrongNgay->isEmpty() ? 'bg-light text-muted' : '' }}"
                    style="{{ $lichTrongNgay->isNotEmpty() ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);' : '' }}">
                    <h5 class="mb-0 {{ $lichTrongNgay->isNotEmpty() ? 'text-white' : '' }}">
                        <i class="bi bi-calendar-day me-2"></i>
                        {{ $tenThu }} - {{ $startDate->copy()->addDays($thu - 2)->format('d/m/Y') }}
                        @if ($lichTrongNgay->isNotEmpty())
                            <span class="badge bg-warning text-dark float-end">
                                <i class="bi bi-book"></i> {{ $lichTrongNgay->count() }} buổi học
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lichTrongNgay->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0 fs-5">Không có lịch học trong ngày này</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="100" class="text-center">Tiết</th>
                                        <th>Môn học</th>
                                        <th>Lớp học phần</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th width="100" class="text-center">Loại lớp</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichTrongNgay->sortBy('tiet_bat_dau') as $lich)
                                        <tr
                                            style="{{ $lich->lopHocPhanSinhVien->lopHocPhan->loai_lop == 'ly_thuyet' ? 'background-color: #e3f2fd;' : 'background-color: #fff3e0;' }}">
                                            <td class="text-center align-middle">
                                                <div class="badge bg-dark fs-6 px-3 py-2">
                                                    {{ $lich->tiet_bat_dau }} -
                                                    {{ $lich->tiet_bat_dau + $lich->so_tiet - 1 }}
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    {{ 6 + floor(($lich->tiet_bat_dau - 1) / 2) }}:{{ ($lich->tiet_bat_dau - 1) % 2 == 0 ? '00' : '50' }}
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-bold text-primary">
                                                    {{ $lich->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-code-square"></i>
                                                    {{ $lich->lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon }}
                                                </small>
                                            </td>
                                            <td class="align-middle">
                                                <code
                                                    class="bg-white px-2 py-1 rounded">{{ $lich->lopHocPhanSinhVien->lopHocPhan->ma_lop_hoc_phan }}</code>
                                            </td>
                                            <td class="align-middle">
                                                @if ($lich->phanCongGiangDay && $lich->phanCongGiangDay->phong)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-geo-alt"></i>
                                                        {{ $lich->phanCongGiangDay->phong->ten_phong }}
                                                    </span>
                                                @else
                                                    <span class="text-muted"><i class="bi bi-question-circle"></i> Chưa
                                                        xếp</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($lich->phanCongGiangDay && $lich->phanCongGiangDay->giangVien)
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                    {{ $lich->phanCongGiangDay->giangVien->ho_ten }}
                                                @else
                                                    <span class="text-muted">Chưa phân công</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($lich->lopHocPhanSinhVien->lopHocPhan->loai_lop == 'ly_thuyet')
                                                    <span class="badge bg-primary"><i class="bi bi-book"></i> Lý
                                                        thuyết</span>
                                                @else
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-laptop"></i>
                                                        Thực hành</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($lich->ghi_chu)
                                                    <small>{{ $lich->ghi_chu }}</small>
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
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Tự động submit khi đổi học kỳ
                $('#selectHocKy').change(function() {
                    $(this).closest('form').submit();
                });
            });
        </script>
    @endpush
@endsection
