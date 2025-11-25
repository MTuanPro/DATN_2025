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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                                    <th class="text-center">Thống kê điểm danh</th>
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
                                                $tk = $item->thong_ke_diem_danh ?? null;
                                            @endphp
                                            @if($tk && $tk['tong_buoi'] > 0)
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="mb-1">
                                                        <span class="badge 
                                                            @if($tk['ty_le_chuyen_can'] >= 80) bg-success
                                                            @elseif($tk['ty_le_chuyen_can'] >= 60) bg-warning
                                                            @else bg-danger
                                                            @endif">
                                                            {{ $tk['ty_le_chuyen_can'] }}%
                                                        </span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <span class="text-success">✓ {{ $tk['co_mat'] }}</span> / 
                                                        <span class="text-danger">✗ {{ $tk['vang'] }}</span>
                                                        @if($tk['di_tre'] > 0 || $tk['nghi_phep'] > 0)
                                                            <br>
                                                            <span class="text-warning">⏱ {{ $tk['di_tre'] }}</span> / 
                                                            <span class="text-info">☂ {{ $tk['nghi_phep'] }}</span>
                                                        @endif
                                                    </small>
                                                    <small class="text-muted">({{ $tk['tong_buoi'] }} buổi)</small>
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
