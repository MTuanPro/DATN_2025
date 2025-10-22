@extends('layouts.layout-admin')

@section('title', 'Sửa Vai trò')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Vai trò</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin vai trò: {{ $vaiTro->ten_vai_tro }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.vai-tro.index') }}">Vai trò</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Vai trò</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.vai-tro.update', $vaiTro->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Mã vai trò --}}
                                <div class="form-group mb-3">
                                    <label for="ma_vai_tro" class="form-label">
                                        Mã vai trò <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('ma_vai_tro') is-invalid @enderror"
                                        id="ma_vai_tro" name="ma_vai_tro"
                                        value="{{ old('ma_vai_tro', $vaiTro->ma_vai_tro) }}" required>
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
                                        id="ten_vai_tro" name="ten_vai_tro"
                                        value="{{ old('ten_vai_tro', $vaiTro->ten_vai_tro) }}" required>
                                    @error('ten_vai_tro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mô tả --}}
                                <div class="form-group mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3">{{ old('mo_ta', $vaiTro->mo_ta) }}</textarea>
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
                                        id="muc_do_uu_tien" name="muc_do_uu_tien"
                                        value="{{ old('muc_do_uu_tien', $vaiTro->muc_do_uu_tien) }}" min="1"
                                        max="100" required>
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
                                        <i class="bi bi-check-circle"></i> Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Thông tin bổ sung --}}
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Số người dùng:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vaiTro->users_count }} người</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $vaiTro->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật:</strong></td>
                                    <td>{{ $vaiTro->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if ($vaiTro->users_count > 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Lưu ý:</strong> Vai trò này đang được gán cho {{ $vaiTro->users_count }} người dùng.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
