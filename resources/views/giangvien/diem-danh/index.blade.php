@extends('layouts.layout-giangvien')

@section('title', 'Điểm danh')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Quản lý điểm danh</h3>
                <p class="text-subtitle text-muted">Danh sách buổi học cần điểm danh</p>
            </div>
            <a href="{{ route('giangvien.diem-danh.report') }}" class="btn btn-primary">
                <i class="bi bi-graph-up"></i> Báo cáo điểm danh
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.diem-danh.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($danhSachLopHocPhan as $lop)
                                        <option value="{{ $lop->id }}" {{ request('lop_hoc_phan_id') == $lop->id ? 'selected' : '' }}>
                                            {{ $lop->ma_lop_hp }} - {{ $lop->monHoc->ten_mon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <option value="chua_day" {{ request('trang_thai') == 'chua_day' ? 'selected' : '' }}>Chưa dạy</option>
                                    <option value="dang_day" {{ request('trang_thai') == 'dang_day' ? 'selected' : '' }}>Đang dạy</option>
                                    <option value="da_day" {{ request('trang_thai') == 'da_day' ? 'selected' : '' }}>Đã dạy</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" name="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}"
                                       onchange="document.getElementById('filterForm').submit()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" name="den_ngay" class="form-control" value="{{ request('den_ngay') }}"
                                       onchange="document.getElementById('filterForm').submit()">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('giangvien.diem-danh.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách buổi học -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Danh sách buổi học
                        <span class="badge bg-primary">{{ $buoiHocList->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($buoiHocList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày học</th>
                                        <th>Tiết</th>
                                        <th>Lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Phòng</th>
                                        <th>Trạng thái</th>
                                        <th>Thống kê điểm danh</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($buoiHocList as $index => $buoiHoc)
                                        <tr>
                                            <td>{{ $buoiHocList->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $buoiHoc->ngay_hoc->format('d/m/Y') }}</strong><br>
                                                <small class="text-muted">{{ $buoiHoc->ngay_hoc->dayName }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $buoiHoc->tiet_bat_dau }}-{{ $buoiHoc->tiet_ket_thuc }}
                                                </span>
                                            </td>
                                            <td>{{ $buoiHoc->lopHocPhan->ma_lop_hp }}</td>
                                            <td>
                                                <strong>{{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ $buoiHoc->phongHoc->ten_phong ?? 'N/A' }}</td>
                                            <td>
                                                @if($buoiHoc->trang_thai == 'chua_day')
                                                    <span class="badge bg-secondary">Chưa dạy</span>
                                                @elseif($buoiHoc->trang_thai == 'dang_day')
                                                    <span class="badge bg-warning">Đang dạy</span>
                                                @elseif($buoiHoc->trang_thai == 'da_day')
                                                    <span class="badge bg-success">Đã dạy</span>
                                                @else
                                                    <span class="badge bg-danger">Hủy</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($buoiHoc->diem_danh_stats && $buoiHoc->diem_danh_stats->tong > 0)
                                                    <div class="small">
                                                        <span class="text-success">✓ {{ $buoiHoc->diem_danh_stats->co_mat }}</span> /
                                                        <span class="text-danger">✗ {{ $buoiHoc->diem_danh_stats->vang }}</span> /
                                                        <span class="text-warning">⏱ {{ $buoiHoc->diem_danh_stats->di_tre }}</span> /
                                                        <span class="text-info">☂ {{ $buoiHoc->diem_danh_stats->nghi_phep }}</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        Tổng: {{ $buoiHoc->diem_danh_stats->tong }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">Chưa điểm danh</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('giangvien.diem-danh.show', $buoiHoc->id) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   title="Điểm danh">
                                                    <i class="bi bi-clipboard-check"></i> Điểm danh
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Hiển thị {{ $buoiHocList->firstItem() }} - {{ $buoiHocList->lastItem() }} 
                                trong tổng {{ $buoiHocList->total() }} buổi học
                            </div>
                            <div>
                                {{ $buoiHocList->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Không tìm thấy buổi học nào.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
