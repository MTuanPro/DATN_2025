@extends('layouts.layout-daotao')

@section('title', 'Thêm Môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Môn học</h3>
                    <p class="text-subtitle text-muted">Thêm môn học mới vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.mon-hoc.index') }}">Môn học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Môn học</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.mon-hoc.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ma_mon" class="form-label">Mã môn học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_mon') is-invalid @enderror"
                                        id="ma_mon" name="ma_mon" value="{{ old('ma_mon') }}" placeholder="VD: IT101"
                                        required>
                                    @error('ma_mon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ten_mon" class="form-label">Tên môn học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_mon') is-invalid @enderror"
                                        id="ten_mon" name="ten_mon" value="{{ old('ten_mon') }}"
                                        placeholder="VD: Lập trình căn bản" required>
                                    @error('ten_mon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi" class="form-label">Tổng số tín chỉ <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('so_tin_chi') is-invalid @enderror"
                                        id="so_tin_chi" name="so_tin_chi" value="{{ old('so_tin_chi') }}" min="1"
                                        max="5" required>
                                    @error('so_tin_chi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Phải bằng Lý thuyết + Thực hành</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_ly_thuyet" class="form-label">Tín chỉ lý thuyết <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('so_tin_chi_ly_thuyet') is-invalid @enderror"
                                        id="so_tin_chi_ly_thuyet" name="so_tin_chi_ly_thuyet"
                                        value="{{ old('so_tin_chi_ly_thuyet', 0) }}" min="0" max="5" required>
                                    @error('so_tin_chi_ly_thuyet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_thuc_hanh" class="form-label">Tín chỉ thực hành <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('so_tin_chi_thuc_hanh') is-invalid @enderror"
                                        id="so_tin_chi_thuc_hanh" name="so_tin_chi_thuc_hanh"
                                        value="{{ old('so_tin_chi_thuc_hanh', 0) }}" min="0" max="5"
                                        required>
                                    @error('so_tin_chi_thuc_hanh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="loai_mon" class="form-label">Loại môn học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('loai_mon') is-invalid @enderror" id="loai_mon"
                                        name="loai_mon" required>
                                        <option value="">-- Chọn loại môn --</option>
                                        <option value="dai_cuong" {{ old('loai_mon') == 'dai_cuong' ? 'selected' : '' }}>
                                            Đại cương</option>
                                        <option value="co_so_nganh"
                                            {{ old('loai_mon') == 'co_so_nganh' ? 'selected' : '' }}>Cơ sở ngành</option>
                                        <option value="chuyen_nganh_bat_buoc"
                                            {{ old('loai_mon') == 'chuyen_nganh_bat_buoc' ? 'selected' : '' }}>Chuyên ngành
                                            bắt buộc</option>
                                        <option value="chuyen_nganh_tu_chon"
                                            {{ old('loai_mon') == 'chuyen_nganh_tu_chon' ? 'selected' : '' }}>Chuyên ngành
                                            tự chọn</option>
                                        <option value="thuc_tap" {{ old('loai_mon') == 'thuc_tap' ? 'selected' : '' }}>
                                            Thực tập</option>
                                        <option value="do_an_tot_nghiep"
                                            {{ old('loai_mon') == 'do_an_tot_nghiep' ? 'selected' : '' }}>Đồ án tốt nghiệp
                                        </option>
                                    </select>
                                    @error('loai_mon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="khoa_id" class="form-label">Khoa quản lý <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('khoa_id') is-invalid @enderror" id="khoa_id"
                                        name="khoa_id" required>
                                        <option value="">-- Chọn khoa --</option>
                                        @foreach ($khoas as $khoa)
                                            <option value="{{ $khoa->id }}"
                                                {{ old('khoa_id') == $khoa->id ? 'selected' : '' }}>
                                                {{ $khoa->ten_khoa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('khoa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="hinh_thuc_day" class="form-label">Hình thức dạy <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('hinh_thuc_day') is-invalid @enderror"
                                        id="hinh_thuc_day" name="hinh_thuc_day" required>
                                        <option value="">-- Chọn hình thức --</option>
                                        <option value="offline" {{ old('hinh_thuc_day') == 'offline' ? 'selected' : '' }}>
                                            Offline (Trực tiếp)</option>
                                        <option value="online" {{ old('hinh_thuc_day') == 'online' ? 'selected' : '' }}>
                                            Online (Trực tuyến)</option>
                                        <option value="hybrid" {{ old('hinh_thuc_day') == 'hybrid' ? 'selected' : '' }}>
                                            Hybrid (Kết hợp)</option>
                                    </select>
                                    @error('hinh_thuc_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="thoi_luong_hoc" class="form-label">Thời lượng học (giờ)</label>
                                    <input type="number"
                                        class="form-control @error('thoi_luong_hoc') is-invalid @enderror"
                                        id="thoi_luong_hoc" name="thoi_luong_hoc" value="{{ old('thoi_luong_hoc') }}"
                                        min="1">
                                    @error('thoi_luong_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tối thiểu = Số tín chỉ × 15</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="so_buoi_hoc" class="form-label">Số buổi học</label>
                                    <input type="number" class="form-control @error('so_buoi_hoc') is-invalid @enderror"
                                        id="so_buoi_hoc" name="so_buoi_hoc" value="{{ old('so_buoi_hoc') }}"
                                        min="1">
                                    @error('so_buoi_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Số buổi học dự kiến (≥ 10 buổi)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="4"
                                        placeholder="Nhập mô tả chi tiết về môn học...">{{ old('mo_ta') }}</textarea>
                                    @error('mo_ta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu
                            </button>
                            <a href="{{ route('dao-tao.mon-hoc.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
