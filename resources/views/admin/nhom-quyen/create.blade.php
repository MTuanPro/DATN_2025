@extends('layouts.layout-admin')

@section('title', 'Thêm Nhóm quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Nhóm quyền mới</h3>
                    <p class="text-subtitle text-muted">Tạo nhóm quyền mới trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.nhom-quyen.index') }}">Nhóm quyền</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Nhóm quyền</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nhom-quyen.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ma_nhom" class="form-label">
                                        Mã nhóm quyền <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ma_nhom') is-invalid @enderror"
                                        id="ma_nhom" name="ma_nhom" value="{{ old('ma_nhom') }}"
                                        placeholder="VD: QUAN_LY_USER">
                                    @error('ma_nhom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chỉ sử dụng chữ cái, số, dấu gạch ngang và gạch dưới</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ten_nhom" class="form-label">
                                        Tên nhóm quyền <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ten_nhom') is-invalid @enderror"
                                        id="ten_nhom" name="ten_nhom" value="{{ old('ten_nhom') }}"
                                        placeholder="VD: Quản lý người dùng">
                                    @error('ten_nhom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="4"
                                placeholder="Mô tả về nhóm quyền này...">{{ old('mo_ta') }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu nhóm quyền
                            </button>
                            <a href="{{ route('admin.nhom-quyen.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection


