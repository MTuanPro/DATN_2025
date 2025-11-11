@extends('layouts.layout-daotao')

@section('title', 'Thêm Trạng thái Học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Trạng thái Học tập mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}">Trạng thái
                                    Học tập</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin Trạng thái</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.trang-thai-hoc-tap.store') }}" method="POST">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="ten_trang_thai" class="col-md-4 col-form-label">Tên Trạng thái <span
                                    class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="ten_trang_thai" name="ten_trang_thai"
                                    class="form-control @error('ten_trang_thai') is-invalid @enderror"
                                    value="{{ old('ten_trang_thai') }}"
                                    placeholder="Ví dụ: Đang học, Bảo lưu, Thôi học, Tốt nghiệp..." required>
                                @error('ten_trang_thai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="mo_ta" class="col-md-4 col-form-label">Mô tả</label>
                            <div class="col-md-8">
                                <textarea id="mo_ta" name="mo_ta" rows="3" class="form-control @error('mo_ta') is-invalid @enderror"
                                    placeholder="Mô tả chi tiết về trạng thái học tập...">{{ old('mo_ta') }}</textarea>
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
                                <a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}" class="btn btn-secondary">
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
