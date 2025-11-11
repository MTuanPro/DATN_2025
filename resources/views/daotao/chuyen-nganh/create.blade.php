@extends('layouts.layout-daotao')

@section('title', 'Thêm Chuyên ngành')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Chuyên ngành mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.chuyen-nganh.index') }}">Chuyên ngành</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Chuyên ngành</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.chuyen-nganh.store') }}" method="POST">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="ma_chuyen_nganh" class="col-md-4 col-form-label">Mã Chuyên ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ma_chuyen_nganh" name="ma_chuyen_nganh"
                                    class="form-control @error('ma_chuyen_nganh') is-invalid @enderror"
                                    value="{{ old('ma_chuyen_nganh') }}" placeholder="VD: CNPM, HTTT..." required>
                                @error('ma_chuyen_nganh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="ten_chuyen_nganh" class="col-md-4 col-form-label">Tên Chuyên ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_chuyen_nganh" name="ten_chuyen_nganh"
                                    class="form-control @error('ten_chuyen_nganh') is-invalid @enderror"
                                    value="{{ old('ten_chuyen_nganh') }}" placeholder="VD: Công nghệ phần mềm..." required>
                                @error('ten_chuyen_nganh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nganh_id" class="col-md-4 col-form-label">Ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="nganh_id" id="nganh_id"
                                    class="form-select @error('nganh_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn ngành --</option>
                                    @foreach ($nganhs as $nganh)
                                        <option value="{{ $nganh->id }}"
                                            {{ old('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                            {{ $nganh->ten_nganh }} ({{ $nganh->khoa->ten_khoa ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('nganh_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="tong_tin_chi_toi_thieu" class="col-md-4 col-form-label">Tổng tín chỉ tối
                                thiểu</label>
                            <div class="col-md-8">
                                <input type="number" id="tong_tin_chi_toi_thieu" name="tong_tin_chi_toi_thieu"
                                    class="form-control @error('tong_tin_chi_toi_thieu') is-invalid @enderror"
                                    value="{{ old('tong_tin_chi_toi_thieu', 120) }}" min="0" max="200">
                                @error('tong_tin_chi_toi_thieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Số tín chỉ tối thiểu để tốt nghiệp (VD: 120, 140...)</small>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="4" class="form-control @error('mo_ta') is-invalid @enderror"
                                    placeholder="Nhập mô tả...">{{ old('mo_ta') }}</textarea>
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
                                <a href="{{ route('dao-tao.chuyen-nganh.index') }}" class="btn btn-secondary">
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
