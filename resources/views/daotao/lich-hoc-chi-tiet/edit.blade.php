@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa buổi học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa buổi học</h3>
                    <p class="text-subtitle text-muted">{{ $lichChiTiet->lopHocPhan->ma_lop_hp }} -
                        {{ $lichChiTiet->lopHocPhan->monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id) }}">Lịch
                                    chi tiết</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.lich-chi-tiet.update', $lichChiTiet) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ngay_hoc">Ngày học <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ngay_hoc') is-invalid @enderror"
                                        id="ngay_hoc" name="ngay_hoc"
                                        value="{{ old('ngay_hoc', $lichChiTiet->ngay_hoc->format('Y-m-d')) }}" required>
                                    @error('ngay_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ca_hoc_id">Ca học <span class="text-danger">*</span></label>
                                    <select class="form-select @error('ca_hoc_id') is-invalid @enderror" id="ca_hoc_id"
                                        name="ca_hoc_id" required>
                                        <option value="">-- Chọn ca học --</option>
                                        @foreach ($caHocs as $caHoc)
                                            <option value="{{ $caHoc->id }}"
                                                {{ old('ca_hoc_id', $lichChiTiet->ca_hoc_id) == $caHoc->id ? 'selected' : '' }}
                                                data-tiet-bat-dau="{{ $caHoc->tiet_bat_dau }}"
                                                data-tiet-ket-thuc="{{ $caHoc->tiet_ket_thuc }}"
                                                data-gio-bat-dau="{{ \Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i') }}"
                                                data-gio-ket-thuc="{{ \Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i') }}">
                                                {{ $caHoc->ten_ca }} ({{ \Carbon\Carbon::parse($caHoc->gio_bat_dau)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($caHoc->gio_ket_thuc)->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ca_hoc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle"></i> Thông tin tiết và giờ sẽ được tự động điền từ ca học
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Hiển thị thông tin ca học đã chọn (chỉ để xem) --}}
                        <div class="row mb-3" id="caHocInfo" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                        <div class="row">
                                        <div class="col-md-3">
                                            <strong>Tiết:</strong> <span id="displayTiet">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Giờ:</strong> <span id="displayGio">-</span>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phong_hoc_id">Phòng học</label>
                                    <select class="form-select @error('phong_hoc_id') is-invalid @enderror"
                                        id="phong_hoc_id" name="phong_hoc_id">
                                        <option value="">-- Không có (online) --</option>
                                        @foreach ($phongHocs as $phongHoc)
                                            <option value="{{ $phongHoc->id }}"
                                                {{ old('phong_hoc_id', $lichChiTiet->phong_hoc_id) == $phongHoc->id ? 'selected' : '' }}>
                                                {{ $phongHoc->ten_phong }} ({{ $phongHoc->suc_chua }} chỗ)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('phong_hoc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="giang_vien_id">Giảng viên <span class="text-danger">*</span></label>
                                    <select class="form-select @error('giang_vien_id') is-invalid @enderror"
                                        id="giang_vien_id" name="giang_vien_id" required>
                                        <option value="">-- Chọn giảng viên --</option>
                                        @foreach ($giangViens as $giangVien)
                                            <option value="{{ $giangVien->id }}"
                                                {{ old('giang_vien_id', $lichChiTiet->giang_vien_id) == $giangVien->id ? 'selected' : '' }}>
                                                {{ $giangVien->ho_ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('giang_vien_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hinh_thuc">Hình thức <span class="text-danger">*</span></label>
                                    <select class="form-select @error('hinh_thuc') is-invalid @enderror" id="hinh_thuc"
                                        name="hinh_thuc" required>
                                        <option value="offline"
                                            {{ old('hinh_thuc', $lichChiTiet->hinh_thuc) == 'offline' ? 'selected' : '' }}>
                                            Offline</option>
                                        <option value="online"
                                            {{ old('hinh_thuc', $lichChiTiet->hinh_thuc) == 'online' ? 'selected' : '' }}>
                                            Online</option>
                                        <option value="hybrid"
                                            {{ old('hinh_thuc', $lichChiTiet->hinh_thuc) == 'hybrid' ? 'selected' : '' }}>
                                            Hybrid</option>
                                    </select>
                                    @error('hinh_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trang_thai">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select @error('trang_thai') is-invalid @enderror" id="trang_thai"
                                        name="trang_thai" required>
                                        <option value="chua_day"
                                            {{ old('trang_thai', $lichChiTiet->trang_thai) == 'chua_day' ? 'selected' : '' }}>
                                            Chưa dạy</option>
                                        <option value="dang_day"
                                            {{ old('trang_thai', $lichChiTiet->trang_thai) == 'dang_day' ? 'selected' : '' }}>
                                            Đang dạy</option>
                                        <option value="da_day"
                                            {{ old('trang_thai', $lichChiTiet->trang_thai) == 'da_day' ? 'selected' : '' }}>
                                            Đã dạy</option>
                                        <option value="huy"
                                            {{ old('trang_thai', $lichChiTiet->trang_thai) == 'huy' ? 'selected' : '' }}>
                                            Hủy</option>
                                    </select>
                                    @error('trang_thai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="link_online">Link Online</label>
                            <input type="url" class="form-control @error('link_online') is-invalid @enderror"
                                id="link_online" name="link_online"
                                value="{{ old('link_online', $lichChiTiet->link_online) }}"
                                placeholder="https://meet.google.com/...">
                            @error('link_online')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="noi_dung_giang_day">Nội dung giảng dạy</label>
                            <textarea class="form-control" id="noi_dung_giang_day" name="noi_dung_giang_day" rows="3">{{ old('noi_dung_giang_day', $lichChiTiet->noi_dung_giang_day) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="tai_lieu_dinh_kem">Tài liệu đính kèm</label>
                            <input type="text" class="form-control" id="tai_lieu_dinh_kem" name="tai_lieu_dinh_kem"
                                value="{{ old('tai_lieu_dinh_kem', $lichChiTiet->tai_lieu_dinh_kem) }}"
                                placeholder="Link hoặc tên file tài liệu">
                        </div>

                        <div class="form-group">
                            <label for="ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="2">{{ old('ghi_chu', $lichChiTiet->ghi_chu) }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lichChiTiet->lop_hoc_phan_id) }}"
                                class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const caHocSelect = document.getElementById('ca_hoc_id');
                const caHocInfo = document.getElementById('caHocInfo');
                const displayTiet = document.getElementById('displayTiet');
                const displayGio = document.getElementById('displayGio');

                function updateCaHocInfo() {
                    const selectedOption = caHocSelect.options[caHocSelect.selectedIndex];
                    if (selectedOption.value) {
                        const tietBatDau = selectedOption.getAttribute('data-tiet-bat-dau');
                        const tietKetThuc = selectedOption.getAttribute('data-tiet-ket-thuc');
                        const gioBatDau = selectedOption.getAttribute('data-gio-bat-dau');
                        const gioKetThuc = selectedOption.getAttribute('data-gio-ket-thuc');

                        displayTiet.textContent = `${tietBatDau} - ${tietKetThuc}`;
                        displayGio.textContent = `${gioBatDau} - ${gioKetThuc}`;
                        caHocInfo.style.display = 'block';
                    } else {
                        caHocInfo.style.display = 'none';
                    }
                }

                // Cập nhật khi chọn ca học
                caHocSelect.addEventListener('change', updateCaHocInfo);

                // Cập nhật khi trang load (nếu đã có ca học được chọn)
                updateCaHocInfo();
            });
        </script>
    @endpush
@endsection
