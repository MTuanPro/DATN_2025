@extends('layouts.layout-admin')

@section('title', 'Sửa Thông tin Tài khoản')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Thông tin Tài khoản</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin tài khoản: {{ $user->name }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Tài khoản</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">
                    {{-- Form sửa thông tin cơ bản --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin Cơ bản</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Họ tên --}}
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
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
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Trạng thái --}}
                                <div class="form-group mb-3">
                                    <label for="trang_thai" class="form-label">
                                        Trạng thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('trang_thai') is-invalid @enderror" id="trang_thai"
                                        name="trang_thai" required>
                                        <option value="hoat_dong"
                                            {{ old('trang_thai', $user->trang_thai) == 'hoat_dong' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="khoa"
                                            {{ old('trang_thai', $user->trang_thai) == 'khoa' ? 'selected' : '' }}>
                                            Khóa
                                        </option>
                                        <option value="ngung_hoat_dong"
                                            {{ old('trang_thai', $user->trang_thai) == 'ngung_hoat_dong' ? 'selected' : '' }}>
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
                                                        {{ in_array($vaiTro->id, old('vai_tro', $userVaiTroIds)) ? 'checked' : '' }}>
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
                                        <i class="bi bi-check-circle"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    {{-- Thông tin bổ sung --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin Bổ sung</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật:</strong></td>
                                    <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email verified:</strong></td>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i>
                                                {{ $user->email_verified_at->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Chưa xác thực</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Đăng nhập cuối:</strong></td>
                                    <td>
                                        @if ($user->lan_dang_nhap_cuoi)
                                            {{ $user->lan_dang_nhap_cuoi->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Chưa đăng nhập</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Reset Mật khẩu qua Email --}}
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-lock text-warning"></i>
                                Reset Mật khẩu
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle-fill"></i>
                                <strong>Lưu ý:</strong>
                                <ul class="mb-0 mt-2 small">
                                    <li>Hệ thống sẽ gửi <strong>link reset mật khẩu</strong> qua email</li>
                                    <li>Người dùng tự tạo mật khẩu mới theo yêu cầu bảo mật</li>
                                    <li>Link có hiệu lực trong <strong>60 phút</strong></li>
                                </ul>
                            </div>

                            <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST"
                                onsubmit="return confirm('⚠️ Xác nhận gửi email reset mật khẩu đến:\n\n📧 {{ $user->email }}\n\nNgười dùng sẽ nhận link để tự đặt lại mật khẩu mới.')">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-envelope-fill"></i>
                                    Gửi Email Reset Mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Hành động khác --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Hành động Khác</h5>
                        </div>
                        <div class="card-body">
                            @if ($user->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100"
                                        onclick="return confirm('Xác nhận xóa tài khoản này?')">
                                        <i class="bi bi-trash"></i> Xóa tài khoản
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
