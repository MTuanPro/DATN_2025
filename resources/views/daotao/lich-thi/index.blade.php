@extends('layouts.layout-daotao')

@section('title', 'Danh sách Lịch thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Lịch thi</h3>
                <p class="text-subtitle text-muted">Danh sách lịch thi các lớp học phần</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dao-tao.lich-thi.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($hocKys as $hocKy)
                                        <option value="{{ $hocKy->id }}" {{ request('hoc_ky_id') == $hocKy->id ? 'selected' : '' }}>
                                            {{ $hocKy->ten_hoc_ky }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Loại thi</label>
                                <select name="loai_thi" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="giua_ky" {{ request('loai_thi') == 'giua_ky' ? 'selected' : '' }}>Giữa kỳ</option>
                                    <option value="cuoi_ky" {{ request('loai_thi') == 'cuoi_ky' ? 'selected' : '' }}>Cuối kỳ</option>
                                    <option value="thi_lai" {{ request('loai_thi') == 'thi_lai' ? 'selected' : '' }}>Thi lại</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Từ ngày</label>
                                <input type="date" name="ngay_thi_from" class="form-control" value="{{ request('ngay_thi_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Đến ngày</label>
                                <input type="date" name="ngay_thi_to" class="form-control" value="{{ request('ngay_thi_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tìm kiếm môn học</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã môn, tên môn..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('dao-tao.lich-thi.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                            <div class="float-end d-flex gap-2">
                                <a href="{{ route('dao-tao.lich-thi.show-import-form') }}" class="btn btn-info text-white">
                                    <i class="bi bi-upload"></i> Import Excel
                                </a>
                                <a href="{{ route('dao-tao.lich-thi.create') }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Thêm lịch thi
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã lớp HP</th>
                                <th>Môn học</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ thi</th>
                                <th>Phòng</th>
                                <th>SL dự thi</th>
                                <th>Giám thị</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichThis as $index => $lichThi)
                            <tr>
                                <td>{{ $lichThis->firstItem() + $index }}</td>
                                <td><strong>{{ $lichThi->lopHocPhan->ma_lop_hp }}</strong></td>
                                <td>
                                    {{ $lichThi->lopHocPhan->monHoc->ten_mon }}
                                    <br><small class="text-muted">{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</small>
                                </td>
                                <td>
                                    @if($lichThi->loai_thi == 'giua_ky')
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    @elseif($lichThi->loai_thi == 'cuoi_ky')
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    @else
                                        <span class="badge bg-warning">Thi lại</span>
                                    @endif
                                </td>
                                <td>{{ $lichThi->ngay_thi->format('d/m/Y') }}</td>
                                <td>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</td>
                                <td>{{ $lichThi->phongThi->ten_phong ?? 'Chưa phân phòng' }}</td>
                                <td>
                                    <strong>{{ $lichThi->lopHocPhan->lopHocPhanSinhViens->count() }}</strong>
                                    @if($lichThi->so_sinh_vien_du_thi && $lichThi->so_sinh_vien_du_thi != $lichThi->lopHocPhan->lopHocPhanSinhViens->count())
                                        <br><small class="text-muted">(Dự kiến: {{ $lichThi->so_sinh_vien_du_thi }})</small>
                                    @endif
                                </td>
                                <td>
                                    @if($lichThi->giamThi1)
                                        <small>1. {{ $lichThi->giamThi1->ho_ten }}</small><br>
                                    @endif
                                    @if($lichThi->giamThi2)
                                        <small>2. {{ $lichThi->giamThi2->ho_ten }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dao-tao.lich-thi.show', $lichThi) }}" class="btn btn-sm btn-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('dao-tao.lich-thi.edit', $lichThi) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('dao-tao.lich-thi.destroy', $lichThi) }}" method="POST" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa lịch thi này?')" class="d-inline">
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
                                <td colspan="10" class="text-center text-muted">
                                    <i class="bi bi-inbox"></i> Không có dữ liệu lịch thi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $lichThis->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
