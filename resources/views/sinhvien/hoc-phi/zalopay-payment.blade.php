@if(isset($isAdminView) && $isAdminView)
    @extends('layouts.layout-daotao')
@else
    @extends('layouts.layout-sinhvien')
@endif

@section('title', 'Thanh toán học phí qua ZaloPay')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-credit-card text-primary"></i> Thanh toán học phí qua ZaloPay</h3>
                    <p class="text-subtitle text-muted">Thanh toán học phí trực tuyến an toàn và nhanh chóng</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            @if(isset($isAdminView) && $isAdminView)
                                <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.show', $hocPhi->id) }}">Chi tiết</a></li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}">Chi tiết</a></li>
                            @endif
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán ZaloPay</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Thông tin học phí -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin học phí</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Học kỳ:</strong></p>
                                    <p class="text-muted">{{ $hocPhi->hocKy->ten_hoc_ky }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Năm học:</strong></p>
                                    <p class="text-muted">{{ $hocPhi->hocKy->nam_hoc }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Tổng học phí:</strong></p>
                                    <h5 class="text-primary">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }}đ</h5>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Đã đóng:</strong></p>
                                    <h5 class="text-success">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }}đ</h5>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Còn lại:</strong></p>
                                    <h5 class="text-danger">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($orderUrl) && $orderUrl)
                        <!-- Hiển thị QR Code -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-qr-code me-2"></i>Quét mã QR để thanh toán
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                                    </div>
                                @endif
                                
                                <div class="mb-4">
                                    <p class="text-muted mb-3">Quét mã QR bằng ứng dụng ZaloPay hoặc ngân hàng để thanh toán</p>
                                    <div class="d-flex justify-content-center">
                                        <div class="border p-3 bg-white rounded shadow-sm" style="background-color: #ffffff !important;">
                                            @php
                                                // ✅ Sử dụng URL gốc từ ZaloPay, không decode hay modify
                                                // URL từ ZaloPay đã được encode đúng và cần giữ nguyên
                                                // URL gateway của ZaloPay có format đặc biệt: https://qcgateway.zalopay.vn/pay/v2/vietqr?order=...
                                                $qrCodeUrl = trim($orderUrl);
                                            @endphp
                                            {!! QrCode::size(300)
                                                ->errorCorrection('H')
                                                ->margin(2)
                                                ->format('svg')
                                                ->generate($qrCodeUrl) !!}
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> 
                                            URL: <code style="font-size: 0.75rem; word-break: break-all;">{{ $qrCodeUrl }}</code>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-exclamation-triangle"></i> 
                                            Nếu quét không thành công, vui lòng click vào nút "Mở trang thanh toán ZaloPay" bên dưới
                                        </small>
                                    </div>
                                    <p class="mt-3 mb-0">
                                        <small class="text-muted">Hoặc click vào nút bên dưới để mở trang thanh toán</small>
                                    </p>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Hướng dẫn:</strong>
                                    <ol class="text-start mt-2 mb-0">
                                        <li>Mở ứng dụng ZaloPay trên điện thoại</li>
                                        <li>Chọn "Quét mã" hoặc "Scan QR"</li>
                                        <li>Quét mã QR ở trên</li>
                                        <li>Xác nhận thanh toán trên ứng dụng</li>
                                    </ol>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ $orderUrl }}" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>
                                        Mở trang thanh toán ZaloPay
                                    </a>
                                    <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Quay lại
                                    </a>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> 
                                        Mã QR này có hiệu lực trong 15 phút. Vui lòng thanh toán sớm.
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Form thanh toán -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-wallet2 me-2"></i>Thông tin thanh toán
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($isAdminView) && $isAdminView)
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> 
                                        <strong>Chế độ xem:</strong> Bạn đang xem trang thanh toán ZaloPay ở chế độ chỉ đọc. 
                                        Sinh viên cần đăng nhập vào tài khoản của mình để thực hiện thanh toán.
                                    </div>
                                    <div class="text-center py-4">
                                        <p class="text-muted">Trang này chỉ dành cho sinh viên thực hiện thanh toán.</p>
                                        <a href="{{ route('dao-tao.hoc-phi.show', $hocPhi->id) }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Quay lại
                                        </a>
                                    </div>
                                @else
                                <form action="{{ route('sinh-vien.hoc-phi.zalopay-initiate', $hocPhi->id) }}" method="POST" id="paymentForm">
                                @csrf

                                <div class="mb-4">
                                    <label for="so_tien_dong" class="form-label fw-bold">
                                        <i class="bi bi-cash-stack text-primary"></i> Số tiền thanh toán (VND) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg @error('so_tien_dong') is-invalid @enderror" 
                                           id="so_tien_dong" 
                                           name="so_tien_dong" 
                                           value="{{ old('so_tien_dong', $hocPhi->so_tien_con_lai) }}"
                                           min="1000"
                                           max="{{ $hocPhi->so_tien_con_lai }}"
                                           step="1000"
                                           required>
                                    <div class="form-text">
                                        Số tiền tối thiểu: 1,000đ | Số tiền tối đa: {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ
                                    </div>
                                    @error('so_tien_dong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Gợi ý số tiền -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calculator text-success"></i> Gợi ý số tiền:
                                    </label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $soTienConLai = $hocPhi->so_tien_con_lai;
                                            $goiY = [
                                                ['label' => '50%', 'value' => $soTienConLai * 0.5],
                                                ['label' => '100% (Toàn bộ)', 'value' => $soTienConLai],
                                                ['label' => '1 triệu', 'value' => 1000000],
                                                ['label' => '2 triệu', 'value' => 2000000],
                                                ['label' => '5 triệu', 'value' => 5000000],
                                            ];
                                        @endphp
                                        @foreach($goiY as $item)
                                            @if($item['value'] <= $soTienConLai && $item['value'] >= 1000)
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm quick-amount" 
                                                        data-amount="{{ $item['value'] }}">
                                                    {{ $item['label'] }} ({{ number_format($item['value'], 0, ',', '.') }}đ)
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <hr>

                                <!-- Thông tin ZaloPay -->
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2">Thanh toán qua ZaloPay</h6>
                                            <ul class="mb-0 ps-3">
                                                <li>Hỗ trợ thanh toán qua ZaloPay, ngân hàng, thẻ ATM/Visa/Mastercard</li>
                                                <li>An toàn và bảo mật tuyệt đối</li>
                                                <li>Xử lý giao dịch nhanh chóng (1-3 phút)</li>
                                                <li>Nhận biên lai điện tử ngay sau khi thanh toán</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút thanh toán -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-credit-card-2-front me-2"></i>
                                        Thanh toán ngay qua ZaloPay
                                    </button>
                                    <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Quay lại
                                    </a>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Hướng dẫn -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Hướng dẫn thanh toán</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">Nhập số tiền cần thanh toán (hoặc chọn gợi ý)</li>
                                <li class="mb-2">Nhấn nút "Thanh toán ngay qua ZaloPay"</li>
                                <li class="mb-2">Chọn phương thức thanh toán (ZaloPay, ngân hàng, thẻ)</li>
                                <li class="mb-2">Xác nhận thanh toán trên ứng dụng ZaloPay hoặc ngân hàng</li>
                                <li class="mb-0">Kiểm tra kết quả thanh toán và tải biên lai</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .quick-amount:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .card {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #00c6fb 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
    </style>

    @push('scripts')
    <script>
        // Quick amount buttons
        document.querySelectorAll('.quick-amount').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('so_tien_dong').value = this.dataset.amount;
            });
        });

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const amount = parseInt(document.getElementById('so_tien_dong').value);
            const maxAmount = {{ (int)$hocPhi->so_tien_con_lai }};

            if (amount < 1000) {
                e.preventDefault();
                alert('Số tiền tối thiểu là 1,000đ');
                return false;
            }

            if (amount > maxAmount) {
                e.preventDefault();
                alert('Số tiền không được vượt quá ' + maxAmount.toLocaleString('vi-VN') + 'đ');
                return false;
            }

            // Confirm payment
            if (!confirm('Bạn có chắc chắn muốn thanh toán ' + amount.toLocaleString('vi-VN') + 'đ qua ZaloPay?')) {
                e.preventDefault();
                return false;
            }
        });

        // Format number input
        document.getElementById('so_tien_dong').addEventListener('blur', function() {
            if (this.value) {
                this.value = Math.round(parseInt(this.value) / 1000) * 1000;
            }
        });
    </script>
    @endpush
@endsection
