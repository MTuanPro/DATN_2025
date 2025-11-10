@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Tải giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Tải giảng viên</h3>
                    <p class="text-subtitle text-muted">Khối lượng công việc của giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.bao-cao.index') }}">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tải giảng viên</li>
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
                <form method="GET" action="{{ route('dao-tao.bao-cao.tai-giang-vien') }}">
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
                                <a href="{{ route('dao-tao.bao-cao.tai-giang-vien') }}" class="btn btn-secondary">
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
                                    <i class="iconly-boldUser"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Tổng giảng viên</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['total_giang_vien']) }}</h6>
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
                                    <i class="iconly-boldDocument"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">TB lớp/GV</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['avg_lop_per_gv'], 1) }}</h6>
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
                                <h6 class="text-muted font-semibold">TB giờ/GV</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($statistics['avg_gio_per_gv'], 1) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Chi tiết tải giảng viên</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Mã GV</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Khoa</th>
                                <th>Số lớp</th>
                                <th>Tổng tín chỉ</th>
                                <th>Tổng giờ</th>
                                <th>Tải</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workloadData as $gv)
                                <tr>
                                    <td><strong>{{ $gv->ma_giang_vien }}</strong></td>
                                    <td>{{ $gv->ho_ten }}</td>
                                    <td>{{ $gv->email }}</td>
                                    <td>{{ $gv->ten_khoa ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $gv->so_lop }}</span></td>
                                    <td>{{ $gv->tong_tin_chi }}</td>
                                    <td>{{ $gv->tong_gio }}</td>
                                    <td>
                                        @php
                                            $taiChuan = 360; // Giờ chuẩn theo quy định
                                            $tyLe = $taiChuan > 0 ? ($gv->tong_gio / $taiChuan * 100) : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $tyLe > 120 ? 'bg-danger' : ($tyLe >= 80 ? 'bg-success' : 'bg-warning') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min($tyLe, 100) }}%">
                                                {{ number_format($tyLe, 1) }}%
                                            </div>
                                        </div>
                                        @if($tyLe > 120)
                                            <small class="text-danger">Quá tải {{ number_format($tyLe - 100, 1) }}%</small>
                                        @elseif($tyLe < 80)
                                            <small class="text-warning">Thiếu {{ number_format(100 - $tyLe, 1) }}%</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Workload Distribution -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Phân bố tải giảng viên</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Theo số lớp</h6>
                        <ul class="list-group">
                            @php
                                $lopGroups = [
                                    ['label' => '1-2 lớp', 'count' => $workloadData->whereBetween('so_lop', [1, 2])->count()],
                                    ['label' => '3-4 lớp', 'count' => $workloadData->whereBetween('so_lop', [3, 4])->count()],
                                    ['label' => '5-6 lớp', 'count' => $workloadData->whereBetween('so_lop', [5, 6])->count()],
                                    ['label' => '>6 lớp', 'count' => $workloadData->where('so_lop', '>', 6)->count()],
                                ];
                            @endphp
                            @foreach($lopGroups as $group)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $group['label'] }}
                                    <span class="badge bg-primary rounded-pill">{{ $group['count'] }} GV</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Theo tải công việc</h6>
                        <ul class="list-group">
                            @php
                                $taiChuan = 360;
                                $taiGroups = [
                                    ['label' => 'Dưới 80% (Thiếu tải)', 'count' => $workloadData->filter(fn($gv) => ($gv->tong_gio / $taiChuan * 100) < 80)->count(), 'color' => 'warning'],
                                    ['label' => '80-120% (Chuẩn)', 'count' => $workloadData->filter(fn($gv) => ($gv->tong_gio / $taiChuan * 100) >= 80 && ($gv->tong_gio / $taiChuan * 100) <= 120)->count(), 'color' => 'success'],
                                    ['label' => 'Trên 120% (Quá tải)', 'count' => $workloadData->filter(fn($gv) => ($gv->tong_gio / $taiChuan * 100) > 120)->count(), 'color' => 'danger'],
                                ];
                            @endphp
                            @foreach($taiGroups as $group)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $group['label'] }}
                                    <span class="badge bg-{{ $group['color'] }} rounded-pill">{{ $group['count'] }} GV</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
