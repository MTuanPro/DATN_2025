@extends('layouts.layout-admin')

@section('title', 'Quản lý Đào tạo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Đào tạo</h3>
                    <p class="text-subtitle text-muted">Danh sách nhân viên phòng đào tạo</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Đào tạo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Danh sách Đào tạo</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.dao-tao.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Đào tạo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.dao-tao.index') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm theo tên, email, SĐT..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            @if (request('search'))
                                <a href="{{ route('admin.dao-tao.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Xóa bộ lọc
                                </a>
                            @endif
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ảnh</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>User gán</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($daoTaos as $daoTao)
                                    <tr>
                                        <td>{{ $loop->iteration + ($daoTaos->currentPage() - 1) * $daoTaos->perPage() }}
                                        </td>
                                        <td>
                                            @if ($daoTao->anh_dai_dien)
                                                <img src="{{ asset('storage/' . $daoTao->anh_dai_dien) }}" alt="Avatar"
                                                    class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $daoTao->ho_ten }}</td>
                                        <td>{{ $daoTao->email }}</td>
                                        <td>{{ $daoTao->so_dien_thoai ?? 'N/A' }}</td>
                                        <td>
                                            @if ($daoTao->user)
                                                <span class="badge bg-success">{{ $daoTao->user->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa gán</span>
                                            @endif
                                        </td>
                                        <td>{{ $daoTao->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.dao-tao.edit', $daoTao) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('admin.dao-tao.destroy', $daoTao) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên đào tạo này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $daoTaos->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
