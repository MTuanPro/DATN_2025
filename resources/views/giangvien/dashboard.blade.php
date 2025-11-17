@extends('layouts.layout-giangvien')

@section('title', 'Giảng viên Dashboard')

@section('content')
    <div class="page-heading">
        <h3>Dashboard Giảng viên</h3>
        <p class="text-subtitle text-muted">Chào mừng, {{ auth()->user()->ho_ten }}</p>
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
                                        <h6 class="text-muted font-semibold">Lớp phụ trách</h6>
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
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
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
                                        <div class="stats-icon green">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Buổi học tuần này</h6>
                                        <h6 class="font-extrabold mb-0">{{ $weekSessions ?? 0 }}</h6>
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
                                        <h6 class="text-muted font-semibold">Cần nhập điểm</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pendingGrades ?? 0 }}</h6>
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
                                <h4>Lịch dạy tuần này</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Thứ</th>
                                                <th>Tiết</th>
                                                <th>Lớp HP</th>
                                                <th>Phòng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($lichDayTuanNay ?? [] as $lich)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}<br>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName }}</small>
                                                    </td>
                                                    <td>Tiết {{ $lich->tiet_bat_dau }}-{{ $lich->tiet_ket_thuc }}</td>
                                                    <td>{{ $lich->lopHocPhan->ten_lop_hp ?? $lich->lopHocPhan->ma_lop_hp }}<br>
                                                        <small class="text-muted">{{ $lich->lopHocPhan->monHoc->ten_mon ?? '' }}</small>
                                                    </td>
                                                    <td>{{ $lich->phongHoc->ten_phong ?? 'Online' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Chưa có lịch dạy tuần này</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Điểm danh gần đây</h4>
                            </div>
                            <div class="card-body">
                                @forelse($diemDanhGanDay ?? [] as $lich)
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div>
                                            <h6 class="mb-1">{{ $lich->lopHocPhan->monHoc->ten_mon ?? '' }}</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }} - 
                                                {{ $lich->diemDanh->count() ?? 0 }} SV điểm danh
                                            </p>
                                        </div>
                                        <a href="{{ route('giangvien.diem-danh.show', $lich->id) }}" class="btn btn-sm btn-primary">
                                            Xem
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Chưa có dữ liệu điểm danh</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Thông báo</h4>
                    </div>
                    <div class="card-content pb-4">
                        @forelse($thongBaoMoi ?? [] as $nguoiNhan)
                            <div class="recent-message d-flex px-4 py-3 border-bottom">
                                <div class="name ms-4">
                                    <h5 class="mb-1">{{ Str::limit($nguoiNhan->thongBao->tieu_de ?? '', 30) }}</h5>
                                    <h6 class="text-muted mb-0">{{ \Carbon\Carbon::parse($nguoiNhan->created_at)->diffForHumans() }}</h6>
                                </div>
                            </div>
                        @empty
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="name ms-4">
                                    <h5 class="mb-1">Chưa có thông báo mới</h5>
                                    <h6 class="text-muted mb-0">Bạn đã xem tất cả thông báo</h6>
                                </div>
                            </div>
                        @endforelse
                        @if(isset($thongBaoMoi) && $thongBaoMoi->count() > 0)
                            <div class="px-4 py-2">
                                <a href="{{ route('giangvien.thong-bao.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                    Xem tất cả
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Lớp chủ nhiệm</h4>
                    </div>
                    <div class="card-content pb-4">
                        <div class="px-4 py-3">
                            <p class="text-muted mb-0">{{ $homeRoomClass ?? 'Chưa có lớp chủ nhiệm' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
