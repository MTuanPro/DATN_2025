@extends('layouts.layout-admin')

@section('title', 'Quản lý Thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Thông báo</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả thông báo trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách thông báo</h5>
                        <a href="{{ route('admin.thong-bao.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tạo thông báo mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('admin.thong-bao.index') }}" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="loai_thong_bao" class="form-select">
                                    <option value="">-- Loại thông báo --</option>
                                    <option value="tin_tuc" {{ request('loai_thong_bao') == 'tin_tuc' ? 'selected' : '' }}>
                                        Tin tức</option>
                                    <option value="thong_bao_chung"
                                        {{ request('loai_thong_bao') == 'thong_bao_chung' ? 'selected' : '' }}>Thông báo
                                        chung</option>
                                    <option value="tin_gap" {{ request('loai_thong_bao') == 'tin_gap' ? 'selected' : '' }}>
                                        Tin gấp</option>
                                    <option value="lich_hoc"
                                        {{ request('loai_thong_bao') == 'lich_hoc' ? 'selected' : '' }}>Lịch học</option>
                                    <option value="lich_thi"
                                        {{ request('loai_thong_bao') == 'lich_thi' ? 'selected' : '' }}>Lịch thi</option>
                                    <option value="hoc_phi" {{ request('loai_thong_bao') == 'hoc_phi' ? 'selected' : '' }}>
                                        Học phí</option>
                                    <option value="diem" {{ request('loai_thong_bao') == 'diem' ? 'selected' : '' }}>Điểm
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="doi_tuong" class="form-select">
                                    <option value="">-- Đối tượng --</option>
                                    <option value="all" {{ request('doi_tuong') == 'all' ? 'selected' : '' }}>Tất cả
                                    </option>
                                    <option value="sinh_vien" {{ request('doi_tuong') == 'sinh_vien' ? 'selected' : '' }}>
                                        Sinh viên</option>
                                    <option value="giang_vien"
                                        {{ request('doi_tuong') == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                                    <option value="dao_tao" {{ request('doi_tuong') == 'dao_tao' ? 'selected' : '' }}>Đào
                                        tạo</option>
                                    <option value="admin" {{ request('doi_tuong') == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Trạng thái --</option>
                                    <option value="cong_khai" {{ request('trang_thai') == 'cong_khai' ? 'selected' : '' }}>
                                        Công khai</option>
                                    <option value="nhap" {{ request('trang_thai') == 'nhap' ? 'selected' : '' }}>Nháp
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.thong-bao.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Loại</th>
                                    <th>Đối tượng</th>
                                    <th>Mức độ</th>
                                    <th>Người gửi</th>
                                    <th>Lượt xem</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($thongBaos as $tb)
                                    <tr>
                                        <td>{{ $tb->id }}</td>
                                        <td>
                                            @if ($tb->ghim_dau_trang)
                                                <i class="bi bi-pin-angle-fill text-danger" title="Đã ghim"></i>
                                            @endif
                                            <a
                                                href="{{ route('admin.thong-bao.show', $tb) }}">{{ Str::limit($tb->tieu_de, 50) }}</a>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $tb->getLoaiColor() }}">
                                                {{ str_replace('_', ' ', ucfirst($tb->loai_thong_bao)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ str_replace('_', ' ', ucfirst($tb->doi_tuong)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $tb->getMucDoColor() }}">
                                                {{ str_replace('_', ' ', ucfirst($tb->muc_do_quan_trong)) }}
                                            </span>
                                        </td>
                                        <td>{{ $tb->nguoiGui->name ?? 'Hệ thống' }}</td>
                                        <td>{{ $tb->so_luot_xem }}</td>
                                        <td>
                                            @if ($tb->trang_thai == 'cong_khai')
                                                <span class="badge bg-success">Công khai</span>
                                            @elseif($tb->trang_thai == 'nhap')
                                                <span class="badge bg-warning">Nháp</span>
                                            @else
                                                <span class="badge bg-danger">Đã xóa</span>
                                            @endif
                                        </td>
                                        <td>{{ $tb->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.thong-bao.show', $tb) }}"
                                                    class="btn btn-sm btn-info" title="Xem">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.thong-bao.edit', $tb) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.thong-bao.destroy', $tb) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Không có thông báo nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $thongBaos->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
