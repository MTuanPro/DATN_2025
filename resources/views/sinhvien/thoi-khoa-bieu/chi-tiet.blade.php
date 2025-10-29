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
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.thoi-khoa-bieu.index') }}">Thời khóa
                                    biểu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc tuần -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('sinhvien.thoi-khoa-bieu.chi-tiet') }}"
                    class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Học kỳ</label>
                        <select name="hoc_ky_id" class="form-select" id="selectHocKy">
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}" {{ $selectedHocKy->id == $hk->id ? 'selected' : '' }}>
                                    {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tuần</label>
                        <input type="number" name="tuan" class="form-control" value="{{ $tuan }}" min="1"
                            max="20">
                        <small class="text-muted">Tuần 1-20</small>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Xem
                        </button>
                        <a href="{{ route('sinhvien.thoi-khoa-bieu.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> TKB tổng quát
                        </a>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('sinhvien.thoi-khoa-bieu.export-pdf', ['hoc_ky_id' => $selectedHocKy->id, 'tuan' => $tuan]) }}"
                            class="btn btn-danger">
                            <i class="bi bi-file-pdf"></i> Xuất PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thông tin tuần học -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-muted">Học kỳ</h6>
                        <p class="mb-0"><strong>{{ $selectedHocKy->ten_hoc_ky }} -
                                {{ $selectedHocKy->nam_hoc }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Tuần hiện tại</h6>
                        <p class="mb-0"><strong>Tuần {{ $tuan }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Thời gian</h6>
                        <p class="mb-0">
                            @php
                                $startDate = $selectedHocKy->ngay_bat_dau->copy()->addWeeks($tuan - 1);
                                $endDate = $startDate->copy()->addDays(6);
                            @endphp
                            <strong>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</strong>
                        </p>
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

            <div class="card mb-3">
                <div class="card-header {{ $lichTrongNgay->isEmpty() ? 'bg-light' : 'bg-primary text-white' }}">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-day"></i>
                        {{ $tenThu }} - {{ $startDate->copy()->addDays($thu - 2)->format('d/m/Y') }}
                        @if ($lichTrongNgay->isNotEmpty())
                            <span class="badge bg-warning float-end">{{ $lichTrongNgay->count() }} buổi học</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lichTrongNgay->isEmpty())
                        <p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Không có lịch học</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="100">Tiết</th>
                                        <th>Môn học</th>
                                        <th>Lớp học phần</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th>Loại lớp</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichTrongNgay->sortBy('tiet_bat_dau') as $lich)
                                        <tr>
                                            <td class="text-center">
                                                <strong>{{ $lich->tiet_bat_dau }} -
                                                    {{ $lich->tiet_bat_dau + $lich->so_tiet - 1 }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ 6 + floor(($lich->tiet_bat_dau - 1) / 2) }}:{{ ($lich->tiet_bat_dau - 1) % 2 == 0 ? '00' : '50' }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong>{{ $lich->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $lich->lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon }}
                                                </small>
                                            </td>
                                            <td>
                                                <code>{{ $lich->lopHocPhanSinhVien->lopHocPhan->ma_lop_hoc_phan }}</code>
                                            </td>
                                            <td>
                                                @if ($lich->phanCongGiangDay && $lich->phanCongGiangDay->phong)
                                                    <i class="bi bi-geo-alt"></i>
                                                    {{ $lich->phanCongGiangDay->phong->ten_phong }}
                                                @else
                                                    <span class="text-muted">Chưa xếp</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($lich->phanCongGiangDay && $lich->phanCongGiangDay->giangVien)
                                                    {{ $lich->phanCongGiangDay->giangVien->ho_ten }}
                                                @else
                                                    <span class="text-muted">Chưa phân công</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($lich->lopHocPhanSinhVien->lopHocPhan->loai_lop == 'ly_thuyet')
                                                    <span class="badge bg-info">Lý thuyết</span>
                                                @else
                                                    <span class="badge bg-warning">Thực hành</span>
                                                @endif
                                            </td>
                                            <td>
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
