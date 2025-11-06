@extends('layouts.sinhvien')

@section('title', 'Lịch thi cá nhân')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch thi cá nhân</h3>
                <p class="text-subtitle text-muted">Xem lịch thi các môn đã đăng ký</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="iconly-boldShow"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Tổng số ca thi</h6>
                            <h6 class="font-extrabold mb-0">{{ $lichThis->total() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="iconly-boldCalendar"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Sắp thi</h6>
                            <h6 class="font-extrabold mb-0">{{ $lichThis->where('ngay_thi', '>=', now()->toDateString())->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sinhvien.lich-thi.index') }}" method="GET">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="sap_thi" {{ request('trang_thai') == 'sap_thi' ? 'selected' : '' }}>Sắp thi</option>
                                    <option value="da_thi" {{ request('trang_thai') == 'da_thi' ? 'selected' : '' }}>Đã thi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('sinhvien.lich-thi.calendar') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar3"></i> Xem dạng lịch
                </a>
                <a href="{{ route('sinhvien.lich-thi.export-pdf') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-pdf"></i> Xuất PDF
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
                                <th>Môn học</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ thi</th>
                                <th>Phòng thi</th>
                                <th>Hình thức</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichThis as $index => $lichThi)
                            <tr class="{{ $lichThi->ngay_thi->isToday() ? 'table-warning' : '' }}">
                                <td>{{ $lichThis->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</strong>
                                    <br><small class="text-muted">{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</small>
                                    <br><small class="text-muted">Lớp: {{ $lichThi->lopHocPhan->ma_lop }}</small>
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
                                <td>
                                    <strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $lichThi->ngay_thi->locale('vi')->isoFormat('dddd') }}</small>
                                </td>
                                <td>
                                    {{ $lichThi->gio_bat_dau }}<br>
                                    <small class="text-muted">đến</small><br>
                                    {{ $lichThi->gio_ket_thuc }}
                                </td>
                                <td>
                                    <strong>{{ $lichThi->phongHoc->ten_phong }}</strong>
                                    @if($lichThi->phongHoc->vi_tri)
                                        <br><small class="text-muted">{{ $lichThi->phongHoc->vi_tri }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($lichThi->hinh_thuc_thi == 'offline')
                                        <span class="badge bg-secondary">Tại trường</span>
                                    @elseif($lichThi->hinh_thuc_thi == 'online')
                                        <span class="badge bg-primary">Online</span>
                                    @else
                                        <span class="badge bg-success">Kết hợp</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lichThi->ngay_thi < now()->toDateString())
                                        <span class="badge bg-success">Đã thi</span>
                                    @elseif($lichThi->ngay_thi->isToday())
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-circle"></i> HÔM NAY
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            Còn {{ $lichThi->ngay_thi->diffInDays(now()) }} ngày
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('sinhvien.lich-thi.show', $lichThi) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Không có lịch thi nào</p>
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
