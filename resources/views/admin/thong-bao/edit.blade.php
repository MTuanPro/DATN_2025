@extends('layouts.layout-admin')

@section('title', 'Chỉnh sửa thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa thông báo</h3>
                    <p class="text-subtitle text-muted">Cập nhật thông tin thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.thong-bao.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.thong-bao.update', $thongBao) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                {{-- Tiêu đề --}}
                                <div class="mb-3">
                                    <label for="tieu_de" class="form-label">Tiêu đề <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tieu_de') is-invalid @enderror"
                                        id="tieu_de" name="tieu_de" value="{{ old('tieu_de', $thongBao->tieu_de) }}"
                                        required>
                                    @error('tieu_de')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nội dung --}}
                                <div class="mb-3">
                                    <label for="noi_dung" class="form-label">Nội dung <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('noi_dung') is-invalid @enderror" id="noi_dung" name="noi_dung" rows="10"
                                        required>{{ old('noi_dung', $thongBao->noi_dung) }}</textarea>
                                    @error('noi_dung')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Hỗ trợ HTML cơ bản</small>
                                </div>

                                {{-- Ảnh đại diện --}}
                                <div class="mb-3">
                                    <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>

                                    @if ($thongBao->anh_dai_dien)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $thongBao->anh_dai_dien) }}" alt="Ảnh hiện tại"
                                                class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                            <div class="mt-1">
                                                <small class="text-muted">Ảnh hiện tại</small>
                                                <label class="ms-2">
                                                    <input type="checkbox" name="xoa_anh" value="1"> Xóa ảnh này
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <input type="file" class="form-control @error('anh_dai_dien') is-invalid @enderror"
                                        id="anh_dai_dien" name="anh_dai_dien" accept="image/*">
                                    @error('anh_dai_dien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB. Chọn ảnh mới để thay
                                        thế.</small>
                                </div>

                                {{-- File đính kèm --}}
                                <div class="mb-3">
                                    <label for="file_dinh_kem" class="form-label">File đính kèm</label>

                                    @if ($thongBao->file_dinh_kem)
                                        <div class="mb-2 p-2 bg-light rounded">
                                            <i class="bi bi-file-earmark-text text-primary"></i>
                                            <a href="{{ asset('storage/' . $thongBao->file_dinh_kem) }}" target="_blank"
                                                class="text-decoration-none">
                                                {{ basename($thongBao->file_dinh_kem) }}
                                            </a>
                                            <label class="ms-2">
                                                <input type="checkbox" name="xoa_file" value="1"> Xóa file này
                                            </label>
                                        </div>
                                    @endif

                                    <input type="file" class="form-control @error('file_dinh_kem') is-invalid @enderror"
                                        id="file_dinh_kem" name="file_dinh_kem" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    @error('file_dinh_kem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Định dạng: PDF, DOC, DOCX, XLS, XLSX. Tối đa 10MB. Chọn file
                                        mới để thay thế.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                {{-- Loại thông báo --}}
                                <div class="mb-3">
                                    <label for="loai_thong_bao" class="form-label">Loại thông báo <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('loai_thong_bao') is-invalid @enderror"
                                        id="loai_thong_bao" name="loai_thong_bao" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="tin_tuc"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'tin_tuc' ? 'selected' : '' }}>
                                            Tin tức</option>
                                        <option value="thong_bao_chung"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'thong_bao_chung' ? 'selected' : '' }}>
                                            Thông báo
                                            chung</option>
                                        <option value="tin_gap"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'tin_gap' ? 'selected' : '' }}>
                                            Tin gấp</option>
                                        <option value="lich_hoc"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'lich_hoc' ? 'selected' : '' }}>
                                            Lịch học</option>
                                        <option value="lich_thi"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'lich_thi' ? 'selected' : '' }}>
                                            Lịch thi</option>
                                        <option value="hoc_phi"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'hoc_phi' ? 'selected' : '' }}>
                                            Học phí</option>
                                        <option value="diem"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'diem' ? 'selected' : '' }}>
                                            Điểm
                                        </option>
                                        <option value="dang_ky_mon"
                                            {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'dang_ky_mon' ? 'selected' : '' }}>
                                            Đăng ký môn
                                        </option>
                                    </select>
                                    @error('loai_thong_bao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Mức độ quan trọng --}}
                                <div class="mb-3">
                                    <label for="muc_do_quan_trong" class="form-label">Mức độ quan trọng <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('muc_do_quan_trong') is-invalid @enderror"
                                        id="muc_do_quan_trong" name="muc_do_quan_trong" required>
                                        <option value="binh_thuong"
                                            {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'binh_thuong' ? 'selected' : '' }}>
                                            Bình thường
                                        </option>
                                        <option value="quan_trong"
                                            {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'quan_trong' ? 'selected' : '' }}>
                                            Quan trọng
                                        </option>
                                        <option value="rat_quan_trong"
                                            {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'rat_quan_trong' ? 'selected' : '' }}>
                                            Rất quan
                                            trọng</option>
                                    </select>
                                    @error('muc_do_quan_trong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Đối tượng --}}
                                <div class="mb-3">
                                    <label for="doi_tuong" class="form-label">Đối tượng nhận <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('doi_tuong') is-invalid @enderror" id="doi_tuong"
                                        name="doi_tuong" required>
                                        <option value="">-- Chọn đối tượng --</option>
                                        <option value="all"
                                            {{ old('doi_tuong', $thongBao->doi_tuong) == 'all' ? 'selected' : '' }}>Tất cả
                                        </option>
                                        <option value="sinh_vien"
                                            {{ old('doi_tuong', $thongBao->doi_tuong) == 'sinh_vien' ? 'selected' : '' }}>
                                            Sinh viên</option>
                                        <option value="giang_vien"
                                            {{ old('doi_tuong', $thongBao->doi_tuong) == 'giang_vien' ? 'selected' : '' }}>
                                            Giảng viên</option>
                                        <option value="dao_tao"
                                            {{ old('doi_tuong', $thongBao->doi_tuong) == 'dao_tao' ? 'selected' : '' }}>Đào
                                            tạo</option>
                                        <option value="admin"
                                            {{ old('doi_tuong', $thongBao->doi_tuong) == 'admin' ? 'selected' : '' }}>Admin
                                        </option>
                                    </select>
                                    @error('doi_tuong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Ghim đầu trang --}}
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ghim_dau_trang"
                                            name="ghim_dau_trang" value="1"
                                            {{ old('ghim_dau_trang', $thongBao->ghim_dau_trang) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ghim_dau_trang">
                                            Ghim đầu trang
                                        </label>
                                    </div>
                                </div>

                                {{-- Gửi email --}}
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gui_email" name="gui_email"
                                            value="1" {{ old('gui_email', $thongBao->gui_email) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gui_email">
                                            Gửi email thông báo
                                        </label>
                                    </div>
                                </div>

                                {{-- Hiển thị từ ngày --}}
                                <div class="mb-3">
                                    <label for="hien_thi_tu_ngay" class="form-label">Hiển thị từ ngày</label>
                                    <input type="datetime-local"
                                        class="form-control @error('hien_thi_tu_ngay') is-invalid @enderror"
                                        id="hien_thi_tu_ngay" name="hien_thi_tu_ngay"
                                        value="{{ old('hien_thi_tu_ngay', $thongBao->hien_thi_tu_ngay) }}">
                                    @error('hien_thi_tu_ngay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Để trống để hiển thị ngay</small>
                                </div>

                                {{-- Ngày hết hạn --}}
                                <div class="mb-3">
                                    <label for="ngay_het_han" class="form-label">Ngày hết hạn</label>
                                    <input type="datetime-local"
                                        class="form-control @error('ngay_het_han') is-invalid @enderror" id="ngay_het_han"
                                        name="ngay_het_han" value="{{ old('ngay_het_han', $thongBao->ngay_het_han) }}">
                                    @error('ngay_het_han')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Để trống nếu không có hạn</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Gửi thông báo
                                </button>
                                <a href="{{ route('admin.thong-bao.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
