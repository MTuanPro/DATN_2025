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
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
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
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('sinhvien.thoi-khoa-bieu.index') }}" class="d-flex gap-2">
                        <select name="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}" {{ $hocKy->id == $hk->id ? 'selected' : '' }}>
                                    {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('sinhvien.thoi-khoa-bieu.export-pdf', ['hoc_ky_id' => $hocKy->id]) }}"
                        class="btn btn-danger">
                        <i class="bi bi-file-pdf"></i> Xuất PDF
                    </a>
                    <a href="{{ route('sinhvien.thoi-khoa-bieu.chi-tiet') }}" class="btn btn-info">
                        <i class="bi bi-calendar-week"></i> Xem chi tiết
                    </a>
                </div>
            </div>

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
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Tiết/Thứ</th>
                                    <th>Thứ 2</th>
                                    <th>Thứ 3</th>
                                    <th>Thứ 4</th>
                                    <th>Thứ 5</th>
                                    <th>Thứ 6</th>
                                    <th>Thứ 7</th>
                                    <th>Chủ nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($tiet = 1; $tiet <= 12; $tiet++)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <strong>{{ $tiet }}</strong>
                                        </td>
                                        @for ($thu = 2; $thu <= 8; $thu++)
                                            @php
                                                $lich = $thoiKhoaBieu[$thu][$tiet] ?? null;
                                            @endphp

                                            @if ($lich === 'span')
                                                {{-- Cell đã được merge --}}
                                            @elseif($lich)
                                                <td rowspan="{{ $lich['so_tiet'] }}"
                                                    class="align-middle {{ $lich['loai_lop'] == 'ly_thuyet' ? 'bg-info' : 'bg-warning' }} bg-opacity-25">
                                                    <div class="p-2">
                                                        <strong class="d-block">{{ $lich['mon_hoc'] }}</strong>
                                                        <small class="d-block text-muted">
                                                            <i class="bi bi-code-square"></i> {{ $lich['ma_mon'] }}
                                                        </small>
                                                        <hr class="my-1">
                                                        <small class="d-block">
                                                            <i class="bi bi-door-closed"></i> {{ $lich['phong'] }}
                                                        </small>
                                                        <small class="d-block">
                                                            <i class="bi bi-person"></i> {{ $lich['giang_vien'] }}
                                                        </small>
                                                        <small class="d-block text-primary">
                                                            <i class="bi bi-clock"></i> {{ $lich['gio_bat_dau'] }} -
                                                            {{ $lich['gio_ket_thuc'] }}
                                                        </small>
                                                    </div>
                                                </td>
                                            @else
                                                <td></td>
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
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách môn học trong học kỳ</h5>
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
