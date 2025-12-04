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
                        <!-- Tabs để chọn giữa QR Code và Thanh toán trực tiếp -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                                <ul class="nav nav-tabs card-header-tabs border-0" id="paymentTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr-payment" type="button" role="tab">
                                            <i class="bi bi-qr-code me-2"></i>Quét QR Code
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="web-tab" data-bs-toggle="tab" data-bs-target="#web-payment" type="button" role="tab">
                                            <i class="bi bi-globe me-2"></i>Thanh toán trên Web
                                        </button>
                                    </li>
                                </ul>
                                <a href="{{ route('sinh-vien.hoc-phi.zalopay-payment', ['id' => $hocPhi->id, 'new' => 1]) }}" class="btn btn-sm btn-light ms-2">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Tạo đơn hàng mới
                                </a>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                                    </div>
                                @endif
                                
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                                        <br><small class="mt-2 d-block">Vui lòng thử lại sau hoặc liên hệ quản trị viên nếu vấn đề vẫn tiếp tục.</small>
                                    </div>
                                @endif
                                
                                @if(session('zalopay_app_trans_id'))
                                    <div class="alert alert-warning border-warning">
                                        <i class="bi bi-info-circle-fill"></i>
                                        <strong>⚠️ Lưu ý quan trọng:</strong>
                                        <p class="mb-2"><strong>Nếu sau khi hủy hoặc thanh toán, bạn bị redirect về trang lạ (không phải trang này):</strong></p>
                                        <p class="mb-2"><strong>Mã giao dịch của bạn:</strong> <code>{{ session('zalopay_app_trans_id') }}</code></p>
                                        <p class="mb-2"><strong>Hãy làm theo các bước sau:</strong></p>
                                        <ol class="mb-2">
                                            <li><strong>Quay lại trang học phí:</strong> Click vào <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="alert-link">đây</a> hoặc vào menu "Học phí"</li>
                                            <li><strong>Nếu đã thanh toán thành công:</strong> Hệ thống sẽ tự động cập nhật sau vài phút, hoặc bạn có thể kiểm tra thủ công</li>
                                            <li><strong>Nếu đã hủy:</strong> Bạn có thể tạo đơn hàng mới để thanh toán lại</li>
                                        </ol>
                                        <p class="mb-0"><small><strong>Lý do:</strong> Callback URL trong ZaloPay Merchant Portal đang được cấu hình sai. Đây là vấn đề cấu hình, không phải lỗi của hệ thống.</small></p>
                                    </div>
                                @endif
                                
                                <div class="tab-content" id="paymentTabContent">
                                    <!-- Tab QR Code -->
                                    <div class="tab-pane fade show active" id="qr-payment" role="tabpanel">
                                        <div class="text-center">
                                            <div class="mb-4">
                                    <p class="text-muted mb-3">Quét mã QR bằng ứng dụng ZaloPay hoặc ngân hàng để thanh toán</p>
                                    <div class="d-flex justify-content-center">
                                        <div class="border p-4 bg-white rounded shadow-sm" style="background-color: #ffffff !important; min-width: 400px;">
                                            @php
                                                // ✅ Sử dụng URL gốc từ ZaloPay, không decode hay modify
                                                // URL từ ZaloPay đã được encode đúng và cần giữ nguyên
                                                // URL gateway của ZaloPay có format đặc biệt: https://qcgateway.zalopay.vn/pay/v2/vietqr?order=...
                                                $qrCodeUrl = trim($orderUrl);
                                                
                                                // Loại bỏ khoảng trắng và ký tự không hợp lệ
                                                $qrCodeUrl = preg_replace('/\s+/', '', $qrCodeUrl);
                                                $qrCodeUrl = str_replace(["\0", "\r", "\n", "\t"], '', $qrCodeUrl);
                                                
                                                // Đảm bảo URL là string hợp lệ
                                                $qrCodeUrl = (string) $qrCodeUrl;
                                                
                                                // Log để debug (chỉ trong development)
                                                if (config('app.debug')) {
                                                    \Log::info('ZaloPay QR Code URL:', [
                                                        'url_length' => strlen($qrCodeUrl),
                                                        'url_preview' => substr($qrCodeUrl, 0, 100) . '...',
                                                        'url_has_spaces' => strpos($qrCodeUrl, ' ') !== false,
                                                    ]);
                                                }
                                            @endphp
                                            <!-- QR Code với error correction cao (H) và margin lớn để dễ quét -->
                                            <div style="display: inline-block; padding: 10px; background: white; border-radius: 8px;">
                                                {!! QrCode::size(450)
                                                    ->errorCorrection('H')
                                                    ->margin(4)
                                                    ->format('svg')
                                                    ->generate($qrCodeUrl) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="alert alert-light border">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <small class="text-muted mb-0">
                                                    <i class="bi bi-link-45deg"></i> 
                                                    <strong>URL thanh toán:</strong>
                                                </small>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyPaymentUrl()">
                                                    <i class="bi bi-clipboard"></i> Copy URL
                                                </button>
                                            </div>
                                            <code id="paymentUrl" style="font-size: 0.7rem; word-break: break-all; display: block; background: #f8f9fa; padding: 8px; border-radius: 4px;">{{ $qrCodeUrl }}</code>
                                        </div>
                                        <div class="alert alert-warning border-0">
                                            <small>
                                                <i class="bi bi-exclamation-triangle-fill"></i> 
                                                <strong>Lưu ý:</strong> Nếu quét không thành công, vui lòng:
                                                <ul class="mb-0 mt-2">
                                                    <li>Đảm bảo ánh sáng đủ để quét mã QR</li>
                                                    <li>Giữ điện thoại ổn định và cách mã QR khoảng 20-30cm</li>
                                                    <li>Hoặc click vào nút "Mở trang thanh toán ZaloPay" bên dưới</li>
                                                </ul>
                                            </small>
                                        </div>
                                    </div>
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
                                
                                <div class="alert alert-success border-0 mb-3">
                                    <i class="bi bi-lightbulb-fill"></i>
                                    <strong>💡 Cách tìm phương thức thanh toán:</strong>
                                    <p class="mb-2">Trong trang ZaloPay, các phương thức thanh toán thường nằm <strong>bên dưới QR code</strong> hoặc ở <strong>phần dưới cùng</strong> của trang. Hãy scroll xuống để tìm!</p>
                                    <p class="mb-0"><strong>Hoặc click nút bên dưới để mở trang ZaloPay đầy đủ trong tab mới (khuyến nghị):</strong></p>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ $orderUrl }}" target="_blank" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>
                                        🔗 Mở trang thanh toán ZaloPay trong tab mới (Khuyến nghị)
                                    </a>
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a href="{{ route('sinh-vien.hoc-phi.zalopay-payment', ['id' => $hocPhi->id, 'new' => 1]) }}" class="btn btn-warning">
                                            <i class="bi bi-arrow-clockwise me-2"></i>
                                            Tạo đơn hàng mới
                                        </a>
                                        <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>
                                            Quay lại
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> 
                                        Mã QR này có hiệu lực trong 15 phút. Vui lòng thanh toán sớm.
                                    </small>
                                </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Tab Thanh toán trên Web -->
                                    <div class="tab-pane fade" id="web-payment" role="tabpanel">
                                        @if(config('payment.zalopay.sandbox', true))
                                            <div class="alert alert-warning mb-3">
                                                <i class="bi bi-credit-card"></i>
                                                <strong>Thẻ test để thanh toán (Sandbox):</strong>
                                                <div class="mt-2">
                                                    <div class="alert alert-info border-0 mb-2 p-2">
                                                        <small><i class="bi bi-info-circle"></i> <strong>💡 Mẹo:</strong> Thanh toán bằng <strong>thẻ test</strong> sẽ <strong>KHÔNG YÊU CẦU MÃ OTP</strong> từ ZaloPay. Đây là cách test nhanh nhất!</small>
                                                    </div>
                                                    <div class="alert alert-danger border-0 mb-2 p-2">
                                                        <small><i class="bi bi-exclamation-triangle-fill"></i> <strong>⚠️ QUAN TRỌNG:</strong> Để <strong>KHÔNG CẦN OTP</strong>, bạn <strong>PHẢI</strong> chọn <strong>"Thẻ quốc tế"</strong> (International Card), <strong>KHÔNG PHẢI</strong> "Thẻ ATM"!</small>
                                                    </div>
                                                    <strong>Thẻ Visa/Master/JCB (Khuyến nghị - KHÔNG CẦN OTP):</strong>
                                                    <ul class="mb-2">
                                                        <li>Số thẻ: <code>4111111111111111</code></li>
                                                        <li>Tên: <code>NGUYEN VAN A</code></li>
                                                        <li>Ngày hết hạn: <code>01/25</code> (hoặc bất kỳ ngày tương lai)</li>
                                                        <li>CVV: <code>123</code></li>
                                                    </ul>
                                                    <div class="alert alert-info border-0 mb-2 p-2">
                                                        <small><i class="bi bi-info-circle"></i> <strong>Lưu ý về "Thẻ ATM":</strong> Nếu chọn <strong>"Thẻ ATM"</strong> hoặc <strong>"Thẻ/tài khoản nội địa"</strong>, bạn <strong>VẪN SẼ BỊ YÊU CẦU OTP</strong> ngay cả khi dùng thẻ test. Để tránh OTP, hãy chọn <strong>"Thẻ quốc tế"</strong>!</small>
                                                    </div>
                                                    <div class="alert alert-success border-0 mt-2 mb-0 p-2">
                                                        <small><i class="bi bi-check-circle"></i> <strong>✅ Không cần OTP:</strong> Khi thanh toán bằng <strong>"Thẻ quốc tế"</strong> với thẻ test ở trên, bạn <strong>KHÔNG CẦN MÃ OTP</strong>. Chỉ cần nhập thông tin thẻ là xong!</small>
                                                    </div>
                                                    <div class="alert alert-warning border-0 mt-2 mb-0 p-2">
                                                        <small><i class="bi bi-exclamation-triangle"></i> <strong>⚠️ Lưu ý:</strong> Nếu chọn thanh toán bằng <strong>ZaloPay wallet</strong> hoặc <strong>"Thẻ ATM"</strong>, bạn sẽ cần nhập <strong>mã OTP</strong> để xác thực. Mã OTP là bắt buộc và không thể bỏ qua. <strong>Để test không cần OTP, hãy chọn "Thẻ quốc tế"!</strong></small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Thanh toán trực tiếp trên web:</strong> Bạn có thể thanh toán ngay trên trang này mà không cần quét QR code. 
                                            <br><strong class="text-danger">⚠️ QUAN TRỌNG:</strong> Trong khung ZaloPay bên dưới, bạn cần:
                                            <ol class="mb-0 mt-2">
                                                <li><strong>Scroll xuống</strong> trong khung thanh toán để tìm các phương thức thanh toán</li>
                                                <li>Tìm các <strong>tab hoặc nút</strong> như: "Thẻ quốc tế", "Thẻ ATM", "ZaloPay", "Ngân hàng"</li>
                                                <li>Click vào phương thức bạn muốn (ví dụ: "Thẻ quốc tế" hoặc "Thẻ ATM")</li>
                                                <li>Nhập thông tin thẻ test ở trên</li>
                                            </ol>
                                            <p class="mb-0 mt-2"><strong>💡 Mẹo:</strong> Nếu không thấy các phương thức, hãy thử <strong>scroll xuống</strong> hoặc click vào nút <strong>"Mở trong tab mới"</strong> bên dưới.</p>
                                        </div>
                                        
                                        <div class="border rounded overflow-hidden shadow-sm" style="min-height: 800px; background: #f8f9fa; position: relative;">
                                            <iframe 
                                                id="zalopay-payment-iframe"
                                                src="{{ $orderUrl }}" 
                                                style="width: 100%; height: 1000px; border: none; display: block;"
                                                allow="payment *"
                                                sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-top-navigation"
                                                scrolling="yes">
                                            </iframe>
                                            <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                                                <button type="button" class="btn btn-sm btn-light" onclick="refreshIframe()" title="Làm mới trang thanh toán">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-primary mt-3">
                                            <i class="bi bi-info-circle-fill"></i>
                                            <strong>Hướng dẫn thanh toán chi tiết:</strong>
                                            <ol class="mb-0 mt-2">
                                                <li><strong>Trong khung ZaloPay bên trên:</strong>
                                                    <ul>
                                                        <li>Bạn sẽ thấy <strong>QR code</strong> ở bên phải</li>
                                                        <li><strong class="text-danger">Scroll xuống</strong> hoặc tìm các tab/nút bên dưới QR code</li>
                                                        <li>Các phương thức thanh toán có thể hiển thị dưới dạng:
                                                            <ul>
                                                                <li>Tab: "Thẻ quốc tế", "Thẻ ATM", "ZaloPay", "Ngân hàng"</li>
                                                                <li>Hoặc nút: "Thanh toán bằng thẻ", "Thanh toán bằng ZaloPay"</li>
                                                                <li>Hoặc danh sách các phương thức</li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li><strong>Chọn phương thức thanh toán:</strong>
                                                    <ul>
                                                        <li>Click vào <strong>"Thẻ quốc tế"</strong> hoặc <strong>"Thẻ ATM"</strong> để test</li>
                                                        <li>Hoặc click vào <strong>"ZaloPay"</strong> nếu muốn dùng ví ZaloPay</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Nhập thông tin thẻ test:</strong>
                                                    <ul>
                                                        <li><strong>Thẻ Visa/Master:</strong> Số thẻ: <code>4111111111111111</code>, CVV: <code>123</code></li>
                                                        <li><strong>Thẻ ATM:</strong> Số thẻ: <code>9704540000000062</code></li>
                                                        <li>Tên: <code>NGUYEN VAN A</code></li>
                                                        <li>Ngày hết hạn: <code>01/25</code> (hoặc bất kỳ ngày tương lai)</li>
                                                    </ul>
                                                </li>
                                                <li>Xác nhận thanh toán</li>
                                                <li>Sau khi thanh toán thành công, hệ thống sẽ tự động cập nhật</li>
                                            </ol>
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <strong>⚠️ Nếu không thấy các phương thức thanh toán:</strong>
                                                <ul class="mb-0">
                                                    <li>Thử <strong>scroll xuống</strong> trong khung ZaloPay</li>
                                                    <li>Click vào nút <strong>"Mở trong tab mới"</strong> bên dưới để mở trang ZaloPay đầy đủ</li>
                                                    <li>Trong sandbox, một số phương thức có thể bị hạn chế</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="alert alert-warning border-0">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <strong>Lưu ý:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Nếu iframe không hiển thị hoặc bị chặn, vui lòng click vào nút "Mở trong tab mới" bên dưới</li>
                                                        <li>Một số trình duyệt có thể chặn iframe từ domain khác (X-Frame-Options)</li>
                                                        <li>Sau khi thanh toán thành công, hệ thống sẽ tự động cập nhật trạng thái</li>
                                                        <li>Bạn có thể chọn tất cả phương thức thanh toán: ZaloPay wallet, ngân hàng, thẻ ATM/Visa/Mastercard</li>
                                                    </ul>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-3">
                                            <a href="{{ $orderUrl }}" target="_blank" class="btn btn-primary btn-lg">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                                Mở trong tab mới (nếu iframe không hoạt động)
                                            </a>
                                            <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-2"></i>
                                                Quay lại
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    
                                    <!-- Tab Thanh toán trên Web -->
                                    <div class="tab-pane fade" id="web-payment" role="tabpanel">
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Thanh toán trực tiếp trên web:</strong> Bạn có thể thanh toán ngay trên trang này mà không cần quét QR code.
                                        </div>
                                        
                                        <div class="border rounded overflow-hidden" style="min-height: 600px; background: #f8f9fa;">
                                            <iframe 
                                                id="zalopay-payment-iframe"
                                                src="{{ $orderUrl }}" 
                                                style="width: 100%; height: 800px; border: none;"
                                                allow="payment *"
                                                sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-top-navigation">
                                            </iframe>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="alert alert-warning border-0">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <strong>Lưu ý:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Nếu iframe không hiển thị, vui lòng click vào nút "Mở trong tab mới" bên dưới</li>
                                                        <li>Một số trình duyệt có thể chặn iframe từ domain khác (X-Frame-Options)</li>
                                                        <li>Sau khi thanh toán thành công, hệ thống sẽ tự động cập nhật trạng thái</li>
                                                        <li>Bạn có thể chọn phương thức thanh toán trực tiếp trong iframe</li>
                                                    </ul>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-3">
                                            <a href="{{ $orderUrl }}" target="_blank" class="btn btn-primary btn-lg">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                                Mở trong tab mới (nếu iframe không hoạt động)
                                            </a>
                                            <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-2"></i>
                                                Quay lại
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    
                                    <!-- Tab Thanh toán trên Web -->
                                    <div class="tab-pane fade" id="web-payment" role="tabpanel">
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle"></i>
                                            <strong>Thanh toán trực tiếp trên web:</strong> Bạn có thể thanh toán ngay trên trang này mà không cần quét QR code.
                                        </div>
                                        
                                        <div class="border rounded" style="min-height: 600px;">
                                            <iframe 
                                                id="zalopay-payment-iframe"
                                                src="{{ $orderUrl }}" 
                                                style="width: 100%; height: 800px; border: none; border-radius: 8px;"
                                                allow="payment"
                                                sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-top-navigation">
                                            </iframe>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="alert alert-warning border-0">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <strong>Lưu ý:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Nếu iframe không hiển thị, vui lòng click vào nút "Mở trang thanh toán ZaloPay" ở tab QR Code</li>
                                                        <li>Một số trình duyệt có thể chặn iframe từ domain khác</li>
                                                        <li>Sau khi thanh toán thành công, hệ thống sẽ tự động cập nhật trạng thái</li>
                                                    </ul>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-3">
                                            <a href="{{ $orderUrl }}" target="_blank" class="btn btn-outline-primary">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                                Mở trong tab mới (nếu iframe không hoạt động)
                                            </a>
                                            <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-2"></i>
                                                Quay lại
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Form thanh toán -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-wallet2 me-2"></i>Thông tin thanh toán
                                </h5>
                                @if(session('zalopay_orderurl'))
                                    <a href="{{ route('sinh-vien.hoc-phi.zalopay-payment', ['id' => $hocPhi->id, 'new' => 1]) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Tạo đơn hàng mới
                                    </a>
                                @endif
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
                                            @if(config('payment.zalopay.sandbox', true))
                                                <div class="alert alert-warning border-0 mb-2 p-2">
                                                    <small><i class="bi bi-exclamation-triangle-fill"></i> <strong>Chế độ TEST:</strong> Bạn đang ở môi trường sandbox. Thanh toán sẽ không chuyển tiền thật, chỉ dùng để test.</small>
                                                </div>
                                            @endif
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
        const soTienDongInput = document.getElementById('so_tien_dong');
        if (soTienDongInput) {
            soTienDongInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = Math.round(parseInt(this.value) / 1000) * 1000;
                }
            });
        }

        // Copy payment URL function
        function copyPaymentUrl() {
            const paymentUrlElement = document.getElementById('paymentUrl');
            if (paymentUrlElement) {
                const url = paymentUrlElement.textContent.trim();
                navigator.clipboard.writeText(url).then(function() {
                    // Show success message
                    const btn = event.target.closest('button');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check"></i> Đã copy!';
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-success');
                    
                    setTimeout(function() {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-primary');
                    }, 2000);
                }).catch(function(err) {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = url;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        alert('Đã copy URL vào clipboard!');
                    } catch (err) {
                        alert('Không thể copy URL. Vui lòng copy thủ công.');
                    }
                    document.body.removeChild(textArea);
                });
            }
        }

        // Refresh iframe function
        function refreshIframe() {
            const iframe = document.getElementById('zalopay-payment-iframe');
            if (iframe) {
                iframe.src = iframe.src; // Reload iframe
            }
        }

        // Check if iframe loads successfully
        document.addEventListener('DOMContentLoaded', function() {
            const iframe = document.getElementById('zalopay-payment-iframe');
            if (iframe) {
                iframe.onload = function() {
                    console.log('ZaloPay iframe loaded successfully');
                    // Try to scroll iframe content to show payment methods
                    try {
                        // Note: Cannot access cross-origin iframe content, but we can try to adjust
                        console.log('Iframe content loaded - payment methods should be visible');
                    } catch (e) {
                        console.log('Cross-origin iframe (expected)');
                    }
                };
                
                iframe.onerror = function() {
                    console.error('ZaloPay iframe failed to load');
                    // Show warning if iframe fails
                    const webTab = document.getElementById('web-tab');
                    if (webTab) {
                        webTab.innerHTML = '<i class="bi bi-globe me-2"></i>Thanh toán trên Web <span class="badge bg-warning">Cần mở tab mới</span>';
                    }
                };
                
                // Auto-scroll to iframe when tab is switched
                const webTab = document.getElementById('web-tab');
                if (webTab) {
                    webTab.addEventListener('shown.bs.tab', function() {
                        setTimeout(function() {
                            iframe.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 300);
                    });
                }
            }
        });

        // Xử lý redirect từ ZaloPay (thành công hoặc hủy)
        // Tự động redirect về trang học phí khi có tham số ZaloPay
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const appTransId = urlParams.get('apptransid');
            const returncode = urlParams.get('returncode');
            const appid = urlParams.get('appid');
            const zptranstoken = urlParams.get('zptranstoken');
            
            // Kiểm tra nếu có tham số ZaloPay (thành công hoặc hủy)
            if ((appTransId || returncode || appid || zptranstoken) && !window.location.pathname.includes('/payment/zalopay/callback')) {
                console.log('Detected ZaloPay parameters in URL, redirecting to callback handler...');
                
                // Lấy hoc_phi_id từ session hoặc từ URL
                const currentPath = window.location.pathname;
                const hocPhiMatch = currentPath.match(/\/hoc-phi\/(\d+)/);
                let hocPhiId = null;
                
                if (hocPhiMatch) {
                    hocPhiId = hocPhiMatch[1];
                } else {
                    // Thử lấy từ session hoặc từ localStorage
                    try {
                        const storedHocPhiId = sessionStorage.getItem('zalopay_hoc_phi_id');
                        if (storedHocPhiId) {
                            hocPhiId = storedHocPhiId;
                        }
                    } catch(e) {
                        console.warn('Could not access sessionStorage');
                    }
                }
                
                // Nếu có returncode = 1 (thành công) hoặc các mã khác, redirect về callback handler
                if (hocPhiId) {
                    // Redirect về callback handler để xử lý
                    const callbackUrl = '/payment/zalopay/callback?' + window.location.search.substring(1);
                    window.location.href = callbackUrl;
                } else if (appTransId) {
                    // Nếu có appTransId, redirect về callback để tìm hoc_phi_id từ database
                    const callbackUrl = '/payment/zalopay/callback?' + window.location.search.substring(1);
                    window.location.href = callbackUrl;
                } else {
                    // Nếu không tìm thấy, redirect về danh sách học phí
                    console.warn('Could not find hoc_phi_id, redirecting to index');
                    const errorMsg = returncode == 1 ? 'Thanh toán thành công!' : 
                                   returncode == -6012 ? 'Giao dịch đã bị hủy bởi người dùng.' : 
                                   returncode == -6013 ? 'Giao dịch đã hết hạn. Vui lòng tạo đơn hàng mới.' :
                                   'Giao dịch đã bị hủy hoặc thất bại.';
                    window.location.href = '/sinh-vien/hoc-phi?message=' + encodeURIComponent(errorMsg);
                }
            }
        })();
    </script>
    @endpush
@endsection
