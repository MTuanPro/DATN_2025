@extends('layouts.layout-sinhvien')

@section('title', 'Thanh toán học phí qua chuyển khoản ngân hàng')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-bank text-primary"></i> Thanh toán học phí qua chuyển khoản</h3>
                    <p class="text-subtitle text-muted">Chuyển khoản ngân hàng an toàn và tiện lợi</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}">Chi tiết</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thanh toán chuyển khoản</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-md-10">
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

                    @if(isset($apiError) && $apiError)
                        <!-- Thông báo lỗi API nhưng vẫn hiển thị thông tin thanh toán -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Thông báo</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Lưu ý:</strong> {{ $apiError }}
                                    <br><small class="mt-2 d-block">Bạn vẫn có thể thanh toán bằng cách chuyển khoản với nội dung chính xác như bên dưới.</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($bankAccounts) && !empty($selectedBankAccount))
                        <!-- Thông tin tài khoản ngân hàng -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-success text-white">
                                <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Thông tin tài khoản ngân hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning border-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>⚠️ QUAN TRỌNG:</strong> Vui lòng chuyển khoản <strong>CHÍNH XÁC</strong> số tiền và nội dung chuyển khoản như bên dưới để hệ thống tự động xác nhận thanh toán.
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Số tài khoản</small></p>
                                            <h4 class="mb-0 text-primary fw-bold">
                                                @php
                                                    $accountNumber = $selectedBankAccount['bankSubAccId'] ?? $selectedBankAccount['accountNumber'] ?? 'N/A';
                                                @endphp
                                                {{ $accountNumber }}
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $accountNumber }}')">
                                                    <i class="bi bi-clipboard"></i> Copy
                                                </button>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Chủ tài khoản</small></p>
                                            <h5 class="mb-0">{{ $selectedBankAccount['bankAccountName'] ?? $selectedBankAccount['accountName'] ?? 'N/A' }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Ngân hàng</small></p>
                                            <h5 class="mb-0">{{ $selectedBankAccount['bank']['fullName'] ?? $selectedBankAccount['bankName'] ?? 'N/A' }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Số tiền cần chuyển</small></p>
                                            <h4 class="mb-0 text-danger fw-bold">
                                                {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                @if($qrCodeUrl)
                                    <!-- QR Code để quét chuyển khoản -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="text-center p-4 bg-light rounded border">
                                                <h6 class="mb-3"><i class="bi bi-qr-code me-2"></i>Quét QR Code để chuyển khoản tự động</h6>
                                                <p class="text-muted small mb-3">Quét mã QR bằng ứng dụng ngân hàng để tự động điền thông tin chuyển khoản</p>
                                                <div class="d-flex justify-content-center mb-3">
                                                    <div class="border p-3 bg-white rounded shadow-sm">
                                                        <img src="{{ $qrCodeUrl }}" alt="QR Code chuyển khoản" class="img-fluid" style="max-width: 350px;">
                                                    </div>
                                                </div>
                                                <div class="alert alert-info border-0 mb-0">
                                                    <small>
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        QR Code đã tự động điền: <strong>Số tài khoản</strong>, <strong>Số tiền: {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ</strong>, và <strong>Nội dung: {{ $paymentMemo }}</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="alert alert-danger border-danger">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2">Nội dung chuyển khoản (BẮT BUỘC)</h6>
                                            <p class="mb-2">Vui lòng nhập <strong>CHÍNH XÁC</strong> nội dung sau khi chuyển khoản:</p>
                                            <div class="bg-light p-3 rounded mb-2">
                                                <h3 class="text-center text-primary fw-bold mb-0" id="paymentMemo">
                                                    {{ $paymentMemo }}
                                                </h3>
                                                <button type="button" class="btn btn-primary w-100 mt-2" onclick="copyToClipboard('{{ $paymentMemo }}')">
                                                    <i class="bi bi-clipboard"></i> Copy nội dung chuyển khoản
                                                </button>
                                            </div>
                                            <p class="mb-0 text-danger"><strong>⚠️ Lưu ý:</strong> Nếu không nhập đúng nội dung này, hệ thống sẽ không thể tự động xác nhận thanh toán của bạn!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hướng dẫn chuyển khoản -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>Hướng dẫn chuyển khoản</h5>
                            </div>
                            <div class="card-body">
                                <ol class="mb-0">
                                    <li class="mb-3">
                                        <strong>Mở ứng dụng ngân hàng</strong> trên điện thoại hoặc đăng nhập internet banking
                                    </li>
                                    <li class="mb-3">
                                        <strong>Chọn chức năng chuyển khoản</strong> đến số tài khoản: 
                                        <code class="bg-light p-1 rounded">{{ $selectedBankAccount['accountNumber'] ?? 'N/A' }}</code>
                                    </li>
                                    <li class="mb-3">
                                        <strong>Nhập số tiền:</strong> 
                                        <code class="bg-light p-1 rounded">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ</code>
                                    </li>
                                    <li class="mb-3">
                                        <strong>Nhập nội dung chuyển khoản:</strong> 
                                        <code class="bg-light p-1 rounded fw-bold">{{ $paymentMemo }}</code>
                                        <br><small class="text-danger">⚠️ BẮT BUỘC phải nhập đúng nội dung này!</small>
                                    </li>
                                    <li class="mb-3">
                                        <strong>Xác nhận và hoàn tất</strong> giao dịch chuyển khoản
                                    </li>
                                    <li class="mb-0">
                                        <strong>Hệ thống sẽ tự động xác nhận</strong> thanh toán trong vòng 1-5 phút sau khi bạn chuyển khoản thành công
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <!-- Lưu ý quan trọng -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Lưu ý quan trọng</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info border-0 mb-3">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <strong>Thời gian xử lý:</strong> Hệ thống sẽ tự động xác nhận thanh toán trong vòng <strong>1-5 phút</strong> sau khi bạn chuyển khoản thành công.
                                </div>
                                <div class="alert alert-warning border-0 mb-3">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Nội dung chuyển khoản:</strong> Bạn <strong>PHẢI</strong> nhập đúng nội dung <code>{{ $paymentMemo }}</code> khi chuyển khoản. Nếu không, hệ thống sẽ không thể tự động xác nhận.
                                </div>
                                <div class="alert alert-success border-0 mb-0">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <strong>Số tiền:</strong> Bạn có thể chuyển đúng số tiền hoặc nhiều hơn một chút (tối đa {{ number_format(config('payment.casso.acceptable_difference', 10000), 0, ',', '.') }}đ). Hệ thống sẽ tự động xử lý.
                                </div>
                            </div>
                        </div>

                        <!-- Nút xác nhận thanh toán và quay lại -->
                        <div class="d-grid gap-2">
                            <form action="{{ route('sinh-vien.hoc-phi.casso-check-status', $hocPhi->id) }}" method="POST" id="confirmPaymentForm">
                                @csrf
                                <input type="hidden" name="payment_memo" value="{{ $paymentMemo }}">
                                <button type="submit" class="btn btn-success btn-lg w-100 mb-2" id="confirmPaymentBtn">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Tôi đã chuyển khoản - Xác nhận thanh toán
                                </button>
                            </form>
                            <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>
                                Quay lại trang học phí
                            </a>
                        </div>
                    @else
                        <!-- Không có thông tin tài khoản từ API - vẫn hiển thị thông tin thanh toán -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Thông báo</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($apiError) && $apiError)
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <strong>Lưu ý:</strong> {{ $apiError }}
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <strong>Lưu ý:</strong> Không thể lấy thông tin tài khoản ngân hàng từ Casso API.
                                    </div>
                                @endif
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <strong>Bạn vẫn có thể thanh toán:</strong> Vui lòng chuyển khoản với nội dung chính xác như bên dưới. Hệ thống sẽ tự động xác nhận khi nhận được tiền.
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin thanh toán (mã đơn hàng) -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-success text-white">
                                <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Thông tin thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger border-danger">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-2">Nội dung chuyển khoản (BẮT BUỘC)</h6>
                                            <p class="mb-2">Vui lòng nhập <strong>CHÍNH XÁC</strong> nội dung sau khi chuyển khoản:</p>
                                            <div class="bg-light p-3 rounded mb-2">
                                                <h3 class="text-center text-primary fw-bold mb-0" id="paymentMemo">
                                                    {{ $paymentMemo }}
                                                </h3>
                                                <button type="button" class="btn btn-primary w-100 mt-2" onclick="copyToClipboard('{{ $paymentMemo }}')">
                                                    <i class="bi bi-clipboard"></i> Copy nội dung chuyển khoản
                                                </button>
                                            </div>
                                            <p class="mb-0 text-danger"><strong>⚠️ Lưu ý:</strong> Nếu không nhập đúng nội dung này, hệ thống sẽ không thể tự động xác nhận thanh toán của bạn!</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Số tiền cần chuyển</small></p>
                                            <h4 class="mb-0 text-danger fw-bold">
                                                {{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box mb-3 p-3 border rounded">
                                            <p class="mb-1 text-muted"><small>Mã đơn hàng</small></p>
                                            <h5 class="mb-0">{{ $paymentMemo }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Lưu ý:</strong> Vui lòng liên hệ quản trị viên để lấy thông tin số tài khoản ngân hàng để chuyển khoản.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <style>
        .card {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #00c6fb 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .info-box {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const btn = event.target.closest('button');
                if (btn) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i> Đã copy!';
                    btn.classList.remove('btn-outline-primary', 'btn-primary');
                    btn.classList.add('btn-success');
                    
                    setTimeout(function() {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-success');
                        if (originalText.includes('Copy')) {
                            btn.classList.add('btn-outline-primary');
                        } else {
                            btn.classList.add('btn-primary');
                        }
                    }, 2000);
                } else {
                    alert('Đã copy vào clipboard!');
                }
            }).catch(function(err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Đã copy vào clipboard!');
                } catch (err) {
                    alert('Không thể copy. Vui lòng copy thủ công: ' + text);
                }
                document.body.removeChild(textArea);
            });
        }

        // Xử lý form xác nhận thanh toán
        document.getElementById('confirmPaymentForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('confirmPaymentBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang kiểm tra...';
            }
        });
    </script>
    @endpush
@endsection

