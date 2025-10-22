@extends('layouts.layout-admin')

@section('title', 'Thêm Quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Quyền mới</h3>
                    <p class="text-subtitle text-muted">Tạo quyền mới trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.quyen.index') }}">Quyền</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Quyền</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.quyen.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ma_quyen" class="form-label">
                                        Mã quyền <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ma_quyen') is-invalid @enderror"
                                        id="ma_quyen" name="ma_quyen" value="{{ old('ma_quyen') }}"
                                        placeholder="VD: user.create">
                                    @error('ma_quyen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chỉ sử dụng chữ cái, số, dấu gạch ngang và gạch dưới</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ten_quyen" class="form-label">
                                        Tên quyền <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ten_quyen') is-invalid @enderror"
                                        id="ten_quyen" name="ten_quyen" value="{{ old('ten_quyen') }}"
                                        placeholder="VD: Tạo người dùng">
                                    @error('ten_quyen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="nhom_quyen_id" class="form-label">
                                Nhóm quyền <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('nhom_quyen_id') is-invalid @enderror" id="nhom_quyen_id"
                                name="nhom_quyen_id">
                                <option value="">-- Chọn nhóm quyền --</option>
                                @foreach ($nhomQuyens as $nhomQuyen)
                                    <option value="{{ $nhomQuyen->id }}"
                                        {{ old('nhom_quyen_id') == $nhomQuyen->id ? 'selected' : '' }}>
                                        {{ $nhomQuyen->ten_nhom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nhom_quyen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="4"
                                placeholder="Mô tả về quyền này...">{{ old('mo_ta') }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu quyền
                            </button>
                            <a href="{{ route('admin.quyen.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection


