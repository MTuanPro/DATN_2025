@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Sử dụng phòng học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Sử dụng phòng học</h3>
                    <p class="text-subtitle text-muted">Thống kê mức độ sử dụng phòng học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sử dụng phòng học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('dao-tao.bao-cao.phong-hoc') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="{{ route('dao-tao.bao-cao.phong-hoc') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                                </a>
                                <button type="button" class="btn btn-success float-end">
                                    <i class="bi bi-file-excel"></i> Xuất Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldHome"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng phòng học</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_phong']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldCheck"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Đang sử dụng</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['dang_su_dung']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldChart"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tỷ lệ sử dụng</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['ty_le_su_dung'], 1) }}%</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Utilization Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết sử dụng phòng học</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Phòng học</th>
                                <th>Tòa nhà</th>
                                <th>Sức chứa</th>
                                <th>Loại phòng</th>
                                <th>Số buổi sử dụng</th>
                                <th>Tổng giờ</th>
                                <th>Tỷ lệ sử dụng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomUsage as $room)
                                <tr>
                                    <td><strong>{{ $room->ten_phong }}</strong></td>
                                    <td>{{ $room->vi_tri ?? 'N/A' }}</td>
                                    <td>{{ $room->suc_chua }} người</td>
                                    <td>
                                        @if($room->loai_phong == 'ly_thuyet')
                                            <span class="badge bg-info">Lý thuyết</span>
                                        @elseif($room->loai_phong == 'thuc_hanh')
                                            <span class="badge bg-warning">Thực hành</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $room->loai_phong }}</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-primary">{{ $room->so_buoi }}</span></td>
                                    <td>{{ $room->tong_gio }} giờ</td>
                                    <td>
                                        @php
                                            // Giả sử 1 tuần có 50 tiết (10 tiết/ngày x 5 ngày), 1 học kỳ 18 tuần
                                            $tietChuan = 50 * 18; // 900 tiết
                                            $tyLe = $tietChuan > 0 ? ($room->tong_gio / $tietChuan * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe >= 80 ? 'bg-danger' : ($tyLe >= 50 ? 'bg-success' : 'bg-warning') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min($tyLe, 100) }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $tietChuan = 900;
                                            $tyLe = $tietChuan > 0 ? ($room->tong_gio / $tietChuan * 100) : 0;
                                        @endphp
                                        @if($tyLe >= 80)
                                            <span class="badge bg-danger">Sử dụng cao</span>
                                        @elseif($tyLe >= 50)
                                            <span class="badge bg-success">Vừa phải</span>
                                        @else
                                            <span class="badge bg-warning">Ít sử dụng</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Utilization Distribution -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Phân bố mức độ sử dụng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $tietChuan = 900;
                        $utilizationGroups = [
                            ['label' => 'Ít sử dụng (<50%)', 'count' => $roomUsage->filter(fn($r) => ($r->tong_gio / $tietChuan * 100) < 50)->count(), 'color' => 'warning'],
                            ['label' => 'Vừa phải (50-80%)', 'count' => $roomUsage->filter(fn($r) => ($r->tong_gio / $tietChuan * 100) >= 50 && ($r->tong_gio / $tietChuan * 100) < 80)->count(), 'color' => 'success'],
                            ['label' => 'Sử dụng cao (≥80%)', 'count' => $roomUsage->filter(fn($r) => ($r->tong_gio / $tietChuan * 100) >= 80)->count(), 'color' => 'danger'],
                        ];
                    @endphp
                    <div class="col-md-12">
                        <ul class="list-group">
                            @foreach($utilizationGroups as $group)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $group['label'] }}
                                    <span class="badge bg-{{ $group['color'] }} rounded-pill">{{ $group['count'] }} phòng</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
