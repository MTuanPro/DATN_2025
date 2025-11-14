@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử đóng học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử đóng học phí</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết lịch sử đóng học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử</li>
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
                            <h4>Lịch sử thanh toán - {{ $hocPhi->hocKy->ten_hoc_ky }}</h4>
                        </div>
                        <div class="card-body">
                            @forelse ($hocPhi->lichSuDongHocPhi as $ls)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5 class="text-success">{{ number_format($ls->so_tien, 0, ',', '.') }} đ</h5>
                                                <p class="mb-1"><strong>Ngày đóng:</strong> {{ $ls->ngay_dong->format('d/m/Y H:i') }}</p>
                                                <p class="mb-1"><strong>Phương thức:</strong> {{ $ls->phuong_thuc_thanh_toan }}</p>
                                                @if ($ls->ghi_chu)
                                                    <p class="mb-0"><strong>Ghi chú:</strong> {{ $ls->ghi_chu }}</p>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-end">
                                                @if ($ls->bien_lai_path)
                                                    <a href="{{ Storage::url($ls->bien_lai_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark-pdf"></i> Xem biên lai
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Chưa có lịch sử đóng học phí cho học kỳ này
                                </div>
                            @endforelse
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
                                    <td class="text-end"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr>
                                    <td>Đã đóng:</td>
                                    <td class="text-end text-success"><strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
                                </tr>
                                <tr style="border-top: 2px solid #ddd;">
                                    <td>Còn lại:</td>
                                    <td class="text-end text-danger"><h4>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</h4></td>
                                </tr>
                            </table>

                            <a href="{{ route('sinh-vien.hoc-phi.show', $hocPhi->id) }}" class="btn btn-secondary w-100 mt-2">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

