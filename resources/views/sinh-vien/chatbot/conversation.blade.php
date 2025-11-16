@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết cuộc trò chuyện')

@section('content')
    <div class="page-heading">
        <h3><i class="bi bi-chat-dots"></i> Chi tiết cuộc trò chuyện</h3>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('sinh-vien.chatbot.history') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                            <strong class="ms-2">{{ $conversation->tieu_de_chat ?? 'Cuộc trò chuyện' }}</strong>
                        </div>
                        <div>
                            <button class="btn btn-danger btn-sm btn-delete-conversation" data-id="{{ $conversation->id }}">
                                <i class="bi bi-trash"></i> Xóa cuộc trò chuyện
                            </button>
                        </div>
                    </div>

                    <div class="card-body conversation-body">
                        @if ($conversation->messages->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">Chưa có tin nhắn trong cuộc trò chuyện này.</p>
                            </div>
                        @else
                            <div class="messages-list">
                                @foreach ($conversation->messages as $message)
                                    @if ($message->nguoi_gui === 'user')
                                        <div class="message-item user-message-item mb-3">
                                            <div class="message-header d-flex justify-content-between mb-2">
                                                <strong class="text-primary"><i class="bi bi-person-circle"></i>
                                                    Bạn</strong>
                                                <small class="text-muted">
                                                    {{ $message->thoi_gian_gui ? $message->thoi_gian_gui->format('H:i d/m/Y') : '' }}
                                                </small>
                                            </div>
                                            <div class="message-content user-message-content">
                                                {!! nl2br(e($message->noi_dung)) !!}
                                            </div>
                                        </div>
                                    @else
                                        <div class="message-item bot-message-item mb-3">
                                            <div class="message-header d-flex justify-content-between mb-2">
                                                <strong class="text-success"><i class="bi bi-robot"></i> AI Chat
                                                    Bot</strong>
                                                <small class="text-muted">
                                                    {{ $message->thoi_gian_gui ? $message->thoi_gian_gui->format('H:i d/m/Y') : '' }}
                                                </small>
                                            </div>

                                            <div class="message-content bot-message-content">
                                                {!! nl2br(e($message->noi_dung)) !!}
                                            </div>

                                            @if ($message->knowledgeBase)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-info-circle"></i> Từ chủ đề:
                                                        <span
                                                            class="badge bg-info">{{ $message->knowledgeBase->chu_de }}</span>
                                                    </small>
                                                </div>
                                            @endif

                                            @if ($message->do_tuong_dong)
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="bi bi-graph-up"></i> Độ khớp:
                                                        <span
                                                            class="badge bg-secondary">{{ number_format($message->do_tuong_dong, 1) }}%</span>
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --conversation-bg: #f8f9fa;
            --message-user-bg: #e3f2fd;
            --message-bot-bg: #ffffff;
            --text-color: #212529;
            --border-color: #dee2e6;
        }

        [data-bs-theme="dark"] {
            --conversation-bg: #1e2139;
            --message-user-bg: #2d3561;
            --message-bot-bg: #252941;
            --text-color: #f0f2f5;
            --border-color: #3d4363;
        }

        .conversation-body {
            background-color: var(--conversation-bg) !important;
            min-height: 400px;
            max-height: 600px;
            overflow-y: auto;
        }

        .messages-list {
            padding: 10px;
        }

        .message-item {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 16px;
            border-radius: 15px 15px 5px 15px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            margin-left: auto;
            max-width: 80%;
            display: inline-block;
        }

        .bot-message-content {
            background-color: var(--message-bot-bg) !important;
            color: var(--text-color) !important;
            padding: 12px 16px;
            border-radius: 15px 15px 15px 5px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 80%;
            display: inline-block;
        }

        [data-bs-theme="dark"] .bot-message-content {
            background: linear-gradient(135deg, #2a2f4a 0%, #252941 100%) !important;
            border-color: #3d4363;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .user-message-item {
            text-align: right;
        }

        .bot-message-item {
            text-align: left;
        }

        .message-header {
            font-size: 0.9rem;
        }

        .message-content {
            word-wrap: break-word;
            line-height: 1.6;
        }

        [data-bs-theme="dark"] .card {
            background-color: #2a2f4a;
            border-color: #3d4363;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: #252941;
            border-color: #3d4363;
            color: #f0f2f5;
        }

        [data-bs-theme="dark"] .btn-delete-conversation {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.4);
            color: #ff6b7f;
        }

        [data-bs-theme="dark"] .btn-delete-conversation:hover {
            background-color: rgba(220, 53, 69, 0.3);
            border-color: rgba(220, 53, 69, 0.6);
            color: #ff8594;
        }

        .conversation-body::-webkit-scrollbar {
            width: 8px;
        }

        .conversation-body::-webkit-scrollbar-track {
            background: var(--conversation-bg);
        }

        .conversation-body::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        .conversation-body::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #7a8ef5 0%, #8a5bb2 100%);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-delete-conversation').click(function() {
                const id = $(this).data('id');
                const $btn = $(this);

                if (confirm('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?')) {
                    // Disable button để tránh click nhiều lần
                    $btn.prop('disabled', true).html('<i class="bi bi-hourglass"></i> Đang xóa...');

                    $.ajax({
                        url: `/sinh-vien/chatbot/conversation/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message || 'Đã xóa cuộc hội thoại');
                                // Chuyển về trang history
                                window.location.href =
                                    '{{ route('sinh-vien.chatbot.history') }}';
                            } else {
                                alert('Lỗi: ' + (response.error ||
                                    'Không thể xóa cuộc trò chuyện'));
                                $btn.prop('disabled', false).html(
                                    '<i class="bi bi-trash"></i> Xóa cuộc trò chuyện');
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
                            $btn.prop('disabled', false).html(
                                '<i class="bi bi-trash"></i> Xóa cuộc trò chuyện');
                        }
                    });
                }
            });
        });
    </script>
@endpush
