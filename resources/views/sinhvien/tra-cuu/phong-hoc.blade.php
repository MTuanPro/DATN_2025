@extends('layouts.layout-sinhvien')

@section('title', 'Tra cứu phòng học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tra cứu phòng học</h3>
                    <p class="text-subtitle text-muted">Tìm kiếm và xem thông tin phòng học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tra cứu phòng học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Bộ lọc --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sinh-vien.tra-cuu.phong-hoc') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Mã phòng, tên phòng, vị trí...">
                        </div>
                        <div class="col-md-2">
                            <label for="loai_phong" class="form-label">Loại phòng</label>
                            <select class="form-select" id="loai_phong" name="loai_phong">
                                <option value="">-- Tất cả --</option>
                                <option value="ly_thuyet" {{ request('loai_phong') == 'ly_thuyet' ? 'selected' : '' }}>Lý thuyết</option>
                                <option value="thuc_hanh" {{ request('loai_phong') == 'thuc_hanh' ? 'selected' : '' }}>Thực hành</option>
                                <option value="lab" {{ request('loai_phong') == 'lab' ? 'selected' : '' }}>Lab</option>
                                <option value="thu_vien" {{ request('loai_phong') == 'thu_vien' ? 'selected' : '' }}>Thư viện</option>
                                <option value="khac" {{ request('loai_phong') == 'khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="trang_thai" class="form-label">Trạng thái</label>
                            <select class="form-select" id="trang_thai" name="trang_thai">
                                <option value="">-- Tất cả --</option>
                                <option value="dang_su_dung" {{ request('trang_thai') == 'dang_su_dung' ? 'selected' : '' }}>Đang sử dụng</option>
                                <option value="bao_tri" {{ request('trang_thai') == 'bao_tri' ? 'selected' : '' }}>Bảo trì</option>
                                <option value="ngung_su_dung" {{ request('trang_thai') == 'ngung_su_dung' ? 'selected' : '' }}>Ngừng sử dụng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="suc_chua_min" class="form-label">Sức chứa tối thiểu</label>
                            <input type="number" class="form-control" id="suc_chua_min" name="suc_chua_min" 
                                   value="{{ request('suc_chua_min') }}" 
                                   placeholder="Số chỗ" min="1">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách phòng học --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách phòng học</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã phòng</th>
                                    <th>Tên phòng</th>
                                    <th>Vị trí</th>
                                    <th>Loại phòng</th>
                                    <th>Sức chứa</th>
                                    <th>Trạng thái</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phongHocs as $index => $ph)
                                    <tr>
                                        <td>{{ $phongHocs->firstItem() + $index }}</td>
                                        <td><strong>{{ $ph->ma_phong }}</strong></td>
                                        <td><strong>{{ $ph->ten_phong }}</strong></td>
                                        <td>{{ $ph->vi_tri ?? 'N/A' }}</td>
                                        <td>
                                            @if($ph->loai_phong == 'ly_thuyet')
                                                <span class="badge bg-primary">Lý thuyết</span>
                                            @elseif($ph->loai_phong == 'thuc_hanh')
                                                <span class="badge bg-success">Thực hành</span>
                                            @elseif($ph->loai_phong == 'lab')
                                                <span class="badge bg-info">Lab</span>
                                            @elseif($ph->loai_phong == 'thu_vien')
                                                <span class="badge bg-warning">Thư viện</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $ph->loai_phong ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $ph->suc_chua ?? 'N/A' }} chỗ</span>
                                        </td>
                                        <td>
                                            @if($ph->trang_thai == 'dang_su_dung')
                                                <span class="badge bg-success">Đang sử dụng</span>
                                            @elseif($ph->trang_thai == 'bao_tri')
                                                <span class="badge bg-warning">Bảo trì</span>
                                            @elseif($ph->trang_thai == 'ngung_su_dung')
                                                <span class="badge bg-danger">Ngừng sử dụng</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $ph->trang_thai ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($ph->mo_ta ?? 'N/A', 50) }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không tìm thấy phòng học nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $phongHocs->firstItem() ?? 0 }} - {{ $phongHocs->lastItem() ?? 0 }}
                                trong tổng số {{ $phongHocs->total() }} phòng học
                            </small>
                        </div>
                        <div>
                            {{ $phongHocs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

