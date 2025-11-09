@extends('layouts.layout-giangvien')

@section('title', 'Lịch Thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch thi lớp giảng dạy</h3>
                <p class="text-subtitle text-muted">Danh sách lịch thi các lớp bạn phụ trách</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

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
                <form action="{{ route('giangvien.lich-thi.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="month" name="thang" class="form-control" value="{{ request('thang') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tìm kiếm môn học</label>
                                <input type="text" name="search" class="form-control" placeholder="Mã môn, tên môn..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('giangvien.lich-thi.lich-coi-thi') }}" class="btn btn-outline-primary">
                    <i class="bi bi-eye"></i> Xem lịch coi thi
                </a>
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
                                <th>Đề thi</th>
                                <th>Đáp án</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichThis as $index => $lichThi)
                            <tr>
                                <td>{{ $lichThis->firstItem() + $index }}</td>
                                <td><strong>{{ $lichThi->lopHocPhan->ma_lop }}</strong></td>
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
                                <td>{{ $lichThi->phongHoc->ten_phong }}</td>
                                <td>{{ $lichThi->so_sinh_vien_du_thi ?? 'N/A' }}</td>
                                <td>
                                    @if($lichThi->de_thi_file)
                                        <span class="badge bg-success"><i class="bi bi-check"></i> Đã có</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lichThi->dap_an_file)
                                        <span class="badge bg-success"><i class="bi bi-check"></i> Đã có</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('giangvien.lich-thi.show', $lichThi) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    <i class="bi bi-inbox"></i> Không có lịch thi nào
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
