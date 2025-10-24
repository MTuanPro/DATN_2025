@extends('layouts.layout-daotao')

@section('title', 'Sửa Trình độ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Trình độ</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin trình độ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.trinh-do.index') }}">Trình độ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
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
                    <form action="{{ route('dao-tao.trinh-do.update', $trinhDo->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ten_trinh_do" class="form-label">Tên Trình độ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_trinh_do') is-invalid @enderror"
                                        id="ten_trinh_do" name="ten_trinh_do"
                                        value="{{ old('ten_trinh_do', $trinhDo->ten_trinh_do) }}" required>
                                    @error('ten_trinh_do')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.trinh-do.index') }}" class="btn btn-secondary">
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
