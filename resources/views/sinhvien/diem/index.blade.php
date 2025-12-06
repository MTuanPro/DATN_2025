@extends('layouts.layout-sinhvien')

@section('title', 'Kết quả học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kết quả học tập</h3>
                    <p class="text-subtitle text-muted">Xem điểm các môn học theo học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kết quả học tập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thống kê tổng quan --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">GPA Tích lũy</h6>
                                    <h2 class="mb-0 text-primary">{{ number_format($gpaTichLuy, 2) }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-primary">
                                    <i class="bi bi-trophy text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">GPA Học kỳ</h6>
                                    <h2 class="mb-0 text-success">{{ number_format($gpaHocKy, 2) }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-success">
                                    <i class="bi bi-graph-up text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tín chỉ tích lũy</h6>
                                    <h2 class="mb-0 text-info">{{ $tongTinChiDat }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-info">
                                    <i class="bi bi-clipboard-check text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tỷ lệ điểm danh</h6>
                                    @php
                                        $tongTyLe = 0;
                                        $soMon = 0;
                                        foreach ($monHocs as $mh) {
                                            if (isset($mh->ty_le_co_mat)) {
                                                $tongTyLe += $mh->ty_le_co_mat;
                                                $soMon++;
                                            }
                                        }
                                        $tyLeTrungBinh = $soMon > 0 ? round($tongTyLe / $soMon, 1) : 0;
                                    @endphp
                                    <h2 class="mb-0 text-warning">{{ $tyLeTrungBinh }}%</h2>
                                </div>
                                <div class="avatar avatar-xl bg-warning">
                                    <i class="bi bi-person-check text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chọn học kỳ --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('sinh-vien.diem.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chọn học kỳ</label>
                                    <select name="hoc_ky_goi_y" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Chọn học kỳ --</option>
                                        @foreach ($kyHocs as $ky)
                                            <option value="{{ $ky }}" {{ $hocKyGoiY == $ky ? 'selected' : '' }}>
                                                Học kỳ {{ $ky }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('sinh-vien.diem.bang-diem') }}" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-text"></i> Xem bảng điểm tổng hợp
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tổng kết điểm danh --}}
            @if ($hocKyGoiY && count($monHocs) > 0)
                @php
                    $tongBuoiHocTatCa = 0;
                    $tongCoMat = 0;
                    $tongVang = 0;
                    $tongDiTre = 0;
                    $tongNghiPhep = 0;
                    $tongTyLe = 0;
                    $soMonCoDiemDanh = 0;
                    
                    $tongBuoiDiemDanhTatCa = 0;
                    foreach ($monHocs as $mh) {
                        $tongBuoiHocMon = $mh->tong_buoi_hoc ?? 0;
                        $tongBuoiHocTatCa += $tongBuoiHocMon;
                        
                        $stats = $mh->diem_danh_stats ?? null;
                        if ($stats) {
                            $tongCoMat += $stats->co_mat ?? 0;
                            $tongVang += $stats->vang ?? 0;
                            $tongDiTre += $stats->di_tre ?? 0;
                            $tongNghiPhep += $stats->nghi_phep ?? 0;
                            $tongBuoiDiemDanhTatCa += $stats->tong_buoi_diem_danh ?? 0;
                        }
                        
                        if ($tongBuoiHocMon > 0) {
                            $tongTyLe += $mh->ty_le_co_mat ?? 0;
                            $soMonCoDiemDanh++;
                        }
                    }
                    
                    $tyLeTrungBinh = $soMonCoDiemDanh > 0 ? round($tongTyLe / $soMonCoDiemDanh, 1) : 0;
                    $tyLeCoMatTong = $tongBuoiHocTatCa > 0 ? round(($tongCoMat / $tongBuoiHocTatCa) * 100, 1) : 0;
                @endphp
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-check"></i> Tổng kết điểm danh Học kỳ {{ $hocKyGoiY }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-4 bg-primary bg-opacity-10 rounded border border-primary">
                                    <h6 class="text-muted mb-2">
                                        <i class="bi bi-calendar-check"></i> Tổng số buổi học
                                    </h6>
                                    <h2 class="mb-0 text-primary fw-bold">{{ $tongBuoiHocTatCa }}</h2>
                                    <p class="text-muted mb-0 mt-2 small">
                                        Đã điểm danh: <strong>{{ $tongBuoiDiemDanhTatCa }}</strong> / {{ $tongBuoiHocTatCa }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-success bg-opacity-10 rounded border border-success">
                                            <h6 class="text-muted mb-1">
                                                <i class="bi bi-check-circle"></i> Có mặt
                                            </h6>
                                            <h4 class="mb-0 text-success fw-bold">{{ $tongCoMat }}</h4>
                                            @if($tongBuoiHocTatCa > 0)
                                                <small class="text-muted">
                                                    {{ round(($tongCoMat / $tongBuoiHocTatCa) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded border border-danger">
                                            <h6 class="text-muted mb-1">
                                                <i class="bi bi-x-circle"></i> Vắng
                                            </h6>
                                            <h4 class="mb-0 text-danger fw-bold">{{ $tongVang }}</h4>
                                            @if($tongBuoiHocTatCa > 0)
                                                <small class="text-muted">
                                                    {{ round(($tongVang / $tongBuoiHocTatCa) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded border border-warning">
                                            <h6 class="text-muted mb-1">
                                                <i class="bi bi-clock-history"></i> Đi trễ
                                            </h6>
                                            <h4 class="mb-0 text-warning fw-bold">{{ $tongDiTre }}</h4>
                                            @if($tongBuoiHocTatCa > 0)
                                                <small class="text-muted">
                                                    {{ round(($tongDiTre / $tongBuoiHocTatCa) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-info bg-opacity-10 rounded border border-info">
                                            <h6 class="text-muted mb-1">
                                                <i class="bi bi-umbrella"></i> Nghỉ phép
                                            </h6>
                                            <h4 class="mb-0 text-info fw-bold">{{ $tongNghiPhep }}</h4>
                                            @if($tongBuoiHocTatCa > 0)
                                                <small class="text-muted">
                                                    {{ round(($tongNghiPhep / $tongBuoiHocTatCa) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">
                                            <i class="bi bi-graph-up"></i> Tỷ lệ có mặt trung bình:
                                        </span>
                                        <span class="badge bg-{{ $tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger') }} fs-6 px-3 py-2">
                                            {{ $tyLeCoMatTong }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar bg-{{ $tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger') }} d-flex align-items-center justify-content-center fw-bold" 
                                             role="progressbar" 
                                             style="width: {{ $tyLeCoMatTong }}%"
                                             aria-valuenow="{{ $tyLeCoMatTong }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $tyLeCoMatTong }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Bảng điểm --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Điểm các môn học</h5>
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

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã môn</th>
                                    <th>Tên môn học</th>
                                    <th>Tín chỉ</th>
                                    <th class="text-center">Điểm (Hệ 10)</th>
                                    <th class="text-center">Điểm (Hệ 4)</th>
                                    <th class="text-center">Điểm chữ</th>
                                    <th class="text-center">Kết quả</th>
                                    <th class="text-center">Điểm danh</th>
                                    <th class="text-center">Điều kiện thi</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monHocs as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->lopHocPhan->monHoc->ma_mon }}</strong></td>
                                        <td>{{ $item->lopHocPhan->monHoc->ten_mon }}</td>
                                        <td class="text-center">{{ $item->lopHocPhan->monHoc->so_tin_chi }}</td>
                                        <td class="text-center">
                                            @if ($item->ketQuaHocTap && $item->ketQuaHocTap->diem_he_10)
                                                <strong
                                                    class="text-primary">{{ number_format($item->ketQuaHocTap->diem_he_10, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->ketQuaHocTap && $item->ketQuaHocTap->diem_he_4)
                                                {{ number_format($item->ketQuaHocTap->diem_he_4, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->ketQuaHocTap && $item->ketQuaHocTap->diem_chu)
                                                <span class="badge bg-{{ $item->ketQuaHocTap->diem_chu_badge }}">
                                                    {{ $item->ketQuaHocTap->diem_chu }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->ketQuaHocTap)
                                                @if ($item->ketQuaHocTap->qua_mon)
                                                    <span class="badge bg-success">Đạt</span>
                                                @else
                                                    <span class="badge bg-danger">Không đạt</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Chưa có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $tongBuoiHoc = $item->tong_buoi_hoc ?? 0;
                                                $stats = $item->diem_danh_stats ?? null;
                                                $coMat = $stats ? ($stats->co_mat ?? 0) : 0;
                                                $vang = $stats ? ($stats->vang ?? 0) : 0;
                                                $diTre = $stats ? ($stats->di_tre ?? 0) : 0;
                                                $nghiPhep = $stats ? ($stats->nghi_phep ?? 0) : 0;
                                                $tongBuoiDiemDanh = $stats ? ($stats->tong_buoi_diem_danh ?? 0) : 0;
                                                $tyLe = $item->ty_le_co_mat ?? 0;
                                            @endphp
                                            
                                            <div>
                                                <div class="mb-2">
                                                    <strong class="text-primary fs-6">
                                                        {{ $tongBuoiDiemDanh }} / {{ $tongBuoiHoc }}
                                                    </strong>
                                                    <small class="text-muted d-block">buổi điểm danh / tổng buổi học</small>
                                                </div>
                                                @if ($tongBuoiDiemDanh > 0)
                                                    <div class="d-flex justify-content-center gap-2 mb-2 flex-wrap">
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> {{ $coMat }}
                                                        </span>
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle"></i> {{ $vang }}
                                                        </span>
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-clock-history"></i> {{ $diTre }}
                                                        </span>
                                                        <span class="badge bg-info">
                                                            <i class="bi bi-umbrella"></i> {{ $nghiPhep }}
                                                        </span>
                                                    </div>
                                                    @if ($tongBuoiHoc > 0 && $tyLe > 0)
                                                        <div>
                                                            <span class="badge bg-{{ $tyLe >= 80 ? 'success' : ($tyLe >= 60 ? 'warning' : 'danger') }} fs-6">
                                                                Tỷ lệ: {{ $tyLe }}%
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                // Lấy điểm CC (Chuyên cần) từ NhapDiem
                                                $diemCC = null;
                                                $cauHinhCC = \App\Models\CauHinhDauDiem::where('lop_hoc_phan_id', $item->lopHocPhan->id)
                                                    ->where('ten_dau_diem', 'Chuyên cần')
                                                    ->first();
                                                
                                                if ($cauHinhCC) {
                                                    $diemCCRecord = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $item->id)
                                                        ->where('cau_hinh_id', $cauHinhCC->id)
                                                        ->first();
                                                    
                                                    if ($diemCCRecord) {
                                                        // Chuyển điểm hệ 10 sang hệ 4
                                                        $diemCC = $diemCCRecord->diem_so;
                                                        if ($diemCC >= 9.0) $diemCC = 4.0;
                                                        elseif ($diemCC >= 8.5) $diemCC = 3.5;
                                                        elseif ($diemCC >= 8.0) $diemCC = 3.0;
                                                        elseif ($diemCC >= 7.0) $diemCC = 2.5;
                                                        elseif ($diemCC >= 6.5) $diemCC = 2.0;
                                                        elseif ($diemCC >= 5.5) $diemCC = 1.5;
                                                        elseif ($diemCC >= 5.0) $diemCC = 1.0;
                                                        else $diemCC = 0;
                                                    }
                                                }
                                                
                                                // Kiểm tra điều kiện: điểm danh >= 80% VÀ điểm CC >= 2/4
                                                $duDieuKienDiemDanh = $tyLe >= 80;
                                                $duDieuKienDiemCC = $diemCC !== null && $diemCC >= 2.0;
                                                $duDieuKienThi = $duDieuKienDiemDanh && $duDieuKienDiemCC;
                                            @endphp
                                            @if ($tongBuoiDiemDanh > 0)
                                                @if($duDieuKienThi)
                                                    <span class="badge bg-success fs-6">
                                                        <i class="bi bi-check-circle-fill"></i> Đủ điều kiện
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="bi bi-x-circle-fill"></i> Không đủ điều kiện
                                                    </span>
                                                    <br><small class="text-danger mt-1 d-block">
                                                        @if(!$duDieuKienDiemDanh)
                                                            Điểm danh < 80%
                                                        @endif
                                                        @if(!$duDieuKienDiemCC)
                                                            {{ !$duDieuKienDiemDanh ? '<br>' : '' }}Điểm CC < 2/4
                                                        @endif
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('sinh-vien.diem.show', $item->lopHocPhan->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có điểm môn học nào trong học kỳ này</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
