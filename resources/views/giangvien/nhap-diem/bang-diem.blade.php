@extends('layouts.layout-sinhvien')

@section('title', 'Bảng điểm tổng hợp')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Bảng điểm tổng hợp</h3>
                    <p class="text-subtitle text-muted">Tổng hợp kết quả học tập qua các học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.nhap-diem.index') }}">Nhập điểm</a>
                            </li>
                            <li class="breadcrumb-item active">Bảng điểm tổng hợp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thông tin sinh viên --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Thông tin sinh viên</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mã sinh viên:</strong></td>
                                    <td>{{ $sinhVien->ma_sinh_vien ?? '-' }}</td>
                                    <td width="150"><strong>Lớp:</strong></td>
                                    <td>{{ '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $sinhVien->user->ho_ten ?? $sinhVien->ho_ten ?? '-' }}</td>
                                    <td><strong>Khoa:</strong></td>
                                    <td>{{ $sinhVien->nganh->ten_nganh ?? 'N/A'->ten_khoa ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="mb-3">Tổng kết</h5>
                                <div class="mb-2">
                                    <span class="text-muted">GPA tích lũy:</span>
                                    <h3 class="text-primary mb-0">{{ number_format($gpaTichLuy, 2) }}</h3>
                                </div>
                                <div>
                                    <span class="text-muted">Tín chỉ đạt:</span>
<h4 class="text-success mb-0">{{ $tongTinChiDat }}/{{ $tongTinChiHoc }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('sinh-vien.diem.export-pdf') }}" class="btn btn-danger">
                            <i class="bi bi-file-pdf"></i> Xuất PDF
                        </a>
                    </div>
                </div>
            </div>

            {{-- Bảng điểm theo học kỳ --}}
            @foreach ($monHocs as $hocKyId => $dsMonHoc)
                @php
                    $hocKy = $dsMonHoc->first()->lopHocPhan->hocKy;
                    $tongTinChi = 0;
                    $tongDiemHe4 = 0;
                    $soMonDat = 0;
                @endphp

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3"></i> {{ $hocKy->ten_hoc_ky }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">STT</th>
                                        <th width="100">Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th width="80" class="text-center">Tín chỉ</th>
                                        <th width="100" class="text-center">Điểm (10)</th>
                                        <th width="100" class="text-center">Điểm (4)</th>
                                        <th width="100" class="text-center">Điểm chữ</th>
                                        <th width="100" class="text-center">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dsMonHoc as $index => $item)
                                        @php
                                            $monHoc = $item->lopHocPhan->monHoc;
                                            $ketQua = $item->ketQuaHocTap;
                                            $tinChi = $monHoc->so_tin_chi;
                                            $tongTinChi += $tinChi;

                                            if ($ketQua) {
                                                $tongDiemHe4 += $ketQua->diem_he_4 * $tinChi;
                                                if ($ketQua->qua_mon) {
                                                    $soMonDat++;
                                                }
}
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $monHoc->ma_mon }}</strong></td>
                                            <td>{{ $monHoc->ten_mon }}</td>
                                            <td class="text-center">{{ $tinChi }}</td>
                                            <td class="text-center">
                                                @if ($ketQua && $ketQua->diem_he_10)
                                                    <strong
                                                        class="text-primary">{{ number_format($ketQua->diem_he_10, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($ketQua && $ketQua->diem_he_4)
                                                    {{ number_format($ketQua->diem_he_4, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($ketQua && $ketQua->diem_chu)
                                                    <span class="badge bg-{{ $ketQua->diem_chu_badge }} fs-6">
                                                        {{ $ketQua->diem_chu }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($ketQua)
                                                    @if ($ketQua->qua_mon)
                                                        <span class="badge bg-success">Đạt</span>
                                                    @else
                                                        <span class="badge bg-danger">Không đạt</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Chưa có</span>
                                                @endif
                                            </td>
</tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng kết học kỳ:</strong></td>
                                        <td class="text-center"><strong>{{ $tongTinChi }}</strong></td>
                                        <td colspan="2" class="text-center">
                                            <strong>GPA:
                                                {{ $tongTinChi > 0 ? number_format($tongDiemHe4 / $tongTinChi, 2) : '0.00' }}</strong>
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <strong>{{ $soMonDat }}/{{ $dsMonHoc->count() }} môn đạt</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Trường hợp chưa có điểm --}}
            @if ($monHocs->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Chưa có kết quả học tập nào được công bố</p>
                        <a href="{{ route('sinh-vien.diem.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection