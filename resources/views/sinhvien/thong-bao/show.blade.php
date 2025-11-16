@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết thông báo</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.thong-bao.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <!-- Header -->
                            <div class="mb-4">
                                <h2 class="mb-3">{{ $thongBao->tieu_de }}</h2>
                                <div class="d-flex align-items-center text-muted mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <span>{{ $thongBao->ngay_gui ? $thongBao->ngay_gui->format('d/m/Y H:i') : '' }}</span>
                                    @if ($thongBao->nguoiGui)
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-person me-2"></i>
                                        <span>{{ $thongBao->nguoiGui->name ?? 'Hệ thống' }}</span>
                                    @endif
                                </div>
                                <div>
                                    @php
                                        $loaiColors = [
                                            'tin_gap' => 'danger',
                                            'lich_thi' => 'warning',
                                            'diem' => 'success',
                                            'hoc_phi' => 'primary',
                                            'lich_hoc' => 'info',
                                            'dang_ky_mon' => 'warning',
                                            'tin_tuc' => 'secondary',
                                            'thong_bao_chung' => 'dark',
                                        ];
                                        $mucDoColors = [
                                            'rat_quan_trong' => 'danger',
                                            'quan_trong' => 'warning',
                                            'binh_thuong' => 'info',
                                        ];
                                        $loaiLabels = [
                                            'tin_gap' => 'Tin gấp',
                                            'lich_thi' => 'Lịch thi',
                                            'diem' => 'Điểm',
                                            'hoc_phi' => 'Học phí',
                                            'lich_hoc' => 'Lịch học',
                                            'dang_ky_mon' => 'Đăng ký môn',
                                            'tin_tuc' => 'Tin tức',
                                            'thong_bao_chung' => 'Thông báo chung',
                                        ];
                                        $mucDoLabels = [
                                            'rat_quan_trong' => 'Rất quan trọng',
                                            'quan_trong' => 'Quan trọng',
                                            'binh_thuong' => 'Bình thường',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $loaiColors[$thongBao->loai_thong_bao] ?? 'secondary' }} me-1">
                                        {{ $loaiLabels[$thongBao->loai_thong_bao] ?? $thongBao->loai_thong_bao }}
                                    </span>
                                    <span class="badge bg-{{ $mucDoColors[$thongBao->muc_do_quan_trong] ?? 'info' }}">
                                        {{ $mucDoLabels[$thongBao->muc_do_quan_trong] ?? $thongBao->muc_do_quan_trong }}
                                    </span>
                                    @if ($thongBao->ghim_dau_trang)
                                        <span class="badge bg-warning">
                                            <i class="bi bi-pin-angle-fill"></i> Ghim đầu trang
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <!-- Ảnh đại diện -->
                            @if ($thongBao->anh_dai_dien)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($thongBao->anh_dai_dien) }}" alt="Ảnh đại diện"
                                        class="img-fluid rounded">
                                </div>
                            @endif

                            <!-- Nội dung -->
                            <div class="content mb-4" style="font-size: 1.05rem; line-height: 1.8;">
                                {!! nl2br(e($thongBao->noi_dung)) !!}
                            </div>

                            <!-- File đính kèm -->
                            @if ($thongBao->file_dinh_kem)
                                <div class="alert alert-light d-flex align-items-center">
                                    <i class="bi bi-paperclip fs-4 me-3"></i>
                                    <div>
                                        <strong>File đính kèm:</strong><br>
                                        <a href="{{ Storage::url($thongBao->file_dinh_kem) }}" target="_blank"
                                            class="btn btn-sm btn-primary mt-2">
                                            <i class="bi bi-download"></i> Tải xuống
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('sinh-vien.thong-bao.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>

                                @if ($nguoiNhan && !$nguoiNhan->da_doc)
                                    <form action="{{ route('sinh-vien.thong-bao.mark-read', $thongBao->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Đánh dấu đã đọc
                                        </button>
                                    </form>
                                @else
                                    <div class="text-success">
                                        <i class="bi bi-check-circle-fill"></i> Đã đọc
                                        @if ($nguoiNhan && $nguoiNhan->ngay_doc)
                                            <br><small
                                                class="text-muted">{{ $nguoiNhan->ngay_doc->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
