@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa giảng viên</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.giang-vien.index') }}">Giảng viên</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin giảng viên</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.giang-vien.update', $giangVien->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ma_giang_vien" class="form-label">Mã giảng viên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_giang_vien') is-invalid @enderror"
                                        id="ma_giang_vien" name="ma_giang_vien"
                                        value="{{ old('ma_giang_vien', $giangVien->ma_giang_vien) }}" required>
                                    @error('ma_giang_vien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ho_ten" class="form-label">Họ và tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ho_ten') is-invalid @enderror"
                                        id="ho_ten" name="ho_ten" value="{{ old('ho_ten', $giangVien->ho_ten) }}"
                                        required>
                                    @error('ho_ten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $giangVien->email) }}"
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                        id="so_dien_thoai" name="so_dien_thoai"
                                        value="{{ old('so_dien_thoai', $giangVien->so_dien_thoai) }}">
                                    @error('so_dien_thoai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="khoa_id" class="form-label">Khoa <span class="text-danger">*</span></label>
                                    <select class="form-select @error('khoa_id') is-invalid @enderror" id="khoa_id"
                                        name="khoa_id" required>
                                        <option value="">-- Chọn khoa --</option>
                                        @foreach ($khoas as $khoa)
                                            <option value="{{ $khoa->id }}"
                                                {{ old('khoa_id', $giangVien->khoa_id) == $khoa->id ? 'selected' : '' }}>
                                                {{ $khoa->ten_khoa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('khoa_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trinh_do_id" class="form-label">Trình độ</label>
                                    <select class="form-select @error('trinh_do_id') is-invalid @enderror" id="trinh_do_id"
                                        name="trinh_do_id">
                                        <option value="">-- Chọn trình độ --</option>
                                        @foreach ($trinhDos as $trinhDo)
                                            <option value="{{ $trinhDo->id }}"
                                                {{ old('trinh_do_id', $giangVien->trinh_do_id) == $trinhDo->id ? 'selected' : '' }}>
                                                {{ $trinhDo->ten_trinh_do }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('trinh_do_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror"
                                        id="ngay_sinh" name="ngay_sinh"
                                        value="{{ old('ngay_sinh', $giangVien->ngay_sinh?->format('Y-m-d')) }}">
                                    @error('ngay_sinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gioi_tinh" class="form-label">Giới tính</label>
                                    <select class="form-select @error('gioi_tinh') is-invalid @enderror" id="gioi_tinh"
                                        name="gioi_tinh">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="Nam"
                                            {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Nam' ? 'selected' : '' }}>Nam
                                        </option>
                                        <option value="Nữ"
                                            {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Nữ' ? 'selected' : '' }}>Nữ
                                        </option>
                                        <option value="Khác"
                                            {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Khác' ? 'selected' : '' }}>Khác
                                        </option>
                                    </select>
                                    @error('gioi_tinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dia_chi" class="form-label">Địa chỉ</label>
                                    <textarea class="form-control @error('dia_chi') is-invalid @enderror" id="dia_chi" name="dia_chi" rows="2">{{ old('dia_chi', $giangVien->dia_chi) }}</textarea>
                                    @error('dia_chi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                                    @if ($giangVien->anh_dai_dien)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $giangVien->anh_dai_dien) }}" alt="Avatar"
                                                class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                        id="avatar" name="avatar" accept="image/*">
                                    <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.giang-vien.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
