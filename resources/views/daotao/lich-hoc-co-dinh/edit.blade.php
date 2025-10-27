@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa lịch học cố định')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa lịch học cố định</h3>
                    <p class="text-subtitle text-muted">{{ $lichCoDinh->lopHocPhan->ma_lop_hp }} -
                        {{ $lichCoDinh->lopHocPhan->monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id) }}">Lịch
                                    cố định</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin lịch học</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.lich-co-dinh.update', $lichCoDinh) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="thu_trong_tuan">Thứ <span class="text-danger">*</span></label>
                                    <select class="form-select @error('thu_trong_tuan') is-invalid @enderror"
                                        id="thu_trong_tuan" name="thu_trong_tuan" required>
                                        <option value="">-- Chọn thứ --</option>
                                        <option value="2"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 2 ? 'selected' : '' }}>
                                            Thứ 2</option>
                                        <option value="3"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 3 ? 'selected' : '' }}>
                                            Thứ 3</option>
                                        <option value="4"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 4 ? 'selected' : '' }}>
                                            Thứ 4</option>
                                        <option value="5"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 5 ? 'selected' : '' }}>
                                            Thứ 5</option>
                                        <option value="6"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 6 ? 'selected' : '' }}>
                                            Thứ 6</option>
                                        <option value="7"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 7 ? 'selected' : '' }}>
                                            Thứ 7</option>
                                        <option value="8"
                                            {{ old('thu_trong_tuan', $lichCoDinh->thu_trong_tuan) == 8 ? 'selected' : '' }}>
                                            Chủ nhật</option>
                                    </select>
                                    @error('thu_trong_tuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tiet_bat_dau">Tiết bắt đầu <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tiet_bat_dau') is-invalid @enderror"
                                        id="tiet_bat_dau" name="tiet_bat_dau"
                                        value="{{ old('tiet_bat_dau', $lichCoDinh->tiet_bat_dau) }}" min="1"
                                        max="10" required>
                                    @error('tiet_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tiet_ket_thuc">Tiết kết thúc <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tiet_ket_thuc') is-invalid @enderror"
                                        id="tiet_ket_thuc" name="tiet_ket_thuc"
                                        value="{{ old('tiet_ket_thuc', $lichCoDinh->tiet_ket_thuc) }}" min="1"
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
                                        value="{{ old('gio_bat_dau', \Carbon\Carbon::parse($lichCoDinh->gio_bat_dau)->format('H:i')) }}"
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
                                        value="{{ old('gio_ket_thuc', \Carbon\Carbon::parse($lichCoDinh->gio_ket_thuc)->format('H:i')) }}"
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
                                    <label for="phong_hoc_id">Phòng học <span class="text-danger">*</span></label>
                                    <select class="form-select @error('phong_hoc_id') is-invalid @enderror"
                                        id="phong_hoc_id" name="phong_hoc_id" required>
                                        <option value="">-- Chọn phòng học --</option>
                                        @foreach ($phongHocs as $phongHoc)
                                            <option value="{{ $phongHoc->id }}"
                                                {{ old('phong_hoc_id', $lichCoDinh->phong_hoc_id) == $phongHoc->id ? 'selected' : '' }}>
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
                                                {{ old('giang_vien_id', $lichCoDinh->giang_vien_id) == $giangVien->id ? 'selected' : '' }}>
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
                                            {{ old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'offline' ? 'selected' : '' }}>
                                            Offline</option>
                                        <option value="online"
                                            {{ old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'online' ? 'selected' : '' }}>
                                            Online</option>
                                        <option value="hybrid"
                                            {{ old('hinh_thuc', $lichCoDinh->hinh_thuc) == 'hybrid' ? 'selected' : '' }}>
                                            Hybrid</option>
                                    </select>
                                    @error('hinh_thuc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="link_online">Link Online</label>
                                    <input type="url" class="form-control @error('link_online') is-invalid @enderror"
                                        id="link_online" name="link_online"
                                        value="{{ old('link_online', $lichCoDinh->link_online) }}"
                                        placeholder="https://meet.google.com/...">
                                    @error('link_online')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3">{{ old('ghi_chu', $lichCoDinh->ghi_chu) }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh', $lichCoDinh->lop_hoc_phan_id) }}"
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
