@extends('layouts.layout-daotao')

@section('title', 'Thống kê Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thống kê Học phí</h3>
                    <p class="text-subtitle text-muted">Báo cáo tổng hợp học phí - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thống kê</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Tổng sinh viên</h6>
                            <h3>{{ $stats['tong_sinh_vien'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Đã nộp đủ</h6>
                            <h3>{{ $stats['da_nop_du'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>Nộp một phần</h6>
                            <h3>{{ $stats['da_nop_mot_phan'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6>Quá hạn</h6>
                            <h3>{{ $stats['qua_han'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Tổng học phí</h6>
                            <h3 class="text-primary">{{ number_format($stats['tong_hoc_phi'] ?? 0, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Đã thu</h6>
                            <h3 class="text-success">{{ number_format($stats['da_thu'] ?? 0, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Còn lại</h6>
                            <h3 class="text-danger">{{ number_format($stats['con_lai'] ?? 0, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
