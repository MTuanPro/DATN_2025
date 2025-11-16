@extends('layouts.layout-sinhvien')

@section('title', 'Chương trình đào tạo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chương trình đào tạo</h3>
                    <p class="text-subtitle text-muted">Xem chương trình đào tạo và tiến độ học tập</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chương trình đào tạo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin sinh viên -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Thông tin chương trình</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Sinh viên:</strong></td>
                                    <td>{{ $sinhVien->ho_ten }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mã sinh viên:</strong></td>
                                    <td>{{ $sinhVien->ma_sinh_vien }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lớp:</strong></td>
                                    <td>{{ $sinhVien->lopHanhChinh->ten_lop }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Khóa học:</strong></td>
                                    <td>{{ $sinhVien->lopHanhChinh->khoaHoc->ten_khoa_hoc }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Khoa:</strong></td>
                                    <td>{{ $chuyenNganh->nganh->khoa->ten_khoa }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngành:</strong></td>
                                    <td>{{ $chuyenNganh->nganh->ten_nganh }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Chuyên ngành:</strong></td>
                                    <td>{{ $chuyenNganh->ten_chuyen_nganh }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê tiến độ -->
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted">Tổng tín chỉ CTĐT</h6>
                                    <h3 class="mb-0">{{ $thongKe['tong_tin_chi_ctdt'] }}</h3>
                                </div>
                                <div class="avatar bg-light-primary">
                                    <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted">Tín chỉ đã đạt</h6>
                                    <h3 class="mb-0">{{ $thongKe['tin_chi_dat'] }}</h3>
                                    <small class="text-success">{{ number_format($tienDo, 1) }}%</small>
                                </div>
                                <div class="avatar bg-light-success">
                                    <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted">Môn đã học</h6>
                                    <h3 class="mb-0">{{ $thongKe['so_mon_da_hoc'] }}/{{ $thongKe['so_mon_ctdt'] }}</h3>
                                </div>
                                <div class="avatar bg-light-info">
                                    <i class="bi bi-list-check text-info" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted">Tiến độ</h6>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $tienDo }}%"
                                             aria-valuenow="{{ $tienDo }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small>{{ number_format($tienDo, 1) }}% hoàn thành</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chương trình theo học kỳ -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Chương trình đào tạo theo học kỳ</h5>
                </div>
                <div class="card-body">
                    @if($chuongTrinhTheoHocKy->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Chưa có chương trình đào tạo cho chuyên ngành này.
                        </div>
                    @else
                        <div class="accordion" id="accordionCTDT">
                            @foreach($chuongTrinhTheoHocKy as $hocKy => $monHocs)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $hocKy }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $hocKy }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $hocKy }}">
                                            <strong>Học kỳ {{ $hocKy }}</strong>
                                            <span class="ms-3 badge bg-primary">
                                                {{ $monHocs->count() }} môn - 
                                                {{ $monHocs->sum(function($m) { return $m->monHoc->so_tin_chi; }) }} tín chỉ
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $hocKy }}" 
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $hocKy }}" 
                                         data-bs-parent="#accordionCTDT">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="50">STT</th>
                                                            <th width="120">Mã môn</th>
                                                            <th>Tên môn học</th>
                                                            <th width="80">Tín chỉ</th>
                                                            <th width="150">Loại môn</th>
                                                            <th width="100">Bắt buộc</th>
                                                            <th width="120">Trạng thái</th>
                                                            <th width="100">Điểm</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($monHocs as $item)
                                                            @php
                                                                $ketQua = $ketQuaHocTap->first(function ($kq) use ($item) {
                                                                    $monHocId = $kq->lopHocPhanSinhVien->lopHocPhan->mon_hoc_id ?? null;
                                                                    return $monHocId == $item->mon_hoc_id;
                                                                });
                                                                $daDat = $ketQua && ($ketQua->diem_he_10 ?? 0) >= 4.0;
                                                                $loaiLabels = [
                                                                    'dai_cuong' => 'Đại cương',
                                                                    'co_so_nganh' => 'Cơ sở ngành',
                                                                    'chuyen_nganh_bat_buoc' => 'CN bắt buộc',
                                                                    'chuyen_nganh_tu_chon' => 'CN tự chọn',
                                                                    'thuc_tap' => 'Thực tập',
                                                                    'do_an_tot_nghiep' => 'ĐATN',
                                                                ];
                                                            @endphp
                                                            <tr class="{{ $daDat ? 'table-success' : '' }}">
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td><strong>{{ $item->monHoc->ma_mon }}</strong></td>
                                                                <td>
                                                                    {{ $item->monHoc->ten_mon }}
                                                                    @if($item->monHoc->monTienQuyet->isNotEmpty())
                                                                        <br>
                                                                        <small class="text-warning">
                                                                            <i class="bi bi-exclamation-triangle"></i>
                                                                            Có môn tiên quyết
                                                                        </small>
                                                                    @endif
                                                                </td>
                                                                <td><span class="badge bg-info">{{ $item->monHoc->so_tin_chi }}</span></td>
                                                                <td><small>{{ $loaiLabels[$item->loai_mon_hoc] ?? $item->loai_mon_hoc }}</small></td>
                                                                <td>
                                                                    @if($item->bat_buoc)
                                                                        <span class="badge bg-danger">Bắt buộc</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Tự chọn</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($ketQua)
                                                                        @if($daDat)
                                                                            <span class="badge bg-success">
                                                                                <i class="bi bi-check-circle"></i> Đã đạt
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-warning">
                                                                                <i class="bi bi-x-circle"></i> Chưa đạt
                                                                            </span>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge bg-secondary">
                                                                            <i class="bi bi-dash-circle"></i> Chưa học
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($ketQua && $ketQua->diem_he_10)
                                                                        <strong class="{{ $daDat ? 'text-success' : 'text-danger' }}">
                                                                            {{ number_format($ketQua->diem_he_10, 1) }}
                                                                        </strong>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Ghi chú</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Môn có nền <span class="badge bg-success">xanh lá</span> là môn đã đạt (điểm >= 4.0)</li>
                        <li><span class="badge bg-danger">Bắt buộc</span>: Môn học bắt buộc phải hoàn thành</li>
                        <li><span class="badge bg-secondary">Tự chọn</span>: Môn học tự chọn (đạt đủ số tín chỉ yêu cầu)</li>
                        <li><i class="bi bi-exclamation-triangle text-warning"></i> Môn có ký hiệu này có môn tiên quyết, cần hoàn thành môn tiên quyết trước</li>
                        <li>Xem <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.dieu-kien-tot-nghiep') }}">Điều kiện tốt nghiệp</a> để biết thêm chi tiết</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
@endsection

