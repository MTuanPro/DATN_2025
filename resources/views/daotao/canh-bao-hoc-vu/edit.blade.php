@extends('layouts.layout-daotao')

@section('title', 'Sửa Cảnh Báo Học Vụ')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Sửa Cảnh báo Học vụ #{{ $canhBao->id }}</h3>
                <p class="text-subtitle text-muted">Chỉnh sửa thông tin cảnh báo học vụ</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}">Cảnh báo Học vụ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.canh-bao-hoc-vu.show', $canhBao) }}">Chi tiết</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa</li>
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

        <div class="row">
            <!-- Form chỉnh sửa -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin cảnh báo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dao-tao.canh-bao-hoc-vu.update', $canhBao) }}" method="POST" id="editCanhBaoForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Sinh viên (readonly) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Sinh viên</label>
                                        <input type="text" class="form-control" value="{{ $canhBao->sinhVien->ma_sinh_vien }} - {{ $canhBao->sinhVien->ho_ten }}" readonly>
                                        <small class="form-text text-muted">Không thể thay đổi sinh viên</small>
                                    </div>
                                </div>

                                <!-- Học kỳ -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hoc_ky_id" class="form-label required">Học kỳ</label>
                                        <select name="hoc_ky_id" id="hoc_ky_id" class="form-select @error('hoc_ky_id') is-invalid @enderror" required>
                                            @foreach($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ old('hoc_ky_id', $canhBao->hoc_ky_id) == $hk->id ? 'selected' : '' }}>
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
                                            <option value="diem_thap" {{ old('loai_canh_bao', $canhBao->loai_canh_bao) == 'diem_thap' ? 'selected' : '' }}>Điểm trung bình thấp</option>
                                            <option value="vang_nhieu" {{ old('loai_canh_bao', $canhBao->loai_canh_bao) == 'vang_nhieu' ? 'selected' : '' }}>Vắng học nhiều</option>
                                            <option value="no_hoc_phi" {{ old('loai_canh_bao', $canhBao->loai_canh_bao) == 'no_hoc_phi' ? 'selected' : '' }}>Nợ học phí</option>
                                            <option value="hoc_ky_lien_tiep" {{ old('loai_canh_bao', $canhBao->loai_canh_bao) == 'hoc_ky_lien_tiep' ? 'selected' : '' }}>Học kỳ liên tiếp không đạt</option>
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
                                            <option value="canh_cao" {{ old('muc_do', $canhBao->muc_do) == 'canh_cao' ? 'selected' : '' }}>Cảnh cáo</option>
                                            <option value="dinh_chi" {{ old('muc_do', $canhBao->muc_do) == 'dinh_chi' ? 'selected' : '' }}>Đình chỉ học tập</option>
                                            <option value="buoc_thoi_hoc" {{ old('muc_do', $canhBao->muc_do) == 'buoc_thoi_hoc' ? 'selected' : '' }}>Buộc thôi học</option>
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
                                <textarea name="ly_do" id="ly_do" rows="3" class="form-control @error('ly_do') is-invalid @enderror" required>{{ old('ly_do', $canhBao->ly_do) }}</textarea>
                                @error('ly_do')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ghi chú -->
                            <div class="form-group">
                                <label for="ghi_chu" class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" id="ghi_chu" rows="2" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu', $canhBao->ghi_chu) }}</textarea>
                                @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Trạng thái -->
                            <div class="form-group">
                                <label for="trang_thai" class="form-label required">Trạng thái</label>
                                <select name="trang_thai" id="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                                    <option value="chua_xu_ly" {{ old('trang_thai', $canhBao->trang_thai) == 'chua_xu_ly' ? 'selected' : '' }}>Chưa xử lý</option>
                                    <option value="dang_xu_ly" {{ old('trang_thai', $canhBao->trang_thai) == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="da_xu_ly" {{ old('trang_thai', $canhBao->trang_thai) == 'da_xu_ly' ? 'selected' : '' }}>Đã xử lý</option>
                                </select>
                                @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kết quả xử lý (chỉ hiển thị nếu trạng thái là đang/đã xử lý) -->
                            <div class="form-group" id="ketQuaXuLyGroup" style="{{ in_array(old('trang_thai', $canhBao->trang_thai), ['dang_xu_ly', 'da_xu_ly']) ? '' : 'display:none;' }}">
                                <label for="ket_qua_xu_ly" class="form-label">Kết quả xử lý</label>
                                <textarea name="ket_qua_xu_ly" id="ket_qua_xu_ly" rows="3" class="form-control @error('ket_qua_xu_ly') is-invalid @enderror">{{ old('ket_qua_xu_ly', $canhBao->ket_qua_xu_ly) }}</textarea>
                                @error('ket_qua_xu_ly')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Mô tả chi tiết các biện pháp xử lý và kết quả</small>
                            </div>

                            <!-- Ngày cảnh báo -->
                            <div class="form-group">
                                <label for="ngay_canh_bao" class="form-label required">Ngày cảnh báo</label>
                                <input type="datetime-local" name="ngay_canh_bao" id="ngay_canh_bao" 
                                       class="form-control @error('ngay_canh_bao') is-invalid @enderror" 
                                       value="{{ old('ngay_canh_bao', $canhBao->ngay_canh_bao->format('Y-m-d\TH:i')) }}" required>
                                @error('ngay_canh_bao')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gửi email -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="gui_email" name="gui_email" value="1" {{ old('gui_email') ? 'checked' : '' }}>
                                <label class="form-check-label" for="gui_email">
                                    Gửi email thông báo cập nhật cho sinh viên
                                </label>
                            </div>

                            <hr class="my-4">

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('dao-tao.canh-bao-hoc-vu.show', $canhBao) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Thông tin bổ sung -->
            <div class="col-lg-4">
                <!-- Thông tin sinh viên -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-person"></i> Sinh viên
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $canhBao->sinhVien->ho_ten }}</strong></p>
                        <p class="mb-1 text-muted">{{ $canhBao->sinhVien->ma_sinh_vien }}</p>
                        <p class="mb-1">Lớp: {{ $canhBao->sinhVien->lop_hanh_chinh }}</p>
                        <p class="mb-0">Email: {{ $canhBao->sinhVien->email }}</p>
                    </div>
                </div>

                <!-- Thông tin cảnh báo hiện tại -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Thông tin hiện tại
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th width="45%">Mã CB:</th>
                                    <td><code>#{{ $canhBao->id }}</code></td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        @php
                                            $ttText = match($canhBao->trang_thai) {
                                                'chua_xu_ly' => 'Chưa xử lý',
                                                'dang_xu_ly' => 'Đang xử lý',
                                                'da_xu_ly' => 'Đã xử lý',
                                                default => $canhBao->trang_thai
                                            };
                                        @endphp
                                        {{ $ttText }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo:</th>
                                    <td>{{ $canhBao->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Người tạo:</th>
                                    <td>{{ $canhBao->nguoiTao->name ?? 'Hệ thống' }}</td>
                                </tr>
                                @if($canhBao->updated_at != $canhBao->created_at)
                                <tr>
                                    <th>Cập nhật lần cuối:</th>
                                    <td>{{ $canhBao->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card mt-3 border-warning">
                    <div class="card-header bg-warning">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightbulb"></i> Lưu ý
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Không thể thay đổi sinh viên được cảnh báo</li>
                            <li>Khi thay đổi mức độ từ <strong>Cảnh cáo</strong> lên <strong>Đình chỉ</strong> hoặc <strong>Buộc thôi học</strong>, cần ghi rõ lý do</li>
                            <li>Nếu chọn "Đã xử lý", bắt buộc phải ghi kết quả xử lý</li>
                            <li>Khi gửi email, sinh viên sẽ nhận được thông báo cập nhật mới nhất</li>
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
    const trangThaiSelect = document.getElementById('trang_thai');
    const ketQuaXuLyGroup = document.getElementById('ketQuaXuLyGroup');
    const ketQuaXuLyTextarea = document.getElementById('ket_qua_xu_ly');

    // Toggle kết quả xử lý field based on trạng thái
    trangThaiSelect.addEventListener('change', function() {
        if (this.value === 'dang_xu_ly' || this.value === 'da_xu_ly') {
            ketQuaXuLyGroup.style.display = 'block';
            if (this.value === 'da_xu_ly') {
                ketQuaXuLyTextarea.required = true;
            } else {
                ketQuaXuLyTextarea.required = false;
            }
        } else {
            ketQuaXuLyGroup.style.display = 'none';
            ketQuaXuLyTextarea.required = false;
        }
    });

    // Form validation
    const form = document.getElementById('editCanhBaoForm');
    form.addEventListener('submit', function(e) {
        const trangThai = trangThaiSelect.value;
        const ketQuaXuLy = ketQuaXuLyTextarea.value.trim();

        if (trangThai === 'da_xu_ly' && !ketQuaXuLy) {
            e.preventDefault();
            alert('Vui lòng nhập kết quả xử lý khi đánh dấu "Đã xử lý"');
            ketQuaXuLyTextarea.focus();
            return false;
        }

        const lyDo = document.getElementById('ly_do').value.trim();
        if (lyDo.length < 20) {
            e.preventDefault();
            alert('Lý do cảnh báo phải có ít nhất 20 ký tự');
            document.getElementById('ly_do').focus();
            return false;
        }

        // Confirm before submit
        if (!confirm('Xác nhận cập nhật thông tin cảnh báo?')) {
            e.preventDefault();
            return false;
        }
    });

    // Warn when changing mức độ to higher severity
    const mucDoSelect = document.getElementById('muc_do');
    const originalMucDo = '{{ $canhBao->muc_do }}';
    
    mucDoSelect.addEventListener('change', function() {
        const severityOrder = ['canh_cao', 'dinh_chi', 'buoc_thoi_hoc'];
        const oldIndex = severityOrder.indexOf(originalMucDo);
        const newIndex = severityOrder.indexOf(this.value);
        
        if (newIndex > oldIndex) {
            alert('⚠️ Lưu ý: Bạn đang nâng mức độ cảnh báo lên cao hơn.\n\nVui lòng đảm bảo có lý do rõ ràng trong phần "Lý do cảnh báo".');
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
