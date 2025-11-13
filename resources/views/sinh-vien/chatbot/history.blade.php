@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử chat')

@section('content')
<div class="page-heading">
    <h3>Lịch sử chat với Trợ lý ảo</h3>
</div>

<div class="page-content">
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
                                    @if($conv->trang_thai == 'dang_mo')
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
    if (confirm('Xóa cuộc trò chuyện này?')) {
        $.ajax({
            url: `/sinh-vien/chatbot/conversation/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function() {
                location.reload();
            }
        });
    }
});
</script>
@endpush
@endsection
