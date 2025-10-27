@extends('layouts.layout-daotao')

@section('title', 'Thêm Học kỳ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Học kỳ Mới</h3>
                    <p class="text-subtitle text-muted">Nhập thông tin học kỳ vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-ky.index') }}">Học kỳ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Học kỳ</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dao-tao.hoc-ky.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    {{-- Tên học kỳ --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ten_hoc_ky" class="form-label">
                                            Tên học kỳ <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('ten_hoc_ky') is-invalid @enderror"
                                            id="ten_hoc_ky" name="ten_hoc_ky" value="{{ old('ten_hoc_ky') }}"
                                            placeholder="VD: HK1, HK2, HK Hè" required>
                                        @error('ten_hoc_ky')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Năm học --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="nam_hoc" class="form-label">
                                            Năm học <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('nam_hoc') is-invalid @enderror"
                                            id="nam_hoc" name="nam_hoc" value="{{ old('nam_hoc') }}"
                                            placeholder="VD: 2024-2025" required>
                                        @error('nam_hoc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày bắt đầu --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_bat_dau" class="form-label">
                                            Ngày bắt đầu <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('ngay_bat_dau') is-invalid @enderror"
                                            id="ngay_bat_dau" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}"
                                            required>
                                        @error('ngay_bat_dau')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày kết thúc --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_ket_thuc" class="form-label">
                                            Ngày kết thúc <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('ngay_ket_thuc') is-invalid @enderror"
                                            id="ngay_ket_thuc" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}"
                                            required>
                                        @error('ngay_ket_thuc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày bắt đầu đăng ký --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_bat_dau_dang_ky" class="form-label">
                                            Ngày bắt đầu đăng ký môn
                                        </label>
                                        <input type="date"
                                            class="form-control @error('ngay_bat_dau_dang_ky') is-invalid @enderror"
                                            id="ngay_bat_dau_dang_ky" name="ngay_bat_dau_dang_ky"
                                            value="{{ old('ngay_bat_dau_dang_ky') }}">
                                        @error('ngay_bat_dau_dang_ky')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày kết thúc đăng ký --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_ket_thuc_dang_ky" class="form-label">
                                            Ngày kết thúc đăng ký môn
                                        </label>
                                        <input type="date"
                                            class="form-control @error('ngay_ket_thuc_dang_ky') is-invalid @enderror"
                                            id="ngay_ket_thuc_dang_ky" name="ngay_ket_thuc_dang_ky"
                                            value="{{ old('ngay_ket_thuc_dang_ky') }}">
                                        @error('ngay_ket_thuc_dang_ky')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Mô tả --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="mo_ta" class="form-label">Mô tả</label>
                                        <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3"
                                            placeholder="Nhập mô tả về học kỳ">{{ old('mo_ta') }}</textarea>
                                        @error('mo_ta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Học kỳ hiện tại --}}
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="la_hoc_ky_hien_tai"
                                                id="la_hoc_ky_hien_tai" value="1"
                                                {{ old('la_hoc_ky_hien_tai') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="la_hoc_ky_hien_tai">
                                                Đặt làm học kỳ hiện tại
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> Chỉ có 1 học kỳ được đánh dấu là hiện tại
                                        </small>
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Lưu
                                    </button>
                                    <a href="{{ route('dao-tao.hoc-ky.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
