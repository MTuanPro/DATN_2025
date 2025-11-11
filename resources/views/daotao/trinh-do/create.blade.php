@extends('layouts.layout-daotao')

@section('title', 'Thêm Trình độ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Trình độ mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.trinh-do.index') }}">Trình độ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Trình độ</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.trinh-do.store') }}" method="POST">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="ten_trinh_do" class="col-md-4 col-form-label">Tên Trình độ <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_trinh_do" name="ten_trinh_do"
                                    class="form-control @error('ten_trinh_do') is-invalid @enderror"
                                    value="{{ old('ten_trinh_do') }}" placeholder="Ví dụ: Cử nhân, Thạc sĩ, Tiến sĩ..."
                                    required>
                                @error('ten_trinh_do')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Lưu
                                </button>
                                <a href="{{ route('dao-tao.trinh-do.index') }}" class="btn btn-secondary">
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
