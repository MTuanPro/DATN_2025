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
                                    <small
                                        class="text-muted">{{ $thongKe['tong_sv'] > 0 ? round(($thongKe['sv_qua_mon'] / $thongKe['tong_sv']) * 100, 1) : 0 }}%</small>
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
                                    <small
                                        class="text-muted">{{ $thongKe['tong_sv'] > 0 ? round(($thongKe['sv_truot'] / $thongKe['tong_sv']) * 100, 1) : 0 }}%</small>
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
                                    <h3 class="font-extrabold mb-0 text-primary">
                                        {{ number_format($thongKe['diem_trung_binh'], 2) }}</h3>
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
                                    <h3
                                        class="mb-0 
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
                                    @foreach ($cauHinhs as $ch)
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
                                    @foreach ($cauHinhs as $ch)
                                        @for ($i = 1; $i <= $ch->so_cot; $i++)
                                            <th class="text-center">
                                                @if ($ch->so_cot > 1)
                                                    Cột {{ $i }}
                                                @else
                                                    -
                                                @endif
                                            </th>
                                        @endfor
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachSinhVien as $index => $lhpsv)
                                    @php
                                        // Tính tỷ lệ điểm danh
                                        $tongBuoi = \App\Models\DiemDanh::whereHas('lopHocPhanSinhVien', function (
                                            $q,
                                        ) use ($lopHocPhan) {
                                            $q->where('lop_hoc_phan_id', $lopHocPhan->id);
                                        })
                                            ->distinct('lich_hoc_chi_tiet_id')
                                            ->count('lich_hoc_chi_tiet_id');

                                        $buoiCoMat = \App\Models\DiemDanh::where(
                                            'lop_hoc_phan_sinh_vien_id',
                                            $lhpsv->id,
                                        )
                                            ->where('trang_thai', 'co_mat')
                                            ->count();

                                        $tyLeDiemDanh = $tongBuoi > 0 ? round(($buoiCoMat / $tongBuoi) * 100, 1) : 0;

                                        // Lấy điểm CC (Chuyên cần)
                                        $cauHinhCC = $cauHinhs->firstWhere('ten_dau_diem', 'Chuyên cần');
                                        $diemCC = null;
                                        if ($cauHinhCC) {
                                            $diemCCRecord = $lhpsv->danh_sach_diem
                                                ->where('cau_hinh_id', $cauHinhCC->id)
                                                ->first();
                                            if ($diemCCRecord) {
                                                // Chuyển điểm hệ 10 sang hệ 4
                                                $diemCC = $diemCCRecord->diem_so;
                                                if ($diemCC >= 9.0) {
                                                    $diemCC = 4.0;
                                                } elseif ($diemCC >= 8.5) {
                                                    $diemCC = 3.5;
                                                } elseif ($diemCC >= 8.0) {
                                                    $diemCC = 3.0;
                                                } elseif ($diemCC >= 7.0) {
                                                    $diemCC = 2.5;
                                                } elseif ($diemCC >= 6.5) {
                                                    $diemCC = 2.0;
                                                } elseif ($diemCC >= 5.5) {
                                                    $diemCC = 1.5;
                                                } elseif ($diemCC >= 5.0) {
                                                    $diemCC = 1.0;
                                                } else {
                                                    $diemCC = 0;
                                                }
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
                                            @if ($tongBuoi > 0)
                                                <span
                                                    class="badge {{ $duDieuKienDiemDanh ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $tyLeDiemDanh }}% ({{ $buoiCoMat }}/{{ $tongBuoi }})
                                                </span>
                                                @if (!$duDieuKienThi)
                                                    <br><small class="text-danger">
                                                        @if (!$duDieuKienDiemDanh)
                                                            Điểm danh < 80% @endif
                                                                @if (!$duDieuKienDiemCC)
                                                                    {{ !$duDieuKienDiemDanh ? ', ' : '' }}Điểm CC < 2/4
                                                                        @endif
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        @foreach ($cauHinhs as $ch)
                                            @for ($cot = 1; $cot <= $ch->so_cot; $cot++)
                                                @php
                                                    $diem = $lhpsv->danh_sach_diem
                                                        ->where('cau_hinh_id', $ch->id)
                                                        ->where('cot_diem', $cot)
                                                        ->first();
                                                @endphp
                                                <td class="text-center">
                                                    {{ $diem ? number_format($diem->diem_so, 2) : '-' }}
                                                </td>
                                            @endfor
                                        @endforeach

                                        <td class="text-center">
                                            @if ($lhpsv->diem_tong_ket)
                                                <strong>{{ number_format($lhpsv->diem_tong_ket, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_4)
                                                {{ number_format($lhpsv->ketQuaHocTap->diem_he_4, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($lhpsv->diem_chu)
                                                <span class="badge bg-info">{{ $lhpsv->diem_chu }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($lhpsv->diem_tong_ket)
                                                @if ($lhpsv->diem_tong_ket >= 4)
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
            // Script đã xóa
        </script>
    @endpush

@endsection
