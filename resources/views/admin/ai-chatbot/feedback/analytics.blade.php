@extends('layouts.layout-admin')

@section('title', 'Phân tích Feedback - AI Chatbot')

@section('content')
<div class="page-heading">
    <h3>Phân tích Feedback</h3>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Thống kê 7 ngày gần nhất</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Ngày</th><th>Hữu ích</th><th>Không hữu ích</th></tr></thead>
                        <tbody>
                            @foreach($dailyStats as $d)
                                <tr>
                                    <td>{{ $d['date'] }}</td>
                                    <td>{{ $d['huu_ich'] }}</td>
                                    <td>{{ $d['khong_huu_ich'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Thống kê theo chủ đề</div>
                <div class="card-body">
                    <ul>
                        @foreach($statsByChuDe as $s)
                            <li>{{ $s->chu_de }} - Hữu ích: {{ $s->huu_ich }} | Không hữu ích: {{ $s->khong_huu_ich }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.ai-chatbot.feedback.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
