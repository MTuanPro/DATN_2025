@extends('layouts.layout-daotao')

@section('title', 'Sửa Trạng thái Học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Trạng thái Học tập</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin trạng thái</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}">Trạng thái
                                    Học tập</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
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
                    <form action="{{ route('dao-tao.trang-thai-hoc-tap.update', $trangThai->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ten_trang_thai" class="form-label">Tên Trạng thái <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten_trang_thai') is-invalid @enderror"
                                        id="ten_trang_thai" name="ten_trang_thai"
                                        value="{{ old('ten_trang_thai', $trangThai->ten_trang_thai) }}" required>
                                    @error('ten_trang_thai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="mo_ta" class="form-label">Mô tả</label>
                                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3">{{ old('mo_ta', $trangThai->mo_ta) }}</textarea>
                                    @error('mo_ta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.trang-thai-hoc-tap.index') }}" class="btn btn-secondary">
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
