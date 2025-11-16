@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử chat')

@section('content')
    <div class="page-heading">
        <h3>Lịch sử chat với Trợ lý ảo</h3>
    </div>

    <div class="page-content">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Tất cả cuộc trò chuyện</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Số tin nhắn</th>
                                <th>Bắt đầu</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conversations as $conv)
                                <tr>
                                    <td>
                                        <strong>{{ $conv->tieu_de_chat ?? 'Cuộc trò chuyện #' . $conv->id }}</strong>
                                    </td>
                                    <td><span class="badge bg-info">{{ $conv->messages_count }}</span></td>
                                    <td>{{ $conv->ngay_bat_dau->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($conv->trang_thai == 'dang_mo')
                                            <span class="badge bg-success">Đang mở</span>
                                        @else
                                            <span class="badge bg-secondary">Đã đóng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sinh-vien.chatbot.conversation.show', $conv->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $conv->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Chưa có lịch sử chat
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $conversations->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $('.btn-delete').click(function() {
                const id = $(this).data('id');
                const $btn = $(this);

                if (confirm('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?')) {
                    // Disable button để tránh click nhiều lần
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: `/sinh-vien/chatbot/conversation/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Hiển thị thông báo thành công
                                alert(response.message || 'Đã xóa cuộc hội thoại');
                                // Reload trang
                                location.reload();
                            } else {
                                alert('Lỗi: ' + (response.error || 'Không thể xóa cuộc trò chuyện'));
                                $btn.prop('disabled', false);
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Không thể xóa cuộc trò chuyện';

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            } else if (xhr.status === 404) {
                                errorMsg = 'Cuộc hội thoại không tồn tại hoặc đã bị xóa';
                            } else if (xhr.status === 403) {
                                errorMsg = 'Bạn không có quyền xóa cuộc hội thoại này';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Lỗi hệ thống, vui lòng thử lại sau';
                            }

                            alert('Lỗi: ' + errorMsg);
                            $btn.prop('disabled', false);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
