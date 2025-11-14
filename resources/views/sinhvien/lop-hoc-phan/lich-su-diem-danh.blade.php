@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử điểm danh')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử điểm danh</h3>
                    <p class="text-subtitle text-muted">
                        {{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }} - {{ $lopHocPhanSinhVien->lopHocPhan->ten_lop_hp }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.lop-hoc-phan.index') }}">Lớp học phần</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.lop-hoc-phan.show', $lopHocPhanSinhVien->id) }}">Chi tiết</a></li>
                            <li class="breadcrumb-item active">Lịch sử điểm danh</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thông tin lớp học phần --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> Thông tin lớp học phần
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Mã lớp HP:</strong> {{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }}</p>
                            <p class="mb-2"><strong>Tên lớp HP:</strong> {{ $lopHocPhanSinhVien->lopHocPhan->ten_lop_hp }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Môn học:</strong> {{ $lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</p>
                            <p class="mb-2"><strong>Số tín chỉ:</strong> <span class="badge bg-info">{{ $lopHocPhanSinhVien->lopHocPhan->monHoc->so_tin_chi }} TC</span></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Giảng viên:</strong> 
                                @if($lopHocPhanSinhVien->lopHocPhan->giangVienChinh && $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien)
                                    {{ $lopHocPhanSinhVien->lopHocPhan->giangVienChinh->giangVien->ho_ten }}
                                @else
                                    <span class="text-muted">Chưa phân công</span>
                                @endif
                            </p>
                            <p class="mb-2"><strong>Học kỳ:</strong> {{ $lopHocPhanSinhVien->lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhanSinhVien->lopHocPhan->hocKy->nam_hoc }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thống kê điểm danh --}}
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-2">Tổng buổi</h6>
                                    <h3 class="mb-0 text-primary">{{ $thongKe['tong_buoi'] }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-calendar-check fs-2 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-2">Có mặt</h6>
                                    <h3 class="mb-0 text-success">{{ $thongKe['co_mat'] }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle fs-2 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-2">Vắng</h6>
                                    <h3 class="mb-0 text-danger">{{ $thongKe['vang'] }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-x-circle fs-2 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-2">Đi trễ</h6>
                                    <h3 class="mb-0 text-warning">{{ $thongKe['di_tre'] }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clock-history fs-2 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-2">Nghỉ phép</h6>
                                    <h3 class="mb-0 text-info">{{ $thongKe['nghi_phep'] }}</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-clipboard-check fs-2 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body py-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-white mb-2">Tỷ lệ chuyên cần</h6>
                                    <h3 class="mb-0 text-white">{{ $thongKe['ty_le_chuyen_can'] }}%</h3>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="bi bi-graph-up-arrow fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cảnh báo tỷ lệ chuyên cần --}}
            @if($thongKe['ty_le_chuyen_can'] < 80)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Cảnh báo!</strong> Tỷ lệ chuyên cần của bạn là <strong>{{ $thongKe['ty_le_chuyen_can'] }}%</strong>, 
                    thấp hơn mức yêu cầu (80%). Bạn có thể bị cấm thi nếu không cải thiện.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif($thongKe['ty_le_chuyen_can'] < 90)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <strong>Lưu ý!</strong> Tỷ lệ chuyên cần của bạn là <strong>{{ $thongKe['ty_le_chuyen_can'] }}%</strong>. 
                    Hãy duy trì đi học đầy đủ để đảm bảo điều kiện dự thi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Danh sách điểm danh --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check"></i> Chi tiết điểm danh
                    </h5>
                    @if(!$diemDanhs->isEmpty())
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                <i class="bi bi-printer"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToExcel()">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="exportTableToPDF()">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($diemDanhs->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có dữ liệu điểm danh cho lớp học phần này.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="attendanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Ngày học</th>
                                        <th>Tiết học</th>
                                        <th>Phòng học</th>
                                        <th>Giảng viên</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian điểm danh</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($diemDanhs as $index => $diemDanh)
                                        <tr>
                                            <td>{{ $diemDanhs->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($diemDanh->ngay_hoc)->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($diemDanh->ngay_hoc)->translatedFormat('l') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    Tiết {{ $diemDanh->tiet_bat_dau }} - {{ $diemDanh->tiet_ket_thuc }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($diemDanh->lichHocChiTiet && $diemDanh->lichHocChiTiet->phongHoc)
                                                    <i class="bi bi-geo-alt"></i> {{ $diemDanh->lichHocChiTiet->phongHoc->ten_phong }}
                                                    <br>
                                                    <small class="text-muted">{{ $diemDanh->lichHocChiTiet->phongHoc->khu_vuc }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($diemDanh->lichHocChiTiet && $diemDanh->lichHocChiTiet->giangVien)
                                                    {{ $diemDanh->lichHocChiTiet->giangVien->ho_ten }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($diemDanh->trang_thai == 'co_mat')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Có mặt
                                                    </span>
                                                @elseif($diemDanh->trang_thai == 'vang')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Vắng
                                                    </span>
                                                @elseif($diemDanh->trang_thai == 'di_tre')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock-history"></i> Đi trễ
                                                    </span>
                                                @elseif($diemDanh->trang_thai == 'nghi_phep')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-clipboard-check"></i> Nghỉ phép
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $diemDanh->trang_thai }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($diemDanh->thoi_gian_diem_danh)
                                                    {{ \Carbon\Carbon::parse($diemDanh->thoi_gian_diem_danh)->format('H:i') }}
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($diemDanh->thoi_gian_diem_danh)->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($diemDanh->ghi_chu)
                                                    <span data-bs-toggle="tooltip" title="{{ $diemDanh->ghi_chu }}">
                                                        <i class="bi bi-info-circle text-primary"></i>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Hiển thị {{ $diemDanhs->firstItem() }} đến {{ $diemDanhs->lastItem() }} 
                                trong tổng số {{ $diemDanhs->total() }} bản ghi
                            </div>
                            <div>
                                {{ $diemDanhs->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Nút quay lại --}}
            <div class="mt-3">
                <a href="{{ route('sinh-vien.lop-hoc-phan.show', $lopHocPhanSinhVien->id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại chi tiết lớp học phần
                </a>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Export to Excel
    function exportTableToExcel() {
        const table = document.getElementById('attendanceTable');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Điểm danh"});
        XLSX.writeFile(wb, 'Lich_su_diem_danh_{{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }}.xlsx');
    }

    // Export to PDF
    function exportTableToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape orientation
        
        // Add Unicode font support (you may need to add a custom font for Vietnamese)
        doc.setFont("helvetica");
        
        // Title
        doc.setFontSize(16);
        doc.text('LỊCH SỬ ĐIỂM DANH', 148, 15, { align: 'center' });
        
        // Class info
        doc.setFontSize(10);
        doc.text('Lớp HP: {{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }} - {{ $lopHocPhanSinhVien->lopHocPhan->ten_lop_hp }}', 14, 25);
        doc.text('Môn học: {{ $lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}', 14, 30);
        doc.text('Sinh viên: {{ $sinhVien->ho_ten }} ({{ $sinhVien->ma_sinh_vien }})', 14, 35);
        doc.text('Tỷ lệ chuyên cần: {{ $thongKe["ty_le_chuyen_can"] }}%', 14, 40);
        
        // Table
        const table = document.getElementById('attendanceTable');
        doc.autoTable({
            html: table,
            startY: 45,
            theme: 'grid',
            styles: { 
                fontSize: 8,
                font: 'helvetica'
            },
            headStyles: { 
                fillColor: [66, 139, 202],
                textColor: 255
            }
        });
        
        doc.save('Lich_su_diem_danh_{{ $lopHocPhanSinhVien->lopHocPhan->ma_lop_hp }}.pdf');
    }

    // Print styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .sidebar, .navbar, .breadcrumb, .btn, .pagination, nav, .card-header .btn-group {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .page-heading {
                margin: 0 !important;
            }
            body {
                padding: 20px !important;
            }
            h3 {
                font-size: 18px !important;
                margin-bottom: 10px !important;
            }
            .table {
                font-size: 10px !important;
            }
            .badge {
                border: 1px solid #ddd !important;
                padding: 2px 5px !important;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
