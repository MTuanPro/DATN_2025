@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử điểm danh')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử điểm danh</h3>
                    <p class="text-subtitle text-muted">Xem lịch sử điểm danh các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lịch sử điểm danh</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Filter --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('sinh-vien.diem-danh.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="hoc_ky_id" class="form-label mb-1">Học kỳ</label>
                            <select name="hoc_ky_id" id="hoc_ky_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- Tất cả học kỳ --</option>
                                @foreach($hocKys as $hk)
                                    <option value="{{ $hk->id }}" {{ $hocKyId == $hk->id ? 'selected' : '' }}>
                                        {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        @if($hk->la_hoc_ky_hien_tai) (Hiện tại) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            @if($lopHocPhans->isEmpty())
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Không tìm thấy lớp học phần nào trong học kỳ này.
                        </div>
                    </div>
                </div>
            @else
                {{-- Bảng điểm danh --}}
                @foreach($lopHocPhans as $lhp)
                    @php
                        $thongKe = $thongKeData[$lhp->id] ?? [
                            'tong_buoi' => 0,
                            'co_mat' => 0,
                            'vang' => 0,
                            'di_tre' => 0,
                            'nghi_phep' => 0,
                            'ty_le' => 0
                        ];
                    @endphp

                    <div class="card mb-4">
                        {{-- Header với info lớp --}}
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-journal-text text-primary"></i>
                                        {{ $lhp->lopHocPhan->monHoc->ten_mon }} ({{ $lhp->lopHocPhan->monHoc->ma_mon }}) - {{ $lhp->lopHocPhan->ma_lop_hp }}
                                    </h6>
                                    <small class="text-muted">
                                        <span class="me-3">
                                            <i class="bi bi-person"></i>
                                            @if($lhp->lopHocPhan->giangVienChinh && $lhp->lopHocPhan->giangVienChinh->giangVien)
                                                {{ $lhp->lopHocPhan->giangVienChinh->giangVien->ho_ten }}
                                            @else
                                                Chưa phân công
                                            @endif
                                        </span>
                                    </small>
                                </div>
                                @if($thongKe['tong_buoi'] > 0)
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="printTable('table-{{ $lhp->id }}')">
                                            <i class="bi bi-printer"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyTable('table-{{ $lhp->id }}')">
                                            <i class="bi bi-clipboard"></i> Copy
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="exportToExcel('table-{{ $lhp->id }}', '{{ $lhp->lopHocPhan->ma_lop_hp }}')">
                                            <i class="bi bi-file-earmark-excel"></i> Excel
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="exportToCSV('table-{{ $lhp->id }}', '{{ $lhp->lopHocPhan->ma_lop_hp }}')">
                                            <i class="bi bi-filetype-csv"></i> CSV
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="exportToPDF('table-{{ $lhp->id }}', '{{ $lhp->lopHocPhan->monHoc->ten_mon }}', '{{ $lhp->lopHocPhan->ma_lop_hp }}')">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @if($thongKe['tong_buoi'] == 0)
                                <div class="p-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> Chưa có dữ liệu điểm danh cho lớp này.
                                    </div>
                                </div>
                            @else
                                {{-- Thống kê dạng text --}}
                                <div class="px-4 py-3 border-bottom bg-light">
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Vắng: <span class="text-danger">{{ $thongKe['vang'] }}/{{ $thongKe['tong_buoi'] }} ({{ round(($thongKe['vang']/$thongKe['tong_buoi'])*100, 0) }}%)</span> trên tổng số buổi điểm danh</strong>
                                            <span class="float-end">
                                                <strong>Tỷ lệ chuyên cần: 
                                                    <span class="
                                                        @if($thongKe['ty_le'] >= 90) text-success
                                                        @elseif($thongKe['ty_le'] >= 80) text-warning
                                                        @else text-danger
                                                        @endif
                                                    ">{{ $thongKe['ty_le'] }}%</span>
                                                </strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bảng chi tiết --}}
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0" id="table-{{ $lhp->id }}">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">STT</th>
                                                <th>Ngày</th>
                                                <th>Tiết</th>
                                                <th>Phòng học</th>
                                                <th>Giảng viên</th>
                                                <th>Trạng thái</th>
                                                <th>Thời gian điểm danh</th>
                                                <th>Ghi chú</th>
                                                <th style="width: 150px;">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $diemDanhList = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                                                    ->with(['lichHocChiTiet.phongHoc', 'lichHocChiTiet.giangVien'])
                                                    ->join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                                                    ->select('diem_danh.*', 'lich_hoc_chi_tiet.ngay_hoc', 'lich_hoc_chi_tiet.tiet_bat_dau', 'lich_hoc_chi_tiet.tiet_ket_thuc')
                                                    ->orderBy('lich_hoc_chi_tiet.ngay_hoc', 'desc')
                                                    ->get();
                                            @endphp
                                            
                                            @foreach($diemDanhList as $index => $dd)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($dd->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse($dd->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->translatedFormat('l') }}
                                                    </td>
                                                    <td>{{ $dd->tiet_bat_dau }}</td>
                                                    <td>
                                                        @if($dd->lichHocChiTiet && $dd->lichHocChiTiet->phongHoc)
                                                            {{ $dd->lichHocChiTiet->phongHoc->ten_phong }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($dd->lichHocChiTiet && $dd->lichHocChiTiet->giangVien)
                                                            {{ $dd->lichHocChiTiet->giangVien->ho_ten }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($dd->trang_thai == 'co_mat')
                                                            <span class="text-success fw-bold">Present</span>
                                                        @elseif($dd->trang_thai == 'vang')
                                                            <span class="text-danger fw-bold">Absent</span>
                                                        @elseif($dd->trang_thai == 'di_tre')
                                                            <span class="text-warning fw-bold">Late</span>
                                                        @elseif($dd->trang_thai == 'nghi_phep')
                                                            <span class="text-info fw-bold">Excused</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($dd->thoi_gian_diem_danh)
                                                            {{ \Carbon\Carbon::parse($dd->thoi_gian_diem_danh)->setTimezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $dd->ghi_chu ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $ngayHoc = \Carbon\Carbon::parse($dd->ngay_hoc)->setTimezone('Asia/Ho_Chi_Minh')->startOfDay();
                                                            $ngayHienTai = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
                                                            
                                                            // Nếu có thời gian điểm danh, kiểm tra xem có phải trong vòng 24h từ khi điểm danh không
                                                            $coTheGuiYeuCau = false;
                                                            if ($dd->thoi_gian_diem_danh && in_array($dd->trang_thai, ['vang', 'di_tre'])) {
                                                                $thoiGianDiemDanh = \Carbon\Carbon::parse($dd->thoi_gian_diem_danh)->setTimezone('Asia/Ho_Chi_Minh');
                                                                $thoiGianHienTai = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                                                                // Cho phép gửi trong vòng 24h từ khi điểm danh (nếu điểm danh trong ngày học) hoặc trong ngày học
                                                                $coTheGuiYeuCau = ($ngayHoc->isSameDay($ngayHienTai) || 
                                                                                  ($thoiGianDiemDanh->isSameDay($ngayHienTai) && $thoiGianHienTai->diffInHours($thoiGianDiemDanh) <= 24));
                                                            } else {
                                                                // Nếu chưa có điểm danh hoặc không phải vắng/đi trễ, chỉ cho phép trong ngày học
                                                                $coTheGuiYeuCau = $ngayHoc->isSameDay($ngayHienTai) && 
                                                                                  in_array($dd->trang_thai, ['vang', 'di_tre']);
                                                            }
                                                            
                                                            $daCoYeuCau = \App\Models\YeuCauDiemDanhBu::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                                                                ->where('lich_hoc_chi_tiet_id', $dd->lich_hoc_chi_tiet_id)
                                                                ->whereIn('trang_thai', ['cho_duyet', 'da_duyet'])
                                                                ->exists();
                                                        @endphp
                                                        @if($coTheGuiYeuCau && !$daCoYeuCau && in_array($dd->trang_thai, ['vang', 'di_tre']))
                                                            <button type="button" class="btn btn-sm btn-warning" 
                                                                    onclick="guiYeuCauDiemDanhBu({{ $dd->lich_hoc_chi_tiet_id }}, '{{ $dd->ngay_hoc }}')">
                                                                <i class="bi bi-send"></i> Gửi yêu cầu
                                                            </button>
                                                        @elseif($daCoYeuCau)
                                                            <span class="badge bg-info">Đã gửi yêu cầu</span>
                                                        @elseif(in_array($dd->trang_thai, ['vang', 'di_tre']) && !$coTheGuiYeuCau)
                                                            <small class="text-muted">Đã quá ngày</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Footer info --}}
                                <div class="px-4 py-2 border-top bg-light">
                                    <small class="text-muted">
                                        Đang xem 1 đến {{ $diemDanhList->count() }} trong tổng số {{ $thongKe['tong_buoi'] }} bản ghi
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </section>
    </div>
{{-- Modal gửi yêu cầu điểm danh bù --}}
<div class="modal fade" id="modalYeuCauDiemDanhBu" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gửi yêu cầu điểm danh bù</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formYeuCauDiemDanhBu">
                <div class="modal-body">
                    <input type="hidden" id="lich_hoc_chi_tiet_id" name="lich_hoc_chi_tiet_id">
                    <div class="mb-3">
                        <label class="form-label">Ngày học</label>
                        <input type="text" id="ngay_hoc_display" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ly_do" name="ly_do" rows="4" required 
                                  placeholder="Vui lòng nhập lý do xin điểm danh bù (tối đa 1000 ký tự)"></textarea>
                        <small class="text-muted">Lưu ý: Chỉ có thể gửi yêu cầu trong ngày học. Ngày hôm sau sẽ không được gửi yêu cầu.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function guiYeuCauDiemDanhBu(lichHocChiTietId, ngayHoc) {
    document.getElementById('lich_hoc_chi_tiet_id').value = lichHocChiTietId;
    document.getElementById('ngay_hoc_display').value = new Date(ngayHoc).toLocaleDateString('vi-VN');
    document.getElementById('ly_do').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('modalYeuCauDiemDanhBu'));
    modal.show();
}

document.getElementById('formYeuCauDiemDanhBu').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        lich_hoc_chi_tiet_id: document.getElementById('lich_hoc_chi_tiet_id').value,
        ly_do: document.getElementById('ly_do').value,
        _token: '{{ csrf_token() }}'
    };
    
    fetch('{{ route("sinh-vien.diem-danh.yeu-cau-diem-danh-bu.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Thành công!', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Lỗi!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi gửi yêu cầu', 'error');
    });
});
</script>
@endpush

