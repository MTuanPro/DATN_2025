@extends('layouts.layout-giangvien')

@section('title', 'Lịch sử giảng dạy')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Lịch sử giảng dạy</h3>
                <p class="text-subtitle text-muted">Danh sách các buổi học đã hoàn thành</p>
            </div>
            <a href="{{ route('giangvien.buoi-hoc.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Tổng buổi đã dạy</h6>
                                    <h3 class="mb-0 text-success">{{ $tongBuoiDay }}</h3>
                                </div>
                                <div class="avatar bg-success">
                                    <i class="bi bi-check-circle text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Có tài liệu</h6>
                                    <h3 class="mb-0 text-primary">{{ $buoiCoTaiLieu }}</h3>
                                </div>
                                <div class="avatar bg-primary">
                                    <i class="bi bi-file-earmark-text text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Lớp học phần</h6>
                                    <h3 class="mb-0 text-info">{{ $soLopHocPhan }}</h3>
                                </div>
                                <div class="avatar bg-info">
                                    <i class="bi bi-book text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-0">Tỷ lệ hoàn thành</h6>
                                    <h3 class="mb-0 text-warning">{{ $tyLeHoanThanh }}%</h3>
                                </div>
                                <div class="avatar bg-warning">
                                    <i class="bi bi-graph-up text-white fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.buoi-hoc.history') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($danhSachLopHocPhan as $lop)
                                        <option value="{{ $lop->id }}" {{ request('lop_hoc_phan_id') == $lop->id ? 'selected' : '' }}>
                                            {{ $lop->ma_lop_hp }} - {{ $lop->monHoc->ten_mon ?? 'N/A' }}
                                        </option>
                                    @endforeach
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
                            <div class="col-md-3">
                                <label class="form-label">Tài liệu</label>
                                <select name="co_tai_lieu" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Tất cả --</option>
                                    <option value="1" {{ request('co_tai_lieu') == '1' ? 'selected' : '' }}>Có tài liệu</option>
                                    <option value="0" {{ request('co_tai_lieu') == '0' ? 'selected' : '' }}>Không có tài liệu</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách lịch sử -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Danh sách buổi học đã dạy
                        <span class="badge bg-success">{{ $buoiHocList->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($buoiHocList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày dạy</th>
                                        <th>Tiết</th>
                                        <th>Giờ</th>
                                        <th>Lớp HP</th>
                                        <th>Môn học</th>
                                        <th>Phòng</th>
                                        <th>Nội dung</th>
                                        <th>Tài liệu</th>
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
                                            <td>
                                                <small>
                                                    {{ $buoiHoc->gio_bat_dau }}<br>
                                                    {{ $buoiHoc->gio_ket_thuc }}
                                                </small>
                                            </td>
                                            <td>{{ $buoiHoc->lopHocPhan->ma_lop_hp }}</td>
                                            <td>
                                                <strong>{{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $buoiHoc->lopHocPhan->monHoc->ma_mon ?? '' }}</small>
                                            </td>
                                            <td>{{ $buoiHoc->phongHoc->ten_phong ?? 'N/A' }}</td>
                                            <td>
                                                @if($buoiHoc->noi_dung_giang_day)
                                                    <div style="max-width: 200px;">
                                                        {{ Str::limit($buoiHoc->noi_dung_giang_day, 50) }}
                                                        @if(strlen($buoiHoc->noi_dung_giang_day) > 50)
                                                            <button type="button" class="btn btn-link btn-sm p-0" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#contentModal{{ $buoiHoc->id }}">
                                                                <small>Xem thêm</small>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <!-- Modal nội dung -->
                                                    <div class="modal fade" id="contentModal{{ $buoiHoc->id }}" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Nội dung giảng dạy</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Ngày:</strong> {{ $buoiHoc->ngay_hoc->format('d/m/Y') }}</p>
                                                                    <p><strong>Môn:</strong> {{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</p>
                                                                    <hr>
                                                                    <p>{{ $buoiHoc->noi_dung_giang_day }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($buoiHoc->tai_lieu_dinh_kem)
                                                    <a href="{{ route('giangvien.buoi-hoc.download-tai-lieu', $buoiHoc->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank" 
                                                       title="Tải xuống tài liệu">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                            Không tìm thấy buổi học nào đã dạy.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
