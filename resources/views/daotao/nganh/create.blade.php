@extends('layouts.layout-daotao')

@section('title', 'Thêm Ngành')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Ngành</h3>
                    <p class="text-subtitle text-muted">Thêm ngành mới vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.nganh.index') }}">Ngành</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Ngành</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.nganh.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ma_nganh" class="form-label">Mã Ngành <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_nganh') is-invalid @enderror"
                                        id="ma_nganh" name="ma_nganh" value="{{ old('ma_nganh') }}"
                                        placeholder="VD: 7480201" required>
                                    @error('ma_nganh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ten_nganh" class="form-label">Tên Ngành <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_nganh') is-invalid @enderror"
                                        id="ten_nganh" name="ten_nganh" value="{{ old('ten_nganh') }}"
                                        placeholder="VD: Công nghệ Thông tin" required>
                                    @error('ten_nganh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="khoa_id" class="form-label">Khoa Quản Lý <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('khoa_id') is-invalid @enderror" id="khoa_id"
                                        name="khoa_id" required>
                                        <option value="">-- Chọn Khoa --</option>
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
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="mo_ta" class="form-label">Mô Tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3"
                                        placeholder="Nhập mô tả về ngành...">{{ old('mo_ta') }}</textarea>
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
                            <a href="{{ route('dao-tao.nganh.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
