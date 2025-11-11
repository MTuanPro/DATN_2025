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
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.hoc-phi.index') }}">Học phí</a></li>
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
                                        @foreach ($hocPhi->chiTietHocPhiMon as $index => $ct)
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
                                    <td>Tổng học phí:</td>
                                    <td class="text-end"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }}
                                            đ</strong></td>
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

                            <a href="{{ route('sinhvien.hoc-phi.lich-su', $hocPhi->id) }}" class="btn btn-info w-100 mt-2">
                                <i class="bi bi-clock-history"></i> Xem lịch sử đóng
                            </a>
                            <a href="{{ route('sinhvien.hoc-phi.huong-dan') }}" class="btn btn-success w-100 mt-2">
                                <i class="bi bi-question-circle"></i> Hướng dẫn nộp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
