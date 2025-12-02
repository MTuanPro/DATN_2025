@extends('layouts.layout-daotao')

@section('title', 'Đào tạo Dashboard')

@section('content')
    <div class="page-heading">
        <h3>Dashboard Đào tạo</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Sinh viên</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalStudents ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Giảng viên</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalTeachers ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Lớp học phần</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalClasses ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Học phần</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalSubjects ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Thống kê đăng ký học phần</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-registration"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Cảnh báo học vụ</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Loại</th>
                                                <th>Số lượng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="col-3">
                                                    <p class="font-bold mb-0">Học vụ</p>
                                                </td>
                                                <td class="col-auto">
                                                    <span class="badge bg-danger">{{ $warningsAcademic ?? 0 }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-3">
                                                    <p class="font-bold mb-0">Học phí</p>
                                                </td>
                                                <td class="col-auto">
                                                    <span class="badge bg-warning">{{ $warningsTuition ?? 0 }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h4 class="mb-0">
                                            <i class="bi bi-calendar-week"></i> Lịch dạy tuần này
                                        </h4>
                                        <p class="mb-0 mt-1" style="font-size: 0.85rem; opacity: 0.9;">
                                            <i class="bi bi-calendar-range"></i>
                                            Từ {{ \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m/Y') }} 
                                            đến {{ \Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    @if($lichDayTuanNay && $lichDayTuanNay->count() > 0)
                                        <span class="badge bg-light text-primary" style="font-size: 0.9rem;">
                                            {{ $lichDayTuanNay->count() }} buổi học
                                        </span>
                                    @endif
                                </div>
                                {{-- Bộ lọc --}}
                                <form method="GET" action="{{ route('dao-tao.dashboard') }}" class="mt-2">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select name="giang_vien_id" class="form-select form-select-sm" 
                                                    onchange="this.form.submit()" style="font-size: 0.85rem;">
                                                <option value="">-- Tất cả giảng viên --</option>
                                                @foreach($giangViens ?? [] as $gv)
                                                    <option value="{{ $gv->id }}" 
                                                            {{ request('giang_vien_id') == $gv->id ? 'selected' : '' }}>
                                                        {{ $gv->ma_giang_vien ?? '' }} - {{ $gv->ho_ten }}
                                                    </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="tim_kiem_giang_vien" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Tìm theo tên hoặc mã GV..." 
                                                   value="{{ request('tim_kiem_giang_vien') }}"
                                                   style="font-size: 0.85rem;">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="btn-group w-100" role="group">
                                                <button type="submit" class="btn btn-light btn-sm">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <a href="{{ route('dao-tao.dashboard') }}" 
                                                   class="btn btn-light btn-sm" title="Reset">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                @if($lichDayTuanNay && $lichDayTuanNay->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">Ngày</th>
                                                    <th style="width: 8%;">Thứ</th>
                                                    <th style="width: 8%;">Ca</th>
                                                    <th style="width: 10%;">Giờ</th>
                                                    <th style="width: 10%;">Phòng</th>
                                                    <th style="width: 10%;">Lớp HP</th>
                                                    <th style="width: 20%;">Môn học</th>
                                                    <th style="width: 14%;">Giảng viên</th>
                                                    <th style="width: 10%;">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($lichDayTuanNay as $lich)
                                                    <tr style="cursor: pointer;" 
                                                        onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                                        onmouseout="this.style.backgroundColor=''">
                                                        <td>
                                                            <strong class="text-primary">
                                                                {{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info text-white" style="font-size: 0.75rem; padding: 0.35em 0.65em;">
                                                                {{ \Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($lich->caHoc)
                                                                <span class="badge bg-primary text-white" style="font-size: 0.75rem; padding: 0.35em 0.65em;">
                                                                    <i class="bi bi-clock"></i> {{ $lich->caHoc->ten_ca }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-clock-history text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} - 
                                                                {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-building text-success"></i>
                                                                {{ $lich->phongHoc->ten_phong ?? 'Chưa phân phòng' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong class="text-primary">
                                                                {{ $lich->lopHocPhan->ma_lop_hp ?? 'N/A' }}
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark" style="font-size: 0.9rem;">
                                                                {{ $lich->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-person-circle text-warning"></i>
                                                                {{ $lich->giangVien->ho_ten ?? 'Chưa phân công' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($lich->trang_thai == 'chua_day')
                                                                <span class="badge bg-secondary text-white">
                                                                    <i class="bi bi-hourglass-split"></i> Chưa dạy
                                                                </span>
                                                            @elseif($lich->trang_thai == 'dang_day')
                                                                <span class="badge bg-info text-white">
                                                                    <i class="bi bi-play-circle"></i> Đang dạy
                                                                </span>
                                                            @elseif($lich->trang_thai == 'da_day')
                                                                <span class="badge bg-success text-white">
                                                                    <i class="bi bi-check-circle"></i> Đã dạy
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger text-white">
                                                                    <i class="bi bi-x-circle"></i> Hủy
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="bi bi-calendar-x" style="font-size: 4rem; color: #dee2e6;"></i>
                                        <p class="text-muted mt-3 mb-0" style="font-size: 1.1rem;">
                                            Không có lịch dạy trong tuần này
                                        </p>
                                        <small class="text-muted">
                                            Từ {{ \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m/Y') }} 
                                            đến {{ \Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Thông báo mới</h4>
                    </div>
                    <div class="card-content pb-4">
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="name ms-4">
                                <h5 class="mb-1">Thông báo mẫu</h5>
                                <h6 class="text-muted mb-0">Nội dung thông báo...</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
