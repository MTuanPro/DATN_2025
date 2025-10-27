@extends('layouts.layout-giangvien')

@section('content')
<div class="container">
    <h2>Lịch dạy cá nhân</h2>

    <form method="get" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="date">Chọn ngày:</label>
            <input type="date" id="date" name="date" class="form-control ml-2" value="{{ request('date', $date) }}">
        </div>
        <div class="form-group mr-2">
            <label for="period">Hiển thị:</label>
            <select id="period" name="period" class="form-control ml-2">
                <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Ngày</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Tuần</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Tháng</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a href="{{ route('giangvien.schedule.export', ['date' => request('date', $date), 'period' => $period]) }}" class="btn btn-success ml-2">Xuất CSV</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Thứ</th>
                    <th>Tiết</th>
                    <th>Giờ</th>
                    <th>Phòng</th>
                    <th>Lớp HP</th>
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
                        <td>{{ $ev['phong'] ?? '' }}</td>
                        <td>{{ $ev['lop_hoc_phan'] ?? '' }}</td>
                        <td>
                            @if (!empty($ev['link_online']))
                                <a href="{{ $ev['link_online'] }}" target="_blank">Link</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Không có buổi học trong khoảng thời gian này.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
