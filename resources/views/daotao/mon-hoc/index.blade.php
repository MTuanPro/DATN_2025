@extends('layouts.layout-daotao')

@section('title', 'Danh sách Môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Môn học</h3>
                    <p class="text-subtitle text-muted">Danh sách môn học trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Môn học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tìm kiếm & Lọc</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.mon-hoc.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Mã môn, Tên môn..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Khoa</label>
                                    <select name="khoa_id" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        @foreach ($khoas as $khoa)
                                            <option value="{{ $khoa->id }}"
                                                {{ request('khoa_id') == $khoa->id ? 'selected' : '' }}>
                                                {{ $khoa->ten_khoa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Loại môn</label>
                                    <select name="loai_mon" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="dai_cuong"
                                            {{ request('loai_mon') == 'dai_cuong' ? 'selected' : '' }}>Đại cương</option>
                                        <option value="co_so_nganh"
                                            {{ request('loai_mon') == 'co_so_nganh' ? 'selected' : '' }}>Cơ sở ngành
                                        </option>
                                        <option value="chuyen_nganh_bat_buoc"
                                            {{ request('loai_mon') == 'chuyen_nganh_bat_buoc' ? 'selected' : '' }}>Chuyên
                                            ngành bắt buộc</option>
                                        <option value="chuyen_nganh_tu_chon"
                                            {{ request('loai_mon') == 'chuyen_nganh_tu_chon' ? 'selected' : '' }}>Chuyên
                                            ngành tự chọn</option>
                                        <option value="thuc_tap" {{ request('loai_mon') == 'thuc_tap' ? 'selected' : '' }}>
                                            Thực tập</option>
                                        <option value="do_an_tot_nghiep"
                                            {{ request('loai_mon') == 'do_an_tot_nghiep' ? 'selected' : '' }}>Đồ án tốt
                                            nghiệp</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hình thức</label>
                                    <select name="hinh_thuc_day" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="offline"
                                            {{ request('hinh_thuc_day') == 'offline' ? 'selected' : '' }}>Offline</option>
                                        <option value="online"
                                            {{ request('hinh_thuc_day') == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="hybrid"
                                            {{ request('hinh_thuc_day') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('dao-tao.mon-hoc.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách Môn học</h5>
                    <a href="{{ route('dao-tao.mon-hoc.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm môn học
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã môn</th>
                                    <th>Tên môn</th>
                                    <th>Khoa</th>
                                    <th>Tín chỉ</th>
                                    <th>Loại môn</th>
                                    <th>Hình thức</th>
                                    <th>Môn TQ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monHocs as $monHoc)
                                    <tr>
                                        <td>{{ $loop->iteration + ($monHocs->currentPage() - 1) * $monHocs->perPage() }}
                                        </td>
                                        <td><strong>{{ $monHoc->ma_mon }}</strong></td>
                                        <td>{{ $monHoc->ten_mon }}</td>
                                        <td>{{ $monHoc->khoa->ten_khoa ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $monHoc->so_tin_chi }} TC</span>
                                            <small class="text-muted">(LT: {{ $monHoc->so_tin_chi_ly_thuyet }} / TH:
                                                {{ $monHoc->so_tin_chi_thuc_hanh }})</small>
                                        </td>
                                        <td>
                                            @php
                                                $loaiMonLabels = [
                                                    'dai_cuong' => 'Đại cương',
                                                    'co_so_nganh' => 'Cơ sở ngành',
                                                    'chuyen_nganh_bat_buoc' => 'CN bắt buộc',
                                                    'chuyen_nganh_tu_chon' => 'CN tự chọn',
                                                    'thuc_tap' => 'Thực tập',
                                                    'do_an_tot_nghiep' => 'ĐATN',
                                                ];
                                            @endphp
                                            <span
                                                class="badge bg-info">{{ $loaiMonLabels[$monHoc->loai_mon] ?? $monHoc->loai_mon }}</span>
                                        </td>
                                        <td>
                                            @if ($monHoc->hinh_thuc_day == 'offline')
                                                <span class="badge bg-secondary">Offline</span>
                                            @elseif($monHoc->hinh_thuc_day == 'online')
                                                <span class="badge bg-success">Online</span>
                                            @else
                                                <span class="badge bg-warning">Hybrid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('dao-tao.mon-hoc.tien-quyet', $monHoc->id) }}"
                                                class="badge bg-primary" title="Quản lý môn tiên quyết">
                                                {{ $monHoc->monTienQuyet->count() }} môn
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.mon-hoc.edit', $monHoc->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.mon-hoc.tien-quyet', $monHoc->id) }}"
                                                    class="btn btn-sm btn-info" title="Môn tiên quyết">
                                                    <i class="bi bi-diagram-3"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.mon-hoc.destroy', $monHoc->id) }}"
                                                    method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                        <td colspan="9" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $monHocs->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
