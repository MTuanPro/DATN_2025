@extends('layouts.layout-sinhvien')

@section('title', 'Cảnh Báo Học Vụ')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Cảnh báo Học vụ của tôi</h3>
                <p class="text-subtitle text-muted">Xem và theo dõi các cảnh báo học vụ</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cảnh báo Học vụ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Alert nếu có cảnh báo chưa xử lý -->
        @php
            $chuaXuLy = $canhBaos->where('trang_thai', 'chua_xu_ly')->count();
        @endphp
        @if($chuaXuLy > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Cảnh báo quan trọng!</h4>
            <p>Bạn có <strong>{{ $chuaXuLy }} cảnh báo học vụ</strong> chưa xử lý. Vui lòng xem chi tiết và liên hệ với Phòng Đào tạo hoặc Giảng viên chủ nhiệm để được hỗ trợ.</p>
            <hr>
            <p class="mb-0">
                <strong>Liên hệ:</strong> daotao@smis.edu.vn hoặc hotline: 024.xxxx.xxxx
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sinh-vien.canh-bao-hoc-vu.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('sinh-vien.canh-bao-hoc-vu.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách cảnh báo -->
        @if($canhBaos->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Tuyệt vời!</h4>
                <p class="text-muted">Bạn không có cảnh báo học vụ nào.</p>
            </div>
        </div>
        @else
        @foreach($canhBaos as $cb)
        <div class="card mb-3 border-{{ $cb->muc_do == 'buoc_thoi_hoc' ? 'danger' : ($cb->muc_do == 'dinh_chi' ? 'warning' : 'secondary') }}">
            <div class="card-header bg-{{ $cb->muc_do == 'buoc_thoi_hoc' ? 'danger' : ($cb->muc_do == 'dinh_chi' ? 'warning' : 'light') }} text-{{ $cb->muc_do != 'canh_cao' ? 'white' : 'dark' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        @php
                            $loaiText = match($cb->loai_canh_bao) {
                                'diem_thap' => 'Điểm trung bình thấp',
                                'vang_nhieu' => 'Vắng học nhiều',
                                'no_hoc_phi' => 'Nợ học phí',
                                'hoc_ky_lien_tiep' => 'Học kỳ liên tiếp không đạt',
                                default => $cb->loai_canh_bao
                            };
                            $mucDoText = match($cb->muc_do) {
                                'canh_cao' => 'Cảnh cáo',
                                'dinh_chi' => 'Đình chỉ học tập',
                                'buoc_thoi_hoc' => 'Buộc thôi học',
                                default => $cb->muc_do
                            };
                        @endphp
                        {{ $loaiText }}
                    </h5>
                    <span class="badge bg-{{ $cb->muc_do == 'buoc_thoi_hoc' ? 'dark' : ($cb->muc_do == 'dinh_chi' ? 'danger' : 'warning') }}">
                        {{ $mucDoText }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <strong>Học kỳ:</strong> {{ $cb->hocKy->ten_hoc_ky }} - {{ $cb->hocKy->nam_hoc }}
                        </p>
                        <p class="mb-2">
                            <strong>Lý do:</strong> <span class="text-danger">{{ $cb->ly_do }}</span>
                        </p>
                        @if($cb->ghi_chu)
                        <p class="mb-2">
                            <strong>Ghi chú:</strong> {{ $cb->ghi_chu }}
                        </p>
                        @endif
                        <p class="mb-0">
                            <strong>Ngày cảnh báo:</strong> {{ $cb->ngay_canh_bao->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <p class="mb-2">
                            <strong>Trạng thái:</strong><br>
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
                        </p>
                        <a href="{{ route('sinh-vien.canh-bao-hoc-vu.show', $cb) }}" class="btn btn-sm btn-primary mt-2">
                            <i class="bi bi-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        <div class="mt-3">
            {{ $canhBaos->links() }}
        </div>
        @endif
    </section>
</div>
@endsection
