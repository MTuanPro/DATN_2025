@extends('layouts.layout-giangvien')

@section('title', 'Báo cáo điểm danh')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Báo cáo điểm danh</h3>
                <p class="text-subtitle text-muted">Thống kê chuyên cần theo lớp học phần</p>
            </div>
            <a href="{{ route('giangvien.diem-danh.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Chọn lớp -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.diem-danh.report') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label class="form-label">Chọn lớp học phần <span class="text-danger">*</span></label>
                                <select name="lop_hoc_phan_id" class="form-select" required>
                                    <option value="">-- Chọn lớp học phần --</option>
                                    @foreach($danhSachLopHocPhan as $lop)
                                        <option value="{{ $lop->id }}" {{ $lopHocPhanId == $lop->id ? 'selected' : '' }}>
                                            {{ $lop->ma_lop_hp }} - {{ $lop->monHoc->ten_mon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Xem báo cáo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($baoCao !== null)
                <!-- Báo cáo chi tiết -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Báo cáo điểm danh
                            <span class="badge bg-primary">{{ count($baoCao) }} sinh viên</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($baoCao) > 0)
                            <!-- Thống kê tổng quan -->
                            @php
                                $tongCoMat = collect($baoCao)->sum('stats.co_mat');
                                $tongVang = collect($baoCao)->sum('stats.vang');
                                $tongDiTre = collect($baoCao)->sum('stats.di_tre');
                                $tongNghiPhep = collect($baoCao)->sum('stats.nghi_phep');
                                $tyLeTrungBinh = collect($baoCao)->avg('ty_le_co_mat');
                            @endphp

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="text-white mb-0">Tổng có mặt</h6>
                                            <h3 class="text-white mb-0">{{ $tongCoMat }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body">
                                            <h6 class="text-white mb-0">Tổng vắng</h6>
                                            <h3 class="text-white mb-0">{{ $tongVang }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h6 class="text-white mb-0">Tổng đi trễ</h6>
                                            <h3 class="text-white mb-0">{{ $tongDiTre }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h6 class="text-white mb-0">Tỷ lệ TB</h6>
                                            <h3 class="text-white mb-0">{{ number_format($tyLeTrungBinh, 1) }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã SV</th>
                                            <th>Họ và tên</th>
                                            <th>Lớp HC</th>
                                            <th class="text-center">Tổng buổi</th>
                                            <th class="text-center">Có mặt</th>
                                            <th class="text-center">Vắng</th>
                                            <th class="text-center">Đi trễ</th>
                                            <th class="text-center">Nghỉ phép</th>
                                            <th class="text-center">Tỷ lệ (%)</th>
                                            <th class="text-center">Đánh giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($baoCao as $index => $item)
                                            @php
                                                $tyLe = $item['ty_le_co_mat'];
                                                if ($tyLe >= 90) {
                                                    $danhGia = ['Xuất sắc', 'success'];
                                                } elseif ($tyLe >= 80) {
                                                    $danhGia = ['Tốt', 'primary'];
                                                } elseif ($tyLe >= 70) {
                                                    $danhGia = ['Khá', 'info'];
                                                } elseif ($tyLe >= 60) {
                                                    $danhGia = ['Trung bình', 'warning'];
                                                } else {
                                                    $danhGia = ['Yếu', 'danger'];
                                                }
                                            @endphp
                                            <tr class="{{ $tyLe < 60 ? 'table-danger' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $item['sinh_vien']->ma_sinh_vien }}</strong></td>
                                                <td>{{ $item['sinh_vien']->ho_ten }}</td>
                                                <td>{{ $item['sinh_vien']->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
                                                <td class="text-center"><strong>{{ $item['tong_buoi_hoc'] }}</strong></td>
                                                <td class="text-center text-success"><strong>{{ $item['stats']->co_mat }}</strong></td>
                                                <td class="text-center text-danger">{{ $item['stats']->vang }}</td>
                                                <td class="text-center text-warning">{{ $item['stats']->di_tre }}</td>
                                                <td class="text-center text-info">{{ $item['stats']->nghi_phep }}</td>
                                                <td class="text-center">
                                                    <strong>{{ $tyLe }}%</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $danhGia[1] }}">{{ $danhGia[0] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Ghi chú -->
                            <div class="alert alert-info mt-3">
                                <h6><i class="bi bi-info-circle"></i> Ghi chú:</h6>
                                <ul class="mb-0">
                                    <li><strong>Xuất sắc:</strong> ≥ 90% - Chuyên cần tốt</li>
                                    <li><strong>Tốt:</strong> 80-89% - Chuyên cần khá tốt</li>
                                    <li><strong>Khá:</strong> 70-79% - Chuyên cần ổn định</li>
                                    <li><strong>Trung bình:</strong> 60-69% - Cần cải thiện</li>
                                    <li><strong>Yếu:</strong> < 60% - Cần quan tâm đặc biệt</li>
                                </ul>
                            </div>

                            <!-- Nút xuất báo cáo -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <form method="POST" action="{{ route('giangvien.diem-danh.canh-bao') }}" 
                                          onsubmit="return confirm('Xác nhận gửi cảnh báo đến các sinh viên có tỷ lệ chuyên cần < 80%?');"
                                          class="d-inline">
                                        @csrf
                                        <input type="hidden" name="lop_hoc_phan_id" value="{{ $lopHocPhanId }}">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-send"></i> Gửi cảnh báo chuyên cần
                                        </button>
                                    </form>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('giangvien.diem-danh.export-pdf', ['lop_hoc_phan_id' => $lopHocPhanId]) }}" 
                                       class="btn btn-danger" 
                                       target="_blank"
                                       title="Xuất PDF để in">
                                        <i class="bi bi-file-pdf"></i> Xuất PDF
                                    </a>
                                    <a href="{{ route('giangvien.diem-danh.export-excel', ['lop_hoc_phan_id' => $lopHocPhanId]) }}" 
                                       class="btn btn-success"
                                       title="Tải xuống file Excel">
                                        <i class="bi bi-file-excel"></i> Xuất Excel
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Không có dữ liệu điểm danh cho lớp này.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
