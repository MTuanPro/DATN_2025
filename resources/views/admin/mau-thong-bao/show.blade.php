@php
    $loaiThongBaoOptions = \App\Models\MauThongBaoTuDong::getLoaiThongBaoOptions();
@endphp

@extends('layouts.layout-admin')

@section('title', 'Chi tiết mẫu thông báo tự động')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Xem thông tin chi tiết mẫu thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.mau-thong-bao.index') }}">Mẫu thông báo</a></li>
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
                            <h5 class="card-title">Thông tin mẫu thông báo</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Loại thông báo:</label>
                                <p>
                                    <span class="badge bg-info">
                                        {{ $loaiThongBaoOptions[$mauThongBao->loai_thong_bao] ?? $mauThongBao->loai_thong_bao }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tiêu đề mẫu:</label>
                                <p>{{ $mauThongBao->tieu_de_mau }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nội dung mẫu:</label>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($mauThongBao->noi_dung_mau)) !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Đối tượng mặc định:</label>
                                <p>{{ $mauThongBao->doi_tuong_mac_dinh ?? 'Tất cả' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mức độ ưu tiên:</label>
                                <p>
                                    @if ($mauThongBao->muc_do_uu_tien == 'rat_quan_trong')
                                        <span class="badge bg-danger">Rất quan trọng</span>
                                    @elseif($mauThongBao->muc_do_uu_tien == 'quan_trong')
                                        <span class="badge bg-warning">Quan trọng</span>
                                    @else
                                        <span class="badge bg-secondary">Bình thường</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Cấu hình gửi:</label>
                                <p>
                                    @if ($mauThongBao->gui_email_mac_dinh)
                                        <span class="badge bg-success me-2">Email</span>
                                    @endif
                                    @if ($mauThongBao->gui_sms_mac_dinh)
                                        <span class="badge bg-info me-2">SMS</span>
                                    @endif
                                    @if (!$mauThongBao->gui_email_mac_dinh && !$mauThongBao->gui_sms_mac_dinh)
                                        <span class="text-muted">Không có</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Trạng thái:</label>
                                <p>
                                    @if ($mauThongBao->kich_hoat)
                                        <span class="badge bg-success">Bật</span>
                                    @else
                                        <span class="badge bg-secondary">Tắt</span>
                                    @endif
                                </p>
                            </div>

                            @if ($mauThongBao->ghi_chu)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ghi chú:</label>
                                    <p>{{ $mauThongBao->ghi_chu }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Thông tin bổ sung</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Ngày tạo:</strong><br>
                                <small class="text-muted">{{ $mauThongBao->created_at->format('d/m/Y H:i') }}</small>
                            </div>

                            <div class="mb-3">
                                <strong>Cập nhật lần cuối:</strong><br>
                                <small class="text-muted">{{ $mauThongBao->updated_at->format('d/m/Y H:i') }}</small>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('admin.mau-thong-bao.edit', $mauThongBao) }}" class="btn btn-warning w-100 mb-2">
                                    <i class="bi bi-pencil"></i> Sửa mẫu
                                </a>
                                <a href="{{ route('admin.mau-thong-bao.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

