@extends('layouts.layout-daotao')

@section('title', 'Chi tiết Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết học phí sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
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
                            <h4>Thông tin sinh viên</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>MSSV:</strong></td>
                                    <td>{{ $hocPhi->sinhVien->ma_sinh_vien }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $hocPhi->sinhVien->ho_ten }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ:</strong></td>
                                    <td>{{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hạn đóng:</strong></td>
                                    <td><span class="badge bg-warning">{{ $hocPhi->han_dong->format('d/m/Y') }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
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
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Chi tiết học phí từng môn</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã môn</th>
                                            <th>Tên môn</th>
                                            <th>Số tín chỉ</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($hocPhi->chiTietHocPhi as $index => $ct)
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
                                    <td><strong>Tổng học phí:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Đã đóng:</strong></td>
                                    <td class="text-end text-success"><strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td><strong>Còn lại:</strong></td>
                                    <td class="text-end text-danger"><h4>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</h4></td>
                                </tr>
                            </table>

                            @if ($hocPhi->so_tien_con_lai > 0)
                                <a href="{{ route('dao-tao.hoc-phi.payment', $hocPhi->id) }}" class="btn btn-success w-100 mt-3">
                                    <i class="bi bi-cash"></i> Ghi nhận thanh toán
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Lịch sử đóng học phí</h4>
                        </div>
                        <div class="card-body">
                            @forelse ($hocPhi->lichSuDongHocPhi as $ls)
                                <div class="mb-3 pb-3" style="border-bottom: 1px solid #ddd;">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-success">{{ number_format($ls->so_tien, 0, ',', '.') }} đ</strong>
                                        <small class="text-muted">{{ $ls->ngay_dong->format('d/m/Y') }}</small>
                                    </div>
                                    <small>{{ $ls->phuong_thuc_thanh_toan }}</small>
                                    @if ($ls->ghi_chu)
                                        <p class="mb-0 small text-muted">{{ $ls->ghi_chu }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted">Chưa có lịch sử đóng học phí</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
