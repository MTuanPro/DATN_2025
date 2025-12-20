@extends('layouts.' . $layout)

@section('title', 'Chi tiết thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết thông báo</h3>
                    <p class="text-subtitle text-muted">Xem thông tin chi tiết thông báo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            @php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $dashboardRoute = in_array('admin', $roles)
                                    ? 'admin.dashboard'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.dashboard'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.dashboard'
                                            : 'sinh-vien.dashboard'));
                                $indexRoute = in_array('admin', $roles)
                                    ? 'admin.thong-bao.index'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.index'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.index'
                                            : 'sinh-vien.thong-bao.index'));
                            @endphp
                            <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route($indexRoute) }}">Thông báo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ $thongBao->tieu_de }}</h5>
                        <div>
                            @php
                                $roles = auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray();
                                $isAdmin = in_array('admin', $roles);
                                $indexRoute = $isAdmin
                                    ? 'admin.thong-bao.index'
                                    : (in_array('truong_phong_dt', $roles) || in_array('nhan_vien_dt', $roles)
                                        ? 'dao-tao.thong-bao.index'
                                        : (in_array('giang_vien', $roles)
                                            ? 'giangvien.thong-bao.index'
                                            : 'sinh-vien.thong-bao.index'));
                            @endphp

                            @if ($isAdmin)
                                <a href="{{ route('admin.thong-bao.edit', $thongBao) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Sửa
                                </a>
                            @endif

                            <a href="{{ route($indexRoute) }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            {{-- Nội dung --}}
                            <div class="mb-4">
                                <h6>Nội dung:</h6>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($thongBao->noi_dung)) !!}
                                </div>
                            </div>

                            {{-- Ảnh đại diện --}}
                            @if ($thongBao->anh_dai_dien)
                                <div class="mb-4">
                                    <h6>Ảnh đại diện:</h6>
                                    <img src="{{ asset('storage/' . $thongBao->anh_dai_dien) }}" alt="Ảnh đại diện"
                                        class="img-fluid rounded shadow" style="max-height: 400px;">
                                </div>
                            @endif

                            {{-- File đính kèm --}}
                            @if ($thongBao->file_dinh_kem)
                                <div class="mb-4">
                                    <h6>File đính kèm:</h6>
                                    <a href="{{ route('admin.thong-bao.download', $thongBao) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="bi bi-download"></i> Tải xuống file
                                        ({{ basename($thongBao->file_dinh_kem) }})
                                    </a>
                                </div>
                            @endif
                            <!-- sửa giao diện -->
                            {{-- Thống kê người nhận --}}
                            <div class="mb-4">
                                <h6>Thống kê người nhận:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Tổng số người nhận</h6>
                                                <h3>{{ $thongBao->nguoiNhan->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Đã xem</h6>
                                                <h3>{{ $thongBao->nguoiNhan->where('da_doc', true)->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">
                                                <h6 class="card-title">Chưa xem</h6>
                                                <h3>{{ $thongBao->nguoiNhan->where('da_doc', false)->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Thông tin cơ bản --}}
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Thông tin</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Loại thông báo:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $thongBao->getLoaiColor() }}">
                                                    {{ str_replace('_', ' ', ucfirst($thongBao->loai_thong_bao)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mức độ:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $thongBao->getMucDoColor() }}">
                                                    {{ str_replace('_', ' ', ucfirst($thongBao->muc_do_quan_trong)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Đối tượng:</strong></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ str_replace('_', ' ', ucfirst($thongBao->doi_tuong)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Người gửi:</strong></td>
                                            <td>{{ $thongBao->nguoiGui->name ?? 'Hệ thống' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày gửi:</strong></td>
                                            <td>{{ $thongBao->ngay_gui->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                @if ($thongBao->trang_thai == 'cong_khai')
                                                    <span class="badge bg-success">Công khai</span>
                                                @elseif($thongBao->trang_thai == 'nhap')
                                                    <span class="badge bg-warning">Nháp</span>
                                                @else
                                                    <span class="badge bg-danger">Đã xóa</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lượt xem:</strong></td>
                                            <td>{{ $thongBao->so_luot_xem }}</td>
                                        </tr>
                                        @if ($thongBao->ghim_dau_trang)
                                            <tr>
                                                <td colspan="2">
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-pin-angle-fill"></i> Đã ghim
                                                    </span>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($thongBao->hien_thi_tu_ngay)
                                            <tr>
                                                <td><strong>Hiển thị từ:</strong></td>
                                                <td>{{ $thongBao->hien_thi_tu_ngay->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endif
                                        @if ($thongBao->ngay_het_han)
                                            <tr>
                                                <td><strong>Hết hạn:</strong></td>
                                                <td>{{ $thongBao->ngay_het_han->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Danh sách người nhận --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <ul class="nav nav-tabs card-header-tabs border-0" id="nguoiNhanTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active px-4" id="da-xem-tab" data-bs-toggle="tab"
                                                data-bs-target="#da-xem" type="button" role="tab">
                                                <i class="bi bi-check-circle-fill text-success"></i> 
                                                <strong>Đã xem</strong>
                                                <span class="badge bg-success ms-2">{{ $thongBao->nguoiNhan->where('da_doc', true)->count() }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link px-4" id="chua-xem-tab" data-bs-toggle="tab"
                                                data-bs-target="#chua-xem" type="button" role="tab">
                                                <i class="bi bi-clock-fill text-warning"></i> 
                                                <strong>Chưa xem</strong>
                                                <span class="badge bg-warning ms-2">{{ $thongBao->nguoiNhan->where('da_doc', false)->count() }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link px-4" id="tat-ca-tab" data-bs-toggle="tab"
                                                data-bs-target="#tat-ca" type="button" role="tab">
                                                <i class="bi bi-people-fill text-primary"></i> 
                                                <strong>Tất cả</strong>
                                                <span class="badge bg-primary ms-2">{{ $thongBao->nguoiNhan->count() }}</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-4">
                                    <div class="tab-content" id="nguoiNhanTabContent">
                                        {{-- Tab Đã xem --}}
                                        <div class="tab-pane fade show active" id="da-xem" role="tabpanel">
                                            {{-- Tìm kiếm --}}
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white">
                                                            <i class="bi bi-search"></i>
                                                        </span>
                                                        <input type="text" id="searchDaXem" class="form-control border-start-0"
                                                            placeholder="Tìm kiếm người đã xem...">
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Bảng danh sách đã xem --}}
                                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                                <table class="table table-hover align-middle">
                                                    <thead class="table-success" style="position: sticky; top: 0; z-index: 10;">
                                                        <tr>
                                                            <th style="width: 60px" class="text-center">
                                                                <i class="bi bi-hash"></i>
                                                            </th>
                                                            <th style="width: 35%">
                                                                <i class="bi bi-person-fill"></i> Người nhận
                                                            </th>
                                                            <th style="width: 35%">
                                                                <i class="bi bi-envelope-fill"></i> Email
                                                            </th>
                                                            <th style="width: 30%" class="text-center">
                                                                <i class="bi bi-clock-history"></i> Thời gian xem
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="daXemBody">
                                                        @php $stt = 1; @endphp
                                                        @forelse ($thongBao->nguoiNhan->where('da_doc', true)->sortByDesc('thoi_gian_doc') as $nguoiNhan)
                                                            <tr class="da-xem-row">
                                                                <td class="text-center">
                                                                    <span class="badge bg-light text-dark">{{ $stt++ }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        @if ($nguoiNhan->nguoiNhan && $nguoiNhan->nguoiNhan->anh_dai_dien)
                                                                            <img src="{{ asset('storage/' . $nguoiNhan->nguoiNhan->anh_dai_dien) }}"
                                                                                class="rounded-circle me-3 border border-success" 
                                                                                width="40" height="40" 
                                                                                alt="Avatar"
                                                                                style="object-fit: cover;">
                                                                        @else
                                                                            <div class="avatar bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                                                style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                                                                {{ strtoupper(substr($nguoiNhan->nguoiNhan->ho_ten ?? 'U', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <strong class="da-xem-ten">{{ $nguoiNhan->nguoiNhan->ho_ten ?? 'N/A' }}</strong>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="da-xem-email text-muted">
                                                                        <i class="bi bi-envelope"></i> {{ $nguoiNhan->nguoiNhan->email ?? 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                                                        <i class="bi bi-clock-history"></i>
                                                                        {{ \Carbon\Carbon::parse($nguoiNhan->thoi_gian_doc)->format('d/m/Y H:i') }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-5">
                                                                    <div class="text-muted">
                                                                        <i class="bi bi-eye-slash fs-1 d-block mb-3 opacity-50"></i>
                                                                        <h5>Chưa có người xem</h5>
                                                                        <p class="mb-0">Thông báo này chưa được ai xem</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- Tab Chưa xem --}}
                                        <div class="tab-pane fade" id="chua-xem" role="tabpanel">
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white">
                                                            <i class="bi bi-search"></i>
                                                        </span>
                                                        <input type="text" id="searchChuaXem" class="form-control border-start-0"
                                                            placeholder="Tìm kiếm người chưa xem...">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                                <table class="table table-hover align-middle">
                                                    <thead class="table-warning" style="position: sticky; top: 0; z-index: 10;">
                                                        <tr>
                                                            <th style="width: 60px" class="text-center">
                                                                <i class="bi bi-hash"></i>
                                                            </th>
                                                            <th style="width: 40%">
                                                                <i class="bi bi-person-fill"></i> Người nhận
                                                            </th>
                                                            <th style="width: 40%">
                                                                <i class="bi bi-envelope-fill"></i> Email
                                                            </th>
                                                            <th style="width: 20%" class="text-center">
                                                                <i class="bi bi-info-circle"></i> Trạng thái
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="chuaXemBody">
                                                        @php $stt = 1; @endphp
                                                        @forelse ($thongBao->nguoiNhan->where('da_doc', false) as $nguoiNhan)
                                                            <tr class="chua-xem-row">
                                                                <td class="text-center">
                                                                    <span class="badge bg-light text-dark">{{ $stt++ }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        @if ($nguoiNhan->nguoiNhan && $nguoiNhan->nguoiNhan->anh_dai_dien)
                                                                            <img src="{{ asset('storage/' . $nguoiNhan->nguoiNhan->anh_dai_dien) }}"
                                                                                class="rounded-circle me-3 border border-warning" 
                                                                                width="40" height="40" 
                                                                                alt="Avatar"
                                                                                style="object-fit: cover;">
                                                                        @else
                                                                            <div class="avatar bg-warning text-dark rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                                                style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                                                                {{ strtoupper(substr($nguoiNhan->nguoiNhan->ho_ten ?? 'U', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <strong class="chua-xem-ten">{{ $nguoiNhan->nguoiNhan->ho_ten ?? 'N/A' }}</strong>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="chua-xem-email text-muted">
                                                                        <i class="bi bi-envelope"></i> {{ $nguoiNhan->nguoiNhan->email ?? 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-warning px-3 py-2">
                                                                        <i class="bi bi-clock"></i> Chưa xem
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-5">
                                                                    <div class="text-success">
                                                                        <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
                                                                        <h5>Tất cả đã xem!</h5>
                                                                        <p class="mb-0">Mọi người đã xem thông báo này</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- Tab Tất cả --}}
                                        <div class="tab-pane fade" id="tat-ca" role="tabpanel">
                                            {{-- Bộ lọc --}}
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <select id="filterTrangThai" class="form-select">
                                                        <option value="">
                                                            <i class="bi bi-funnel"></i> Tất cả trạng thái
                                                        </option>
                                                        <option value="da_doc">✓ Đã xem</option>
                                                        <option value="chua_doc">⏰ Chưa xem</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white">
                                                            <i class="bi bi-search"></i>
                                                        </span>
                                                        <input type="text" id="searchNguoiNhan" class="form-control border-start-0"
                                                            placeholder="Tìm kiếm người nhận...">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Bảng danh sách tất cả --}}
                                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                                <table class="table table-hover align-middle" id="tableNguoiNhan">
                                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                                        <tr>
                                                            <th style="width: 60px" class="text-center">
                                                                <i class="bi bi-hash"></i>
                                                            </th>
                                                            <th style="width: 30%">
                                                                <i class="bi bi-person-fill"></i> Người nhận
                                                            </th>
                                                            <th style="width: 30%">
                                                                <i class="bi bi-envelope-fill"></i> Email
                                                            </th>
                                                            <th style="width: 20%" class="text-center">
                                                                <i class="bi bi-info-circle"></i> Trạng thái
                                                            </th>
                                                            <th style="width: 20%" class="text-center">
                                                                <i class="bi bi-clock-history"></i> Thời gian xem
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($thongBao->nguoiNhan->sortByDesc(function($item) { return $item->da_doc ? $item->thoi_gian_doc : null; }) as $index => $nguoiNhan)
                                                            <tr class="nguoi-nhan-row"
                                                                data-trang-thai="{{ $nguoiNhan->da_doc ? 'da_doc' : 'chua_doc' }}">
                                                                <td class="text-center">
                                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        @if ($nguoiNhan->nguoiNhan && $nguoiNhan->nguoiNhan->anh_dai_dien)
                                                                            <img src="{{ asset('storage/' . $nguoiNhan->nguoiNhan->anh_dai_dien) }}"
                                                                                class="rounded-circle me-3 border {{ $nguoiNhan->da_doc ? 'border-success' : 'border-warning' }}" 
                                                                                width="40" height="40" 
                                                                                alt="Avatar"
                                                                                style="object-fit: cover;">
                                                                        @else
                                                                            <div class="avatar {{ $nguoiNhan->da_doc ? 'bg-success' : 'bg-warning' }} text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                                                style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                                                                {{ strtoupper(substr($nguoiNhan->nguoiNhan->ho_ten ?? 'U', 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <strong class="nguoi-nhan-ten">{{ $nguoiNhan->nguoiNhan->ho_ten ?? 'N/A' }}</strong>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="nguoi-nhan-email text-muted">
                                                                        <i class="bi bi-envelope"></i> {{ $nguoiNhan->nguoiNhan->email ?? 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($nguoiNhan->da_doc)
                                                                        <span class="badge bg-success px-3 py-2">
                                                                            <i class="bi bi-check-circle"></i> Đã xem
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-warning px-3 py-2">
                                                                            <i class="bi bi-clock"></i> Chưa xem
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($nguoiNhan->da_doc && $nguoiNhan->thoi_gian_doc)
                                                                        <small class="text-muted">
                                                                            <i class="bi bi-clock-history"></i>
                                                                            {{ \Carbon\Carbon::parse($nguoiNhan->thoi_gian_doc)->format('d/m/Y H:i') }}
                                                                        </small>
                                                                    @else
                                                                        <small class="text-muted">-</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-5">
                                                                    <div class="text-muted">
                                                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                                                        <h5>Chưa có người nhận</h5>
                                                                        <p class="mb-0">Danh sách người nhận trống</p>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter cho tab "Tất cả"
            const filterTrangThai = document.getElementById('filterTrangThai');
            const searchNguoiNhan = document.getElementById('searchNguoiNhan');
            const rows = document.querySelectorAll('.nguoi-nhan-row');

            function filterTableTatCa() {
                const trangThaiFilter = filterTrangThai?.value || '';
                const searchText = searchNguoiNhan?.value.toLowerCase() || '';

                rows.forEach(row => {
                    const trangThai = row.dataset.trangThai;
                    const ten = row.querySelector('.nguoi-nhan-ten')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('.nguoi-nhan-email')?.textContent.toLowerCase() || '';

                    const matchTrangThai = !trangThaiFilter || trangThai === trangThaiFilter;
                    const matchSearch = !searchText || ten.includes(searchText) || email.includes(searchText);

                    row.style.display = matchTrangThai && matchSearch ? '' : 'none';
                });
            }

            if (filterTrangThai) filterTrangThai.addEventListener('change', filterTableTatCa);
            if (searchNguoiNhan) searchNguoiNhan.addEventListener('input', filterTableTatCa);

            // Filter cho tab "Đã xem"
            const searchDaXem = document.getElementById('searchDaXem');
            const rowsDaXem = document.querySelectorAll('.da-xem-row');

            if (searchDaXem) {
                searchDaXem.addEventListener('input', function() {
                    const searchText = this.value.toLowerCase();
                    
                    rowsDaXem.forEach(row => {
                        const ten = row.querySelector('.da-xem-ten')?.textContent.toLowerCase() || '';
                        const email = row.querySelector('.da-xem-email')?.textContent.toLowerCase() || '';
                        const match = !searchText || ten.includes(searchText) || email.includes(searchText);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }

            // Filter cho tab "Chưa xem"
            const searchChuaXem = document.getElementById('searchChuaXem');
            const rowsChuaXem = document.querySelectorAll('.chua-xem-row');

            if (searchChuaXem) {
                searchChuaXem.addEventListener('input', function() {
                    const searchText = this.value.toLowerCase();
                    
                    rowsChuaXem.forEach(row => {
                        const ten = row.querySelector('.chua-xem-ten')?.textContent.toLowerCase() || '';
                        const email = row.querySelector('.chua-xem-email')?.textContent.toLowerCase() || '';
                        const match = !searchText || ten.includes(searchText) || email.includes(searchText);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endpush
