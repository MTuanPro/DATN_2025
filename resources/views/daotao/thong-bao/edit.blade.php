@extends('layouts.layout-daotao')

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
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('daotao.thong-bao.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
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
                    <h4 class="card-title">Thông tin thông báo</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('daotao.thong-bao.update', $thongBao->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Tiêu đề -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="tieu_de"
                                    class="form-control @error('tieu_de') is-invalid @enderror"
                                    value="{{ old('tieu_de', $thongBao->tieu_de) }}" required>
                                @error('tieu_de')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nội dung -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="noi_dung" rows="8" class="form-control @error('noi_dung') is-invalid @enderror" required>{{ old('noi_dung', $thongBao->noi_dung) }}</textarea>
                                @error('noi_dung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Loại thông báo -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Loại thông báo <span class="text-danger">*</span></label>
                                <select name="loai_thong_bao"
                                    class="form-select @error('loai_thong_bao') is-invalid @enderror" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="tin_tuc"
                                        {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'tin_tuc' ? 'selected' : '' }}>
                                        Tin tức</option>
                                    <option value="thong_bao_chung"
                                        {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'thong_bao_chung' ? 'selected' : '' }}>
                                        Thông báo chung</option>
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
                                        Điểm</option>
                                    <option value="dang_ky_mon"
                                        {{ old('loai_thong_bao', $thongBao->loai_thong_bao) == 'dang_ky_mon' ? 'selected' : '' }}>
                                        Đăng ký môn</option>
                                </select>
                                @error('loai_thong_bao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mức độ quan trọng -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mức độ quan trọng <span class="text-danger">*</span></label>
                                <select name="muc_do_quan_trong"
                                    class="form-select @error('muc_do_quan_trong') is-invalid @enderror" required>
                                    <option value="binh_thuong"
                                        {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'binh_thuong' ? 'selected' : '' }}>
                                        Bình thường</option>
                                    <option value="quan_trong"
                                        {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'quan_trong' ? 'selected' : '' }}>
                                        Quan trọng</option>
                                    <option value="rat_quan_trong"
                                        {{ old('muc_do_quan_trong', $thongBao->muc_do_quan_trong) == 'rat_quan_trong' ? 'selected' : '' }}>
                                        Rất quan trọng</option>
                                </select>
                                @error('muc_do_quan_trong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ghim đầu trang -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tùy chọn hiển thị</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ghim_dau_trang"
                                        id="ghim_dau_trang" value="1"
                                        {{ old('ghim_dau_trang', $thongBao->ghim_dau_trang) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ghim_dau_trang">
                                        Ghim đầu trang
                                    </label>
                                </div>
                            </div>

                            <!-- Thời gian hiển thị -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hiển thị từ ngày</label>
                                <input type="datetime-local" name="hien_thi_tu_ngay"
                                    class="form-control @error('hien_thi_tu_ngay') is-invalid @enderror"
                                    value="{{ old('hien_thi_tu_ngay', $thongBao->hien_thi_tu_ngay ? $thongBao->hien_thi_tu_ngay->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Để trống = hiển thị ngay</small>
                                @error('hien_thi_tu_ngay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hết hạn ngày</label>
                                <input type="datetime-local" name="ngay_het_han"
                                    class="form-control @error('ngay_het_han') is-invalid @enderror"
                                    value="{{ old('ngay_het_han', $thongBao->ngay_het_han ? $thongBao->ngay_het_han->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Để trống = không giới hạn</small>
                                @error('ngay_het_han')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File đính kèm hiện tại -->
                            @if ($thongBao->anh_dai_dien)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ảnh đại diện hiện tại</label>
                                    <div>
                                        <img src="{{ Storage::url($thongBao->anh_dai_dien) }}" alt="Ảnh đại diện"
                                            style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="xoa_anh_dai_dien"
                                                id="xoa_anh_dai_dien" value="1">
                                            <label class="form-check-label" for="xoa_anh_dai_dien">
                                                Xóa ảnh này
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($thongBao->file_dinh_kem)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">File đính kèm hiện tại</label>
                                    <div>
                                        <a href="{{ Storage::url($thongBao->file_dinh_kem) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark"></i> Xem file
                                        </a>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="xoa_file_dinh_kem"
                                                id="xoa_file_dinh_kem" value="1">
                                            <label class="form-check-label" for="xoa_file_dinh_kem">
                                                Xóa file này
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload file mới -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ảnh đại diện mới</label>
                                <input type="file" name="anh_dai_dien"
                                    class="form-control @error('anh_dai_dien') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Chỉ chấp nhận file ảnh, tối đa 2MB</small>
                                @error('anh_dai_dien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">File đính kèm mới</label>
                                <input type="file" name="file_dinh_kem"
                                    class="form-control @error('file_dinh_kem') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">PDF, Word, Excel, tối đa 10MB</small>
                                @error('file_dinh_kem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('daotao.thong-bao.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
