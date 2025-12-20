@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-receipt-cutoff text-primary"></i> Chi tiết Học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết và thanh toán học phí</p>
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
            <!-- Thông tin tổng quan -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Tổng học phí</h6>
                                    <h4 class="mb-0 text-primary">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }}đ</h4>
                                </div>
                                <div class="avatar avatar-lg bg-primary">
                                    <i class="bi bi-cash-stack text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Đã đóng</h6>
                                    <h4 class="mb-0 text-success">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }}đ</h4>
                                </div>
                                <div class="avatar avatar-lg bg-success">
                                    <i class="bi bi-check-circle text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Còn lại</h6>
                                    <h4 class="mb-0 text-danger">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ</h4>
                                </div>
                                <div class="avatar avatar-lg bg-danger">
                                    <i class="bi bi-exclamation-triangle text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Hạn đóng</h6>
                                    <h4 class="mb-0">{{ $hocPhi->han_dong->format('d/m/Y') }}</h4>
                                </div>
                                <div class="avatar avatar-lg bg-warning">
                                    <i class="bi bi-calendar-event text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Thông tin học kỳ -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin học kỳ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="bi bi-calendar3 text-primary"></i> <strong>Học kỳ:</strong> {{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</p>
                                    <p class="mb-2"><i class="bi bi-person text-primary"></i> <strong>Sinh viên:</strong> {{ $hocPhi->sinhVien->ho_ten }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="bi bi-credit-card text-primary"></i> <strong>MSSV:</strong> {{ $hocPhi->sinhVien->ma_sinh_vien }}</p>
                                    <p class="mb-2">
                                        <i class="bi bi-circle-fill 
                                            @if($hocPhi->trang_thai == 'da_nop_du') text-success
                                            @elseif($hocPhi->trang_thai == 'qua_han') text-danger
                                            @else text-warning @endif"></i>
                                        <strong>Trạng thái:</strong>
                                        @if ($hocPhi->trang_thai == 'da_nop_du')
                                            <span class="badge bg-success">Đã nộp đủ</span>
                                        @elseif ($hocPhi->trang_thai == 'qua_han')
                                            <span class="badge bg-danger">Quá hạn</span>
                                        @else
                                            <span class="badge bg-warning">Chưa nộp đủ</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chi tiết học phí các môn -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0"><i class="bi bi-list-check"></i> Chi tiết học phí các môn học</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50" class="text-center">STT</th>
                                            <th width="100">Mã môn</th>
                                            <th>Tên môn học</th>
                                            <th width="80" class="text-center">Tín chỉ</th>
                                            <th width="120" class="text-end">Đơn giá</th>
                                            <th width="140" class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $chiTietHienThi = $hocPhi->chiTietHocPhiMon->where('trang_thai', '!=', 'huy');
                                            $tongTinChi = 0;
                                        @endphp
                                        @foreach ($chiTietHienThi as $index => $ct)
                                            @php $tongTinChi += $ct->so_tin_chi; @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td><code class="text-primary">{{ $ct->monHoc->ma_mon }}</code></td>
                                                <td>{{ $ct->monHoc->ten_mon }}</td>
                                                <td class="text-center"><span class="badge bg-info">{{ $ct->so_tin_chi }}</span></td>
                                                <td class="text-end">{{ number_format($ct->don_gia_tin_chi, 0, ',', '.') }}đ</td>
                                                <td class="text-end"><strong>{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3" class="text-end">Tổng cộng:</th>
                                            <th class="text-center"><span class="badge bg-primary">{{ $tongTinChi }}</span></th>
                                            <th></th>
                                            <th class="text-end text-primary"><strong>{{ number_format($hocPhi->tong_hoc_phi_mon_hoc, 0, ',', '.') }}đ</strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Tổng hợp thanh toán -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0"><i class="bi bi-calculator"></i> Tổng hợp thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Học phí môn học:</span>
                                    <strong>{{ number_format($hocPhi->tong_hoc_phi_mon_hoc, 0, ',', '.') }}đ</strong>
                                </div>
                                @if($hocPhi->phi_dich_vu > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí dịch vụ:</span>
                                    <strong>{{ number_format($hocPhi->phi_dich_vu, 0, ',', '.') }}đ</strong>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-primary"><strong>Tổng học phí:</strong></span>
                                    <h5 class="mb-0 text-primary">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }}đ</h5>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success">Đã đóng:</span>
                                    <strong class="text-success">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }}đ</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-danger"><strong>Còn phải đóng:</strong></span>
                                    <h4 class="mb-0 text-danger">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }}đ</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Các thao tác -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-gear"></i> Thao tác</h5>
                        </div>
                        <div class="card-body">
                            @if($hocPhi->so_tien_con_lai > 0)
                                <div class="d-grid gap-2 mb-2">
                                    <form action="{{ route('sinh-vien.hoc-phi.zalopay-initiate', $hocPhi->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="so_tien_dong" value="{{ $hocPhi->so_tien_con_lai }}">
                                        <input type="hidden" name="redirect_direct" value="1">
                                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                                            <i class="bi bi-credit-card-2-front"></i> Thanh toán ZaloPay
                                        </button>
                                    </form>
                                    <a href="{{ route('sinh-vien.hoc-phi.payos-payment', $hocPhi->id) }}" 
                                       class="btn btn-success w-100 btn-lg">
                                        <i class="bi bi-wallet2"></i> Thanh toán PayOS
                                    </a>
                                    <a href="{{ route('sinh-vien.hoc-phi.casso-payment', $hocPhi->id) }}" 
                                       class="btn btn-info w-100 btn-lg">
                                        <i class="bi bi-bank"></i> Thanh toán chuyển khoản (Casso)
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-success mb-2">
                                    <i class="bi bi-check-circle-fill"></i> Đã hoàn thành thanh toán
                                </div>
                            @endif
                            <a href="{{ route('sinh-vien.hoc-phi.pdf', $hocPhi->id) }}" 
                               class="btn btn-outline-danger w-100 mb-2" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> In hóa đơn PDF
                            </a>
                            <a href="{{ route('sinh-vien.hoc-phi.lich-su', $hocPhi->id) }}" 
                               class="btn btn-outline-info w-100 mb-2">
                                <i class="bi bi-clock-history"></i> Lịch sử thanh toán
                            </a>
                            
                            @php
                                // Tìm giao dịch ZaloPay đang chờ xác nhận
                                $pendingZaloPay = $hocPhi->lichSuDongHocPhi()
                                    ->where('phuong_thuc_thanh_toan', 'ZaloPay')
                                    ->where('ghi_chu', 'like', '%Đang chờ%')
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                                
                                // Tìm giao dịch PayOS đang chờ xác nhận
                                $pendingPayOS = $hocPhi->lichSuDongHocPhi()
                                    ->where('phuong_thuc_thanh_toan', 'PayOS')
                                    ->where('ghi_chu', 'like', '%Đang chờ%')
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                                
                                // Tìm giao dịch Casso đang chờ xác nhận
                                // Tìm cả giao dịch có "Đang chờ" hoặc chưa có ngay_dong
                                $pendingCasso = $hocPhi->lichSuDongHocPhi()
                                    ->where('phuong_thuc_thanh_toan', 'casso')
                                    ->where(function($query) {
                                        $query->where('ghi_chu', 'like', '%Đang chờ%')
                                              ->orWhereNull('ngay_dong');
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                            @endphp
                            
                            @if($pendingZaloPay)
                                <form action="{{ route('sinh-vien.hoc-phi.zalopay-check-status', $hocPhi->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="app_trans_id" value="{{ $pendingZaloPay->ma_giao_dich }}">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Kiểm tra lại thanh toán ZaloPay
                                    </button>
                                </form>
                            @endif
                            
                            @if($pendingPayOS)
                                <form action="{{ route('sinh-vien.hoc-phi.payos-check-status', $hocPhi->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="order_code" value="{{ $pendingPayOS->ma_giao_dich }}">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Kiểm tra lại thanh toán PayOS
                                    </button>
                                </form>
                            @endif
                            
                            @if($hocPhi->so_tien_con_lai > 0)
                                {{-- Luôn hiển thị nút kiểm tra nếu còn tiền cần thanh toán --}}
                                @php
                                    // Tìm giao dịch Casso gần nhất để lấy mã (nếu có)
                                    $recentCasso = $hocPhi->lichSuDongHocPhi()
                                        ->where('phuong_thuc_thanh_toan', 'casso')
                                        ->where('created_at', '>=', now()->subDays(30))
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                    
                                    // Nếu không có, tạo mã mặc định
                                    $cassoMemo = $recentCasso ? $recentCasso->ma_giao_dich : 'HP' . $hocPhi->id;
                                @endphp
                                <form action="{{ route('sinh-vien.hoc-phi.casso-check-status', $hocPhi->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="payment_memo" value="{{ $cassoMemo }}">
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Kiểm tra lại thanh toán Casso
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('sinh-vien.hoc-phi.huong-dan') }}" 
                               class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-question-circle"></i> Hướng dẫn thanh toán
                            </a>
                            <a href="{{ route('sinh-vien.hoc-phi.index') }}" 
                               class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #4facfe 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .bg-gradient-dark {
            background: linear-gradient(135deg, #434343 0%, #000000 100%);
        }
        .card {
            border-radius: 12px;
            border: none;
        }
        .shadow-sm {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
    </style>

    @push('scripts')
    <script>
        // Tự động kiểm tra trạng thái thanh toán ZaloPay sau khi thanh toán thành công
        @php
            $hasPendingPayment = session('zalopay_app_trans_id') || (isset($pendingZaloPay) && $pendingZaloPay);
            $appTransIdValue = session('zalopay_app_trans_id') ?: (isset($pendingZaloPay) ? $pendingZaloPay->ma_giao_dich : '');
        @endphp
        @if($hasPendingPayment)
            (function() {
                const appTransId = '{{ $appTransIdValue }}';
                const hocPhiId = {{ $hocPhi->id }};
                let checkCount = 0;
                const maxChecks = 10;
                
                function checkPaymentStatus() {
                    if (checkCount >= maxChecks) {
                        console.log('Đã kiểm tra tối đa số lần, dừng lại');
                        return;
                    }
                    
                    checkCount++;
                    console.log('Đang kiểm tra trạng thái thanh toán lần ' + checkCount + '...');
                    
                    const formData = new FormData();
                    formData.append('app_trans_id', appTransId);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        formData.append('_token', csrfToken.getAttribute('content'));
                    }
                    
                    fetch('/sinh-vien/hoc-phi/' + hocPhiId + '/zalopay-check-status', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(response) {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            setTimeout(checkPaymentStatus, 30000);
                        }
                    })
                    .catch(function(error) {
                        console.error('Lỗi khi kiểm tra trạng thái:', error);
                        setTimeout(checkPaymentStatus, 30000);
                    });
                }
                
                setTimeout(checkPaymentStatus, 10000);
            })();
        @endif
    </script>
    @endpush
@endsection
