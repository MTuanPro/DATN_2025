@extends('layouts.layout-admin')

@section('title', 'Thống kê Knowledge Base - AI Chatbot')

@section('content')
<div class="page-heading">
    <h3>Thống kê Knowledge Base</h3>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Tổng KB</h6>
                    <h3>{{ $stats['total_knowledge'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Đang kích hoạt</h6>
                    <h3>{{ $stats['active_knowledge'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Top truy cập</h6>
                    <ol>
                        @foreach($stats['most_accessed'] ?? [] as $kb)
                            <li>{{ Str::limit($kb->cau_hoi_mau, 80) }} ({{ $kb->luot_truy_cap }} lượt)</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Số lượng theo chủ đề</div>
                <div class="card-body">
                    <ul>
                        @foreach($stats['by_chu_de'] ?? [] as $row)
                            <li>{{ $row->chu_de }}: {{ $row->count }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Số lượng theo danh mục</div>
                <div class="card-body">
                    <ul>
                        @foreach($stats['by_danh_muc'] ?? [] as $row)
                            <li>{{ $row->danh_muc }}: {{ $row->count }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
@endsection
