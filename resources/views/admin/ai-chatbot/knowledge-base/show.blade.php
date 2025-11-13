@extends('layouts.layout-admin')

@section('title', 'Chi tiết Knowledge Base - AI Chatbot')

@section('content')
<div class="page-heading">
    <h3>Chi tiết Knowledge Base</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-body">
            <h5>{{ $knowledgeBase->cau_hoi_mau }}</h5>
            <p><strong>Chủ đề:</strong> {{ $knowledgeBase->chu_de }} @if($knowledgeBase->danh_muc) - <small>{{ $knowledgeBase->danh_muc }}</small>@endif</p>
            <p><strong>Từ khóa:</strong> {{ $knowledgeBase->tu_khoa }}</p>
            <p><strong>Độ ưu tiên:</strong> {{ $knowledgeBase->do_uu_tien }}</p>
            <p><strong>Lượt truy cập:</strong> {{ $knowledgeBase->luot_truy_cap }}</p>
            <p><strong>Hữu ích:</strong> {{ $knowledgeBase->huu_ich }} ({{ $knowledgeBase->tyLeHuuIch() }}%)</p>

            <hr>
            <h6>Câu trả lời</h6>
            <div style="white-space: pre-line;">{{ $knowledgeBase->cau_tra_loi }}</div>

            <hr>
            <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" class="btn btn-secondary">Quay lại</a>
            <a href="{{ route('admin.ai-chatbot.knowledge-base.edit', $knowledgeBase) }}" class="btn btn-warning">Sửa</a>
        </div>
    </div>
</div>
@endsection
