@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('sinh-vien.chatbot.history') }}" class="btn btn-sm btn-secondary">&larr; Quay lại</a>
                        <strong class="ms-2">Cuộc trò chuyện: </strong> {{ $conversation->tieu_de_chat ?? 'Không có tiêu đề' }}
                    </div>
                    <div>
                        <form action="{{ route('sinh-vien.chatbot.conversation.delete', $conversation->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa cuộc trò chuyện này?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa cuộc trò chuyện</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if($conversation->messages->isEmpty())
                        <p class="text-muted">Chưa có tin nhắn trong cuộc trò chuyện này.</p>
                    @else
                        <div class="list-group">
                            @foreach($conversation->messages as $message)
                                @if($message->nguoi_gui === 'user')
                                    <div class="list-group-item list-group-item-light">
                                        <div class="d-flex justify-content-between">
                                            <div><strong>Bạn</strong></div>
                                            <small class="text-muted">{{ $message->thoi_gian_gui ? $message->thoi_gian_gui->format('H:i d/m/Y') : '' }}</small>
                                        </div>
                                        <div class="mt-2">{!! nl2br(e($message->noi_dung)) !!}</div>
                                    </div>
                                @else
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div><strong>AI Chat Bot</strong></div>
                                            <small class="text-muted">{{ $message->thoi_gian_gui ? $message->thoi_gian_gui->format('H:i d/m/Y') : '' }}</small>
                                        </div>

                                        <div class="mt-2">{!! nl2br(e($message->noi_dung)) !!}</div>

                                        @if($message->knowledgeBase)
                                            <div class="mt-2">
                                                <small>Được lấy từ chủ đề: <a href="{{ route('admin.ai-chatbot.knowledge-base.show', $message->knowledgeBase->id) }}" target="_blank">{{ $message->knowledgeBase->chu_de }}</a></small>
                                            </div>
                                        @endif

                                        @if($message->do_tuong_dong !== null)
                                            <div class="mt-1">
                                                <small class="text-muted">Độ tương đồng: {{ $message->doTuongDongPhanTram() }}%</small>
                                            </div>
                                        @endif

                                        {{-- Feedback controls for bot replies --}}
                                        <div class="mt-2 d-flex gap-2">
                                            @if(!$message->daCoFeedback())
                                                <form action="{{ route('sinh-vien.chatbot.feedback.submit') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="message_id" value="{{ $message->id }}" />
                                                    <input type="hidden" name="danh_gia" value="huu_ich" />
                                                    <button class="btn btn-outline-success btn-sm">Hữu ích</button>
                                                </form>

                                                <form action="{{ route('sinh-vien.chatbot.feedback.submit') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="message_id" value="{{ $message->id }}" />
                                                    <input type="hidden" name="danh_gia" value="khong_huu_ich" />
                                                    <button class="btn btn-outline-danger btn-sm">Không hữu ích</button>
                                                </form>
                                            @else
                                                <div class="badge bg-info text-dark">Bạn đã gửi phản hồi: {{ $message->feedback->danh_gia === 'huu_ich' ? 'Hữu ích' : 'Không hữu ích' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('sinh-vien.chatbot.index') }}" class="btn btn-primary">Mở giao diện Chat</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
