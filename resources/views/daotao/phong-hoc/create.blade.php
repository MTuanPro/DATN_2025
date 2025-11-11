@extends('layouts.layout-daotao')

@section('title', 'Thêm Phòng học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Phòng học mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.phong-hoc.index') }}">Phòng học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
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
                    <form action="{{ route('dao-tao.phong-hoc.store') }}" method="POST">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="ma_phong" class="col-md-4 col-form-label">Mã Phòng <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ma_phong" name="ma_phong"
                                    class="form-control @error('ma_phong') is-invalid @enderror"
                                    value="{{ old('ma_phong') }}" placeholder="Ví dụ: A101, B205, LAB01..." required>
                                @error('ma_phong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="ten_phong" class="col-md-4 col-form-label">Tên Phòng <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_phong" name="ten_phong"
                                    class="form-control @error('ten_phong') is-invalid @enderror"
                                    value="{{ old('ten_phong') }}" placeholder="Ví dụ: Phòng học 101, Phòng thực hành..."
                                    required>
                                @error('ten_phong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="suc_chua" class="col-md-4 col-form-label">Sức chứa</label>
                            <div class="col-md-8">
                                <input type="number" id="suc_chua" name="suc_chua"
                                    class="form-control @error('suc_chua') is-invalid @enderror"
                                    value="{{ old('suc_chua') }}" min="1" max="500"
                                    placeholder="Số sinh viên tối đa">
                                @error('suc_chua')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="vi_tri" class="col-md-4 col-form-label">Vị trí</label>
                            <div class="col-md-8">
                                <input type="text" id="vi_tri" name="vi_tri"
                                    class="form-control @error('vi_tri') is-invalid @enderror" value="{{ old('vi_tri') }}"
                                    placeholder="Ví dụ: Tầng 1 nhà A, Tầng 2 nhà B...">
                                @error('vi_tri')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="loai_phong" class="col-md-4 col-form-label">Loại phòng</label>
                            <div class="col-md-8">
                                <select name="loai_phong" id="loai_phong"
                                    class="form-select @error('loai_phong') is-invalid @enderror">
                                    <option value="">-- Chọn loại phòng --</option>
                                    <option value="Lý thuyết" {{ old('loai_phong') == 'Lý thuyết' ? 'selected' : '' }}>Lý
                                        thuyết</option>
                                    <option value="Thực hành" {{ old('loai_phong') == 'Thực hành' ? 'selected' : '' }}>Thực
                                        hành</option>
                                    <option value="Phòng máy" {{ old('loai_phong') == 'Phòng máy' ? 'selected' : '' }}>
                                        Phòng máy</option>
                                    <option value="Hội trường" {{ old('loai_phong') == 'Hội trường' ? 'selected' : '' }}>
                                        Hội trường</option>
                                </select>
                                @error('loai_phong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="trang_thai" class="col-md-4 col-form-label">Trạng thái</label>
                            <div class="col-md-8">
                                <select name="trang_thai" id="trang_thai"
                                    class="form-select @error('trang_thai') is-invalid @enderror">
                                    <option value="Hoạt động"
                                        {{ old('trang_thai', 'Hoạt động') == 'Hoạt động' ? 'selected' : '' }}>Hoạt động
                                    </option>
                                    <option value="Bảo trì" {{ old('trang_thai') == 'Bảo trì' ? 'selected' : '' }}>Bảo trì
                                    </option>
                                    <option value="Không sử dụng"
                                        {{ old('trang_thai') == 'Không sử dụng' ? 'selected' : '' }}>Không sử dụng</option>
                                </select>
                                @error('trang_thai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="3" class="form-control @error('mo_ta') is-invalid @enderror"
                                    placeholder="Mô tả chi tiết về phòng học, trang thiết bị...">{{ old('mo_ta') }}</textarea>
                                @error('mo_ta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu
                                </button>
                                <a href="{{ route('dao-tao.phong-hoc.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
