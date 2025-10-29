@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa học kỳ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa học kỳ</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-ky.index') }}">Học kỳ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin học kỳ</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.hoc-ky.update', $hocKy->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ten_hoc_ky" class="form-label">Tên học kỳ <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('ten_hoc_ky') is-invalid @enderror" id="ten_hoc_ky"
                                        name="ten_hoc_ky" required>
                                        <option value="">-- Chọn học kỳ --</option>
                                        <option value="Học kỳ 1"
                                            {{ old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ 1' ? 'selected' : '' }}>Học
                                            kỳ 1</option>
                                        <option value="Học kỳ 2"
                                            {{ old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ 2' ? 'selected' : '' }}>Học
                                            kỳ 2</option>
                                        <option value="Học kỳ hè"
                                            {{ old('ten_hoc_ky', $hocKy->ten_hoc_ky) == 'Học kỳ hè' ? 'selected' : '' }}>Học
                                            kỳ hè</option>
                                    </select>
                                    @error('ten_hoc_ky')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nam_hoc" class="form-label">Năm học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nam_hoc') is-invalid @enderror"
                                        id="nam_hoc" name="nam_hoc" value="{{ old('nam_hoc', $hocKy->nam_hoc) }}"
                                        placeholder="VD: 2024-2025" required>
                                    @error('nam_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ngay_bat_dau') is-invalid @enderror"
                                        id="ngay_bat_dau" name="ngay_bat_dau"
                                        value="{{ old('ngay_bat_dau', $hocKy->ngay_bat_dau->format('Y-m-d')) }}" required>
                                    @error('ngay_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ngay_ket_thuc') is-invalid @enderror"
                                        id="ngay_ket_thuc" name="ngay_ket_thuc"
                                        value="{{ old('ngay_ket_thuc', $hocKy->ngay_ket_thuc->format('Y-m-d')) }}"
                                        required>
                                    @error('ngay_ket_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_bat_dau_dang_ky" class="form-label">Ngày bắt đầu đăng ký</label>
                                    <input type="date"
                                        class="form-control @error('ngay_bat_dau_dang_ky') is-invalid @enderror"
                                        id="ngay_bat_dau_dang_ky" name="ngay_bat_dau_dang_ky"
                                        value="{{ old('ngay_bat_dau_dang_ky', $hocKy->ngay_bat_dau_dang_ky?->format('Y-m-d')) }}">
                                    @error('ngay_bat_dau_dang_ky')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_ket_thuc_dang_ky" class="form-label">Ngày kết thúc đăng ký</label>
                                    <input type="date"
                                        class="form-control @error('ngay_ket_thuc_dang_ky') is-invalid @enderror"
                                        id="ngay_ket_thuc_dang_ky" name="ngay_ket_thuc_dang_ky"
                                        value="{{ old('ngay_ket_thuc_dang_ky', $hocKy->ngay_ket_thuc_dang_ky?->format('Y-m-d')) }}">
                                    @error('ngay_ket_thuc_dang_ky')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="la_hoc_ky_hien_tai"
                                        name="la_hoc_ky_hien_tai" value="1"
                                        {{ old('la_hoc_ky_hien_tai', $hocKy->la_hoc_ky_hien_tai) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="la_hoc_ky_hien_tai">
                                        Đặt làm học kỳ hiện tại
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.hoc-ky.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
