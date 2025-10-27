@extends('layouts.layout-daotao')

@section('title', 'Thêm Sinh viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm Sinh viên mới</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.sinh-vien.index') }}">Sinh viên</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin sinh viên</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.sinh-vien.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Thông tin cơ bản -->
                        <h6 class="mb-3 text-primary"><i class="bi bi-person-fill"></i> Thông tin cơ bản</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_sinh_vien" class="form-label">Mã SV <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ma_sinh_vien') is-invalid @enderror"
                                        id="ma_sinh_vien" name="ma_sinh_vien" value="{{ old('ma_sinh_vien') }}" required>
                                    @error('ma_sinh_vien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ho_ten" class="form-label">Họ tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ho_ten') is-invalid @enderror"
                                        id="ho_ten" name="ho_ten" value="{{ old('ho_ten') }}" required>
                                    @error('ho_ten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ngay_sinh" class="form-label">Ngày sinh <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror"
                                        id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh') }}" required>
                                    @error('ngay_sinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gioi_tinh" class="form-label">Giới tính <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('gioi_tinh') is-invalid @enderror" id="gioi_tinh"
                                        name="gioi_tinh" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="nam" {{ old('gioi_tinh') == 'nam' ? 'selected' : '' }}>Nam
                                        </option>
                                        <option value="nu" {{ old('gioi_tinh') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                        <option value="khac" {{ old('gioi_tinh') == 'khac' ? 'selected' : '' }}>Khác
                                        </option>
                                    </select>
                                    @error('gioi_tinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="so_dien_thoai" class="form-label">SĐT <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                        id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}"
                                        required>
                                    @error('so_dien_thoai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Giấy tờ tùy thân -->
                        <h6 class="mb-3 mt-4 text-primary"><i class="bi bi-card-text"></i> Giấy tờ tùy thân</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="can_cuoc_cong_dan" class="form-label">CCCD <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('can_cuoc_cong_dan') is-invalid @enderror"
                                        id="can_cuoc_cong_dan" name="can_cuoc_cong_dan"
                                        value="{{ old('can_cuoc_cong_dan') }}" required>
                                    @error('can_cuoc_cong_dan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ngay_cap_cccd" class="form-label">Ngày cấp</label>
                                    <input type="date"
                                        class="form-control @error('ngay_cap_cccd') is-invalid @enderror"
                                        id="ngay_cap_cccd" name="ngay_cap_cccd" value="{{ old('ngay_cap_cccd') }}">
                                    @error('ngay_cap_cccd')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="noi_cap_cccd" class="form-label">Nơi cấp</label>
                                    <input type="text" class="form-control @error('noi_cap_cccd') is-invalid @enderror"
                                        id="noi_cap_cccd" name="noi_cap_cccd" value="{{ old('noi_cap_cccd') }}">
                                    @error('noi_cap_cccd')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin học vụ -->
                        <h6 class="mb-3 mt-4 text-primary"><i class="bi bi-book-fill"></i> Thông tin học vụ</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lop_hanh_chinh_id" class="form-label">Lớp hành chính <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('lop_hanh_chinh_id') is-invalid @enderror"
                                        id="lop_hanh_chinh_id" name="lop_hanh_chinh_id" required>
                                        <option value="">-- Chọn lớp --</option>
                                        @foreach ($lopHanhChinhs as $lop)
                                            <option value="{{ $lop->id }}"
                                                {{ old('lop_hanh_chinh_id') == $lop->id ? 'selected' : '' }}>
                                                {{ $lop->ma_lop }} - {{ $lop->ten_lop }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lop_hanh_chinh_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="khoa_hoc_id" class="form-label">Khóa học <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('khoa_hoc_id') is-invalid @enderror"
                                        id="khoa_hoc_id" name="khoa_hoc_id" required>
                                        <option value="">-- Chọn khóa --</option>
                                        @foreach ($khoaHocs as $kh)
                                            <option value="{{ $kh->id }}"
                                                {{ old('khoa_hoc_id') == $kh->id ? 'selected' : '' }}>
                                                {{ $kh->ma_khoa_hoc }} - {{ $kh->ten_khoa_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('khoa_hoc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nganh_id" class="form-label">Ngành <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('nganh_id') is-invalid @enderror" id="nganh_id"
                                        name="nganh_id" required>
                                        <option value="">-- Chọn ngành --</option>
                                        @foreach ($nganhs as $n)
                                            <option value="{{ $n->id }}"
                                                {{ old('nganh_id') == $n->id ? 'selected' : '' }}>
                                                {{ $n->ma_nganh }} - {{ $n->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nganh_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="chuyen_nganh_id" class="form-label">Chuyên ngành</label>
                                    <select class="form-select @error('chuyen_nganh_id') is-invalid @enderror"
                                        id="chuyen_nganh_id" name="chuyen_nganh_id">
                                        <option value="">-- Chưa chọn --</option>
                                        @foreach ($chuyenNganhs as $cn)
                                            <option value="{{ $cn->id }}"
                                                {{ old('chuyen_nganh_id') == $cn->id ? 'selected' : '' }}>
                                                {{ $cn->ma_chuyen_nganh }} - {{ $cn->ten_chuyen_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('chuyen_nganh_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ky_hien_tai" class="form-label">Kỳ hiện tại <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('ky_hien_tai') is-invalid @enderror"
                                        id="ky_hien_tai" name="ky_hien_tai" required>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('ky_hien_tai', 1) == $i ? 'selected' : '' }}>Kỳ {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('ky_hien_tai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trang_thai_hoc_tap_id" class="form-label">Trạng thái <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('trang_thai_hoc_tap_id') is-invalid @enderror"
                                        id="trang_thai_hoc_tap_id" name="trang_thai_hoc_tap_id" required>
                                        <option value="">-- Chọn trạng thái --</option>
                                        @foreach ($trangThais as $tt)
                                            <option value="{{ $tt->id }}"
                                                {{ old('trang_thai_hoc_tap_id') == $tt->id ? 'selected' : '' }}>
                                                {{ $tt->ten_trang_thai }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('trang_thai_hoc_tap_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Lưu ý:</strong> Hệ thống sẽ tự động tạo tài khoản đăng nhập với mật khẩu mặc định là
                            <strong>Mã sinh viên</strong>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.sinh-vien.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Lưu sinh viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
