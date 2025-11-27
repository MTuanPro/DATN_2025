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
                                    <select name="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Chọn học kỳ --</option>
                                        @foreach ($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ $hocKyId == $hk->id ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }}
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
            @if ($hocKyId && count($monHocs) > 0)
                @php
                    $tongBuoiHocTatCa = 0;
                    $tongCoMat = 0;
                    $tongVang = 0;
                    $tongDiTre = 0;
                    $tongNghiPhep = 0;
                    $tongTyLe = 0;
                    $soMonCoDiemDanh = 0;
                    
                    foreach ($monHocs as $mh) {
                        if (isset($mh->tong_buoi_hoc) && $mh->tong_buoi_hoc > 0) {
                            $tongBuoiHocTatCa += $mh->tong_buoi_hoc;
                            $stats = $mh->diem_danh_stats;
                            if ($stats) {
                                $tongCoMat += $stats->co_mat ?? 0;
                                $tongVang += $stats->vang ?? 0;
                                $tongDiTre += $stats->di_tre ?? 0;
                                $tongNghiPhep += $stats->nghi_phep ?? 0;
                            }
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
                            <i class="bi bi-clipboard-check"></i> Tổng kết điểm danh học kỳ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Tổng số buổi học</h6>
                                    <h3 class="mb-0 text-primary">{{ $tongBuoiHocTatCa }}</h3>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-success bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Có mặt</h6>
                                            <h4 class="mb-0 text-success">{{ $tongCoMat }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Vắng</h6>
                                            <h4 class="mb-0 text-danger">{{ $tongVang }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Đi trễ</h6>
                                            <h4 class="mb-0 text-warning">{{ $tongDiTre }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-info bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Nghỉ phép</h6>
                                            <h4 class="mb-0 text-info">{{ $tongNghiPhep }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Tỷ lệ có mặt trung bình:</span>
                                        <span class="badge bg-{{ $tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger') }} fs-6">
                                            {{ $tyLeCoMatTong }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger') }}" 
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
                                            @if (isset($item->tong_buoi_hoc) && $item->tong_buoi_hoc > 0)
                                                @php
                                                    $stats = $item->diem_danh_stats;
                                                    $coMat = $stats ? ($stats->co_mat ?? 0) : 0;
                                                    $vang = $stats ? ($stats->vang ?? 0) : 0;
                                                    $diTre = $stats ? ($stats->di_tre ?? 0) : 0;
                                                    $nghiPhep = $stats ? ($stats->nghi_phep ?? 0) : 0;
                                                    $tyLe = $item->ty_le_co_mat ?? 0;
                                                @endphp
                                                <div class="small">
                                                    <div class="mb-1">
                                                        <span class="text-success">✓ {{ $coMat }}</span> /
                                                        <span class="text-danger">✗ {{ $vang }}</span> /
                                                        <span class="text-warning">⏱ {{ $diTre }}</span> /
                                                        <span class="text-info">☂ {{ $nghiPhep }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-{{ $tyLe >= 80 ? 'success' : ($tyLe >= 60 ? 'warning' : 'danger') }}">
                                                            {{ $tyLe }}%
                                                        </span>
                                                        <small class="text-muted">/ {{ $item->tong_buoi_hoc }} buổi</small>
                                                    </div>
                                                </div>
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
                                        <td colspan="10" class="text-center text-muted py-4">
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
