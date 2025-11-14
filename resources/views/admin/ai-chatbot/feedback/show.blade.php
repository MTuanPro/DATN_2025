@extends('layouts.layout-admin')

@section('title', 'Chi tiết Feedback - AI Chatbot')

@section('content')
<div class="page-heading">
    <h3>Chi tiết Feedback</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $feedback->id }}</p>
            <p><strong>Sinh viên:</strong> {{ $feedback->sinhVien->ho_ten }} ({{ $feedback->sinhVien->ma_sinh_vien }})</p>
            <p><strong>Đánh giá:</strong> {{ $feedback->danh_gia }}</p>
            <p><strong>Lý do:</strong> {{ $feedback->ly_do ?? '-' }}</p>
            <p><strong>Thời gian:</strong> {{ $feedback->created_at->format('d/m/Y H:i') }}</p>

            <hr>
            <p><strong>Tin nhắn gốc:</strong></p>
            <div style="white-space: pre-line;">{{ $feedback->message->noi_dung }}</div>

            <a href="{{ route('admin.ai-chatbot.feedback.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
        </div>
    </div>
</div>
@endsection
