@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết học phí của tôi</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã môn</th>
                                            <th>Tên môn học</th>
                                            <th>Số tín chỉ</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $chiTietHienThi = $hocPhi->chiTietHocPhiMon->where('trang_thai', '!=', 'huy');
                                        @endphp
                                        @foreach ($chiTietHienThi as $index => $ct)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $ct->monHoc->ma_mon }}</td>
                                                <td>{{ $ct->monHoc->ten_mon }}</td>
                                                <td>{{ $ct->so_tin_chi }}</td>
                                                <td>{{ number_format($ct->don_gia_tin_chi, 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($ct->thanh_tien, 0, ',', '.') }} đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Tổng hợp</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Tổng học phí môn học:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($hocPhi->tong_hoc_phi_mon_hoc, 0, ',', '.') }} đ</strong>
                                    </td>
                                </tr>
                                @if($hocPhi->phi_dich_vu > 0)
                                <tr>
                                    <td>Phí dịch vụ:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($hocPhi->phi_dich_vu, 0, ',', '.') }} đ</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr style="border-top: 2px solid #ddd;">
                                    <td><strong>Tổng học phí:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Đã đóng:</td>
                                    <td class="text-end text-success">
                                        <strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td>Còn lại:</td>
                                    <td class="text-end text-danger">
                                        <h4>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hạn đóng:</td>
                                    <td class="text-end"><span
                                            class="badge bg-warning">{{ $hocPhi->han_dong->format('d/m/Y') }}</span></td>
                                </tr>
                                <tr>
                                    <td>Trạng thái:</td>
                                    <td class="text-end">
                                        @if ($hocPhi->trang_thai == 'da_nop_du')
                                            <span class="badge bg-success">Đã nộp đủ</span>
                                        @elseif ($hocPhi->trang_thai == 'qua_han')
                                            <span class="badge bg-danger">Quá hạn</span>
                                        @else
                                            <span class="badge bg-warning">Chưa nộp đủ</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($hocPhi->so_tien_con_lai > 0)
                            <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bi bi-credit-card"></i> Thanh toán online
                            </button>
                            @endif

                            <a href="{{ route('sinh-vien.hoc-phi.lich-su', $hocPhi->id) }}" class="btn btn-info w-100 mt-2">
                                <i class="bi bi-clock-history"></i> Xem lịch sử đóng
                            </a>
                            <a href="{{ route('sinh-vien.hoc-phi.huong-dan') }}" class="btn btn-success w-100 mt-2">
                                <i class="bi bi-question-circle"></i> Hướng dẫn nộp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Thanh toán Online -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="paymentModalLabel">
                            <i class="bi bi-credit-card"></i> Thanh toán Online
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="paymentForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Số tiền cần đóng <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       id="soTienDong" 
                                       name="so_tien_dong" 
                                       min="10000"
                                       max="{{ $hocPhi->so_tien_con_lai }}"
                                       value="{{ $hocPhi->so_tien_con_lai }}"
                                       required>
                                <small class="text-muted">
                                    Số tiền còn lại: <strong>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} VNĐ</strong>
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Chọn phương thức thanh toán <span class="text-danger">*</span></label>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary payment-method" data-gateway="vnpay">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="bi bi-bank2"></i>
                                                <strong>VNPay</strong>
                                            </div>
                                            <small class="text-muted">ATM/Visa/MasterCard/QR</small>
                                        </div>
                                    </button>
                                    
                                    <button type="button" class="btn btn-outline-danger payment-method" data-gateway="momo">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <i class="bi bi-wallet2"></i>
                                                <strong>MoMo</strong>
                                            </div>
                                            <small class="text-muted">Ví điện tử/QR Code</small>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <small>
                                    Sau khi nhấn "Thanh toán", bạn sẽ được chuyển đến trang thanh toán của cổng thanh toán.
                                    Vui lòng hoàn tất thanh toán trong vòng 15 phút.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentButtons = document.querySelectorAll('.payment-method');
            const soTienDongInput = document.getElementById('soTienDong');
            const hocPhiId = {{ $hocPhi->id }};

            paymentButtons.forEach(button => {
                button.addEventListener('click', async function() {
                    const gateway = this.dataset.gateway;
                    const soTienDong = parseInt(soTienDongInput.value);

                    // Validate số tiền
                    if (!soTienDong || soTienDong < 10000) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Số tiền tối thiểu là 10,000 VNĐ',
                        });
                        return;
                    }

                    if (soTienDong > {{ $hocPhi->so_tien_con_lai }}) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Số tiền vượt quá số tiền còn lại',
                        });
                        return;
                    }

                    // Hiển thị loading
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng đợi trong giây lát',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch(`{{ route('sinh-vien.hoc-phi.thanh-toan-online', $hocPhi->id) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                gateway: gateway,
                                so_tien_dong: soTienDong
                            })
                        });

                        const result = await response.json();

                        if (result.success && result.demo) {
                            // DEMO: Thanh toán thành công ngay lập tức
                            Swal.fire({
                                icon: 'success',
                                title: 'Thanh toán thành công!',
                                html: `
                                    <div class="text-left">
                                        <table class="table table-sm">
                                            <tr>
                                                <th style="width: 40%">Mã giao dịch:</th>
                                                <td><strong>${result.data.ma_giao_dich}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Số tiền:</th>
                                                <td><strong class="text-success">${new Intl.NumberFormat('vi-VN').format(result.data.so_tien)}₫</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Cổng thanh toán:</th>
                                                <td><span class="badge badge-primary">${result.data.gateway}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Thời gian:</th>
                                                <td>${result.data.ngay_thanh_toan}</td>
                                            </tr>
                                            <tr>
                                                <th>Còn lại:</th>
                                                <td><strong class="text-danger">${new Intl.NumberFormat('vi-VN').format(result.data.so_tien_con_lai)}₫</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Trạng thái:</th>
                                                <td><span class="badge badge-info">${result.data.trang_thai}</span></td>
                                            </tr>
                                        </table>
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <i class="fas fa-info-circle"></i> 
                                            <small><strong>Lưu ý:</strong> Đây là giao dịch demo - Không có thanh toán thực tế</small>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'Đóng',
                                confirmButtonColor: '#3085d6',
                            }).then(() => {
                                // Reload trang để cập nhật số liệu
                                location.reload();
                            });
                        } else if (result.success && result.payment_url) {
                            // Redirect đến trang thanh toán (nếu có)
                            window.location.href = result.payment_url;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: result.message || 'Không thể tạo thanh toán',
                            });
                        }
                    } catch (error) {
                        console.error('Payment Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xử lý thanh toán',
                        });
                    }
                });
            });

            // Format số tiền khi nhập
            soTienDongInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value) {
                    this.value = parseInt(value);
                }
            });
        });
    </script>
    @endpush
@endsection
