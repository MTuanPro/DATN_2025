@extends('layouts.layout-daotao')

@section('title', 'Quản Lý Cảnh Báo Học Vụ')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Cảnh báo Học vụ</h3>
                <p class="text-subtitle text-muted">Theo dõi và xử lý cảnh báo học vụ của sinh viên</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cảnh báo Học vụ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-1"></i> {!! session('info') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Quick Actions -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thao tác nhanh</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-plus-circle"></i> Tạo cảnh báo thủ công
                        </a>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('dao-tao.canh-bao-hoc-vu.tu-dong-phat-hien') }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc muốn chạy phát hiện tự động? Hệ thống sẽ kiểm tra toàn bộ sinh viên.')">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-magic"></i> Phát hiện tự động
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.export', request()->all()) }}" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.canh-bao-hoc-vu.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Loại cảnh báo</label>
                                <select name="loai_canh_bao" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="diem_thap" {{ request('loai_canh_bao') == 'diem_thap' ? 'selected' : '' }}>Điểm thấp</option>
                                    <option value="vang_nhieu" {{ request('loai_canh_bao') == 'vang_nhieu' ? 'selected' : '' }}>Vắng nhiều</option>
                                    <option value="no_hoc_phi" {{ request('loai_canh_bao') == 'no_hoc_phi' ? 'selected' : '' }}>Nợ học phí</option>
                                    <option value="hoc_ky_lien_tiep" {{ request('loai_canh_bao') == 'hoc_ky_lien_tiep' ? 'selected' : '' }}>Học kỳ liên tiếp</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mức độ</label>
                                <select name="muc_do" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="canh_cao" {{ request('muc_do') == 'canh_cao' ? 'selected' : '' }}>Cảnh cáo</option>
                                    <option value="dinh_chi" {{ request('muc_do') == 'dinh_chi' ? 'selected' : '' }}>Đình chỉ</option>
                                    <option value="buoc_thoi_hoc" {{ request('muc_do') == 'buoc_thoi_hoc' ? 'selected' : '' }}>Buộc thôi học</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="chua_xu_ly" {{ request('trang_thai') == 'chua_xu_ly' ? 'selected' : '' }}>Chưa xử lý</option>
                                    <option value="dang_xu_ly" {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="da_xu_ly" {{ request('trang_thai') == 'da_xu_ly' ? 'selected' : '' }}>Đã xử lý</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" placeholder="MSSV, Tên SV..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Tổng cảnh báo</h6>
                        <h3 class="text-primary">{{ $canhBaos->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Chưa xử lý</h6>
                        <h3 class="text-warning">{{ \App\Models\CanhBaoHocVu::where('trang_thai', 'chua_xu_ly')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đang xử lý</h6>
                        <h3 class="text-info">{{ \App\Models\CanhBaoHocVu::where('trang_thai', 'dang_xu_ly')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đã xử lý</h6>
                        <h3 class="text-success">{{ \App\Models\CanhBaoHocVu::where('trang_thai', 'da_xu_ly')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Sinh viên</th>
                                <th>Học kỳ</th>
                                <th>Loại cảnh báo</th>
                                <th>Mức độ</th>
                                <th>Lý do</th>
                                <th>Trạng thái</th>
                                <th>Ngày CB</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($canhBaos as $index => $cb)
                            <tr>
                                <td>{{ $canhBaos->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $cb->sinhVien->ma_sinh_vien }}</strong><br>
                                    <small>{{ $cb->sinhVien->ho_ten }}</small><br>
                                    <small class="text-muted">{{ $cb->sinhVien->nganh->ten_nganh ?? 'N/A' ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $cb->hocKy->ten_hoc_ky }}<br><small>{{ $cb->hocKy->nam_hoc }}</small></td>
                                <td>
                                    @php
                                        $loaiText = match($cb->loai_canh_bao) {
                                            'diem_thap' => 'Điểm thấp',
                                            'vang_nhieu' => 'Vắng nhiều',
                                            'no_hoc_phi' => 'Nợ học phí',
                                            'hoc_ky_lien_tiep' => 'HK liên tiếp',
                                            default => $cb->loai_canh_bao
                                        };
                                        $loaiColor = match($cb->loai_canh_bao) {
                                            'diem_thap' => 'danger',
                                            'vang_nhieu' => 'warning',
                                            'no_hoc_phi' => 'info',
                                            'hoc_ky_lien_tiep' => 'dark',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $loaiColor }}">{{ $loaiText }}</span>
                                </td>
                                <td>
                                    @php
                                        $mucDoText = match($cb->muc_do) {
                                            'canh_cao' => 'Cảnh cáo',
                                            'dinh_chi' => 'Đình chỉ',
                                            'buoc_thoi_hoc' => 'Buộc thôi học',
                                            default => $cb->muc_do
                                        };
                                        $mucDoColor = match($cb->muc_do) {
                                            'canh_cao' => 'warning',
                                            'dinh_chi' => 'danger',
                                            'buoc_thoi_hoc' => 'dark',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $mucDoColor }}">{{ $mucDoText }}</span>
                                </td>
                                <td>
                                    <small>{{ \Str::limit($cb->ly_do, 50) }}</small>
                                </td>
                                <td>
                                    @php
                                        $ttText = match($cb->trang_thai) {
                                            'chua_xu_ly' => 'Chưa xử lý',
                                            'dang_xu_ly' => 'Đang xử lý',
                                            'da_xu_ly' => 'Đã xử lý',
                                            default => $cb->trang_thai
                                        };
                                        $ttColor = match($cb->trang_thai) {
                                            'chua_xu_ly' => 'secondary',
                                            'dang_xu_ly' => 'info',
                                            'da_xu_ly' => 'success',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $ttColor }}">{{ $ttText }}</span>
                                </td>
                                <td>{{ $cb->ngay_canh_bao->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.show', $cb) }}" class="btn btn-sm btn-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.edit', $cb) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('dao-tao.canh-bao-hoc-vu.destroy', $cb) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa cảnh báo này?')">
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
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Không có cảnh báo học vụ nào</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $canhBaos->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
