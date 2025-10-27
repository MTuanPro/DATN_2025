@extends('layouts.layout-daotao')

@section('title', 'Thêm Lớp hành chính')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Lớp hành chính mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hanh-chinh.index') }}">Lớp hành
                                    chính</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin lớp hành chính</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.lop-hanh-chinh.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ma_lop" class="form-label">Mã lớp <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_lop') is-invalid @enderror"
                                        id="ma_lop" name="ma_lop" value="{{ old('ma_lop') }}" required>
                                    @error('ma_lop')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten_lop" class="form-label">Tên lớp <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_lop') is-invalid @enderror"
                                        id="ten_lop" name="ten_lop" value="{{ old('ten_lop') }}" required>
                                    @error('ten_lop')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="khoa_hoc_id" class="form-label">Khóa học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('khoa_hoc_id') is-invalid @enderror" id="khoa_hoc_id"
                                        name="khoa_hoc_id" required>
                                        <option value="">-- Chọn khóa học --</option>
                                        @foreach ($khoaHocs as $khoaHoc)
                                            <option value="{{ $khoaHoc->id }}"
                                                {{ old('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                                                {{ $khoaHoc->ma_khoa_hoc }} - {{ $khoaHoc->ten_khoa_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('khoa_hoc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nganh_id" class="form-label">Ngành <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('nganh_id') is-invalid @enderror" id="nganh_id"
                                        name="nganh_id" required>
                                        <option value="">-- Chọn ngành --</option>
                                        @foreach ($nganhs as $nganh)
                                            <option value="{{ $nganh->id }}"
                                                {{ old('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                                {{ $nganh->ma_nganh }} - {{ $nganh->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nganh_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="giang_vien_chu_nhiem_id" class="form-label">Giảng viên chủ nhiệm</label>
                                    <select class="form-select @error('giang_vien_chu_nhiem_id') is-invalid @enderror"
                                        id="giang_vien_chu_nhiem_id" name="giang_vien_chu_nhiem_id">
                                        <option value="">-- Chọn GVCN --</option>
                                        @foreach ($giangViens as $gv)
                                            <option value="{{ $gv->id }}"
                                                {{ old('giang_vien_chu_nhiem_id') == $gv->id ? 'selected' : '' }}>
                                                {{ $gv->ma_giang_vien }} - {{ $gv->ho_ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('giang_vien_chu_nhiem_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.lop-hanh-chinh.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Lưu lớp hành chính
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
