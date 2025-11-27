@extends('layouts.layout-sinhvien')

@section('title', 'Tra cứu giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tra cứu giảng viên</h3>
                    <p class="text-subtitle text-muted">Tìm kiếm và xem thông tin giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Tra cứu giảng viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Bộ lọc --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sinh-vien.tra-cuu.giang-vien') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Mã GV, tên, email, SĐT...">
                        </div>
                        <div class="col-md-3">
                            <label for="khoa_id" class="form-label">Khoa</label>
                            <select class="form-select" id="khoa_id" name="khoa_id">
                                <option value="">-- Tất cả --</option>
                                @foreach($khoas as $khoa)
                                    <option value="{{ $khoa->id }}" {{ request('khoa_id') == $khoa->id ? 'selected' : '' }}>
                                        {{ $khoa->ten_khoa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="trinh_do_id" class="form-label">Trình độ</label>
                            <select class="form-select" id="trinh_do_id" name="trinh_do_id">
                                <option value="">-- Tất cả --</option>
                                @foreach($trinhDos as $td)
                                    <option value="{{ $td->id }}" {{ request('trinh_do_id') == $td->id ? 'selected' : '' }}>
                                        {{ $td->ten_trinh_do }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="gioi_tinh" class="form-label">Giới tính</label>
                            <select class="form-select" id="gioi_tinh" name="gioi_tinh">
                                <option value="">-- Tất cả --</option>
                                <option value="nam" {{ request('gioi_tinh') == 'nam' ? 'selected' : '' }}>Nam</option>
                                <option value="nu" {{ request('gioi_tinh') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                <option value="khac" {{ request('gioi_tinh') == 'khac' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách giảng viên --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách giảng viên</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Khoa</th>
                                    <th>Trình độ</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($giangViens as $index => $gv)
                                    <tr>
                                        <td>{{ $giangViens->firstItem() + $index }}</td>
                                        <td><strong>{{ $gv->ma_giang_vien }}</strong></td>
                                        <td>
                                            <strong>{{ $gv->ho_ten }}</strong>
                                            @if($gv->gioi_tinh)
                                                <br><small class="text-muted">
                                                    {{ $gv->gioi_tinh == 'nam' ? 'Nam' : ($gv->gioi_tinh == 'nu' ? 'Nữ' : 'Khác') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $gv->khoa->ten_khoa ?? 'N/A' }}</td>
                                        <td>{{ $gv->trinhDo->ten_trinh_do ?? 'N/A' }}</td>
                                        <td>{{ $gv->email ?? 'N/A' }}</td>
                                        <td>{{ $gv->so_dien_thoai ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Không tìm thấy giảng viên nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $giangViens->firstItem() ?? 0 }} - {{ $giangViens->lastItem() ?? 0 }}
                                trong tổng số {{ $giangViens->total() }} giảng viên
                            </small>
                        </div>
                        <div>
                            {{ $giangViens->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

