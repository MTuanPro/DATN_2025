@extends('layouts.layout-daotao')

@section('title', 'Quản lý Lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Lớp học phần</h3>
                    <p class="text-subtitle text-muted">Danh sách các lớp học phần trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lớp học phần</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Danh sách Lớp học phần</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <form method="POST" action="{{ route('dao-tao.lop-hoc-phan.sync-so-luong') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm shadow-sm" onclick="return confirm('Bạn có chắc muốn đồng bộ lại số lượng đăng ký?')">
                                        <i class="bi bi-arrow-repeat"></i> Đồng bộ sĩ số
                                    </button>
                                </form>
                                <a href="{{ route('dao-tao.lop-hoc-phan.show-import-form') }}" class="btn btn-success btn-sm shadow-sm text-white">
                                    <i class="bi bi-upload"></i> Import Excel
                                </a>
                                <a href="{{ route('dao-tao.lop-hoc-phan.create') }}" class="btn btn-primary btn-sm shadow-sm text-white">
                                    <i class="bi bi-plus-circle"></i> Thêm lớp học phần
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('dao-tao.lop-hoc-phan.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả học kỳ --</option>
                                    @foreach ($hocKys as $hocKy)
                                        <option value="{{ $hocKy->id }}"
                                            {{ request('hoc_ky_id') == $hocKy->id ? 'selected' : '' }}>
                                            {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Môn học</label>
                                <select name="mon_hoc_id" class="form-select">
                                    <option value="">-- Tất cả môn học --</option>
                                    @foreach ($monHocs as $monHoc)
                                        <option value="{{ $monHoc->id }}"
                                            {{ request('mon_hoc_id') == $monHoc->id ? 'selected' : '' }}>
                                            {{ $monHoc->ma_mon }} - {{ $monHoc->ten_mon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã hoặc tên lớp..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: center;">Mã lớp HP</th>
                                    <th style="text-align: center;">Tên lớp HP</th>
                                    <th style="text-align: center;">Môn học</th>
                                    <th style="text-align: center;">Học kỳ</th>
                                    <th style="text-align: center;">Sĩ số</th>
                                    <th style="text-align: center;">Hình thức</th>
                                    <th style="text-align: center;">Trạng thái</th>
                                    <th style="text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhans as $index => $lhp)
                                    <tr>
                                        <td>{{ $lopHocPhans->firstItem() + $index }}</td>
                                        <td><strong>{{ $lhp->ma_lop_hp }}</strong></td>
                                        <td>{{ $lhp->ten_lop_hp }}</td>
                                        <td>{{ $lhp->monHoc->ten_mon ?? 'N/A' }}</td>
                                        <td>{{ $lhp->hocKy->ten_hoc_ky ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $lhp->so_luong_thuc_te ?? $lhp->so_luong_dang_ky }}/{{ $lhp->suc_chua }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($lhp->hinh_thuc == 'offline')
                                                <span class="badge bg-secondary">Offline</span>
                                            @elseif($lhp->hinh_thuc == 'online')
                                                <span class="badge bg-primary">Online</span>
                                            @else
                                                <span class="badge bg-info">Hybrid</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lhp->trang_thai_lop == 'mo_dang_ky')
                                                <span class="badge bg-success">Mở đăng ký</span>
                                            @elseif($lhp->trang_thai_lop == 'dang_hoc')
                                                <span class="badge bg-primary">Đang học</span>
                                            @elseif($lhp->trang_thai_lop == 'ket_thuc')
                                                <span class="badge bg-secondary">Kết thúc</span>
                                            @else
                                                <span class="badge bg-danger">Hủy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.lop-hoc-phan.lich-co-dinh', $lhp->id) }}"
                                                    class="btn btn-sm btn-success" title="Lịch cố định (TKB)">
                                                    <i class="bi bi-calendar-week"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.lop-hoc-phan.lich-chi-tiet', $lhp->id) }}"
                                                    class="btn btn-sm btn-info" title="Lịch chi tiết">
                                                    <i class="bi bi-calendar-check"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.lop-hoc-phan.phan-cong', $lhp->id) }}"
                                                    class="btn btn-sm btn-secondary" title="Phân công GV">
                                                    <i class="bi bi-person-badge"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.lop-hoc-phan.edit', $lhp->id) }}"
                                                    class="btn btn-sm btn-primary" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $lhp->id }})" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $lhp->id }}"
                                                action="{{ route('dao-tao.lop-hoc-phan.destroy', $lhp->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Chưa có lớp học phần nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $lopHocPhans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Bạn có chắc chắn muốn xóa lớp học phần này?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endsection
