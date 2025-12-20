@extends('layouts.layout-daotao')

@section('title', 'Cấu hình Điểm danh')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Điểm danh test</h3>
                <p class="text-subtitle text-muted">Quản lý cài đặt điểm danh hệ thống</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cấu hình Điểm danh</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Cài đặt Điểm danh</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.cau-hinh.diem-danh.update') }}" method="POST" id="configForm">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="cho_phep_tuong_lai" 
                                       name="cho_phep_tuong_lai" 
                                       value="1"
                                       {{ $choPhepTuongLai ? 'checked' : '' }}
                                       onchange="this.form.submit();">
                                <label class="form-check-label" for="cho_phep_tuong_lai">
                                    <strong>Cho phép điểm danh tất cả buổi học </strong>
                                </label>
                            </div>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle"></i>
                                <strong>Khi bật:</strong> Giảng viên có thể điểm danh cho các buổi học khi chưa đến ngày học.<br>
                                <strong>Khi tắt:</strong> Giảng viên chỉ có thể điểm danh trong khi ca học cùng ngày đang diễn ra
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-lightbulb"></i> Hướng dẫn:</h6>
                        <ul class="mb-0">
                            <li><strong>Trạng thái hiện tại:</strong> 
                                @if($choPhepTuongLai)
                                    <span class="badge bg-success">Đã bật</span> - Giảng viên có thể điểm danh cho các buổi học tương lai
                                @else
                                    <span class="badge bg-danger">Đã tắt</span> - Giảng viên chỉ có thể điểm danh trong ngày học
                                @endif
                            </li>
                            <li>Thay đổi sẽ có hiệu lực ngay lập tức cho tất cả giảng viên trong hệ thống.</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

