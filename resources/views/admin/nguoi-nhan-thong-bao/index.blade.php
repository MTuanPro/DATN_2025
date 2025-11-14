@extends('layouts.layout-admin')

@section('title', 'Quản lý Người nhận')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Người nhận Thông báo</h3>
                    <p class="text-subtitle text-muted">Xem danh sách người nhận và trạng thái đọc</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Người nhận</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light-primary">
                        <div class="card-body">
                            <h6 class="text-muted">Tổng người nhận</h6>
                            <h4>{{ \App\Models\NguoiNhanThongBao::count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-success">
                        <div class="card-body">
                            <h6 class="text-muted">Đã đọc</h6>
                            <h4>{{ \App\Models\NguoiNhanThongBao::where('da_doc', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-warning">
                        <div class="card-body">
                            <h6 class="text-muted">Chưa đọc</h6>
                            <h4>{{ \App\Models\NguoiNhanThongBao::where('da_doc', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light-info">
                        <div class="card-body">
                            <h6 class="text-muted">Đã gửi email</h6>
                            <h4>{{ \App\Models\NguoiNhanThongBao::where('da_gui_email', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách người nhận</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <select name="thong_bao_id" class="form-select">
                                <option value="">-- Tất cả thông báo --</option>
                                @foreach($thongBaos as $tb)
                                    <option value="{{ $tb->id }}" {{ request('thong_bao_id') == $tb->id ? 'selected' : '' }}>
                                        {{ $tb->tieu_de }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="da_doc" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="1" {{ request('da_doc') == '1' ? 'selected' : '' }}>Đã đọc</option>
                                <option value="0" {{ request('da_doc') == '0' ? 'selected' : '' }}>Chưa đọc</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Tìm người nhận..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Lọc
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.nguoi-nhan-thong-bao.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-x"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Thông báo</th>
                                    <th>Người nhận</th>
                                    <th>Email</th>
                                    <th>Đã đọc</th>
                                    <th>Email gửi</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nguoiNhans as $nn)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.thong-bao.show', $nn->thongBao->id) }}">
                                                {{ Str::limit($nn->thongBao->tieu_de, 50) }}
                                            </a>
                                        </td>
                                        <td>{{ $nn->nguoiNhan->name }}</td>
                                        <td>{{ $nn->nguoiNhan->email }}</td>
                                        <td>
                                            @if($nn->da_doc)
                                                <span class="badge bg-success">Đã đọc</span>
                                                <br><small>{{ $nn->ngay_doc?->format('d/m/Y H:i') }}</small>
                                            @else
                                                <span class="badge bg-warning">Chưa đọc</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($nn->da_gui_email)
                                                <i class="bi bi-check-circle text-success"></i> Đã gửi
                                            @else
                                                <i class="bi bi-x-circle text-muted"></i> Chưa gửi
                                            @endif
                                        </td>
                                        <td><small>{{ $nn->created_at->format('d/m/Y H:i') }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $nguoiNhans->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

