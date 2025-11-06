@extends('layouts.layout-giangvien')

@section('title', 'Lớp chủ nhiệm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lớp chủ nhiệm</h3>
                    <p class="text-subtitle text-muted">Quản lý các lớp hành chính bạn phụ trách</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lớp chủ nhiệm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($lopChuNhiem->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3">Chưa có lớp chủ nhiệm</h5>
                        <p class="text-muted">Bạn chưa được phân công làm chủ nhiệm lớp nào.</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($lopChuNhiem as $lop)
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-people-fill text-primary"></i>
                                            {{ $lop->ma_lop }}
                                        </h5>
                                        <span
                                            class="badge bg-light-primary">{{ $lop->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</span>
                                    </div>

                                    <h6 class="text-muted mb-3">{{ $lop->ten_lop }}</h6>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-building"></i> Ngành: {{ $lop->nganh->ten_nganh ?? 'N/A' }}
                                        </small>
                                    </div>

                                    <hr>

                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h6 class="font-bold mb-0 text-primary">{{ $lop->tong_sinh_vien }}</h6>
                                            <small class="text-muted">Tổng SV</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="font-bold mb-0 text-info">{{ $lop->sinh_vien_nam }}</h6>
                                            <small class="text-muted">Nam</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="font-bold mb-0 text-danger">{{ $lop->sinh_vien_nu }}</h6>
                                            <small class="text-muted">Nữ</small>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('giangvien.lop-chu-nhiem.show', $lop->id) }}"
                                            class="btn btn-sm btn-primary flex-fill">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </a>
                                        <a href="{{ route('giangvien.lop-chu-nhiem.sinh-vien', $lop->id) }}"
                                            class="btn btn-sm btn-success flex-fill">
                                            <i class="bi bi-list-ul"></i> Sinh viên
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush
