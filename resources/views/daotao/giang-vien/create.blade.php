@extends('layouts.layout-daotao')

@section('title', 'Thêm Giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Giảng viên Mới</h3>
                    <p class="text-subtitle text-muted">Nhập thông tin giảng viên vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.giang-vien.index') }}">Giảng viên</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Giảng viên</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dao-tao.giang-vien.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    {{-- Mã giảng viên --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ma_giang_vien" class="form-label">
                                            Mã giảng viên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('ma_giang_vien') is-invalid @enderror"
                                            id="ma_giang_vien" name="ma_giang_vien" value="{{ old('ma_giang_vien') }}"
                                            placeholder="VD: GV001" required>
                                        @error('ma_giang_vien')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Họ tên --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ho_ten" class="form-label">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('ho_ten') is-invalid @enderror"
                                            id="ho_ten" name="ho_ten" value="{{ old('ho_ten') }}"
                                            placeholder="Nhập họ và tên" required>
                                        @error('ho_ten')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="email@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Số điện thoại --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="so_dien_thoai" class="form-label">
                                            Số điện thoại <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                            id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}"
                                            placeholder="0123456789" required>
                                        @error('so_dien_thoai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày sinh --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                        <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror"
                                            id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh') }}">
                                        @error('ngay_sinh')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Giới tính --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="gioi_tinh" class="form-label">Giới tính</label>
                                        <select class="form-select @error('gioi_tinh') is-invalid @enderror" id="gioi_tinh"
                                            name="gioi_tinh">
                                            <option value="">-- Chọn giới tính --</option>
                                            <option value="Nam" {{ old('gioi_tinh') == 'Nam' ? 'selected' : '' }}>Nam
                                            </option>
                                            <option value="Nữ" {{ old('gioi_tinh') == 'Nữ' ? 'selected' : '' }}>Nữ
                                            </option>
                                            <option value="Khác" {{ old('gioi_tinh') == 'Khác' ? 'selected' : '' }}>Khác
                                            </option>
                                        </select>
                                        @error('gioi_tinh')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Địa chỉ --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="dia_chi" class="form-label">Địa chỉ</label>
                                        <textarea class="form-control @error('dia_chi') is-invalid @enderror" id="dia_chi" name="dia_chi" rows="2"
                                            placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành">{{ old('dia_chi') }}</textarea>
                                        @error('dia_chi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Khoa --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="khoa_id" class="form-label">
                                            Khoa <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('khoa_id') is-invalid @enderror" id="khoa_id"
                                            name="khoa_id" required>
                                            <option value="">-- Chọn khoa --</option>
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

                                    {{-- Trình độ --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="trinh_do_id" class="form-label">
                                            Trình độ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('trinh_do_id') is-invalid @enderror"
                                            id="trinh_do_id" name="trinh_do_id" required>
                                            <option value="">-- Chọn trình độ --</option>
                                            @foreach ($trinhDos as $trinhDo)
                                                <option value="{{ $trinhDo->id }}"
                                                    {{ old('trinh_do_id') == $trinhDo->id ? 'selected' : '' }}>
                                                    {{ $trinhDo->ten_trinh_do }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('trinh_do_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Chuyên môn --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="chuyen_mon" class="form-label">
                                            Chuyên môn <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('chuyen_mon') is-invalid @enderror" id="chuyen_mon"
                                            name="chuyen_mon" value="{{ old('chuyen_mon') }}"
                                            placeholder="VD: Lập trình, Mạng máy tính..." required>
                                        @error('chuyen_mon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ngày vào trường --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="ngay_vao_truong" class="form-label">
                                            Ngày vào trường <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('ngay_vao_truong') is-invalid @enderror"
                                            id="ngay_vao_truong" name="ngay_vao_truong"
                                            value="{{ old('ngay_vao_truong') }}" required>
                                        @error('ngay_vao_truong')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Ảnh đại diện --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>
                                        <input type="file"
                                            class="form-control @error('anh_dai_dien') is-invalid @enderror"
                                            id="anh_dai_dien" name="anh_dai_dien" accept="image/*">
                                        @error('anh_dai_dien')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Chấp nhận: JPG, PNG (tối đa 2MB)</small>
                                    </div>

                                    {{-- Tạo tài khoản --}}
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tao_tai_khoan"
                                                id="tao_tai_khoan" value="1"
                                                {{ old('tao_tai_khoan') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tao_tai_khoan">
                                                Tạo tài khoản đăng nhập cho giảng viên (mật khẩu mặc định: 12345678)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Lưu
                                    </button>
                                    <a href="{{ route('dao-tao.giang-vien.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
