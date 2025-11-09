@extends('layouts.layout-daotao')

@section('title', 'Tạo Cảnh Báo Học Vụ')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tạo Cảnh báo Học vụ</h3>
                <p class="text-subtitle text-muted">Thêm mới cảnh báo học vụ cho sinh viên</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}">Cảnh báo Học vụ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tạo mới</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Có lỗi xảy ra!</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin cảnh báo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.canh-bao-hoc-vu.store') }}" method="POST" id="canhBaoForm">
                    @csrf

                    <div class="row">
                        <!-- Sinh viên -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sinh_vien_id" class="form-label required">Sinh viên</label>
                                <select name="sinh_vien_id" id="sinh_vien_id" class="form-select @error('sinh_vien_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn sinh viên --</option>
                                    @foreach($sinhViens as $sv)
                                    <option value="{{ $sv->id }}" {{ old('sinh_vien_id') == $sv->id ? 'selected' : '' }}>
                                        {{ $sv->ma_sinh_vien }} - {{ $sv->ho_ten }} ({{ $sv->lop_hanh_chinh }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('sinh_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Học kỳ -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hoc_ky_id" class="form-label required">Học kỳ</label>
                                <select name="hoc_ky_id" id="hoc_ky_id" class="form-select @error('hoc_ky_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn học kỳ --</option>
                                    @foreach($hocKys as $hk)
                                    <option value="{{ $hk->id }}" {{ old('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                        {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('hoc_ky_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Loại cảnh báo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loai_canh_bao" class="form-label required">Loại cảnh báo</label>
                                <select name="loai_canh_bao" id="loai_canh_bao" class="form-select @error('loai_canh_bao') is-invalid @enderror" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="diem_thap" {{ old('loai_canh_bao') == 'diem_thap' ? 'selected' : '' }}>Điểm trung bình thấp</option>
                                    <option value="vang_nhieu" {{ old('loai_canh_bao') == 'vang_nhieu' ? 'selected' : '' }}>Vắng học nhiều</option>
                                    <option value="no_hoc_phi" {{ old('loai_canh_bao') == 'no_hoc_phi' ? 'selected' : '' }}>Nợ học phí</option>
                                    <option value="hoc_ky_lien_tiep" {{ old('loai_canh_bao') == 'hoc_ky_lien_tiep' ? 'selected' : '' }}>Học kỳ liên tiếp không đạt</option>
                                </select>
                                @error('loai_canh_bao')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mức độ -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="muc_do" class="form-label required">Mức độ</label>
                                <select name="muc_do" id="muc_do" class="form-select @error('muc_do') is-invalid @enderror" required>
                                    <option value="">-- Chọn mức độ --</option>
                                    <option value="canh_cao" {{ old('muc_do') == 'canh_cao' ? 'selected' : '' }}>Cảnh cáo</option>
                                    <option value="dinh_chi" {{ old('muc_do') == 'dinh_chi' ? 'selected' : '' }}>Đình chỉ học tập</option>
                                    <option value="buoc_thoi_hoc" {{ old('muc_do') == 'buoc_thoi_hoc' ? 'selected' : '' }}>Buộc thôi học</option>
                                </select>
                                @error('muc_do')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Lý do -->
                    <div class="form-group">
                        <label for="ly_do" class="form-label required">Lý do cảnh báo</label>
                        <textarea name="ly_do" id="ly_do" rows="3" class="form-control @error('ly_do') is-invalid @enderror" required placeholder="Mô tả chi tiết lý do cảnh báo...">{{ old('ly_do') }}</textarea>
                        @error('ly_do')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Ví dụ: "GPA học kỳ 1 chỉ đạt 0.8/4.0, không đạt yêu cầu tối thiểu 1.0"</small>
                    </div>

                    <!-- Ghi chú -->
                    <div class="form-group">
                        <label for="ghi_chu" class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" id="ghi_chu" rows="2" class="form-control @error('ghi_chu') is-invalid @enderror" placeholder="Thông tin bổ sung (không bắt buộc)...">{{ old('ghi_chu') }}</textarea>
                        @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ngày cảnh báo -->
                    <div class="form-group">
                        <label for="ngay_canh_bao" class="form-label required">Ngày cảnh báo</label>
                        <input type="datetime-local" name="ngay_canh_bao" id="ngay_canh_bao" 
                               class="form-control @error('ngay_canh_bao') is-invalid @enderror" 
                               value="{{ old('ngay_canh_bao', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('ngay_canh_bao')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gửi email -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="gui_email" name="gui_email" value="1" {{ old('gui_email', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="gui_email">
                            Gửi email thông báo cho sinh viên
                        </label>
                    </div>

                    <hr class="my-4">

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <div>
                            <button type="reset" class="btn btn-warning me-2">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu cảnh báo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mẫu lý do -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightbulb"></i> Mẫu lý do cảnh báo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Điểm thấp</h6>
                        <ul class="small">
                            <li>GPA học kỳ [X] chỉ đạt [Y]/4.0, thấp hơn mức tối thiểu 1.0</li>
                            <li>GPA tích lũy đến học kỳ [X] chỉ đạt [Y]/4.0</li>
                            <li>Có [X] môn điểm F trong học kỳ [Y]</li>
                        </ul>

                        <h6 class="text-primary mt-3">Vắng nhiều</h6>
                        <ul class="small">
                            <li>Vắng [X]% buổi học trong học kỳ [Y], vượt quá mức cho phép 20%</li>
                            <li>Vắng không phép [X] buổi môn [Tên môn]</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Nợ học phí</h6>
                        <ul class="small">
                            <li>Nợ học phí học kỳ [X] số tiền [Y] VNĐ, quá hạn thanh toán từ [ngày]</li>
                            <li>Tổng nợ học phí tích lũy: [X] VNĐ</li>
                        </ul>

                        <h6 class="text-primary mt-3">Học kỳ liên tiếp</h6>
                        <ul class="small">
                            <li>Không đạt yêu cầu [X] học kỳ liên tiếp (HK[Y], HK[Z])</li>
                            <li>GPA dưới 1.0 trong [X] học kỳ liên tiếp</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill lý do based on loại cảnh báo
    const loaiCanhBao = document.getElementById('loai_canh_bao');
    const lyDoTextarea = document.getElementById('ly_do');
    
    loaiCanhBao.addEventListener('change', function() {
        if (lyDoTextarea.value.trim() !== '') return; // Don't overwrite existing text
        
        const templates = {
            'diem_thap': 'GPA học kỳ chỉ đạt [điền GPA]/4.0, thấp hơn mức tối thiểu 1.0',
            'vang_nhieu': 'Vắng [điền %]% buổi học trong học kỳ, vượt quá mức cho phép 20%',
            'no_hoc_phi': 'Nợ học phí học kỳ số tiền [điền số tiền] VNĐ, quá hạn thanh toán',
            'hoc_ky_lien_tiep': 'Không đạt yêu cầu [điền số] học kỳ liên tiếp với GPA < 1.0'
        };
        
        if (templates[this.value]) {
            lyDoTextarea.value = templates[this.value];
            lyDoTextarea.focus();
        }
    });

    // Form validation
    const form = document.getElementById('canhBaoForm');
    form.addEventListener('submit', function(e) {
        const sinhVienId = document.getElementById('sinh_vien_id').value;
        const hocKyId = document.getElementById('hoc_ky_id').value;
        const loai = document.getElementById('loai_canh_bao').value;
        const mucDo = document.getElementById('muc_do').value;
        const lyDo = document.getElementById('ly_do').value.trim();

        if (!sinhVienId || !hocKyId || !loai || !mucDo || !lyDo) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ các trường bắt buộc (đánh dấu *)');
            return false;
        }

        if (lyDo.length < 20) {
            e.preventDefault();
            alert('Lý do cảnh báo phải có ít nhất 20 ký tự để đảm bảo đầy đủ thông tin');
            lyDoTextarea.focus();
            return false;
        }

        // Confirm before submit
        const sinhVienText = document.querySelector('#sinh_vien_id option:checked').text;
        const mucDoText = document.querySelector('#muc_do option:checked').text;
        
        if (!confirm(`Xác nhận tạo cảnh báo "${mucDoText}" cho sinh viên:\n${sinhVienText}\n\nLý do: ${lyDo.substring(0, 100)}...`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush

<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endsection
