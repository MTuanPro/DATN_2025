@extends('layouts.layout-admin')

@section('title', 'Sửa Vai trò')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Vai trò</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin vai trò</p>
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
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Vai trò</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.vai-tro.update', $vaiTro) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ma_vai_tro" class="form-label">
                                                Mã vai trò <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('ma_vai_tro') is-invalid @enderror"
                                                id="ma_vai_tro" name="ma_vai_tro"
                                                value="{{ old('ma_vai_tro', $vaiTro->ma_vai_tro) }}"
                                                placeholder="VD: giang_vien">
                                            @error('ma_vai_tro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Chỉ sử dụng chữ cái, số, dấu gạch ngang và gạch
                                                dưới</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="ten_vai_tro" class="form-label">
                                                Tên vai trò <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control @error('ten_vai_tro') is-invalid @enderror"
                                                id="ten_vai_tro" name="ten_vai_tro"
                                                value="{{ old('ten_vai_tro', $vaiTro->ten_vai_tro) }}"
                                                placeholder="VD: Giảng viên">
                                            @error('ten_vai_tro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="mo_ta" class="form-label">Mô tả</label>
                                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3"
                                                placeholder="Mô tả về vai trò này...">{{ old('mo_ta', $vaiTro->mo_ta) }}</textarea>
                                            @error('mo_ta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="muc_do_uu_tien" class="form-label">
                                                Mức độ ưu tiên <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                class="form-control @error('muc_do_uu_tien') is-invalid @enderror"
                                                id="muc_do_uu_tien" name="muc_do_uu_tien"
                                                value="{{ old('muc_do_uu_tien', $vaiTro->muc_do_uu_tien) }}" min="1"
                                                max="100" placeholder="1-100">
                                            @error('muc_do_uu_tien')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Từ 1 (thấp nhất) đến 100 (cao nhất)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.vai-tro.index') }}" class="btn btn-secondary">
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
                                <strong>Số lượng người dùng:</strong>
                                <span class="badge bg-info">{{ $vaiTro->users_count }} người</span>
                            </div>

                            <div class="mb-3">
                                <strong>Ngày tạo:</strong><br>
                                <small class="text-muted">{{ $vaiTro->created_at->format('d/m/Y H:i') }}</small>
                            </div>

                            <div class="mb-3">
                                <strong>Cập nhật lần cuối:</strong><br>
                                <small class="text-muted">{{ $vaiTro->updated_at->format('d/m/Y H:i') }}</small>
                            </div>

                            @if ($vaiTro->users_count > 0)
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Vai trò này đang có {{ $vaiTro->users_count }} người dùng.
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
