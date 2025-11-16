@extends('layouts.layout-daotao')

@section('title', 'Ghi nhận thanh toán')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ghi nhận thanh toán</h3>
                    <p class="text-subtitle text-muted">Ghi nhận khoản thanh toán học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ghi nhận thanh toán</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('dao-tao.hoc-phi.storePayment', $hocPhi->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="alert alert-info">
                                    <h5>Thông tin sinh viên</h5>
                                    <p class="mb-0"><strong>MSSV:</strong> {{ $hocPhi->sinhVien->ma_sinh_vien }}</p>
                                    <p class="mb-0"><strong>Họ tên:</strong> {{ $hocPhi->sinhVien->ho_ten }}</p>
                                    <p class="mb-0"><strong>Học kỳ:</strong> {{ $hocPhi->hocKy->ten_hoc_ky }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                                    <input type="number" name="so_tien_dong" id="so_tien_dong" class="form-control @error('so_tien_dong') is-invalid @enderror" 
                                           value="{{ old('so_tien_dong') }}" required min="1000" step="1000" max="{{ $hocPhi->so_tien_con_lai }}"
                                           placeholder="Nhập số tiền thanh toán">
                                    @error('so_tien_dong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Số tiền còn lại: <strong class="text-danger">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</strong></small>
                                    <small id="so_tien_error" class="text-danger d-none"></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày đóng <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_dong" class="form-control @error('ngay_dong') is-invalid @enderror" 
                                           value="{{ old('ngay_dong', date('Y-m-d')) }}" required>
                                    @error('ngay_dong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                                    <select name="phuong_thuc_thanh_toan" id="phuong_thuc_thanh_toan" class="form-select @error('phuong_thuc_thanh_toan') is-invalid @enderror" required>
                                        <option value="">-- Chọn phương thức --</option>
                                        <option value="Tiền mặt" {{ old('phuong_thuc_thanh_toan') == 'Tiền mặt' ? 'selected' : '' }}>Tiền mặt</option>
                                        <option value="Chuyển khoản" {{ old('phuong_thuc_thanh_toan') == 'Chuyển khoản' ? 'selected' : '' }}>Chuyển khoản</option>
                                        <option value="Thẻ ATM" {{ old('phuong_thuc_thanh_toan') == 'Thẻ ATM' ? 'selected' : '' }}>Thẻ ATM</option>
                                    </select>
                                    @error('phuong_thuc_thanh_toan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="ngan_hang_field" style="display: none;">
                                    <label class="form-label">Ngân hàng</label>
                                    <input type="text" name="ngan_hang" class="form-control @error('ngan_hang') is-invalid @enderror" 
                                           value="{{ old('ngan_hang') }}" placeholder="Tên ngân hàng (nếu chuyển khoản)">
                                    @error('ngan_hang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú (nếu có)">{{ old('ghi_chu') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Biên lai thanh toán (nếu có)</label>
                                    <input type="file" name="bien_lai_file" class="form-control @error('bien_lai_file') is-invalid @enderror" 
                                           accept="image/*,.pdf">
                                    @error('bien_lai_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('dao-tao.hoc-phi.show', $hocPhi->id) }}" class="btn btn-secondary">Hủy</a>
                                    <button type="submit" class="btn btn-success">Ghi nhận thanh toán</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Tổng hợp học phí</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tổng học phí:</td>
                                    <td class="text-end"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr>
                                    <td>Đã đóng:</td>
                                    <td class="text-end text-success"><strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td>Còn lại:</td>
                                    <td class="text-end text-danger"><h4>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phuongThucSelect = document.getElementById('phuong_thuc_thanh_toan');
        const nganHangField = document.getElementById('ngan_hang_field');
        const soTienDongInput = document.getElementById('so_tien_dong');
        const soTienError = document.getElementById('so_tien_error');
        const soTienConLai = {{ $hocPhi->so_tien_con_lai }};
        
        // Hiển thị/ẩn trường ngân hàng
        if (phuongThucSelect && nganHangField) {
            phuongThucSelect.addEventListener('change', function() {
                if (this.value === 'Chuyển khoản') {
                    nganHangField.style.display = 'block';
                } else {
                    nganHangField.style.display = 'none';
                }
            });
            
            // Check on page load (for old input)
            if (phuongThucSelect.value === 'Chuyển khoản') {
                nganHangField.style.display = 'block';
            }
        }
        
        // Validate số tiền không vượt quá số tiền còn lại
        if (soTienDongInput && soTienError) {
            soTienDongInput.addEventListener('input', function() {
                const soTien = parseFloat(this.value) || 0;
                if (soTien > soTienConLai) {
                    soTienError.textContent = 'Số tiền đóng không được vượt quá số tiền còn lại (' + new Intl.NumberFormat('vi-VN').format(soTienConLai) + ' đ)';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                } else {
                    soTienError.classList.add('d-none');
                    this.classList.remove('is-invalid');
                }
            });
            
            // Validate khi submit form
            const form = soTienDongInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const soTien = parseFloat(soTienDongInput.value) || 0;
                    if (soTien > soTienConLai) {
                        e.preventDefault();
                        soTienError.textContent = 'Số tiền đóng không được vượt quá số tiền còn lại (' + new Intl.NumberFormat('vi-VN').format(soTienConLai) + ' đ)';
                        soTienError.classList.remove('d-none');
                        soTienDongInput.classList.add('is-invalid');
                        soTienDongInput.focus();
                        return false;
                    }
                });
            }
        }
    });
</script>
@endpush
