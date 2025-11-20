@extends('layouts.layout-sinhvien')

@section('title', 'Thanh toán học phí qua MoMo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thanh toán học phí qua MoMo</h3>
                    <p class="text-subtitle text-muted">Thanh toán nhanh chóng và an toàn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}">Chi tiết</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán MoMo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-wallet2"></i> Thanh toán qua MoMo Wallet
                            </h4>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <h5><i class="bi bi-info-circle"></i> Thông tin sinh viên</h5>
                                <p class="mb-1"><strong>MSSV:</strong> {{ $hocPhi->sinhVien->ma_sinh_vien }}</p>
                                <p class="mb-1"><strong>Họ tên:</strong> {{ $hocPhi->sinhVien->ho_ten }}</p>
                                <p class="mb-0"><strong>Học kỳ:</strong> {{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</p>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mô tả</th>
                                            <th class="text-end">Số tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Tổng học phí</td>
                                            <td class="text-end"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Đã thanh toán</td>
                                            <td class="text-end text-success">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td><strong>Còn lại</strong></td>
                                            <td class="text-end"><strong class="text-danger">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <form action="{{ route('sinh-vien.hoc-phi.momo-initiate', $hocPhi->id) }}" method="POST" id="paymentForm">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>Số tiền thanh toán <span class="text-danger">*</span></strong>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="so_tien_dong" 
                                               id="so_tien_dong" 
                                               class="form-control @error('so_tien_dong') is-invalid @enderror" 
                                               value="{{ old('so_tien_dong', $hocPhi->so_tien_con_lai) }}" 
                                               required 
                                               min="1000" 
                                               step="1000" 
                                               max="{{ $hocPhi->so_tien_con_lai }}"
                                               placeholder="Nhập số tiền thanh toán">
                                        <span class="input-group-text">VND</span>
                                    </div>
                                    @error('so_tien_dong')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Số tiền tối thiểu: 1,000 đ | Số tiền tối đa: {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ
                                    </small>
                                    <small id="so_tien_error" class="text-danger d-none"></small>
                                </div>

                                <div class="mb-3">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="setAmount({{ $hocPhi->so_tien_con_lai }})">
                                            Thanh toán toàn bộ
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="setAmount({{ min(1000000, $hocPhi->so_tien_con_lai) }})">
                                            1,000,000 đ
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="setAmount({{ min(500000, $hocPhi->so_tien_con_lai) }})">
                                            500,000 đ
                                        </button>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-exclamation-triangle"></i> Lưu ý:</h6>
                                    <ul class="mb-0">
                                        <li>Bạn sẽ được chuyển đến trang thanh toán của MoMo</li>
                                        <li>Vui lòng không đóng trình duyệt trong quá trình thanh toán</li>
                                        <li>Sau khi thanh toán thành công, bạn sẽ được chuyển về trang này</li>
                                        <li>Nếu có vấn đề, vui lòng liên hệ bộ phận tài vụ</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="bi bi-wallet2"></i> Thanh toán qua MoMo
                                    </button>
                                    <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-shield-check"></i> Bảo mật</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Thanh toán được bảo mật bởi MoMo
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Không lưu trữ thông tin thẻ
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Giao dịch được mã hóa SSL
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle text-success"></i> 
                                    Hỗ trợ 24/7
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-question-circle"></i> Hỗ trợ</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Hotline:</strong> 1900-xxxx</p>
                            <p class="mb-2"><strong>Email:</strong> support@university.edu.vn</p>
                            <p class="mb-0"><strong>Thời gian:</strong> 8:00 - 17:00 (T2-T6)</p>
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
        const soTienDongInput = document.getElementById('so_tien_dong');
        const soTienError = document.getElementById('so_tien_error');
        const soTienConLai = {{ $hocPhi->so_tien_con_lai }};
        const submitBtn = document.getElementById('submitBtn');
        const paymentForm = document.getElementById('paymentForm');

        // Validate số tiền không vượt quá số tiền còn lại
        if (soTienDongInput) {
            soTienDongInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                
                if (value < 1000) {
                    soTienError.textContent = 'Số tiền tối thiểu là 1,000 đ';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else if (value > soTienConLai) {
                    soTienError.textContent = 'Số tiền không được vượt quá số tiền còn lại';
                    soTienError.classList.remove('d-none');
                    this.classList.add('is-invalid');
                    submitBtn.disabled = true;
                } else {
                    soTienError.classList.add('d-none');
                    this.classList.remove('is-invalid');
                    submitBtn.disabled = false;
                }
            });

            // Check on page load
            soTienDongInput.dispatchEvent(new Event('input'));
        }

        // Prevent double submission
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            });
        }
    });

    function setAmount(amount) {
        const input = document.getElementById('so_tien_dong');
        if (input) {
            input.value = amount;
            input.dispatchEvent(new Event('input'));
        }
    }
</script>
@endpush

