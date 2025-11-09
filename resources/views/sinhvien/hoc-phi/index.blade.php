@extends('layouts.layout-sinhvien')

@section('title', 'Học phí của tôi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Học phí của tôi</h3>
                    <p class="text-subtitle text-muted">Xem công nợ học phí - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học phí</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Tổng học phí</h6>
                            <h3>{{ number_format($tongHocPhi, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Đã đóng</h6>
                            <h3>{{ number_format($daDong, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6>Còn lại</h6>
                            <h3>{{ number_format($conLai, 0, ',', '.') }} đ</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Học kỳ</th>
                                    <th>Tín chỉ</th>
                                    <th>Tổng học phí</th>
                                    <th>Đã đóng</th>
                                    <th>Còn lại</th>
                                    <th>Hạn đóng</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hocPhis as $index => $hp)
                                    <tr>
                                        <td>{{ $hocPhis->firstItem() + $index }}</td>
                                        <td><strong>{{ $hp->hocKy->ten_hoc_ky }} - {{ $hp->hocKy->nam_hoc }}</strong></td>
                                        <td><span class="badge bg-primary">{{ $hp->tong_tin_chi_dang_ky }} TC</span></td>
                                        <td>{{ number_format($hp->tong_so_tien, 0, ',', '.') }} đ</td>
                                        <td class="text-success">{{ number_format($hp->so_tien_da_dong, 0, ',', '.') }} đ</td>
                                        <td class="text-danger">{{ number_format($hp->so_tien_con_lai, 0, ',', '.') }} đ</td>
                                        <td>{{ $hp->han_dong->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($hp->trang_thai == 'da_nop_du')
                                                <span class="badge bg-success">Đã nộp đủ</span>
                                            @elseif ($hp->trang_thai == 'qua_han')
                                                <span class="badge bg-danger">Quá hạn</span>
                                            @else
                                                <span class="badge bg-warning">Chưa nộp đủ</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('sinh-vien.hoc-phi.show', $hp->id) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <p class="text-muted">Chưa có dữ liệu học phí</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($hocPhis->hasPages())
                        {{ $hocPhis->links() }}
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

