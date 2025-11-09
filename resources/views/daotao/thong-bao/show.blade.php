@extends('layouts.layout-daotao')

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
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('daotao.thong-bao.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-light-primary">
                    <div class="card-body px-3 py-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Tổng người nhận</h6>
                                <h6 class="font-extrabold mb-0">{{ $tongNguoiNhan }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-light-success">
                    <div class="card-body px-3 py-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldShow"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Đã đọc</h6>
                                <h6 class="font-extrabold mb-0">{{ $daDoc }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-light-warning">
                    <div class="card-body px-3 py-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon orange mb-2">
                                    <i class="iconly-boldHide"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Chưa đọc</h6>
                                <h6 class="font-extrabold mb-0">{{ $chuaDoc }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-light-info">
                    <div class="card-body px-3 py-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldMessage"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Đã gửi email</h6>
                                <h6 class="font-extrabold mb-0">{{ $daGuiEmail }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Nội dung thông báo -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ $thongBao->tieu_de }}</h4>
                        <div>
                            <a href="{{ route('daotao.thong-bao.edit', $thongBao->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <form action="{{ route('daotao.thong-bao.destroy', $thongBao->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-{{ $thongBao->loai_badge }}">{{ $thongBao->loai_thong_bao }}</span>
                            <span class="badge bg-{{ $thongBao->muc_do_badge }}">{{ $thongBao->muc_do_quan_trong }}</span>
                            @if ($thongBao->ghim_dau_trang)
                                <span class="badge bg-info"><i class="bi bi-pin-angle-fill"></i> Ghim</span>
                            @endif
                        </div>

                        @if ($thongBao->anh_dai_dien)
                            <div class="mb-3">
                                <img src="{{ Storage::url($thongBao->anh_dai_dien) }}" alt="Ảnh đại diện"
                                    class="img-fluid rounded">
                            </div>
                        @endif

                        <div class="content mb-3" style="white-space: pre-line;">
                            {{ $thongBao->noi_dung }}
                        </div>

                        @if ($thongBao->file_dinh_kem)
                            <div class="alert alert-light">
                                <i class="bi bi-paperclip"></i>
                                <a href="{{ Storage::url($thongBao->file_dinh_kem) }}" target="_blank">
                                    File đính kèm
                                </a>
                            </div>
                        @endif

                        <hr>

                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p><strong>Người gửi:</strong> {{ $thongBao->daoTao->ho_ten ?? 'N/A' }}</p>
                                <p><strong>Ngày gửi:</strong>
                                    {{ $thongBao->ngay_gui ? $thongBao->ngay_gui->format('d/m/Y H:i') : 'Chưa gửi' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Đối tượng:</strong> {{ $thongBao->doi_tuong }}</p>
                                @if ($thongBao->ngay_het_han)
                                    <p><strong>Hết hạn:</strong> {{ $thongBao->ngay_het_han->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách người nhận -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Danh sách người nhận</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter -->
                        <form method="GET" class="mb-3">
                            <select name="trang_thai" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Tất cả</option>
                                <option value="da_doc" {{ request('trang_thai') == 'da_doc' ? 'selected' : '' }}>Đã đọc
                                </option>
                                <option value="chua_doc" {{ request('trang_thai') == 'chua_doc' ? 'selected' : '' }}>Chưa
                                    đọc</option>
                            </select>
                        </form>

                        <div class="list-group" style="max-height: 600px; overflow-y: auto;">
                            @forelse($nguoiNhans as $nguoiNhan)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $nguoiNhan->user->name }}</div>
                                        <small class="text-muted">{{ $nguoiNhan->user->email }}</small>
                                        @if ($nguoiNhan->da_doc)
                                            <br><small class="text-success">
                                                <i class="bi bi-check-circle"></i>
                                                {{ $nguoiNhan->ngay_doc ? $nguoiNhan->ngay_doc->format('d/m/Y H:i') : '' }}
                                            </small>
                                        @else
                                            <br><small class="text-muted">
                                                <i class="bi bi-circle"></i> Chưa đọc
                                            </small>
                                        @endif
                                    </div>
                                    @if ($nguoiNhan->da_doc)
                                        <span class="badge bg-success rounded-pill">Đã đọc</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Chưa đọc</span>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    Không có người nhận
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3">
                            {{ $nguoiNhans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
