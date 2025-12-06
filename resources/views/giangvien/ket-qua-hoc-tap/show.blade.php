@extends('layouts.layout-giangvien')

@section('title', 'Bảng điểm tổng kết')

@section('content')
    <div class="page-heading">
                <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Bảng điểm tổng kết</h3>
                    <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.ket-qua-hoc-tap.index') }}">Kết quả học
                                    tập</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Bảng điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông tin lớp học phần -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Mã lớp:</th>
                                    <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>{{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}</td>
                                </tr>
                                <tr>
                                    <th>Số tín chỉ:</th>
                                    <td>{{ $lopHocPhan->monHoc->so_tin_chi }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Học kỳ:</th>
                                    <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><span class="badge bg-info">{{ $danhSachSinhVien->count() }} sinh viên</span></td>
                                </tr>
                                <tr>
                                    <th>Giảng viên:</th>
                                    <td>
                                        @foreach ($lopHocPhan->giangViens as $gv)
                                            <span class="badge bg-primary">{{ $gv->ho_ten }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Thống kê tổng quan -->
        <section class="section">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                    <h3 class="font-extrabold mb-0">{{ $thongKe['tong_sv'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Qua môn</h6>
                                    <h3 class="font-extrabold mb-0 text-success">{{ $thongKe['sv_qua_mon'] }}</h3>
                                    <small class="text-muted">{{ $thongKe['tong_sv'] > 0 ? round($thongKe['sv_qua_mon'] / $thongKe['tong_sv'] * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-x-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Trượt môn</h6>
                                    <h3 class="font-extrabold mb-0 text-danger">{{ $thongKe['sv_truot'] }}</h3>
                                    <small class="text-muted">{{ $thongKe['tong_sv'] > 0 ? round($thongKe['sv_truot'] / $thongKe['tong_sv'] * 100, 1) : 0 }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Điểm TB</h6>
                                    <h3 class="font-extrabold mb-0 text-primary">{{ number_format($thongKe['diem_trung_binh'], 2) }}</h3>
                                    <small class="text-muted">Hệ 10</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Phân bố điểm -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân bố điểm theo loại</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $phanBoDiemChu = $danhSachSinhVien->groupBy('diem_chu')->map(fn($g) => $g->count());
                        @endphp
                        @foreach (['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'] as $loai)
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <h6 class="mb-1">Loại {{ $loai }}</h6>
                                    <h3 class="mb-0 
                                        @if ($loai == 'A') text-success
                                        @elseif($loai == 'F') text-danger
                                        @else text-primary @endif">
                                        {{ $phanBoDiemChu[$loai] ?? 0 }}
                                    </h3>
                                    <small class="text-muted">sinh viên</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Bảng điểm chi tiết -->
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Bảng điểm chi tiết</h4>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="xuatDanhSachThi()">
                            <i class="bi bi-file-earmark-text"></i> Xuất danh sách thi
                        </button>
                        {{-- Tạm thời ẩn xuất file --}}
                        {{--
                        <a href="{{ route('giangvien.ket-qua-hoc-tap.export-excel', $lopHocPhan->id) }}"
                            class="btn btn-success btn-sm">
                            <i class="bi bi-file-excel"></i> Xuất Excel
                        </a>
                        <a href="{{ route('giangvien.ket-qua-hoc-tap.export-pdf', $lopHocPhan->id) }}"
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-file-pdf"></i> Xuất PDF
                        </a>
                        --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2">STT</th>
                                    <th rowspan="2">Mã SV</th>
                                    <th rowspan="2">Họ tên</th>
                                    <th rowspan="2">Điểm danh</th>
                                    @foreach($cauHinhs as $ch)
                                        <th colspan="{{ $ch->so_cot }}" class="text-center">
                                            {{ $ch->ten_dau_diem }}<br>
                                            <small>({{ $ch->ty_le }}%)</small>
                                        </th>
                                    @endforeach
                                    <th rowspan="2">Hệ 10</th>
                                    <th rowspan="2">Hệ 4</th>
                                    <th rowspan="2">Chữ</th>
                                    <th rowspan="2">Kết quả</th>
                                </tr>
                                <tr>
                                    @foreach($cauHinhs as $ch)
                                        @for($i = 1; $i <= $ch->so_cot; $i++)
                                            <th class="text-center">
                                                @if($ch->so_cot > 1) Cột {{ $i }} @else - @endif
                                            </th>
                                        @endfor
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachSinhVien as $index => $lhpsv)
                                    @php
                                        // Tính tỷ lệ điểm danh
                                        $tongBuoi = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function($q) use ($lopHocPhan) {
                                                $q->where('lop_hoc_phan_id', $lopHocPhan->id);
                                            })
                                            ->distinct('lich_hoc_chi_tiet_id')
                                            ->count('lich_hoc_chi_tiet_id');
                                        
                                        $buoiCoMat = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)
                                            ->where('trang_thai', 'co_mat')
                                            ->count();
                                        
                                        $tyLeDiemDanh = $tongBuoi > 0 ? round(($buoiCoMat / $tongBuoi) * 100, 1) : 0;
                                        
                                        // Lấy điểm CC (Chuyên cần)
                                        $cauHinhCC = $cauHinhs->firstWhere('loai_dau_diem', 'chuyen_can');
                                        $diemCC = null;
                                        if ($cauHinhCC) {
                                            $diemCCRecord = $lhpsv->danh_sach_diem->where('cau_hinh_id', $cauHinhCC->id)->first();
                                            if ($diemCCRecord) {
                                                // Chuyển điểm hệ 10 sang hệ 4
                                                $diemCC = $diemCCRecord->diem_so;
                                                if ($diemCC >= 9.0) $diemCC = 4.0;
                                                elseif ($diemCC >= 8.5) $diemCC = 3.5;
                                                elseif ($diemCC >= 8.0) $diemCC = 3.0;
                                                elseif ($diemCC >= 7.0) $diemCC = 2.5;
                                                elseif ($diemCC >= 6.5) $diemCC = 2.0;
                                                elseif ($diemCC >= 5.5) $diemCC = 1.5;
                                                elseif ($diemCC >= 5.0) $diemCC = 1.0;
                                                else $diemCC = 0;
                                            }
                                        }
                                        
                                        // Kiểm tra điều kiện: điểm danh >= 80% VÀ điểm CC >= 2/4
                                        $duDieuKienDiemDanh = $tyLeDiemDanh >= 80;
                                        $duDieuKienDiemCC = $diemCC !== null && $diemCC >= 2.0;
                                        $duDieuKienThi = $duDieuKienDiemDanh && $duDieuKienDiemCC;
                                    @endphp
                                    <tr class="{{ !$duDieuKienThi && $tongBuoi > 0 ? 'table-danger' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $lhpsv->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                        <td class="text-center">
                                            @if($tongBuoi > 0)
                                                <span class="badge {{ $duDieuKienDiemDanh ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $tyLeDiemDanh }}% ({{ $buoiCoMat }}/{{ $tongBuoi }})
                                                </span>
                                                @if(!$duDieuKienThi)
                                                    <br><small class="text-danger">
                                                        @if(!$duDieuKienDiemDanh)
                                                            Điểm danh < 80%
                                                        @endif
                                                        @if(!$duDieuKienDiemCC)
                                                            {{ !$duDieuKienDiemDanh ? ', ' : '' }}Điểm CC < 2/4
                                                        @endif
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        @foreach($cauHinhs as $ch)
                                            @for($cot = 1; $cot <= $ch->so_cot; $cot++)
                                                @php
                                                    $diem = $lhpsv->danh_sach_diem->where('cau_hinh_id', $ch->id)->where('cot_diem', $cot)->first();
                                                @endphp
                                                <td class="text-center">
                                                    {{ $diem ? number_format($diem->diem_so, 2) : '-' }}
                                                </td>
                                            @endfor
                                        @endforeach
                                        
                                        <td class="text-center">
                                            @if($lhpsv->diem_tong_ket)
                                                <strong>{{ number_format($lhpsv->diem_tong_ket, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_4)
                                                {{ number_format($lhpsv->ketQuaHocTap->diem_he_4, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($lhpsv->diem_chu)
                                                <span class="badge bg-info">{{ $lhpsv->diem_chu }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($lhpsv->diem_tong_ket)
                                                @if($lhpsv->diem_tong_ket >= 4)
                                                    <span class="badge bg-success">Qua môn</span>
                                                @else
                                                    <span class="badge bg-danger">Trượt</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Chưa có</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center text-muted">Chưa có dữ liệu điểm</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@push('scripts')
<script>
function xuatDanhSachThi() {
    // Tìm bảng điểm chính xác
    const bangDiem = document.querySelector('.card-body .table-responsive table tbody');
    if (!bangDiem) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không tìm thấy bảng điểm',
        });
        return;
    }
    
    // Thu thập dữ liệu sinh viên đủ điều kiện thi
    const rows = bangDiem.querySelectorAll('tr:not(.table-danger)');
    
    if (rows.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Không có sinh viên đủ điều kiện',
            text: 'Không có sinh viên nào đủ điều kiện dự thi (Yêu cầu: Điểm danh >= 80% VÀ Điểm CC >= 2/4)',
        });
        return;
    }
    
    let danhSach = [];
    let stt = 1;
    
    rows.forEach(row => {
        const cells = row.cells;
        if (cells.length > 0 && !row.classList.contains('table-danger')) {
            const maSV = cells[1].textContent.trim();
            const hoTen = cells[2].textContent.trim();
            const diemDanh = cells[3].textContent.trim();
            
            danhSach.push({
                stt: stt++,
                maSV: maSV,
                hoTen: hoTen,
                diemDanh: diemDanh
            });
        }
    });
    
    // Tạo HTML cho bảng danh sách thi
    let html = `
        <div style="padding: 20px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3>DANH SÁCH SINH VIÊN DỰ THI</h3>
                <p><strong>Môn:</strong> {{ $lopHocPhan->monHoc->ten_mon }}</p>
                <p><strong>Lớp:</strong> {{ $lopHocPhan->ma_lop_hp }}</p>
                <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</p>
                <p><strong>Tổng sinh viên đủ điều kiện:</strong> ${danhSach.length}</p>
            </div>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="width: 50px;">STT</th>
                        <th style="width: 120px;">Mã SV</th>
                        <th>Họ và tên</th>
                        <th style="width: 150px;">Điểm danh</th>
                        <th style="width: 150px;">Chữ ký</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    danhSach.forEach(sv => {
        html += `
            <tr>
                <td style="text-align: center;">${sv.stt}</td>
                <td style="text-align: center;"><strong>${sv.maSV}</strong></td>
                <td>${sv.hoTen}</td>
                <td style="text-align: center;">${sv.diemDanh}</td>
                <td></td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
            <div style="margin-top: 30px; text-align: right;">
                <p><em>Ngày ${new Date().getDate()} tháng ${new Date().getMonth() + 1} năm ${new Date().getFullYear()}</em></p>
                <p><strong>Giảng viên</strong></p>
                <p style="margin-top: 60px;"><em>(Ký và ghi rõ họ tên)</em></p>
            </div>
        </div>
    `;
    
    // Mở cửa sổ mới và in
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Danh sách sinh viên dự thi</title>
            <style>
                @media print {
                    body { margin: 20px; }
                }
                body { font-family: 'Times New Roman', Times, serif; font-size: 14px; }
                table { width: 100%; }
                th, td { padding: 8px; text-align: left; }
                th { background-color: #f0f0f0; }
                @page { margin: 2cm; }
            </style>
        </head>
        <body>
            ${html}
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endpush

@endsection
