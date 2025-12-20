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

                    {{-- Danh sách người nhận (chỉ hiển thị cho Admin) --}}
                    @if (in_array('admin', auth()->user()->vaiTro()->pluck('ma_vai_tro')->toArray()))
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-people"></i> Danh sách người nhận
                                            <span class="badge bg-primary">{{ $thongBao->nguoiNhan->count() }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        {{-- Bộ lọc --}}
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <select id="filterTrangThai" class="form-select form-select-sm">
                                                    <option value="">-- Tất cả trạng thái --</option>
                                                    <option value="da_doc">Đã đọc</option>
                                                    <option value="chua_doc">Chưa đọc</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" id="searchNguoiNhan"
                                                    class="form-control form-control-sm" placeholder="Tìm người nhận...">
                                            </div>
                                        </div>

                                        {{-- Bảng danh sách --}}
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-hover table-sm" id="tableNguoiNhan">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th style="width: 50px">STT</th>
                                                        <th>Người nhận</th>
                                                        <th>Email</th>
                                                        <th style="width: 120px">Trạng thái</th>
                                                        <th style="width: 150px">Thời gian đọc</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($thongBao->nguoiNhan as $index => $nguoiNhan)
                                                        <tr class="nguoi-nhan-row"
                                                            data-trang-thai="{{ $nguoiNhan->da_doc ? 'da_doc' : 'chua_doc' }}">
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if ($nguoiNhan->nguoiNhan && $nguoiNhan->nguoiNhan->avatar)
                                                                        <img src="{{ asset('storage/' . $nguoiNhan->nguoiNhan->avatar) }}"
                                                                            class="rounded-circle me-2" width="32"
                                                                            height="32" alt="Avatar">
                                                                    @else
                                                                        <div class="avatar avatar-sm bg-secondary me-2">
                                                                            <span
                                                                                class="avatar-content">{{ substr($nguoiNhan->nguoiNhan->name ?? 'U', 0, 1) }}</span>
                                                                        </div>
                                                                    @endif
                                                                    <span
                                                                        class="nguoi-nhan-ten">{{ $nguoiNhan->nguoiNhan->name ?? 'N/A' }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="nguoi-nhan-email">
                                                                {{ $nguoiNhan->nguoiNhan->email ?? 'N/A' }}</td>
                                                            <td>
                                                                @if ($nguoiNhan->da_doc)
                                                                    <span class="badge bg-success">
                                                                        <i class="bi bi-check-circle"></i> Đã đọc
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning">
                                                                        <i class="bi bi-clock"></i> Chưa đọc
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($nguoiNhan->da_doc && $nguoiNhan->thoi_gian_doc)
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($nguoiNhan->thoi_gian_doc)->format('d/m/Y H:i') }}
                                                                    </small>
                                                                @else
                                                                    <small class="text-muted">-</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-3">
                                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                                                Chưa có người nhận
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
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterTrangThai = document.getElementById('filterTrangThai');
            const searchNguoiNhan = document.getElementById('searchNguoiNhan');
            const rows = document.querySelectorAll('.nguoi-nhan-row');

            function filterTable() {
                const trangThaiFilter = filterTrangThai ? filterTrangThai.value : '';
                const searchText = searchNguoiNhan ? searchNguoiNhan.value.toLowerCase() : '';

                rows.forEach(row => {
                    const trangThai = row.dataset.trangThai;
                    const ten = row.querySelector('.nguoi-nhan-ten')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('.nguoi-nhan-email')?.textContent.toLowerCase() || '';

                    const matchTrangThai = !trangThaiFilter || trangThai === trangThaiFilter;
                    const matchSearch = !searchText || ten.includes(searchText) || email.includes(
                        searchText);

                    row.style.display = matchTrangThai && matchSearch ? '' : 'none';
                });
            }

            if (filterTrangThai) filterTrangThai.addEventListener('change', filterTable);
            if (searchNguoiNhan) searchNguoiNhan.addEventListener('input', filterTable);
        });
    </script>
@endpush
