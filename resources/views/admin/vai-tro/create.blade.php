@extends('layouts.layout-admin')

@section('title', 'Thêm Vai trò mới')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Vai trò mới</h3>
                    <p class="text-subtitle text-muted">Tạo vai trò mới cho hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.vai-tro.index') }}">Vai trò</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Vai trò</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.vai-tro.store') }}" method="POST">
                                @csrf

                                {{-- Mã vai trò --}}
                                <div class="form-group mb-3">
                                    <label for="ma_vai_tro" class="form-label">
                                        Mã vai trò <span class="text-danger">*</span>
                                        <small class="text-muted">(VD: admin, giang_vien, sinh_vien)</small>
                                    </label>
                                    <input type="text" class="form-control @error('ma_vai_tro') is-invalid @enderror"
                                        id="ma_vai_tro" name="ma_vai_tro" value="{{ old('ma_vai_tro') }}"
                                        placeholder="VD: truong_phong_dt" required>
                                    @error('ma_vai_tro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Chỉ chữ thường, số, gạch ngang và gạch dưới
                                    </small>
                                </div>

                                {{-- Tên vai trò --}}
                                <div class="form-group mb-3">
                                    <label for="ten_vai_tro" class="form-label">
                                        Tên vai trò <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ten_vai_tro') is-invalid @enderror"
                                        id="ten_vai_tro" name="ten_vai_tro" value="{{ old('ten_vai_tro') }}"
                                        placeholder="VD: Trưởng phòng Đào tạo" required>
                                    @error('ten_vai_tro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả --}}
                                <div class="form-group mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3"
                                        placeholder="Nhập mô tả vai trò...">{{ old('mo_ta') }}</textarea>
                                    @error('mo_ta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mức độ ưu tiên --}}
                                <div class="form-group mb-4">
                                    <label for="muc_do_uu_tien" class="form-label">
                                        Mức độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('muc_do_uu_tien') is-invalid @enderror"
                                        id="muc_do_uu_tien" name="muc_do_uu_tien" value="{{ old('muc_do_uu_tien', 50) }}"
                                        min="1" max="100" required>
                                    @error('muc_do_uu_tien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Số càng nhỏ càng ưu tiên cao (1-100)
                                    </small>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.vai-tro.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Lưu vai trò
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
