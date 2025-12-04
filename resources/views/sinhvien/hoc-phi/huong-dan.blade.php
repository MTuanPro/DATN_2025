@extends('layouts.layout-sinhvien')

@section('title', 'Hướng dẫn nộp học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hướng dẫn nộp học phí</h3>
                    <p class="text-subtitle text-muted">Các phương thức nộp học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hướng dẫn</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h4>Các phương thức nộp học phí</h4>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Vui lòng nộp học phí đúng hạn để tránh bị ảnh hưởng đến quá trình học tập.
                    </div>

                    <h5 class="mt-4">1. Nộp trực tiếp tại Phòng Tài vụ</h5>
                    <p>- Địa chỉ: Phòng Tài vụ - Tầng 1, Nhà A</p>
                    <p>- Thời gian: 8h00 - 17h00 (Thứ 2 - Thứ 6)</p>

                    <h5 class="mt-4">2. Chuyển khoản ngân hàng</h5>
                    <p><strong>Thông tin tài khoản:</strong></p>
                    <ul>
                        <li>Tên tài khoản: TRƯỜNG ĐẠI HỌC ABC</li>
                        <li>Số tài khoản: 0123456789</li>
                        <li>Ngân hàng: Vietcombank - Chi nhánh XYZ</li>
                        <li>Nội dung: MSSV - Họ tên - Học phí HK1 2024-2025</li>
                    </ul>

                    <h5 class="mt-4">3. Thanh toán qua ZaloPay</h5>
                    <p><strong>Hướng dẫn thanh toán qua ZaloPay:</strong></p>
                    <ol>
                        <li>Truy cập vào trang <strong>Học phí</strong> và chọn kỳ học cần thanh toán</li>
                        <li>Click vào nút <strong>"Thanh toán ZaloPay"</strong></li>
                        <li>Nhập số tiền cần thanh toán (hoặc chọn số tiền gợi ý)</li>
                        <li>Click <strong>"Thanh toán ngay qua ZaloPay"</strong></li>
                        <li>Hệ thống sẽ chuyển bạn đến trang thanh toán ZaloPay</li>
                        <li>Chọn phương thức thanh toán:
                            <ul>
                                <li><strong>Quét QR Code:</strong> Mở ứng dụng ZaloPay, chọn "Quét mã" và quét mã QR trên màn hình</li>
                                <li><strong>Thanh toán trên Web:</strong> Scroll xuống để chọn Thẻ quốc tế (Visa/Master), Thẻ ATM, hoặc Ví ZaloPay</li>
                            </ul>
                        </li>
                        <li>Xác nhận thanh toán và hoàn tất giao dịch</li>
                        <li>Hệ thống sẽ tự động cập nhật trạng thái thanh toán sau 1-3 phút</li>
                    </ol>
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> <strong>Ưu điểm:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Thanh toán nhanh chóng, an toàn và bảo mật</li>
                            <li>Hỗ trợ nhiều phương thức: QR Code, Thẻ quốc tế, Thẻ ATM, Ví ZaloPay</li>
                            <li>Nhận biên lai điện tử ngay lập tức</li>
                            <li>Không cần đến trường, thanh toán mọi lúc mọi nơi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

