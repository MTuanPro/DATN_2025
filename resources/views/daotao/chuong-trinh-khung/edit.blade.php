@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa môn học trong CTĐT')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa môn học trong CTĐT</h3>
                    <p class="text-subtitle text-muted">{{ $chuongTrinhKhung->chuyenNganh->nganh->khoa->ten_khoa }} -
                        {{ $chuongTrinhKhung->chuyenNganh->nganh->ten_nganh }} -
                        {{ $chuongTrinhKhung->chuyenNganh->ten_chuyen_nganh }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.chuong-trinh-khung.index') }}">Chương
                                    trình khung</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin môn học trong CTĐT</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.chuong-trinh-khung.update', $chuongTrinhKhung->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="chuyen_nganh_id" value="{{ $chuongTrinhKhung->chuyen_nganh_id }}">
                        <input type="hidden" name="mon_hoc_id" value="{{ $chuongTrinhKhung->mon_hoc_id }}">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Môn học:
                                    <strong>{{ $chuongTrinhKhung->monHoc->ma_mon }} -
                                        {{ $chuongTrinhKhung->monHoc->ten_mon }}
                                        ({{ $chuongTrinhKhung->monHoc->so_tin_chi }} TC)</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="hoc_ky_goi_y" class="form-label">Học kỳ gợi ý <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('hoc_ky_goi_y') is-invalid @enderror"
                                        id="hoc_ky_goi_y" name="hoc_ky_goi_y" required>
                                        <option value="">-- Chọn học kỳ --</option>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('hoc_ky_goi_y', $chuongTrinhKhung->hoc_ky_goi_y) == $i ? 'selected' : '' }}>
                                                Học kỳ {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('hoc_ky_goi_y')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="loai_mon_hoc" class="form-label">Loại môn học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('loai_mon_hoc') is-invalid @enderror"
                                        id="loai_mon_hoc" name="loai_mon_hoc" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="dai_cuong"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'dai_cuong' ? 'selected' : '' }}>
                                            Đại cương</option>
                                        <option value="co_so_nganh"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'co_so_nganh' ? 'selected' : '' }}>
                                            Cơ sở ngành</option>
                                        <option value="chuyen_nganh_bat_buoc"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'chuyen_nganh_bat_buoc' ? 'selected' : '' }}>
                                            Chuyên ngành bắt buộc</option>
                                        <option value="chuyen_nganh_tu_chon"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'chuyen_nganh_tu_chon' ? 'selected' : '' }}>
                                            Chuyên ngành tự chọn</option>
                                        <option value="thuc_tap"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'thuc_tap' ? 'selected' : '' }}>
                                            Thực tập</option>
                                        <option value="do_an_tot_nghiep"
                                            {{ old('loai_mon_hoc', $chuongTrinhKhung->loai_mon_hoc) == 'do_an_tot_nghiep' ? 'selected' : '' }}>
                                            Đồ án tốt nghiệp</option>
                                    </select>
                                    @error('loai_mon_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="bat_buoc" class="form-label">Bắt buộc/Tự chọn <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('bat_buoc') is-invalid @enderror" id="bat_buoc"
                                        name="bat_buoc" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="1"
                                            {{ old('bat_buoc', $chuongTrinhKhung->bat_buoc) == 1 ? 'selected' : '' }}>Bắt
                                            buộc</option>
                                        <option value="0"
                                            {{ old('bat_buoc', $chuongTrinhKhung->bat_buoc) == 0 ? 'selected' : '' }}>Tự
                                            chọn</option>
                                    </select>
                                    @error('bat_buoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="thu_tu_hoc" class="form-label">Thứ tự học</label>
                                    <input type="number" class="form-control @error('thu_tu_hoc') is-invalid @enderror"
                                        id="thu_tu_hoc" name="thu_tu_hoc"
                                        value="{{ old('thu_tu_hoc', $chuongTrinhKhung->thu_tu_hoc) }}" min="1"
                                        placeholder="VD: 1, 2, 3...">
                                    @error('thu_tu_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Thứ tự ưu tiên học trong học kỳ</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="so_tin_chi_toi_thieu" class="form-label">Số tín chỉ tối thiểu</label>
                                    <input type="number"
                                        class="form-control @error('so_tin_chi_toi_thieu') is-invalid @enderror"
                                        id="so_tin_chi_toi_thieu" name="so_tin_chi_toi_thieu"
                                        value="{{ old('so_tin_chi_toi_thieu', $chuongTrinhKhung->so_tin_chi_toi_thieu) }}"
                                        min="1">
                                    @error('so_tin_chi_toi_thieu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Chỉ áp dụng cho nhóm môn tự chọn</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                                    <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu" rows="3"
                                        placeholder="Nhập ghi chú...">{{ old('ghi_chu', $chuongTrinhKhung->ghi_chu) }}</textarea>
                                    @error('ghi_chu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.chuong-trinh-khung.index', ['chuyen_nganh_id' => $chuongTrinhKhung->chuyen_nganh_id]) }}"
                                class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
