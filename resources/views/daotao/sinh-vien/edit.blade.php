@extends('layouts.layout-daotao')

@section('title', 'Sửa Sinh viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa Sinh viên</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.sinh-vien.index') }}">Sinh viên</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5>Sửa thông tin sinh viên: {{ $sinhVien->ma_sinh_vien }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.sinh-vien.update', $sinhVien->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Thông tin cơ bản -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Thông tin cơ bản</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ma_sinh_vien" class="form-label">MSSV <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ma_sinh_vien') is-invalid @enderror"
                                    id="ma_sinh_vien" name="ma_sinh_vien"
                                    value="{{ old('ma_sinh_vien', $sinhVien->ma_sinh_vien) }}" required>
                                @error('ma_sinh_vien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ho_ten" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ho_ten') is-invalid @enderror"
                                    id="ho_ten" name="ho_ten" value="{{ old('ho_ten', $sinhVien->ho_ten) }}" required>
                                @error('ho_ten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $sinhVien->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="so_dien_thoai" class="form-label">SĐT</label>
                                <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                    id="so_dien_thoai" name="so_dien_thoai"
                                    value="{{ old('so_dien_thoai', $sinhVien->so_dien_thoai) }}">
                                @error('so_dien_thoai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control @error('ngay_sinh') is-invalid @enderror"
                                    id="ngay_sinh" name="ngay_sinh"
                                    value="{{ old('ngay_sinh', $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('Y-m-d') : '') }}">
                                @error('ngay_sinh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                <select class="form-select @error('gioi_tinh') is-invalid @enderror" id="gioi_tinh"
                                    name="gioi_tinh">
                                    <option value="">-- Chọn --</option>
                                    <option value="nam"
                                        {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'nam' ? 'selected' : '' }}>Nam
                                    </option>
                                    <option value="nu"
                                        {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'nu' ? 'selected' : '' }}>Nữ</option>
                                    <option value="khac"
                                        {{ old('gioi_tinh', $sinhVien->gioi_tinh) == 'khac' ? 'selected' : '' }}>Khác
                                    </option>
                                </select>
                                @error('gioi_tinh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Giấy tờ tùy thân -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Giấy tờ tùy thân</h6>
                                <hr>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="can_cuoc_cong_dan" class="form-label">CCCD <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('can_cuoc_cong_dan') is-invalid @enderror"
                                    id="can_cuoc_cong_dan" name="can_cuoc_cong_dan"
                                    value="{{ old('can_cuoc_cong_dan', $sinhVien->can_cuoc_cong_dan) }}" required>
                                @error('can_cuoc_cong_dan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ngay_cap_cccd" class="form-label">Ngày cấp</label>
                                <input type="date" class="form-control @error('ngay_cap_cccd') is-invalid @enderror"
                                    id="ngay_cap_cccd" name="ngay_cap_cccd"
                                    value="{{ old('ngay_cap_cccd', $sinhVien->ngay_cap_cccd ? $sinhVien->ngay_cap_cccd->format('Y-m-d') : '') }}">
                                @error('ngay_cap_cccd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="noi_cap_cccd" class="form-label">Nơi cấp</label>
                                <input type="text" class="form-control @error('noi_cap_cccd') is-invalid @enderror"
                                    id="noi_cap_cccd" name="noi_cap_cccd"
                                    value="{{ old('noi_cap_cccd', $sinhVien->noi_cap_cccd) }}">
                                @error('noi_cap_cccd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Địa chỉ</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="so_nha_duong" class="form-label">Số nhà, đường</label>
                                <input type="text" class="form-control @error('so_nha_duong') is-invalid @enderror"
                                    id="so_nha_duong" name="so_nha_duong"
                                    value="{{ old('so_nha_duong', $sinhVien->so_nha_duong) }}">
                                @error('so_nha_duong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phuong_xa" class="form-label">Phường/Xã</label>
                                <input type="text" class="form-control @error('phuong_xa') is-invalid @enderror"
                                    id="phuong_xa" name="phuong_xa"
                                    value="{{ old('phuong_xa', $sinhVien->phuong_xa) }}">
                                @error('phuong_xa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quan_huyen" class="form-label">Quận/Huyện</label>
                                <input type="text" class="form-control @error('quan_huyen') is-invalid @enderror"
                                    id="quan_huyen" name="quan_huyen"
                                    value="{{ old('quan_huyen', $sinhVien->quan_huyen) }}">
                                @error('quan_huyen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tinh_thanh" class="form-label">Tỉnh/Thành phố</label>
                                <input type="text" class="form-control @error('tinh_thanh') is-invalid @enderror"
                                    id="tinh_thanh" name="tinh_thanh"
                                    value="{{ old('tinh_thanh', $sinhVien->tinh_thanh) }}">
                                @error('tinh_thanh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Ảnh đại diện -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Ảnh đại diện</h6>
                                <hr>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>
                                @if ($sinhVien->anh_dai_dien)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($sinhVien->anh_dai_dien) }}" alt="Avatar"
                                            class="img-thumbnail" style="max-width: 150px;">
                                        <small class="d-block text-muted">Ảnh hiện tại</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('anh_dai_dien') is-invalid @enderror"
                                    id="anh_dai_dien" name="anh_dai_dien" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Chọn file mới để thay đổi ảnh. Định dạng: JPG, PNG. Kích thước
                                    tối đa: 2MB</small>
                                @error('anh_dai_dien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Thông tin học vụ -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Thông tin học vụ</h6>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="khoa_hoc_id" class="form-label">Khóa học <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('khoa_hoc_id') is-invalid @enderror" id="khoa_hoc_id"
                                    name="khoa_hoc_id" required>
                                    <option value="">-- Chọn khóa học --</option>
                                    @foreach ($khoaHocs as $kh)
                                        <option value="{{ $kh->id }}"
                                            {{ old('khoa_hoc_id', $sinhVien->khoa_hoc_id) == $kh->id ? 'selected' : '' }}>
                                            {{ $kh->ten_khoa_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('khoa_hoc_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lop_hanh_chinh_id" class="form-label">Lớp hành chính <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('lop_hanh_chinh_id') is-invalid @enderror"
                                    id="lop_hanh_chinh_id" name="lop_hanh_chinh_id" required>
                                    <option value="">-- Chọn lớp --</option>
                                    @foreach ($lopHanhChinhs as $lop)
                                        <option value="{{ $lop->id }}" data-nganh-id="{{ $lop->nganh_id }}"
                                            data-khoa-hoc-id="{{ $lop->khoa_hoc_id }}"
                                            {{ old('lop_hanh_chinh_id', $sinhVien->lop_hanh_chinh_id) == $lop->id ? 'selected' : '' }}>
                                            {{ $lop->ma_lop }} - {{ $lop->ten_lop }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-info">⚠️ Chuyển lớp sẽ tự động cập nhật sĩ số</small>
                                @error('lop_hanh_chinh_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nganh_id" class="form-label">Ngành <span class="text-danger">*</span></label>
                                <select class="form-select @error('nganh_id') is-invalid @enderror" id="nganh_id"
                                    name="nganh_id" required>
                                    <option value="">-- Chọn ngành --</option>
                                    @foreach ($nganhs as $nganh)
                                        <option value="{{ $nganh->id }}"
                                            {{ old('nganh_id', $sinhVien->nganh_id) == $nganh->id ? 'selected' : '' }}>
                                            {{ $nganh->ma_nganh }} - {{ $nganh->ten_nganh }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nganh_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="chuyen_nganh_id" class="form-label">Chuyên ngành</label>
                                <select class="form-select @error('chuyen_nganh_id') is-invalid @enderror"
                                    id="chuyen_nganh_id" name="chuyen_nganh_id">
                                    <option value="">-- Chọn chuyên ngành (nếu có) --</option>
                                    @foreach ($chuyenNganhs as $cn)
                                        <option value="{{ $cn->id }}" data-nganh-id="{{ $cn->nganh_id }}"
                                            {{ old('chuyen_nganh_id', $sinhVien->chuyen_nganh_id) == $cn->id ? 'selected' : '' }}>
                                            {{ $cn->ma_chuyen_nganh }} - {{ $cn->ten_chuyen_nganh }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('chuyen_nganh_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ky_hien_tai" class="form-label">Kỳ hiện tại <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('ky_hien_tai') is-invalid @enderror"
                                    id="ky_hien_tai" name="ky_hien_tai" min="1" max="8"
                                    value="{{ old('ky_hien_tai', $sinhVien->ky_hien_tai) }}" required>
                                @error('ky_hien_tai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="trang_thai_hoc_tap_id" class="form-label">Trạng thái học tập <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('trang_thai_hoc_tap_id') is-invalid @enderror"
                                    id="trang_thai_hoc_tap_id" name="trang_thai_hoc_tap_id" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    @foreach ($trangThais as $tt)
                                        <option value="{{ $tt->id }}"
                                            {{ old('trang_thai_hoc_tap_id', $sinhVien->trang_thai_hoc_tap_id) == $tt->id ? 'selected' : '' }}>
                                            {{ $tt->ten_trang_thai }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('trang_thai_hoc_tap_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.sinh-vien.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @php
        $oldChuyenNganhId = old('chuyen_nganh_id', $sinhVien->chuyen_nganh_id ?? '');
        $oldLopHanhChinhId = old('lop_hanh_chinh_id', $sinhVien->lop_hanh_chinh_id ?? '');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nganhSelect = document.getElementById('nganh_id');
            const chuyenNganhSelect = document.getElementById('chuyen_nganh_id');
            const khoaHocSelect = document.getElementById('khoa_hoc_id');
            const lopHanhChinhSelect = document.getElementById('lop_hanh_chinh_id');

            const allChuyenNganhs = Array.from(chuyenNganhSelect.options).slice(1);
            const allLopHanhChinhs = Array.from(lopHanhChinhSelect.options).slice(1);

            const currentChuyenNganhId = '{{ $oldChuyenNganhId }}';
            const currentLopHanhChinhId = '{{ $oldLopHanhChinhId }}';

            // Hàm lọc chuyên ngành theo ngành
            function filterChuyenNganh(nganhId) {
                chuyenNganhSelect.innerHTML = '';

                if (!nganhId) {
                    chuyenNganhSelect.disabled = true;
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Vui lòng chọn ngành trước --';
                    chuyenNganhSelect.appendChild(defaultOption);
                } else {
                    chuyenNganhSelect.disabled = false;
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Chọn chuyên ngành (nếu có) --';
                    chuyenNganhSelect.appendChild(defaultOption);

                    let hasOptions = false;
                    allChuyenNganhs.forEach(option => {
                        if (option.dataset.nganhId == nganhId) {
                            const newOption = option.cloneNode(true);
                            chuyenNganhSelect.appendChild(newOption);
                            hasOptions = true;
                        }
                    });

                    if (!hasOptions) {
                        const noDataOption = document.createElement('option');
                        noDataOption.value = '';
                        noDataOption.textContent = '-- Không có chuyên ngành --';
                        chuyenNganhSelect.appendChild(noDataOption);
                    }
                }
            }

            // Hàm lọc lớp hành chính theo ngành và khóa học
            function filterLopHanhChinh() {
                const nganhId = nganhSelect.value;
                const khoaHocId = khoaHocSelect.value;

                // Lưu giá trị hiện tại trước khi xóa
                const currentValue = lopHanhChinhSelect.value;
                lopHanhChinhSelect.innerHTML = '';

                if (!nganhId || !khoaHocId) {
                    lopHanhChinhSelect.disabled = true;
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Chọn khóa học và ngành trước --';
                    lopHanhChinhSelect.appendChild(defaultOption);
                } else {
                    lopHanhChinhSelect.disabled = false;
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Chọn lớp hành chính --';
                    lopHanhChinhSelect.appendChild(defaultOption);

                    let hasOptions = false;
                    allLopHanhChinhs.forEach(option => {
                        if (option.dataset.nganhId == nganhId && option.dataset.khoaHocId == khoaHocId) {
                            lopHanhChinhSelect.appendChild(option.cloneNode(true));
                            hasOptions = true;
                        }
                    });

                    if (!hasOptions) {
                        const noDataOption = document.createElement('option');
                        noDataOption.value = '';
                        noDataOption.textContent = '-- Không có lớp phù hợp --';
                        lopHanhChinhSelect.appendChild(noDataOption);
                    }
                }

                // Khôi phục giá trị đã chọn nếu còn tồn tại
                if (currentValue) {
                    lopHanhChinhSelect.value = currentValue;
                }
            }

            // Lắng nghe sự kiện thay đổi ngành
            nganhSelect.addEventListener('change', function() {
                filterChuyenNganh(this.value);
                filterLopHanhChinh();
            });

            // Lắng nghe sự kiện thay đổi khóa học
            khoaHocSelect.addEventListener('change', function() {
                filterLopHanhChinh();
            });

            // Khởi tạo trạng thái ban đầu
            const initialNganhId = nganhSelect.value;
            const initialKhoaHocId = khoaHocSelect.value;

            if (initialNganhId) {
                filterChuyenNganh(initialNganhId);
                if (currentChuyenNganhId) {
                    chuyenNganhSelect.value = currentChuyenNganhId;
                }
            } else {
                filterChuyenNganh(null);
            }

            // Khởi tạo lọc lớp hành chính
            if (initialNganhId && initialKhoaHocId) {
                filterLopHanhChinh();
                if (currentLopHanhChinhId) {
                    lopHanhChinhSelect.value = currentLopHanhChinhId;
                }
            }
        });
    </script>
@endpush
