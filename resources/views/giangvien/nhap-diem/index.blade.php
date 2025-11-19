@extends('layouts.layout-giangvien')

@section('title', 'Nhập điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Nhập điểm</h3>
                    <p class="text-subtitle text-muted">Quản lý nhập điểm lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Nhập điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lớp học phần</h4>
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

                    @if ($lopHocPhans->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Bạn chưa được phân công giảng dạy lớp học phần nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã lớp HP</th>
                                        <th>Tên lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Học kỳ</th>
                                        <th>Vai trò</th>
                                        <th>Số SV</th>
                                        <th>Tiến độ</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lopHocPhans as $lhp)
                                        <tr>
                                            <td><strong>{{ $lhp['ma_lop_hp'] }}</strong></td>
                                            <td>{{ $lhp['ten_lop_hp'] }}</td>
                                            <td>{{ $lhp['mon_hoc'] }}</td>
                                            <td>{{ $lhp['hoc_ky'] }}</td>
                                            <td>
                                                @if ($lhp['vai_tro'] === 'giang_vien_chinh')
                                                    <span class="badge bg-primary">Giảng viên chính</span>
                                                @else
                                                    <span class="badge bg-secondary">Giảng viên phụ</span>
                                                @endif
                                            </td>
                                            <td>{{ $lhp['tong_sv'] }}</td>
                                            <td>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar {{ $lhp['ty_le'] >= 100 ? 'bg-success' : 'bg-warning' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $lhp['ty_le'] }}%">
                                                        {{ $lhp['sv_co_diem'] }}/{{ $lhp['tong_sv'] }} ({{ $lhp['ty_le'] }}%)
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if (isset($lhp['da_ket_thuc']) && $lhp['da_ket_thuc'])
                                                    <span class="badge bg-dark">Kết thúc</span>
                                                @elseif ($lhp['da_khoa_diem'])
                                                    <span class="badge bg-danger">Đã khóa điểm</span>
                                                @else
                                                    <span class="badge bg-success">Đang mở</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($lhp['da_ket_thuc']) && $lhp['da_ket_thuc'])
                                                    <button class="btn btn-sm btn-secondary" disabled title="Lớp đã kết thúc">
                                                        <i class="bi bi-lock"></i> Đã kết thúc
                                                    </button>
                                                @else
                                                    <a href="{{ route('giangvien.nhap-diem.show', $lhp['id']) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil-square"></i> Nhập điểm
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
