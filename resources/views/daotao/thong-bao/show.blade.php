@extends('layouts.layout-daotao')

@section('title', 'Chi tiết thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        <i class="bi bi-file-earmark-text-fill text-success"></i> Chi tiết thông báo
                    </h3>
                    <p class="text-subtitle text-muted mb-0">
                        <span class="badge bg-light-success">Đào tạo</span>
                        @if($thongBao->loai_nguon == 'tu_dong')
                            <span class="badge bg-light-primary">Tự động</span>
                        @else
                            <span class="badge bg-light-info">Thủ công</span>
                        @endif
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.thong-bao.index') }}">Thông báo</a></li>
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
                <div class="card border-success">
                    <div class="card-header bg-light-success d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-bell-fill"></i> {{ $thongBao->tieu_de }}
                        </h4>
                        <div>
                            @if($thongBao->loai_nguon == 'thu_cong')
                                <a href="{{ route('dao-tao.thong-bao.edit', $thongBao->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                </a>
                                <form action="{{ route('dao-tao.thong-bao.destroy', $thongBao->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn xóa thông báo này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash-fill"></i> Xóa
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-info">
                                    <i class="bi bi-robot"></i> Thông báo tự động - không thể chỉnh sửa
                                </span>
                            @endif
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
                                <p>
                                    <i class="bi bi-person-circle text-success"></i> 
                                    <strong>Người gửi:</strong> 
                                    @if($thongBao->nguoiGui)
                                        @if($thongBao->nguoiGui->id == Auth::id())
                                            <span class="badge bg-light-success">Bạn</span> ({{ $thongBao->nguoiGui->name }})
                                        @else
                                            {{ $thongBao->nguoiGui->name }}
                                        @endif
                                    @else
                                        <span class="badge bg-light-primary">
                                            <i class="bi bi-robot"></i> Hệ thống tự động
                                        </span>
                                    @endif
                                </p>
                                <p>
                                    <i class="bi bi-tag-fill text-success"></i>
                                    <strong>Loại nguồn:</strong>
                                    @if ($thongBao->loai_nguon == 'tu_dong')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-gear-fill"></i> Tự động
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="bi bi-pencil-fill"></i> Thủ công
                                        </span>
                                    @endif
                                </p>
                                <p>
                                    <i class="bi bi-calendar-check text-success"></i>
                                    <strong>Ngày gửi:</strong>
                                    {{ $thongBao->ngay_gui ? $thongBao->ngay_gui->format('d/m/Y H:i') : 'Chưa gửi' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Đối tượng:</strong>
                                    @if ($thongBao->doi_tuong == 'all')
                                        Tất cả
                                    @elseif($thongBao->doi_tuong == 'sinh_vien')
                                        Sinh viên
                                    @elseif($thongBao->doi_tuong == 'giang_vien')
                                        Giảng viên
                                    @elseif($thongBao->doi_tuong == 'lop_hanh_chinh')
                                        Lớp hành chính
                                    @elseif($thongBao->doi_tuong == 'lop_hoc_phan')
                                        Lớp học phần
                                    @else
                                        {{ $thongBao->doi_tuong }}
                                    @endif
                                </p>
                                <p><strong>Lượt xem:</strong> {{ $thongBao->so_luot_xem ?? 0 }}</p>
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
                <div class="card border-success">
                    <div class="card-header bg-light-success">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people-fill"></i> Danh sách người nhận
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter -->
                        <form method="GET" class="mb-3">
                            <label class="form-label small">
                                <i class="bi bi-funnel"></i> Lọc theo trạng thái
                            </label>
                            <select name="trang_thai" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">📋 Tất cả</option>
                                <option value="da_doc" {{ request('trang_thai') == 'da_doc' ? 'selected' : '' }}>
                                    ✅ Đã đọc
                                </option>
                                <option value="chua_doc" {{ request('trang_thai') == 'chua_doc' ? 'selected' : '' }}>
                                    ⏳ Chưa đọc
                                </option>
                            </select>
                        </form>

                        <div class="list-group" style="max-height: 600px; overflow-y: auto;">
                            @forelse($nguoiNhans as $nguoiNhan)
                                @if($nguoiNhan->nguoiNhan)
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $nguoiNhan->nguoiNhan->name }}</div>
                                            <small class="text-muted">{{ $nguoiNhan->nguoiNhan->email }}</small>
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
                                @else
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold text-muted">
                                                <i class="bi bi-exclamation-triangle"></i> Người dùng không tồn tại
                                            </div>
                                            <small class="text-muted">ID: {{ $nguoiNhan->nguoi_nhan_id }}</small>
                                        </div>
                                        <span class="badge bg-warning rounded-pill">Lỗi</span>
                                    </div>
                                @endif
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