@push('styles')
<style>
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    @media print {
        .btn-group, .breadcrumb, nav, .card-header .btn-group {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
// Print table
function printTable(tableId) {
    var printContents = document.getElementById(tableId).outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = '<table class="table table-bordered">' + printContents + '</table>';
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

// Copy table
function copyTable(tableId) {
    var table = document.getElementById(tableId);
    var range = document.createRange();
    range.selectNode(table);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    alert('Đã sao chép bảng vào clipboard!');
}

// Export to Excel
function exportToExcel(tableId, fileName) {
    var table = document.getElementById(tableId);
    var wb = XLSX.utils.table_to_book(table, {sheet: "Điểm danh"});
    XLSX.writeFile(wb, 'DiemDanh_' + fileName + '.xlsx');
}

// Export to CSV
function exportToCSV(tableId, fileName) {
    var table = document.getElementById(tableId);
    var csv = [];
    var rows = table.querySelectorAll('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(','));
    }
    
    var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    downloadLink.download = 'DiemDanh_' + fileName + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

// Export to PDF
function exportToPDF(tableId, monHoc, maLop) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    
    doc.setFont("helvetica");
    doc.setFontSize(16);
    doc.text('LỊCH SỬ ĐIỂM DANH', 148, 15, { align: 'center' });
    
    doc.setFontSize(11);
    doc.text('Môn: ' + monHoc, 14, 25);
    doc.text('Lớp: ' + maLop, 14, 32);
    
    const table = document.getElementById(tableId);
    doc.autoTable({
        html: table,
        startY: 38,
        theme: 'grid',
        styles: { fontSize: 8, font: 'helvetica' },
        headStyles: { fillColor: [66, 139, 202] }
    });
    
    doc.save('DiemDanh_' + maLop + '.pdf');
}
</script>
@endpush

@endsection
