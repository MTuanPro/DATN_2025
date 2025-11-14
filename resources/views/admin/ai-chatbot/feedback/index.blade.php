@extends('layouts.layout-admin')

@section('title', 'Danh sách Feedback - AI Chatbot')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Danh sách Feedback</h3>
                    <p class="text-subtitle text-muted">Đánh giá từ sinh viên về chatbot</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Feedback</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Tổng feedback</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Hữu ích</h6>
                        <h3>{{ $stats['huu_ich'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Không hữu ích</h6>
                        <h3>{{ $stats['khong_huu_ich'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Tỷ lệ hài lòng</h6>
                        <h3>{{ $stats['ty_le_huu_ich'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-0">Danh sách Feedback</h5>
                        <a href="{{ route('admin.ai-chatbot.feedback.analytics') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-graph-up"></i> Phân tích
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter --}}
                    <form method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="danh_gia" class="form-select form-select-sm">
                                    <option value="">-- Đánh giá --</option>
                                    <option value="huu_ich" {{ request('danh_gia') == 'huu_ich' ? 'selected' : '' }}>Hữu ích</option>
                                    <option value="khong_huu_ich" {{ request('danh_gia') == 'khong_huu_ich' ? 'selected' : '' }}>Không hữu ích</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tu_ngay" class="form-control form-control-sm" 
                                       value="{{ request('tu_ngay') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="den_ngay" class="form-control form-control-sm" 
                                       value="{{ request('den_ngay') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm">Tìm</button>
                                <a href="{{ route('admin.ai-chatbot.feedback.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sinh viên</th>
                                    <th>Tin nhắn</th>
                                    <th>Đánh giá</th>
                                    <th>Lý do</th>
                                    <th>Thời gian</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $fb)
                                    <tr>
                                        <td>{{ $fb->id }}</td>
                                        <td>
                                            <strong>{{ $fb->sinhVien->ma_sinh_vien }}</strong><br>
                                            <small>{{ $fb->sinhVien->ho_ten }}</small>
                                        </td>
                                        <td>{{ Str::limit($fb->message->noi_dung, 50) }}</td>
                                        <td>
                                            @if($fb->danh_gia == 'huu_ich')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-hand-thumbs-up"></i> Hữu ích
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-hand-thumbs-down"></i> Không hữu ích
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($fb->ly_do)
                                                {{ Str::limit($fb->ly_do, 40) }}
                                                @if(mb_strlen($fb->ly_do) > 40)
                                                    <br>
                                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1 view-reason" data-reason="{{ e($fb->ly_do) }}">
                                                        Xem lý do
                                                    </button>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td><small>{{ $fb->created_at->format('d/m/Y H:i') }}</small></td>
                                        <td>
                                            <a href="{{ route('admin.ai-chatbot.feedback.show', $fb) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $feedbacks->links() }}
                </div>
            </div>

            {{-- Top Knowledge Good/Bad --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Top KB được đánh giá tốt</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0">
                                @foreach($stats['top_knowledge_good'] as $kb)
                                    <li class="mb-2">
                                        <strong>{{ $kb->cau_hoi_mau }}</strong>
                                        <span class="badge bg-success">{{ $kb->feedback_count }} lượt</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Top KB cần cải thiện</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0">
                                @foreach($stats['top_knowledge_bad'] as $kb)
                                    <li class="mb-2">
                                        <strong>{{ $kb->cau_hoi_mau }}</strong>
                                        <span class="badge bg-danger">{{ $kb->feedback_count }} lượt</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('modals')
<!-- Modal to show full reason -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reasonModalLabel">Lý do đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reasonModalBody">
                <!-- content injected by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
        // Attach click handler to buttons to open the reason modal
        $(document).on('click', '.view-reason', function() {
                var reason = $(this).data('reason') || '';
                $('#reasonModalBody').text(reason);
                var modal = new bootstrap.Modal(document.getElementById('reasonModal'));
                modal.show();
        });
</script>
@endpush
