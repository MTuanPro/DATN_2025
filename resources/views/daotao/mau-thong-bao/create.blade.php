@extends('layouts.layout-daotao')

@section('title', 'Tạo mẫu thông báo tự động')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tạo mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Thêm mới mẫu thông báo tự động cho hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.mau-thong-bao.index') }}">Mẫu thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin mẫu thông báo</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5>Có lỗi xảy ra:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('dao-tao.mau-thong-bao.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="loai_thong_bao" class="form-label">
                                        Loại thông báo <span class="text-danger">*</span>
                                    </label>
                                    <select name="loai_thong_bao" id="loai_thong_bao" 
                                            class="form-select @error('loai_thong_bao') is-invalid @enderror" required>
                                        <option value="">-- Chọn loại thông báo --</option>
                                        @foreach($loaiThongBaoOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('loai_thong_bao') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('loai_thong_bao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Loại thông báo phải là duy nhất trong hệ thống</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="muc_do_uu_tien" class="form-label">
                                        Mức độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <select name="muc_do_uu_tien" id="muc_do_uu_tien" 
                                            class="form-select @error('muc_do_uu_tien') is-invalid @enderror" required>
                                        <option value="binh_thuong" {{ old('muc_do_uu_tien') == 'binh_thuong' ? 'selected' : '' }}>
                                            Bình thường
                                        </option>
                                        <option value="quan_trong" {{ old('muc_do_uu_tien') == 'quan_trong' ? 'selected' : '' }}>
                                            Quan trọng
                                        </option>
                                        <option value="rat_quan_trong" {{ old('muc_do_uu_tien') == 'rat_quan_trong' ? 'selected' : '' }}>
                                            Rất quan trọng
                                        </option>
                                    </select>
                                    @error('muc_do_uu_tien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tieu_de_mau" class="form-label">
                                Tiêu đề mẫu <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tieu_de_mau" id="tieu_de_mau" 
                                   class="form-control @error('tieu_de_mau') is-invalid @enderror"
                                   value="{{ old('tieu_de_mau') }}" 
                                   placeholder="Nhập tiêu đề mẫu thông báo" 
                                   maxlength="255" required>
                            @error('tieu_de_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Sử dụng các biến như {ho_ten}, {mon_hoc}, {ngay_hoc}, v.v.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="noi_dung_mau" class="form-label">
                                Nội dung mẫu <span class="text-danger">*</span>
                            </label>
                            <textarea name="noi_dung_mau" id="noi_dung_mau" rows="8"
                                      class="form-control @error('noi_dung_mau') is-invalid @enderror"
                                      placeholder="Nhập nội dung mẫu thông báo" required>{{ old('noi_dung_mau') }}</textarea>
                            @error('noi_dung_mau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Sử dụng các biến như {ho_ten}, {mon_hoc}, {phong_hoc}, {so_tien}, v.v.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="doi_tuong_mac_dinh" class="form-label">Đối tượng mặc định</label>
                            <select name="doi_tuong_mac_dinh" id="doi_tuong_mac_dinh" 
                                    class="form-select @error('doi_tuong_mac_dinh') is-invalid @enderror">
                                <option value="">-- Không chọn --</option>
                                <option value="all" {{ old('doi_tuong_mac_dinh') == 'all' ? 'selected' : '' }}>Tất cả</option>
                                <option value="sinh_vien" {{ old('doi_tuong_mac_dinh') == 'sinh_vien' ? 'selected' : '' }}>Sinh viên</option>
                                <option value="giang_vien" {{ old('doi_tuong_mac_dinh') == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                                <option value="lop_hoc_phan" {{ old('doi_tuong_mac_dinh') == 'lop_hoc_phan' ? 'selected' : '' }}>Lớp học phần</option>
                            </select>
                            @error('doi_tuong_mac_dinh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea name="ghi_chu" id="ghi_chu" rows="3"
                                      class="form-control @error('ghi_chu') is-invalid @enderror"
                                      placeholder="Thêm ghi chú về mẫu thông báo (nếu có)">{{ old('ghi_chu') }}</textarea>
                            @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="gui_email_mac_dinh" id="gui_email_mac_dinh" 
                                           class="form-check-input" value="1" {{ old('gui_email_mac_dinh') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gui_email_mac_dinh">
                                        Gửi email mặc định
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="gui_sms_mac_dinh" id="gui_sms_mac_dinh" 
                                           class="form-check-input" value="1" {{ old('gui_sms_mac_dinh') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gui_sms_mac_dinh">
                                        Gửi SMS mặc định
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="kich_hoat" id="kich_hoat" 
                                           class="form-check-input" value="1" {{ old('kich_hoat', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kich_hoat">
                                        Kích hoạt ngay
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dao-tao.mau-thong-bao.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu mẫu thông báo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hướng dẫn sử dụng biến -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">📝 Hướng dẫn sử dụng biến trong template</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Các biến có thể sử dụng:</strong></p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-3">
                                <li><code>{ho_ten}</code> - Họ tên người nhận</li>
                                <li><code>{mon_hoc}</code> - Tên môn học</li>
                                <li><code>{ngay_hoc}</code> - Ngày học</li>
                                <li><code>{gio_hoc}</code> - Giờ học</li>
                                <li><code>{phong_hoc}</code> - Phòng học</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-3">
                                <li><code>{ngay_thi}</code> - Ngày thi</li>
                                <li><code>{so_tien}</code> - Số tiền học phí</li>
                                <li><code>{han_dong}</code> - Hạn đóng học phí</li>
                                <li><code>{diem}</code> - Điểm số</li>
                                <li><code>{ghi_chu}</code> - Ghi chú</li>
                            </ul>
                        </div>
                    </div>
                    
                    <p class="mb-2"><strong>Ví dụ:</strong></p>
                    <div class="alert alert-info mb-0">
                        <p class="mb-2"><strong>Tiêu đề:</strong> Thông báo lịch học môn {mon_hoc}</p>
                        <p class="mb-0"><strong>Nội dung:</strong> Xin chào {ho_ten}, lớp học phần {mon_hoc} sẽ được học vào {ngay_hoc} tại {phong_hoc}.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
