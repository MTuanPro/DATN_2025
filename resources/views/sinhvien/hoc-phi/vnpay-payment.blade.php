@extends('layouts.layout-sinhvien')

@section('title', 'Thanh toán học phí qua VNPay')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thanh toán học phí qua VNPay</h3>
                    <p class="text-subtitle text-muted">Thanh toán học phí trực tuyến an toàn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}">Chi tiết</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán VNPay</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Payment Info -->
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Thông tin thanh toán</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Học kỳ:</strong></p>
                                        <p>{{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tổng học phí:</strong></p>
                                        <p class="text-primary h5">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Đã đóng:</strong></p>
                                        <p class="text-success">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Còn lại:</strong></p>
                                        <p class="text-danger h5">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</p>
                                    </div>
                                </div>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            <!-- Payment Form -->
                            <form action="{{ route('sinh-vien.hoc-phi.vnpay-initiate', $hocPhi->id) }}" method="POST" id="paymentForm">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="so_tien_dong" class="form-label">
                                        Số tiền thanh toán <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input 
                                            type="number" 
                                            class="form-control @error('so_tien_dong') is-invalid @enderror" 
                                            id="so_tien_dong" 
                                            name="so_tien_dong" 
                                            value="{{ old('so_tien_dong', $hocPhi->so_tien_con_lai) }}"
                                            min="1000" 
                                            max="{{ $hocPhi->so_tien_con_lai }}"
                                            step="1000"
                                            required
                                        >
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    @error('so_tien_dong')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Số tiền tối thiểu: 1,000 đ. Số tiền tối đa: {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ
                                    </small>
                                </div>

                                <!-- Quick Amount Buttons -->
                                <div class="mb-3">
                                    <label class="form-label">Chọn nhanh:</label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="setAmount({{ $hocPhi->so_tien_con_lai }})">
                                            Toàn bộ ({{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ)
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="setAmount({{ round($hocPhi->so_tien_con_lai / 2, -3) }})">
                                            Một nửa ({{ number_format(round($hocPhi->so_tien_con_lai / 2, -3), 0, ',', '.') }} đ)
                                        </button>
                                    </div>
                                </div>

                                <!-- Payment Instructions -->
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Lưu ý:</h6>
                                    <ul class="mb-0">
                                        <li>Bạn sẽ được chuyển đến trang thanh toán của VNPay</li>
                                        <li>Vui lòng không đóng trình duyệt trong quá trình thanh toán</li>
                                        <li>Sau khi thanh toán thành công, bạn sẽ được chuyển về trang này</li>
                                        <li>Giao dịch sẽ được xử lý tự động sau khi thanh toán</li>
                                    </ul>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-credit-card"></i> Thanh toán qua VNPay
                                    </button>
                                    <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>

                            <!-- VNPay Info -->
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check"></i> Thanh toán được bảo mật bởi VNPay
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function setAmount(amount) {
            document.getElementById('so_tien_dong').value = amount;
        }

        // Format number on input
        document.getElementById('so_tien_dong').addEventListener('input', function(e) {
            let value = parseInt(e.target.value);
            if (value > {{ $hocPhi->so_tien_con_lai }}) {
                e.target.value = {{ $hocPhi->so_tien_con_lai }};
            }
            if (value < 1000 && value > 0) {
                e.target.value = 1000;
            }
        });
    </script>
@endsection

