@extends('layouts.layout-admin')

@section('title', 'Chi tiết Hội thoại - AI Chatbot')

@section('content')
<div class="page-heading">
    <h3>Chi tiết Hội thoại</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <p><strong>Cuộc hội thoại:</strong> {{ $conversation->tieu_de_chat ?? ('#' . $conversation->id) }}</p>
            <p><strong>Sinh viên:</strong> {{ $conversation->sinhVien->ho_ten }} ({{ $conversation->sinhVien->ma_sinh_vien }})</p>
            <p><strong>Trạng thái:</strong> {{ $conversation->trang_thai }}</p>
            <hr>
            <div class="messages">
                @foreach($conversation->messages as $msg)
                    <div class="mb-3">
                        <div class="small text-muted">{{ $msg->thoi_gian_gui ? $msg->thoi_gian_gui->format('d/m/Y H:i') : '' }} - <strong>{{ $msg->nguoi_gui }}</strong></div>
                        <div style="white-space: pre-line;">{{ $msg->noi_dung }}</div>
                        @if($msg->knowledgeBase)
                            <div class="mt-1"><small>Matched KB: <a href="{{ route('admin.ai-chatbot.knowledge-base.show', $msg->knowledgeBase) }}">{{ Str::limit($msg->knowledgeBase->cau_hoi_mau, 80) }}</a></small></div>
                        @endif
                        @if($msg->do_tuong_dong)
                            <div class="mt-1"><small>Độ tương đồng: {{ (int)round($msg->do_tuong_dong * 100) }}%</small></div>
                        @endif
                        @if($msg->feedback)
                            <div class="mt-1"><small>Feedback: {{ $msg->feedback->danh_gia }} - {{ $msg->feedback->ly_do }}</small></div>
                        @endif
                    </div>
                    <hr>
                @endforeach
            </div>

            <a href="{{ route('admin.ai-chatbot.conversation.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
