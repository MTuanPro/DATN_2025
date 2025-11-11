@extends('layouts.layout-giangvien')

@section('title', 'Lịch dạy cá nhân')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch dạy cá nhân</h3>
                <p class="text-subtitle text-muted">Quản lý lịch giảng dạy theo ngày/tuần/tháng</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch dạy</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bộ lọc</h4>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('giangvien.schedule.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Chọn ngày:</label>
                                <input type="date" id="date" name="date" class="form-control" value="{{ request('date', $date) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="period">Hiển thị:</label>
                                <select id="period" name="period" class="form-select">
                                    <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Ngày</option>
                                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Tuần</option>
                                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Tháng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="{{ route('giangvien.schedule.export', ['date' => request('date', $date), 'period' => $period]) }}" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Xuất CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Lịch giảng dạy</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th>Thứ</th>
                                <th>Tiết</th>
                                <th>Giờ</th>
                                <th>Môn học</th>
                                <th>Lớp HP</th>
                                <th>Phòng</th>
                                <th>Link online</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $ev)
                                <tr>
                                    <td>{{ $ev['date'] }}</td>
                                    <td>{{ $ev['weekday'] }}</td>
                                    <td>{{ $ev['tiet_bat_dau'] ?? ($ev['tiet'] ?? '') }}-{{ $ev['tiet_ket_thuc'] ?? '' }}</td>
                                    <td>{{ $ev['gio_bat_dau'] ?? '' }} - {{ $ev['gio_ket_thuc'] ?? '' }}</td>
                                    <td>
                                        @if(!empty($ev['ma_mon']))
                                            <strong>{{ $ev['ma_mon'] }}</strong><br>
                                        @endif
                                        {{ $ev['ten_mon'] ?? 'N/A' }}
                                    </td>
                                    <td>{{ $ev['lop_hoc_phan'] ?? '' }}</td>
                                    <td>{{ $ev['phong'] ?? '' }}</td>
                                    <td>
                                        @if (!empty($ev['link_online']))
                                            <a href="{{ $ev['link_online'] }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-link-45deg"></i> Link
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Không có buổi học trong khoảng thời gian này.</td>
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
