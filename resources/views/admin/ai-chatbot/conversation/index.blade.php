@extends('layouts.layout-admin')

@section('title', 'Danh sách Hội thoại - AI Chatbot')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Danh sách Hội thoại</h3>
                    <p class="text-subtitle text-muted">Theo dõi các cuộc hội thoại của sinh viên với chatbot</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hội thoại</li>
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
                        <h6 class="text-muted">Tổng hội thoại</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đang mở</h6>
                        <h3 class="text-success">{{ $stats['dang_mo'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đã đóng</h6>
                        <h3 class="text-secondary">{{ $stats['da_dong'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Hôm nay</h6>
                        <h3 class="text-primary">{{ $stats['today'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Hội thoại</h5>
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
                                <select name="trang_thai" class="form-select form-select-sm">
                                    <option value="">-- Trạng thái --</option>
                                    <option value="dang_mo" {{ request('trang_thai') == 'dang_mo' ? 'selected' : '' }}>Đang mở</option>
                                    <option value="da_dong" {{ request('trang_thai') == 'da_dong' ? 'selected' : '' }}>Đã đóng</option>
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
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                                <a href="{{ route('admin.ai-chatbot.conversation.index') }}" class="btn btn-secondary btn-sm">
                                    Reset
                                </a>
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
                                    <th>Tiêu đề</th>
                                    <th>Số tin nhắn</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conversations as $conv)
                                    <tr>
                                        <td>{{ $conv->id }}</td>
                                        <td>
                                            <strong>{{ $conv->sinhVien->ma_sinh_vien }}</strong><br>
                                            <small>{{ $conv->sinhVien->ho_ten }}</small>
                                        </td>
                                        <td>{{ $conv->tieu_de_chat ?? 'Chưa có tiêu đề' }}</td>
                                        <td><span class="badge bg-info">{{ $conv->messages->count() }}</span></td>
                                        <td>
                                            <small>{{ $conv->ngay_bat_dau->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($conv->trang_thai == 'dang_mo')
                                                <span class="badge bg-success">Đang mở</span>
                                            @else
                                                <span class="badge bg-secondary">Đã đóng</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.ai-chatbot.conversation.show', $conv) }}" 
                                                   class="btn btn-info" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($conv->trang_thai == 'dang_mo')
                                                    <form action="{{ route('admin.ai-chatbot.conversation.close', $conv) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning" title="Đóng">
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
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

                    {{ $conversations->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
