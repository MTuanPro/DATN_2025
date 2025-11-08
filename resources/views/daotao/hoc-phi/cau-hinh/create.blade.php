@extends('layouts.layout-daotao')

@section('title', 'Thêm Cấu hình Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Cấu hình Học phí</h3>
                    <p class="text-subtitle text-muted">Tạo cấu hình học phí mới</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.cau-hinh.index') }}">Cấu hình học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin cấu hình</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.hoc-phi.cau-hinh.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nam_hoc" class="form-label">Năm học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nam_hoc') is-invalid @enderror"
                                        id="nam_hoc" name="nam_hoc" value="{{ old('nam_hoc') }}"
                                        placeholder="VD: 2024-2025">
                                    @error('nam_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="don_gia_tren_tin_chi" class="form-label">Đơn giá trên tín chỉ (VNĐ) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('don_gia_tren_tin_chi') is-invalid @enderror"
                                        id="don_gia_tren_tin_chi" name="don_gia_tren_tin_chi"
                                        value="{{ old('don_gia_tren_tin_chi') }}" min="0" step="1000"
                                        placeholder="VD: 500000">
                                    @error('don_gia_tren_tin_chi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phi_dich_vu" class="form-label">Phí dịch vụ (VNĐ)</label>
                                    <input type="number" class="form-control @error('phi_dich_vu') is-invalid @enderror"
                                        id="phi_dich_vu" name="phi_dich_vu" value="{{ old('phi_dich_vu', 0) }}"
                                        min="0" step="1000" placeholder="VD: 100000">
                                    @error('phi_dich_vu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Phí dịch vụ, bảo hiểm (mặc định: 0)</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="ap_dung_tu_ngay" class="form-label">Áp dụng từ ngày <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ap_dung_tu_ngay') is-invalid @enderror"
                                        id="ap_dung_tu_ngay" name="ap_dung_tu_ngay" value="{{ old('ap_dung_tu_ngay') }}">
                                    @error('ap_dung_tu_ngay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="ap_dung_den_ngay" class="form-label">Áp dụng đến ngày</label>
                                    <input type="date" class="form-control @error('ap_dung_den_ngay') is-invalid @enderror"
                                        id="ap_dung_den_ngay" name="ap_dung_den_ngay"
                                        value="{{ old('ap_dung_den_ngay') }}">
                                    @error('ap_dung_den_ngay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Để trống nếu vô thời hạn</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu"
                                rows="3" placeholder="Nhập ghi chú...">{{ old('ghi_chu') }}</textarea>
                            @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dao-tao.hoc-phi.cau-hinh.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu cấu hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
