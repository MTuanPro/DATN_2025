@extends('layouts.layout-admin')

@section('title', 'Sửa Nhóm quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Nhóm quyền</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin nhóm quyền</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.nhom-quyen.index') }}">Nhóm quyền</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Nhóm quyền</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.nhom-quyen.update', $nhomQuyen) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ma_nhom" class="form-label">
                                                Mã nhóm quyền <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('ma_nhom') is-invalid @enderror"
                                                id="ma_nhom" name="ma_nhom"
                                                value="{{ old('ma_nhom', $nhomQuyen->ma_nhom) }}"
                                                placeholder="VD: QUAN_LY_USER">
                                            @error('ma_nhom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Chỉ sử dụng chữ cái, số, dấu gạch ngang và gạch
                                                dưới</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ten_nhom" class="form-label">
                                                Tên nhóm quyền <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('ten_nhom') is-invalid @enderror"
                                                id="ten_nhom" name="ten_nhom"
                                                value="{{ old('ten_nhom', $nhomQuyen->ten_nhom) }}"
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
                                        placeholder="Mô tả về nhóm quyền này...">{{ old('mo_ta', $nhomQuyen->mo_ta) }}</textarea>
                                    @error('mo_ta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.nhom-quyen.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin bổ sung</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Số lượng quyền:</strong>
                                <span class="badge bg-info">{{ $nhomQuyen->quyens_count }} quyền</span>
                            </div>

                            <div class="mb-3">
                                <strong>Ngày tạo:</strong><br>
                                <small class="text-muted">{{ $nhomQuyen->created_at->format('d/m/Y H:i') }}</small>
                            </div>

                            <div class="mb-3">
                                <strong>Cập nhật lần cuối:</strong><br>
                                <small class="text-muted">{{ $nhomQuyen->updated_at->format('d/m/Y H:i') }}</small>
                            </div>

                            @if ($nhomQuyen->quyens_count > 0)
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Nhóm quyền này đang có {{ $nhomQuyen->quyens_count }} quyền.
                                    Hãy cẩn thận khi thay đổi!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


