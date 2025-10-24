@extends('layouts.layout-daotao')

@section('title', 'Sửa Phòng học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Phòng học</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin phòng học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.phong-hoc.index') }}">Phòng học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Phòng học</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.phong-hoc.update', $phongHoc->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ma_phong" class="form-label">Mã Phòng <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_phong') is-invalid @enderror"
                                        id="ma_phong" name="ma_phong" value="{{ old('ma_phong', $phongHoc->ma_phong) }}"
                                        required>
                                    @error('ma_phong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten_phong" class="form-label">Tên Phòng <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_phong') is-invalid @enderror"
                                        id="ten_phong" name="ten_phong" value="{{ old('ten_phong', $phongHoc->ten_phong) }}"
                                        required>
                                    @error('ten_phong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="suc_chua" class="form-label">Sức chứa</label>
                                    <input type="number" class="form-control @error('suc_chua') is-invalid @enderror"
                                        id="suc_chua" name="suc_chua" value="{{ old('suc_chua', $phongHoc->suc_chua) }}"
                                        min="1" max="500">
                                    @error('suc_chua')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="loai_phong" class="form-label">Loại phòng</label>
                                    <select class="form-select @error('loai_phong') is-invalid @enderror" id="loai_phong"
                                        name="loai_phong">
                                        <option value="">-- Chọn loại phòng --</option>
                                        <option value="Lý thuyết"
                                            {{ old('loai_phong', $phongHoc->loai_phong) == 'Lý thuyết' ? 'selected' : '' }}>
                                            Lý thuyết</option>
                                        <option value="Thực hành"
                                            {{ old('loai_phong', $phongHoc->loai_phong) == 'Thực hành' ? 'selected' : '' }}>
                                            Thực hành</option>
                                        <option value="Phòng máy"
                                            {{ old('loai_phong', $phongHoc->loai_phong) == 'Phòng máy' ? 'selected' : '' }}>
                                            Phòng máy</option>
                                        <option value="Hội trường"
                                            {{ old('loai_phong', $phongHoc->loai_phong) == 'Hội trường' ? 'selected' : '' }}>
                                            Hội trường</option>
                                    </select>
                                    @error('loai_phong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="trang_thai" class="form-label">Trạng thái</label>
                                    <select class="form-select @error('trang_thai') is-invalid @enderror" id="trang_thai"
                                        name="trang_thai">
                                        <option value="Hoạt động"
                                            {{ old('trang_thai', $phongHoc->trang_thai) == 'Hoạt động' ? 'selected' : '' }}>
                                            Hoạt động</option>
                                        <option value="Bảo trì"
                                            {{ old('trang_thai', $phongHoc->trang_thai) == 'Bảo trì' ? 'selected' : '' }}>
                                            Bảo trì</option>
                                        <option value="Không sử dụng"
                                            {{ old('trang_thai', $phongHoc->trang_thai) == 'Không sử dụng' ? 'selected' : '' }}>
                                            Không sử dụng</option>
                                    </select>
                                    @error('trang_thai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="vi_tri" class="form-label">Vị trí</label>
                                    <input type="text" class="form-control @error('vi_tri') is-invalid @enderror"
                                        id="vi_tri" name="vi_tri" value="{{ old('vi_tri', $phongHoc->vi_tri) }}">
                                    @error('vi_tri')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3">{{ old('mo_ta', $phongHoc->mo_ta) }}</textarea>
                                    @error('mo_ta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.phong-hoc.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
