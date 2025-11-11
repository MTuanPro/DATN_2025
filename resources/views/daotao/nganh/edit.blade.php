@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa Ngành')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa Ngành</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.nganh.index') }}">Ngành</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
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
                    <form action="{{ route('dao-tao.nganh.update', $nganh->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="ma_nganh" class="col-md-4 col-form-label">Mã Ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ma_nganh" name="ma_nganh"
                                    class="form-control @error('ma_nganh') is-invalid @enderror"
                                    value="{{ old('ma_nganh', $nganh->ma_nganh) }}" placeholder="Nhập mã ngành" required>
                                @error('ma_nganh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="ten_nganh" class="col-md-4 col-form-label">Tên Ngành <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_nganh" name="ten_nganh"
                                    class="form-control @error('ten_nganh') is-invalid @enderror"
                                    value="{{ old('ten_nganh', $nganh->ten_nganh) }}" placeholder="Nhập tên ngành" required>
                                @error('ten_nganh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="khoa_id" class="col-md-4 col-form-label">Khoa Quản Lý</label>
                            <div class="col-md-8">
                                <select name="khoa_id" id="khoa_id"
                                    class="form-select @error('khoa_id') is-invalid @enderror">
                                    <option value="">-- Chọn Khoa --</option>
                                    @foreach ($khoas as $khoa)
                                        <option value="{{ $khoa->id }}"
                                            {{ old('khoa_id', $nganh->khoa_id) == $khoa->id ? 'selected' : '' }}>
                                            {{ $khoa->ten_khoa }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('khoa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô Tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="3" class="form-control @error('mo_ta') is-invalid @enderror"
                                    placeholder="Nhập mô tả...">{{ old('mo_ta', $nganh->mo_ta) }}</textarea>
                                @error('mo_ta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Cập nhật
                                </button>
                                <a href="{{ route('dao-tao.nganh.index') }}" class="btn btn-secondary">
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
