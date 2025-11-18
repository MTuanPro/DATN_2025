@extends('layouts.layout-admin')

@section('title', 'Sửa mẫu thông báo tự động')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Sửa mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Chỉnh sửa thông tin mẫu thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mau-thong-bao.index') }}">Mẫu thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin mẫu thông báo</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.mau-thong-bao.update', $mauThongBao) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="loai_thong_bao" class="form-label">
                                        Loại thông báo <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('loai_thong_bao') is-invalid @enderror" id="loai_thong_bao"
                                        name="loai_thong_bao" required>
                                        <option value="">-- Chọn loại thông báo --</option>
                                        @foreach ($loaiThongBaoOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('loai_thong_bao', $mauThongBao->loai_thong_bao) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('loai_thong_bao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="doi_tuong_mac_dinh" class="form-label">Đối tượng mặc định</label>
                                    <input type="text" class="form-control @error('doi_tuong_mac_dinh') is-invalid @enderror"
                                        id="doi_tuong_mac_dinh" name="doi_tuong_mac_dinh"
                                        value="{{ old('doi_tuong_mac_dinh', $mauThongBao->doi_tuong_mac_dinh) }}"
                                        placeholder="VD: sinh_vien, giang_vien">
                                    @error('doi_tuong_mac_dinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Để trống nếu áp dụng cho tất cả</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tieu_de_mau" class="form-label">
                                Tiêu đề mẫu <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('tieu_de_mau') is-invalid @enderror"
                                id="tieu_de_mau" name="tieu_de_mau"
                                value="{{ old('tieu_de_mau', $mauThongBao->tieu_de_mau) }}"
                                placeholder="VD: Thông báo lịch học mới cho {mon_hoc}" required>
                            @error('tieu_de_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Có thể sử dụng biến: {mon_hoc}, {ngay_thi}, {ten_sinh_vien}, ...</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="noi_dung_mau" class="form-label">
                                Nội dung mẫu <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('noi_dung_mau') is-invalid @enderror" id="noi_dung_mau"
                                name="noi_dung_mau" rows="8" required>{{ old('noi_dung_mau', $mauThongBao->noi_dung_mau) }}</textarea>
                            @error('noi_dung_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Có thể sử dụng biến: {mon_hoc}, {ngay_thi}, {ten_sinh_vien}, ...</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="muc_do_uu_tien" class="form-label">
                                        Mức độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('muc_do_uu_tien') is-invalid @enderror" id="muc_do_uu_tien"
                                        name="muc_do_uu_tien" required>
                                        <option value="binh_thuong"
                                            {{ old('muc_do_uu_tien', $mauThongBao->muc_do_uu_tien) == 'binh_thuong' ? 'selected' : '' }}>
                                            Bình thường
                                        </option>
                                        <option value="quan_trong"
                                            {{ old('muc_do_uu_tien', $mauThongBao->muc_do_uu_tien) == 'quan_trong' ? 'selected' : '' }}>
                                            Quan trọng
                                        </option>
                                        <option value="rat_quan_trong"
                                            {{ old('muc_do_uu_tien', $mauThongBao->muc_do_uu_tien) == 'rat_quan_trong' ? 'selected' : '' }}>
                                            Rất quan trọng
                                        </option>
                                    </select>
                                    @error('muc_do_uu_tien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_email_mac_dinh"
                                            name="gui_email_mac_dinh" value="1"
                                            {{ old('gui_email_mac_dinh', $mauThongBao->gui_email_mac_dinh) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gui_email_mac_dinh">
                                            Gửi email mặc định
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_sms_mac_dinh"
                                            name="gui_sms_mac_dinh" value="1"
                                            {{ old('gui_sms_mac_dinh', $mauThongBao->gui_sms_mac_dinh) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gui_sms_mac_dinh">
                                            Gửi SMS mặc định
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kich_hoat" name="kich_hoat"
                                            value="1" {{ old('kich_hoat', $mauThongBao->kich_hoat) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kich_hoat">
                                            Kích hoạt mẫu này
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu"
                                rows="3" placeholder="Ghi chú về mẫu thông báo...">{{ old('ghi_chu', $mauThongBao->ghi_chu) }}</textarea>
                            @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.mau-thong-bao.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

