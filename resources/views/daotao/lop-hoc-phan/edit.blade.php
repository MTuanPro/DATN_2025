@extends('layouts.layout-daotao')

@section('title', 'Sửa Lớp học phần')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Sửa Lớp học phần</h3>
                <p class="text-subtitle text-muted">Chỉnh sửa thông tin lớp học phần</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sửa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thông tin Lớp học phần</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.lop-hoc-phan.update', $lopHocPhan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Mã lớp học phần -->
                        <div class="col-md-6 mb-3">
                            <label for="ma_lop_hp" class="form-label">Mã lớp học phần <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ma_lop_hp') is-invalid @enderror" 
                                   id="ma_lop_hp" name="ma_lop_hp" value="{{ old('ma_lop_hp', $lopHocPhan->ma_lop_hp) }}" 
                                   placeholder="VD: CNTT101.01">
                            @error('ma_lop_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mã lớp học phần phải duy nhất</small>
                        </div>

                        <!-- Tên lớp học phần -->
                        <div class="col-md-6 mb-3">
                            <label for="ten_lop_hp" class="form-label">Tên lớp học phần <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_lop_hp') is-invalid @enderror" 
                                   id="ten_lop_hp" name="ten_lop_hp" value="{{ old('ten_lop_hp', $lopHocPhan->ten_lop_hp) }}" 
                                   placeholder="VD: Lập trình web - Nhóm 1">
                            @error('ten_lop_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Môn học -->
                        <div class="col-md-6 mb-3">
                            <label for="mon_hoc_id" class="form-label">Môn học <span class="text-danger">*</span></label>
                            <select class="form-select @error('mon_hoc_id') is-invalid @enderror" 
                                    id="mon_hoc_id" name="mon_hoc_id">
                                <option value="">-- Chọn môn học --</option>
                                @foreach($monHocs as $monHoc)
                                    <option value="{{ $monHoc->id }}" {{ old('mon_hoc_id', $lopHocPhan->mon_hoc_id) == $monHoc->id ? 'selected' : '' }}>
                                        {{ $monHoc->ma_mon }} - {{ $monHoc->ten_mon }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mon_hoc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Học kỳ -->
                        <div class="col-md-6 mb-3">
                            <label for="hoc_ky_id" class="form-label">Học kỳ <span class="text-danger">*</span></label>
                            <select class="form-select @error('hoc_ky_id') is-invalid @enderror" 
                                    id="hoc_ky_id" name="hoc_ky_id">
                                <option value="">-- Chọn học kỳ --</option>
                                @foreach($hocKys as $hocKy)
                                    <option value="{{ $hocKy->id }}" {{ old('hoc_ky_id', $lopHocPhan->hoc_ky_id) == $hocKy->id ? 'selected' : '' }}>
                                        {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hoc_ky_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nhóm lớp -->
                        <div class="col-md-3 mb-3">
                            <label for="nhom_lop" class="form-label">Nhóm lớp</label>
                            <input type="number" class="form-control @error('nhom_lop') is-invalid @enderror" 
                                   id="nhom_lop" name="nhom_lop" value="{{ old('nhom_lop', $lopHocPhan->nhom_lop) }}" min="1">
                            @error('nhom_lop')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sức chứa -->
                        <div class="col-md-3 mb-3">
                            <label for="suc_chua" class="form-label">Sức chứa (SV tối đa) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('suc_chua') is-invalid @enderror" 
                                   id="suc_chua" name="suc_chua" value="{{ old('suc_chua', $lopHocPhan->suc_chua) }}" 
                                   min="10" max="100">
                            @error('suc_chua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Số lượng tối thiểu -->
                        <div class="col-md-3 mb-3">
                            <label for="so_luong_toi_thieu" class="form-label">SV tối thiểu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('so_luong_toi_thieu') is-invalid @enderror" 
                                   id="so_luong_toi_thieu" name="so_luong_toi_thieu" value="{{ old('so_luong_toi_thieu', $lopHocPhan->so_luong_toi_thieu) }}" 
                                   min="5" max="30">
                            @error('so_luong_toi_thieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hình thức học -->
                        <div class="col-md-3 mb-3">
                            <label for="hinh_thuc" class="form-label">Hình thức học <span class="text-danger">*</span></label>
                            <select class="form-select @error('hinh_thuc') is-invalid @enderror" 
                                    id="hinh_thuc" name="hinh_thuc">
                                <option value="offline" {{ old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'offline' ? 'selected' : '' }}>Offline</option>
                                <option value="online" {{ old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="hybrid" {{ old('hinh_thuc', $lopHocPhan->hinh_thuc) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                            @error('hinh_thuc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Link online (hiện khi chọn online/hybrid) -->
                    <div class="row" id="link_online_group" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label for="link_online" class="form-label">Link học online</label>
                            <input type="url" class="form-control @error('link_online') is-invalid @enderror" 
                                   id="link_online" name="link_online" value="{{ old('link_online', $lopHocPhan->link_online) }}" 
                                   placeholder="https://meet.google.com/...">
                            @error('link_online')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ngày bắt đầu -->
                        <div class="col-md-6 mb-3">
                            <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                                   id="ngay_bat_dau" name="ngay_bat_dau" value="{{ old('ngay_bat_dau', $lopHocPhan->ngay_bat_dau ? $lopHocPhan->ngay_bat_dau->format('Y-m-d') : '') }}">
                            @error('ngay_bat_dau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ngày kết thúc -->
                        <div class="col-md-6 mb-3">
                            <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                                   id="ngay_ket_thuc" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc', $lopHocPhan->ngay_ket_thuc ? $lopHocPhan->ngay_ket_thuc->format('Y-m-d') : '') }}">
                            @error('ngay_ket_thuc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="trang_thai_lop" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('trang_thai_lop') is-invalid @enderror" 
                                    id="trang_thai_lop" name="trang_thai_lop">
                                <option value="mo_dang_ky" {{ old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'mo_dang_ky' ? 'selected' : '' }}>Mở đăng ký</option>
                                <option value="dang_hoc" {{ old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                                <option value="ket_thuc" {{ old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'ket_thuc' ? 'selected' : '' }}>Kết thúc</option>
                                <option value="huy" {{ old('trang_thai_lop', $lopHocPhan->trang_thai_lop) == 'huy' ? 'selected' : '' }}>Hủy</option>
                            </select>
                            @error('trang_thai_lop')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Ghi chú -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('ghi_chu') is-invalid @enderror" 
                                      id="ghi_chu" name="ghi_chu" rows="3">{{ old('ghi_chu', $lopHocPhan->ghi_chu) }}</textarea>
                            @error('ghi_chu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// Hiện/ẩn link online
document.getElementById('hinh_thuc').addEventListener('change', function() {
    const linkGroup = document.getElementById('link_online_group');
    if (this.value === 'online' || this.value === 'hybrid') {
        linkGroup.style.display = 'block';
    } else {
        linkGroup.style.display = 'none';
    }
});

// Trigger on page load
if (document.getElementById('hinh_thuc').value === 'online' || document.getElementById('hinh_thuc').value === 'hybrid') {
    document.getElementById('link_online_group').style.display = 'block';
}
</script>
@endpush
@endsection
