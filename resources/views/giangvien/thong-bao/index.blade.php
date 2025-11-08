@extends('layouts.layout-giangvien')

@section('title', 'Thông báo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Thông báo</h3>
                    <p class="text-subtitle text-muted">Quản lý thông báo của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thông báo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Loại thông báo</label>
                            <select name="loai_thong_bao" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="tin_tuc" {{ request('loai_thong_bao') == 'tin_tuc' ? 'selected' : '' }}>Tin
                                    tức</option>
                                <option value="thong_bao_chung"
                                    {{ request('loai_thong_bao') == 'thong_bao_chung' ? 'selected' : '' }}>Thông báo chung
                                </option>
                                <option value="tin_gap" {{ request('loai_thong_bao') == 'tin_gap' ? 'selected' : '' }}>Tin
                                    gấp</option>
                                <option value="lich_hoc" {{ request('loai_thong_bao') == 'lich_hoc' ? 'selected' : '' }}>
                                    Lịch học</option>
                                <option value="lich_thi" {{ request('loai_thong_bao') == 'lich_thi' ? 'selected' : '' }}>
                                    Lịch thi</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="trang_thai_doc" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="chua_doc" {{ request('trang_thai_doc') == 'chua_doc' ? 'selected' : '' }}>
                                    Chưa đọc</option>
                                <option value="da_doc" {{ request('trang_thai_doc') == 'da_doc' ? 'selected' : '' }}>Đã đọc
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề..."
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách thông báo -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách thông báo</h4>
                    <div>
                        <span class="badge bg-primary" id="unread-count">{{ $chuaDocCount }} chưa đọc</span>
                        <button type="button" class="btn btn-sm btn-success ms-2" onclick="markAllAsRead()">
                            <i class="bi bi-check-all"></i> Đánh dấu tất cả đã đọc
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($thongBaos->count() > 0)
                        <div class="list-group">
                            @foreach ($thongBaos as $tb)
                                <a href="{{ route('giangvien.thong-bao.show', $tb->thongBao->id) }}"
                                    class="list-group-item list-group-item-action {{ !$tb->da_doc ? 'bg-light' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            @if (!$tb->da_doc)
                                                <span class="badge bg-primary me-2">Mới</span>
                                            @endif
                                            @if ($tb->thongBao->ghim_dau_trang)
                                                <i class="bi bi-pin-angle-fill text-warning"></i>
                                            @endif
                                            <h5 class="mb-1 {{ !$tb->da_doc ? 'fw-bold' : '' }}">
                                                {{ $tb->thongBao->tieu_de }}
                                            </h5>
                                            <p class="mb-1 text-muted small">
                                                {{ Str::limit($tb->thongBao->noi_dung, 150) }}
                                            </p>
                                            <div class="mt-2">
                                                <span class="badge bg-{{ $tb->thongBao->loai_badge }} me-1">
                                                    {{ $tb->thongBao->loai_thong_bao }}
                                                </span>
                                                <span class="badge bg-{{ $tb->thongBao->muc_do_badge }}">
                                                    {{ $tb->thongBao->muc_do_quan_trong }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end ms-3">
                                            <small class="text-muted">
                                                {{ $tb->thongBao->ngay_gui ? $tb->thongBao->ngay_gui->format('d/m/Y H:i') : '' }}
                                            </small>
                                            @if ($tb->da_doc)
                                                <br><small class="text-success">
                                                    <i class="bi bi-check-circle"></i> Đã đọc
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $thongBaos->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
                            <p class="text-muted mt-3">Không có thông báo nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function markAllAsRead() {
            if (!confirm('Đánh dấu tất cả thông báo là đã đọc?')) return;

            fetch('{{ route('giangvien.thong-bao.mark-all-read') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra!');
                });
        }
    </script>
@endpush
