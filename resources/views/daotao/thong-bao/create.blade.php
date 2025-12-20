@extends('layouts.layout-daotao')

@section('title', 'Tạo thông báo mới')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        <i class="bi bi-bell-fill text-success"></i> Tạo thông báo mới
                    </h3>
                    <p class="text-subtitle text-muted">
                        <span class="badge bg-light-success">Đào tạo</span>
                        Gửi thông báo học vụ đến sinh viên và giảng viên
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.thong-bao.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Alert hướng dẫn -->
            <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">
                    <i class="bi bi-info-circle-fill"></i> Lưu ý dành cho Đào tạo
                </h5>
                <p class="mb-0">
                    • Bạn chỉ có thể gửi thông báo cho <strong>Sinh viên</strong> và <strong>Giảng viên</strong><br>
                    • Thông báo có thể gửi cho toàn bộ hoặc từng lớp cụ thể<br>
                    • Có thể gửi kèm email tự động cho các thông báo quan trọng
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="card border-success">
                <div class="card-header bg-light-success">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-clipboard-check"></i> Thông tin thông báo học vụ
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.thong-bao.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Tiêu đề -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="tieu_de"
                                    class="form-control @error('tieu_de') is-invalid @enderror" value="{{ old('tieu_de') }}"
                                    required>
                                @error('tieu_de')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nội dung -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea name="noi_dung" rows="8" class="form-control @error('noi_dung') is-invalid @enderror" required>{{ old('noi_dung') }}</textarea>
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
                                    <option value="tin_tuc" {{ old('loai_thong_bao') == 'tin_tuc' ? 'selected' : '' }}>Tin
                                        tức</option>
                                    <option value="thong_bao_chung"
                                        {{ old('loai_thong_bao') == 'thong_bao_chung' ? 'selected' : '' }}>Thông báo chung
                                    </option>
                                    <option value="tin_gap" {{ old('loai_thong_bao') == 'tin_gap' ? 'selected' : '' }}>Tin
                                        gấp</option>
                                    <option value="lich_hoc" {{ old('loai_thong_bao') == 'lich_hoc' ? 'selected' : '' }}>
                                        Lịch học</option>
                                    <option value="lich_thi" {{ old('loai_thong_bao') == 'lich_thi' ? 'selected' : '' }}>
                                        Lịch thi</option>
                                    <option value="hoc_phi" {{ old('loai_thong_bao') == 'hoc_phi' ? 'selected' : '' }}>Học
                                        phí</option>
                                    <option value="diem" {{ old('loai_thong_bao') == 'diem' ? 'selected' : '' }}>Điểm
                                    </option>
                                    <option value="dang_ky_mon"
                                        {{ old('loai_thong_bao') == 'dang_ky_mon' ? 'selected' : '' }}>Đăng ký môn</option>
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
                                        {{ old('muc_do_quan_trong') == 'binh_thuong' ? 'selected' : '' }}>Bình thường
                                    </option>
                                    <option value="quan_trong"
                                        {{ old('muc_do_quan_trong') == 'quan_trong' ? 'selected' : '' }}>Quan trọng
                                    </option>
                                    <option value="rat_quan_trong"
                                        {{ old('muc_do_quan_trong') == 'rat_quan_trong' ? 'selected' : '' }}>Rất quan trọng
                                    </option>
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
                                        id="ghim_dau_trang" value="1" {{ old('ghim_dau_trang') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ghim_dau_trang">
                                        Ghim đầu trang
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="gui_email" id="gui_email"
                                        value="1" {{ old('gui_email') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gui_email">
                                        Gửi email thông báo
                                    </label>
                                </div>
                            </div>

                            <!-- Đối tượng nhận - DÀNH CHO ĐÀO TẠO -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="bi bi-people-fill text-success"></i> Đối tượng nhận 
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="doi_tuong" id="doi_tuong"
                                    class="form-select @error('doi_tuong') is-invalid @enderror" required>
                                    <option value="">-- Chọn đối tượng nhận --</option>
                                    <option value="all" {{ old('doi_tuong') == 'all' ? 'selected' : '' }}>
                                        📢 Tất cả (Sinh viên + Giảng viên)
                                    </option>
                                    <option value="sinh_vien" {{ old('doi_tuong') == 'sinh_vien' ? 'selected' : '' }}>
                                        👨‍🎓 Tất cả sinh viên
                                    </option>
                                    <option value="giang_vien" {{ old('doi_tuong') == 'giang_vien' ? 'selected' : '' }}>
                                        👨‍🏫 Tất cả giảng viên
                                    </option>
                                    <option value="nganh" {{ old('doi_tuong') == 'nganh' ? 'selected' : '' }}>
                                        🏫 Lớp hành chính cụ thể
                                    </option>
                                    <option value="lop_hoc_phan" {{ old('doi_tuong') == 'lop_hoc_phan' ? 'selected' : '' }}>
                                        📚 Lớp học phần cụ thể
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    Chỉ có thể gửi cho sinh viên và giảng viên
                                </small>
                                @error('doi_tuong')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Đối tượng cụ thể -->
                            <div class="col-md-6 mb-3" id="doi_tuong_cu_the_container" style="display: none;">
                                <label class="form-label" id="doi_tuong_cu_the_label">Chọn lớp</label>
                                <select name="doi_tuong_cu_the_id" id="doi_tuong_cu_the_id" class="form-select">
                                    <option value="">-- Chọn --</option>
                                </select>
                            </div>

                            <!-- Thời gian hiển thị -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hiển thị từ ngày</label>
                                <input type="datetime-local" name="hien_thi_tu_ngay"
                                    class="form-control @error('hien_thi_tu_ngay') is-invalid @enderror"
                                    value="{{ old('hien_thi_tu_ngay') }}">
                                <small class="text-muted">Để trống = hiển thị ngay</small>
                                @error('hien_thi_tu_ngay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hết hạn ngày</label>
                                <input type="datetime-local" name="ngay_het_han"
                                    class="form-control @error('ngay_het_han') is-invalid @enderror"
                                    value="{{ old('ngay_het_han') }}">
                                <small class="text-muted">Để trống = không giới hạn</small>
                                @error('ngay_het_han')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File đính kèm -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="anh_dai_dien"
                                    class="form-control @error('anh_dai_dien') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Chỉ chấp nhận file ảnh, tối đa 2MB</small>
                                @error('anh_dai_dien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">File đính kèm</label>
                                <input type="file" name="file_dinh_kem"
                                    class="form-control @error('file_dinh_kem') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                <small class="text-muted">PDF, Word, Excel, tối đa 10MB</small>
                                @error('file_dinh_kem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="text-muted small">
                                <i class="bi bi-shield-check"></i> 
                                Thông báo sẽ được gửi qua hệ thống và email (nếu chọn)
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('dao-tao.thong-bao.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-send-fill"></i> Gửi thông báo ngay
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        // Dynamic load lớp hành chính/học phần
        const nganhs = @json($nganhs);
        const lopHocPhans = @json($lopHocPhans);

        document.getElementById('doi_tuong').addEventListener('change', function() {
            const container = document.getElementById('doi_tuong_cu_the_container');
            const select = document.getElementById('doi_tuong_cu_the_id');
            const label = document.getElementById('doi_tuong_cu_the_label');
            const value = this.value;

            select.innerHTML = '<option value="">-- Chọn --</option>';

            if (value === 'nganh') {
                container.style.display = 'block';
                label.textContent = 'Chọn lớp hành chính';
                nganhs.forEach(lop => {
                    select.innerHTML += `<option value="${lop.id}">${lop.ma_lop} - ${lop.ten_lop}</option>`;
                });
            } else if (value === 'lop_hoc_phan') {
                container.style.display = 'block';
                label.textContent = 'Chọn lớp học phần';
                lopHocPhans.forEach(lop => {
                    const tenMon = lop.mon_hoc ? lop.mon_hoc.ten_mon_hoc : 'N/A';
                    select.innerHTML +=
                        `<option value="${lop.id}">${lop.ma_lop_hoc_phan} - ${tenMon}</option>`;
                });
            } else {
                container.style.display = 'none';
            }
        });

        // Trigger on page load if old value exists
        if (document.getElementById('doi_tuong').value) {
            document.getElementById('doi_tuong').dispatchEvent(new Event('change'));
        }
    </script>
@endpush
