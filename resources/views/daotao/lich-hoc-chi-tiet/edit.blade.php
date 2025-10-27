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

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tiet_bat_dau">Tiết bắt đầu <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tiet_bat_dau') is-invalid @enderror"
                                        id="tiet_bat_dau" name="tiet_bat_dau"
                                        value="{{ old('tiet_bat_dau', $lichChiTiet->tiet_bat_dau) }}" min="1"
                                        max="10" required>
                                    @error('tiet_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tiet_ket_thuc">Tiết kết thúc <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tiet_ket_thuc') is-invalid @enderror"
                                        id="tiet_ket_thuc" name="tiet_ket_thuc"
                                        value="{{ old('tiet_ket_thuc', $lichChiTiet->tiet_ket_thuc) }}" min="1"
                                        max="10" required>
                                    @error('tiet_ket_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gio_bat_dau">Giờ bắt đầu <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('gio_bat_dau') is-invalid @enderror"
                                        id="gio_bat_dau" name="gio_bat_dau"
                                        value="{{ old('gio_bat_dau', \Carbon\Carbon::parse($lichChiTiet->gio_bat_dau)->format('H:i')) }}"
                                        required>
                                    @error('gio_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gio_ket_thuc">Giờ kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('gio_ket_thuc') is-invalid @enderror"
                                        id="gio_ket_thuc" name="gio_ket_thuc"
                                        value="{{ old('gio_ket_thuc', \Carbon\Carbon::parse($lichChiTiet->gio_ket_thuc)->format('H:i')) }}"
                                        required>
                                    @error('gio_ket_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
@endsection
