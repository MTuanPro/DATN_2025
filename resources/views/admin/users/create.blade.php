@extends('layouts.layout-admin')

@section('title', 'Tạo Tài khoản Mới')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tạo Tài khoản Mới</h3>
                    <p class="text-subtitle text-muted">Thêm tài khoản người dùng vào hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Tài khoản</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
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
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Tài khoản</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.store') }}" method="POST">
                                @csrf

                                {{-- Họ tên --}}
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Nhập họ và tên" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}"
                                        placeholder="Nhập địa chỉ email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mật khẩu --}}
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">
                                        Mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                                        required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                                </div>

                                {{-- Xác nhận mật khẩu --}}
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Xác nhận mật khẩu <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                                </div>

                                {{-- Trạng thái --}}
                                <div class="form-group mb-3">
                                    <label for="trang_thai" class="form-label">
                                        Trạng thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('trang_thai') is-invalid @enderror" id="trang_thai"
                                        name="trang_thai" required>
                                        <option value="">-- Chọn trạng thái --</option>
                                        <option value="hoat_dong" {{ old('trang_thai') == 'hoat_dong' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="khoa" {{ old('trang_thai') == 'khoa' ? 'selected' : '' }}>
                                            Khóa
                                        </option>
                                        <option value="ngung_hoat_dong"
                                            {{ old('trang_thai') == 'ngung_hoat_dong' ? 'selected' : '' }}>
                                            Ngừng hoạt động
                                        </option>
                                    </select>
                                    @error('trang_thai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Vai trò --}}
                                <div class="form-group mb-4">
                                    <label class="form-label">Vai trò</label>
                                    <div class="card">
                                        <div class="card-body">
                                            @foreach ($vaiTros as $vaiTro)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="vai_tro[]"
                                                        value="{{ $vaiTro->id }}" id="role_{{ $vaiTro->id }}"
                                                        {{ in_array($vaiTro->id, old('vai_tro', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $vaiTro->id }}">
                                                        <strong>{{ $vaiTro->ten_vai_tro }}</strong>
                                                        @if ($vaiTro->mo_ta)
                                                            <br><small class="text-muted">{{ $vaiTro->mo_ta }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('vai_tro')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Buttons --}}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Tạo tài khoản
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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
