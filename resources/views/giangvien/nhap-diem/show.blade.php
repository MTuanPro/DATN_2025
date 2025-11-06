@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết điểm môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết điểm môn học</h3>
                    <p class="text-subtitle text-muted">Xem chi tiết điểm các thành phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.diem.index') }}">Kết quả học tập</a>
                            </li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thông tin môn học --}}
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book"></i> {{ $lhpsv->lopHocPhan->monHoc->ten_mon }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mã môn:</strong></td>
                                    <td>{{ $lhpsv->lopHocPhan->monHoc->ma_mon }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lớp học phần:</strong></td>
                                    <td>{{ $lhpsv->lopHocPhan->ten_lop_hp }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ:</strong></td>
                                    <td>{{ $lhpsv->lopHocPhan->hocKy->ten_hoc_ky }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Số tín chỉ:</strong></td>
                                    <td>{{ $lhpsv->lopHocPhan->monHoc->so_tin_chi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Giảng viên:</strong></td>
<td>{{ $lhpsv->lopHocPhan->giangVienChinh->giangVien->user->ho_ten ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Điểm thành phần --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Điểm thành phần</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">STT</th>
                                    <th>Loại điểm</th>
                                    <th width="100" class="text-center">Trọng số (%)</th>
                                    <th width="150" class="text-center">Điểm</th>
                                    <th width="150" class="text-center">Điểm sau trọng số</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tongDiem = 0;
                                @endphp
                                @foreach ($lhpsv->lopHocPhan->cauHinhDauDiem as $index => $cauHinh)
                                    @php
                                        $diem = $diemThanhPhan->get($cauHinh->id)?->first();
                                        $diemGoc = $diem ? $diem->diem : null;
                                        $diemSauTrongSo = $diemGoc ? ($diemGoc * $cauHinh->trong_so) / 100 : null;

                                        if ($diemSauTrongSo) {
                                            $tongDiem += $diemSauTrongSo;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $cauHinh->loai_diem }}</strong>
                                            @if ($cauHinh->ghi_chu)
                                                <br><small class="text-muted">{{ $cauHinh->ghi_chu }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $cauHinh->trong_so }}%</td>
                                        <td class="text-center">
                                            @if ($diemGoc !== null)
                                                <strong class="text-primary">{{ number_format($diemGoc, 2) }}</strong>
                                            @else
<span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($diemSauTrongSo !== null)
                                                {{ number_format($diemSauTrongSo, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Tổng điểm (Hệ 10):</strong></td>
                                    <td class="text-center">
                                        <h5 class="mb-0 text-primary">
                                            {{ $lhpsv->ketQuaHocTap ? number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) : '0.00' }}
                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kết quả tổng hợp --}}
            @if ($lhpsv->ketQuaHocTap)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Kết quả tổng hợp</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm hệ 10</h6>
                                    <h2 class="text-primary mb-0">{{ number_format($lhpsv->ketQuaHocTap->diem_he_10, 2) }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm hệ 4</h6>
                                    <h2 class="text-success mb-0">{{ number_format($lhpsv->ketQuaHocTap->diem_he_4, 2) }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Điểm chữ</h6>
                                    <h2 class="mb-0">
<span class="badge bg-{{ $lhpsv->ketQuaHocTap->diem_chu_badge }} fs-4">
                                            {{ $lhpsv->ketQuaHocTap->diem_chu }}
                                        </span>
                                    </h2>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 border rounded">
                                    <h6 class="text-muted mb-2">Kết quả</h6>
                                    <h2 class="mb-0">
                                        @if ($lhpsv->ketQuaHocTap->qua_mon)
                                            <span class="badge bg-success fs-5">Đạt</span>
                                        @else
                                            <span class="badge bg-danger fs-5">Không đạt</span>
                                        @endif
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Nút quay lại --}}
            <div class="text-center">
                <a href="{{ route('sinhvien.diem.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </section>
    </div>
@endsection
