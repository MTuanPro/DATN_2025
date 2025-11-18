@extends('layouts.layout-daotao')

@section('title', 'Thêm lịch học cố định')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thêm lịch học cố định</h3>
                    <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhan) }}">Lịch cố định</a>
                            </li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin môn học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin môn học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Mã môn:</strong> {{ $lopHocPhan->monHoc->ma_mon }}
                        </div>
                        <div class="col-md-3">
                            <strong>Số tín chỉ:</strong> {{ $lopHocPhan->monHoc->so_tin_chi }}
                        </div>
                        <div class="col-md-3">
                            <strong>Số buổi học:</strong> 
                            <span class="badge bg-info">{{ $lopHocPhan->monHoc->so_buoi_hoc ?? 15 }} buổi</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Thời gian:</strong> 
                            {{ \Carbon\Carbon::parse($lopHocPhan->ngay_bat_dau)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($lopHocPhan->ngay_ket_thuc)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Tạo lịch học tự động</h5>
                    <p class="text-muted small mb-0">Hệ thống sẽ tự động tạo tất cả các buổi học theo pattern bạn chọn</p>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh.store', $lopHocPhan) }}" method="POST" id="scheduleForm">
                        @csrf

                        <!-- Phần 1: Chọn Ca học và Thời gian -->
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i> <strong>Bước 1:</strong> Chọn ca học và pattern lặp lại
                        </div>

                        <div class="row">
                            <!-- Ca học -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ca_hoc_id">Ca học <span class="text-danger">*</span></label>
                                    <select class="form-select @error('ca_hoc_id') is-invalid @enderror"
                                        id="ca_hoc_id" name="ca_hoc_id" required>
                                        <option value="">-- Chọn ca học --</option>
                                        @foreach ($caHocs as $caHoc)
                                            <option value="{{ $caHoc->id }}" 
                                                data-gio-bat-dau="{{ $caHoc->gio_bat_dau }}"
                                                data-gio-ket-thuc="{{ $caHoc->gio_ket_thuc }}"
                                                {{ (int)old('ca_hoc_id', 0) === (int)$caHoc->id ? 'selected' : '' }}>
                                                {{ $caHoc->ten_ca }} ({{ date('H:i', strtotime($caHoc->gio_bat_dau)) }} - {{ date('H:i', strtotime($caHoc->gio_ket_thuc)) }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('ca_hoc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Số buổi học -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="so_buoi_hoc">Số buổi học <span class="text-danger">*</span></label>
                                    <input type="number" 
                                        class="form-control @error('so_buoi_hoc') is-invalid @enderror"
                                        id="so_buoi_hoc" 
                                        name="so_buoi_hoc" 
                                        value="{{ old('so_buoi_hoc', $lopHocPhan->monHoc->so_buoi_hoc ?? 15) }}"
                                        min="1" 
                                        max="50" 
                                        required>
                                    @error('so_buoi_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Mặc định: {{ $lopHocPhan->monHoc->so_buoi_hoc ?? 15 }} buổi
                                        <span id="max-sessions-hint" class="text-info d-none">
                                            | <i class="bi bi-info-circle"></i> Tối đa có thể tạo: <strong id="max-sessions-count">0</strong> buổi
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pattern lặp lại -->
                        <div class="row">
                            <!-- Ngày bắt đầu -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ngay_bat_dau_lich">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" 
                                        class="form-control @error('ngay_bat_dau_lich') is-invalid @enderror"
                                        id="ngay_bat_dau_lich" 
                                        name="ngay_bat_dau_lich" 
                                        value="{{ old('ngay_bat_dau_lich', $lopHocPhan->ngay_bat_dau) }}"
                                        min="{{ $lopHocPhan->ngay_bat_dau }}"
                                        max="{{ $lopHocPhan->ngay_ket_thuc }}"
                                        required>
                                    @error('ngay_bat_dau_lich')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Các thứ trong tuần -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Các thứ học trong tuần <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="2" id="thu2">
                                            <label class="form-check-label" for="thu2">Thứ 2</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="3" id="thu3">
                                            <label class="form-check-label" for="thu3">Thứ 3</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="4" id="thu4">
                                            <label class="form-check-label" for="thu4">Thứ 4</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="5" id="thu5">
                                            <label class="form-check-label" for="thu5">Thứ 5</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="6" id="thu6">
                                            <label class="form-check-label" for="thu6">Thứ 6</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="7" id="thu7">
                                            <label class="form-check-label" for="thu7">Thứ 7</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input thu-checkbox" type="checkbox" name="thu_trong_tuan[]" value="8" id="thu8">
                                            <label class="form-check-label" for="thu8">CN</label>
                                        </div>
                                    </div>
                                    @error('thu_trong_tuan')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle"></i> 
                                        Chọn các thứ bạn muốn xếp lịch. Ví dụ: chọn Thứ 2 và Thứ 4 → lịch sẽ lặp theo pattern T2-T4-T2-T4...
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Phần 2: Thông tin cố định -->
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i> <strong>Bước 2:</strong> Chọn phòng học, giảng viên và hình thức (áp dụng cho tất cả buổi)
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
                                                {{ (int)old('phong_hoc_id', 0) === (int)$phongHoc->id ? 'selected' : '' }}>
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
                                                {{ (int)old('giang_vien_id', $giangVienChinhId ?? 0) === (int)$giangVien->id ? 'selected' : '' }}>
                                                {{ $giangVien->ho_ten }}
                                                @if (isset($giangVienChinhId) && $giangVien->id == $giangVienChinhId)
                                                    (Giảng viên chính)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('giang_vien_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if (isset($giangVienChinhId))
                                        <small class="form-text text-muted">
                                            <i class="bi bi-info-circle"></i> Đã tự động chọn giảng viên chính từ phân công. Bạn có thể thay đổi nếu cần.
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hinh_thuc">Hình thức <span class="text-danger">*</span></label>
                                    <select class="form-select @error('hinh_thuc') is-invalid @enderror" id="hinh_thuc"
                                        name="hinh_thuc" required>
                                        <option value="offline" {{ old('hinh_thuc', 'offline') == 'offline' ? 'selected' : '' }}>
                                            Offline</option>
                                        <option value="online" {{ old('hinh_thuc') == 'online' ? 'selected' : '' }}>Online
                                        </option>
                                        <option value="hybrid" {{ old('hinh_thuc') == 'hybrid' ? 'selected' : '' }}>Hybrid
                                        </option>
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
                                        id="link_online" name="link_online" value="{{ old('link_online') }}"
                                        placeholder="https://meet.google.com/...">
                                    @error('link_online')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ghi_chu">Ghi chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="2">{{ old('ghi_chu') }}</textarea>
                        </div>

                        <hr class="my-4">

                        <!-- Preview lịch -->
                        <div id="preview-section" class="d-none">
                            <div class="alert alert-success">
                                <i class="bi bi-calendar-check"></i> <strong>Preview:</strong> 
                                Hệ thống sẽ tạo <span id="preview-count" class="badge bg-success">0</span> buổi học
                            </div>
                            <div id="preview-list" class="small text-muted"></div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-plus"></i> Tạo lịch học tự động
                            </button>
                            <button type="button" id="btn-preview" class="btn btn-info btn-lg">
                                <i class="bi bi-eye"></i> Xem trước
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh', $lopHocPhan) }}"
                                class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const btnPreview = document.getElementById('btn-preview');
    const previewSection = document.getElementById('preview-section');
    const previewCount = document.getElementById('preview-count');
    const previewList = document.getElementById('preview-list');
    const maxSessionsHint = document.getElementById('max-sessions-hint');
    const maxSessionsCount = document.getElementById('max-sessions-count');
    
    // Lấy ngày kết thúc của lớp học phần từ server
    const ngayKetThucLop = '{{ $lopHocPhan->ngay_ket_thuc }}';
    
    // Hàm tính số buổi tối đa có thể tạo
    function calculateMaxSessions() {
        const ngayBatDau = document.getElementById('ngay_bat_dau_lich').value;
        const thuCheckboxes = document.querySelectorAll('.thu-checkbox:checked');
        const thuList = Array.from(thuCheckboxes).map(cb => parseInt(cb.value));
        
        if (!ngayBatDau || thuList.length === 0 || !ngayKetThucLop) {
            maxSessionsHint.classList.add('d-none');
            return;
        }
        
        // Tính toán số buổi tối đa
        const startDate = new Date(ngayBatDau);
        const endDate = new Date(ngayKetThucLop);
        let count = 0;
        let currentDate = new Date(startDate);
        
        while (currentDate <= endDate && count < 100) { // Giới hạn 100 để tránh vòng lặp vô hạn
            const dayOfWeek = currentDate.getDay();
            const thuTrongTuan = dayOfWeek === 0 ? 8 : dayOfWeek + 1;
            
            if (thuList.includes(thuTrongTuan)) {
                count++;
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        // Hiển thị kết quả
        if (count > 0) {
            maxSessionsCount.textContent = count;
            maxSessionsHint.classList.remove('d-none');
            
            // Cảnh báo nếu số buổi nhập vào lớn hơn số buổi tối đa
            const soBuoiHoc = parseInt(document.getElementById('so_buoi_hoc').value);
            if (soBuoiHoc > count) {
                maxSessionsHint.classList.remove('text-info');
                maxSessionsHint.classList.add('text-warning');
            } else {
                maxSessionsHint.classList.remove('text-warning');
                maxSessionsHint.classList.add('text-info');
            }
        } else {
            maxSessionsHint.classList.add('d-none');
        }
    }
    
    // Lắng nghe sự kiện thay đổi
    document.getElementById('ngay_bat_dau_lich').addEventListener('change', calculateMaxSessions);
    document.querySelectorAll('.thu-checkbox').forEach(cb => {
        cb.addEventListener('change', calculateMaxSessions);
    });
    
    // Tính toán ban đầu nếu đã có giá trị
    if (document.getElementById('ngay_bat_dau_lich').value) {
        calculateMaxSessions();
    }
    
    // Hiển thị link online khi chọn online/hybrid
    document.getElementById('hinh_thuc').addEventListener('change', function() {
        const linkOnlineGroup = document.getElementById('link_online').closest('.form-group');
        if (this.value === 'online' || this.value === 'hybrid') {
            linkOnlineGroup.style.display = 'block';
        } else {
            linkOnlineGroup.style.display = 'block'; // Vẫn hiển thị nhưng không bắt buộc
        }
    });

    // Preview lịch học
    btnPreview.addEventListener('click', function() {
        // Lấy dữ liệu từ form
        const ngayBatDau = document.getElementById('ngay_bat_dau_lich').value;
        const soBuoiHoc = parseInt(document.getElementById('so_buoi_hoc').value);
        const caHocSelect = document.getElementById('ca_hoc_id');
        const caHocText = caHocSelect.options[caHocSelect.selectedIndex]?.text || '';
        
        // Lấy các thứ được chọn
        const thuCheckboxes = document.querySelectorAll('.thu-checkbox:checked');
        const thuList = Array.from(thuCheckboxes).map(cb => parseInt(cb.value)).sort();
        
        if (!ngayBatDau || !soBuoiHoc || thuList.length === 0 || !caHocText) {
            alert('Vui lòng điền đầy đủ thông tin: Ngày bắt đầu, Số buổi học, Ca học và Các thứ trong tuần');
            return;
        }

        // Tính toán các ngày học
        const ngayHocList = [];
        let currentDate = new Date(ngayBatDau);
        const endDate = new Date(ngayKetThucLop);
        let iterations = 0;
        const maxIterations = 365;
        
        while (ngayHocList.length < soBuoiHoc && iterations < maxIterations) {
            // Kiểm tra nếu vượt quá ngày kết thúc
            if (currentDate > endDate) {
                break;
            }
            
            // Lấy thứ hiện tại (JavaScript: 0=CN, 1=T2, ..., 6=T7)
            const dayOfWeek = currentDate.getDay();
            // Chuyển đổi: JavaScript -> Hệ thống của ta (2=T2, 3=T3, ..., 7=T7, 8=CN)
            const thuTrongTuan = dayOfWeek === 0 ? 8 : dayOfWeek + 1;
            
            // Kiểm tra nếu thứ hiện tại nằm trong danh sách được chọn
            if (thuList.includes(thuTrongTuan)) {
                ngayHocList.push({
                    ngay: new Date(currentDate),
                    thu: thuTrongTuan
                });
            }
            
            // Chuyển sang ngày tiếp theo
            currentDate.setDate(currentDate.getDate() + 1);
            iterations++;
        }
        
        // Hiển thị preview với cảnh báo nếu không đủ
        previewCount.textContent = ngayHocList.length;
        
        const thuNames = {2: 'T2', 3: 'T3', 4: 'T4', 5: 'T5', 6: 'T6', 7: 'T7', 8: 'CN'};
        let previewHTML = '';
        
        if (ngayHocList.length < soBuoiHoc) {
            previewHTML = `<div class="alert alert-warning mb-2">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Cảnh báo:</strong> Chỉ có thể tạo được ${ngayHocList.length} buổi trong khoảng thời gian của lớp học phần (yêu cầu: ${soBuoiHoc} buổi).
            </div>`;
        }
        
        previewHTML += ngayHocList.map((item, index) => {
            const dateStr = item.ngay.toLocaleDateString('vi-VN');
            return `<span class="badge bg-light text-dark me-2 mb-2">Buổi ${index + 1}: ${thuNames[item.thu]} ${dateStr} - ${caHocText}</span>`;
        }).join('');
        
        previewList.innerHTML = previewHTML;
        previewSection.classList.remove('d-none');
        
        // Scroll to preview
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});
</script>
@endpush
