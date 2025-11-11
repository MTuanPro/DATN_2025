@extends('layouts.layout-giangvien')

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
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hồ sơ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

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
                                                    <img id="preview-avatar"
                                                        src="{{ $giangVien->anh_dai_dien ? asset('storage/' . $giangVien->anh_dai_dien) : asset('assets/compiled/jpg/2.jpg') }}"
                                                        alt="Avatar" class="img-fluid rounded-circle"
                                                        style="width: 150px; height: 150px; object-fit: cover;">
                                                </div>
                                                <label for="anh_dai_dien" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-upload"></i> Chọn Ảnh
                                                </label>
                                                <input type="file" class="d-none" id="anh_dai_dien" name="anh_dai_dien"
                                                    accept="image/*">
                                            </div>

                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mã Giảng Viên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $giangVien->ma_giang_vien }}" disabled>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Họ và Tên <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="ho_ten"
                                                            value="{{ old('ho_ten', $giangVien->ho_ten) }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" name="email"
                                                            value="{{ old('email', $giangVien->email) }}" required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Số Điện Thoại</label>
                                                        <input type="text" class="form-control" name="so_dien_thoai"
                                                            value="{{ old('so_dien_thoai', $giangVien->so_dien_thoai) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Ngày Sinh</label>
                                                        <input type="date" class="form-control" name="ngay_sinh"
                                                            value="{{ old('ngay_sinh', $giangVien->ngay_sinh instanceof \Carbon\Carbon ? $giangVien->ngay_sinh->format('Y-m-d') : $giangVien->ngay_sinh) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Giới Tính</label>
                                                        <select class="form-select" name="gioi_tinh">
                                                            <option value="">-- Chọn --</option>
                                                            <option value="Nam"
                                                                {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Nam' ? 'selected' : '' }}>
                                                                Nam</option>
                                                            <option value="Nữ"
                                                                {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Nữ' ? 'selected' : '' }}>
                                                                Nữ</option>
                                                            <option value="Khác"
                                                                {{ old('gioi_tinh', $giangVien->gioi_tinh) == 'Khác' ? 'selected' : '' }}>
                                                                Khác</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Địa Chỉ</label>
                                                        <textarea class="form-control" name="dia_chi" rows="2">{{ old('dia_chi', $giangVien->dia_chi) }}</textarea>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Chuyên Môn</label>
                                                        <input type="text" class="form-control" name="chuyen_mon"
                                                            value="{{ old('chuyen_mon', $giangVien->chuyen_mon) }}">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Ngày Vào Trường</label>
                                                        <input type="date" class="form-control" name="ngay_vao_truong"
                                                            value="{{ old('ngay_vao_truong', $giangVien->ngay_vao_truong instanceof \Carbon\Carbon ? $giangVien->ngay_vao_truong->format('Y-m-d') : $giangVien->ngay_vao_truong) }}">
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
                                                    <input type="password" class="form-control" name="current_password">
                                                    <small class="text-muted">Chỉ điền nếu muốn đổi mật khẩu</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Mật Khẩu Mới</label>
                                                    <input type="password" class="form-control" name="new_password">
                                                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Xác Nhận Mật Khẩu Mới</label>
                                                    <input type="password" class="form-control"
                                                        name="new_password_confirmation">
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
            document.getElementById('anh_dai_dien').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview-avatar').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush
@endsection
