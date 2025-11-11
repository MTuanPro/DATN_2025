@extends('layouts.layout-daotao')

@section('title', 'Sửa Khoa')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Khoa</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin khoa</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.khoa.index') }}">Khoa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Khoa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.khoa.update', $khoa->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="ma_khoa" class="form-label">Mã Khoa <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_khoa') is-invalid @enderror"
                                        id="ma_khoa" name="ma_khoa" value="{{ old('ma_khoa', $khoa->ma_khoa) }}"
                                        placeholder="VD: CNTT" required>
                                    @error('ma_khoa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ten_khoa" class="form-label">Tên Khoa <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_khoa') is-invalid @enderror"
                                        id="ten_khoa" name="ten_khoa" value="{{ old('ten_khoa', $khoa->ten_khoa) }}"
                                        placeholder="VD: Công nghệ Thông tin" required>
                                    @error('ten_khoa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="truong_khoa_id" class="form-label">Trưởng Khoa</label>
                                    <input type="text" class="form-control @error('truong_khoa_id') is-invalid @enderror"
                                        id="truong_khoa_id" name="truong_khoa_id"
                                        value="{{ old('truong_khoa_id', $khoa->truong_khoa_id) }}"
                                        placeholder="Nhập ID trưởng khoa (tùy chọn)">
                                    @error('truong_khoa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.khoa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
