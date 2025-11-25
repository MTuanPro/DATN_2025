@extends('layouts.layout-sinhvien')

@section('title', 'Hồ Sơ Cá Nhân')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hồ Sơ Cá Nhân</h3>
                    <p class="text-subtitle text-muted">Quản lý thông tin cá nhân của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hồ sơ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
<!-- sửa giao diện -->
        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info"
                                        role="tab">
                                        <i class="bi bi-person"></i> Thông Tin Cá Nhân
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password"
                                        role="tab">
                                        <i class="bi bi-key"></i> Đổi Mật Khẩu
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="tab-content">
                                    {{-- Tab Thông Tin --}}
                                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-3 text-center mb-4">
                                                <div class="mb-3">
                                                    @php
                                                        $avatarUrl = null;
                                                        if ($sinhVien->anh_dai_dien) {
                                                            // Kiểm tra xem ảnh có tồn tại không
                                                            $avatarPath = 'storage/' . $sinhVien->anh_dai_dien;
                                                            if (file_exists(public_path($avatarPath))) {
                                                                $avatarUrl = asset($avatarPath);
                                                            }
                                                        }
                                                        // Nếu không có ảnh hoặc ảnh không tồn tại, dùng ảnh mặc định
                                                        if (!$avatarUrl) {
                                                            $defaultImage = 'assets/images/faces/1.jpg';
                                                            if (file_exists(public_path($defaultImage))) {
                                                                $avatarUrl = asset($defaultImage);
                                                            } else {
                                                                // Nếu không có ảnh mặc định, dùng placeholder
                                                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($sinhVien->ho_ten ?? 'User') . '&size=150&background=0d6efd&color=fff';
                                                            }
                                                        }
                                                    @endphp
                                                    <img id="preview-avatar"
                                                        src="{{ $avatarUrl }}"
                                                        alt="Avatar" 
                                                        class="img-fluid rounded-circle border"
                                                        style="width: 150px; height: 150px; object-fit: cover; border-width: 2px !important;"
                                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($sinhVien->ho_ten ?? 'User') }}&size=150&background=0d6efd&color=fff';">
                                                </div>
                                                <label for="anh_dai_dien" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-upload"></i> Chọn Ảnh
                                                </label>
                                                <input type="file" class="d-none" id="anh_dai_dien" name="anh_dai_dien"
                                                    accept="image/*">
                                                @error('anh_dai_dien')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-9">
                                                <div class="row">
                                                    {{-- Thông tin cơ bản --}}
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mã Sinh Viên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $sinhVien->ma_sinh_vien }}" disabled>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Họ và Tên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="ho_ten"
                                                            value="{{ old('ho_ten', $sinhVien->ho_ten) }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" name="email"
                                                            value="{{ old('email', $sinhVien->email) }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Số Điện Thoại</label>
                                                        <input type="text" class="form-control" name="so_dien_thoai"
                                                            value="{{ old('so_dien_thoai', $sinhVien->so_dien_thoai) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Ngày Sinh</label>
                                                        <input type="date" class="form-control" name="ngay_sinh"
                                                            value="{{ old('ngay_sinh', $sinhVien->ngay_sinh instanceof \Carbon\Carbon ? $sinhVien->ngay_sinh->format('Y-m-d') : $sinhVien->ngay_sinh) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Giới Tính</label>
                                                        <select class="form-select" name="gioi_tinh">
                                                            <option value="">-- Chọn --</option>
                                                            <option value="nam"
                                                                {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'nam' ? 'selected' : '' }}>
                                                                Nam</option>
                                                            <option value="nu"
                                                                {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'nu' ? 'selected' : '' }}>
                                                                Nữ</option>
                                                            <option value="khac"
                                                                {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'khac' ? 'selected' : '' }}>
                                                                Khác</option>
                                                        </select>
                                                    </div>

                                                    {{-- Địa chỉ --}}
                                                    <div class="col-12 mb-3">
                                                        <h6 class="text-primary">Địa Chỉ Liên Lạc</h6>
                                                        <hr>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Số Nhà, Đường</label>
                                                        <input type="text" class="form-control" name="so_nha_duong"
                                                            value="{{ old('so_nha_duong', $sinhVien->so_nha_duong) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Phường/Xã</label>
                                                        <input type="text" class="form-control" name="phuong_xa"
                                                            value="{{ old('phuong_xa', $sinhVien->phuong_xa) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Quận/Huyện</label>
                                                        <input type="text" class="form-control" name="quan_huyen"
                                                            value="{{ old('quan_huyen', $sinhVien->quan_huyen) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Tỉnh/Thành Phố</label>
                                                        <input type="text" class="form-control" name="tinh_thanh"
                                                            value="{{ old('tinh_thanh', $sinhVien->tinh_thanh) }}">
                                                    </div>

                                                    {{-- CCCD --}}
                                                    <div class="col-12 mb-3">
                                                        <h6 class="text-primary">Căn Cước Công Dân</h6>
                                                        <hr>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Số CCCD</label>
                                                        <input type="text" class="form-control"
                                                            name="can_cuoc_cong_dan"
                                                            value="{{ old('can_cuoc_cong_dan', $sinhVien->can_cuoc_cong_dan) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Ngày Cấp</label>
                                                        <input type="date" class="form-control" name="ngay_cap_cccd"
                                                            value="{{ old('ngay_cap_cccd', $sinhVien->ngay_cap_cccd instanceof \Carbon\Carbon ? $sinhVien->ngay_cap_cccd->format('Y-m-d') : $sinhVien->ngay_cap_cccd) }}">
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Nơi Cấp</label>
                                                        <input type="text" class="form-control" name="noi_cap_cccd"
                                                            value="{{ old('noi_cap_cccd', $sinhVien->noi_cap_cccd) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save"></i> Lưu Thay Đổi
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Tab Đổi Mật Khẩu --}}
                                    <div class="tab-pane fade" id="password" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 mx-auto">
                                                <div class="mb-3">
                                                    <label class="form-label">Mật Khẩu Hiện Tại</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="current_password"
                                                            name="current_password" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('current_password')">
                                                            <i class="bi bi-eye" id="current_password_icon"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Chỉ điền nếu muốn đổi mật khẩu</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Mật Khẩu Mới</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="new_password"
                                                            name="new_password" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('new_password')">
                                                            <i class="bi bi-eye" id="new_password_icon"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Xác Nhận Mật Khẩu Mới</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control"
                                                            id="new_password_confirmation"
                                                            name="new_password_confirmation" autocomplete="off">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('new_password_confirmation')">
                                                            <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-key"></i> Đổi Mật Khẩu
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            // Preview avatar
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('anh_dai_dien');
                const previewImg = document.getElementById('preview-avatar');
                
                if (fileInput && previewImg) {
                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            // Kiểm tra loại file
                            if (!file.type.match('image.*')) {
                                alert('Vui lòng chọn file ảnh!');
                                e.target.value = '';
                                return;
                            }
                            
                            // Kiểm tra kích thước file (max 2MB)
                            if (file.size > 2048 * 1024) {
                                alert('Kích thước ảnh không được vượt quá 2MB!');
                                e.target.value = '';
                                return;
                            }
                            
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                            };
                            reader.onerror = function() {
                                alert('Có lỗi xảy ra khi đọc file!');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });

            // Toggle password visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '_icon');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
    @endpush
@endsection
