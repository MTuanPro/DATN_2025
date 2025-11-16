@extends('layouts.layout-sinhvien')

@section('title', 'Chi tiết môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết môn học</h3>
                    <p class="text-subtitle text-muted">Thông tin chi tiết về môn học trong CTĐT</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}">Chương trình đào tạo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chi tiết môn học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin môn học -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-book me-2"></i>{{ $chuongTrinhKhung->monHoc->ma_mon }} - {{ $chuongTrinhKhung->monHoc->ten_mon }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Thông tin cơ bản</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Mã môn học:</strong></td>
                                    <td>{{ $chuongTrinhKhung->monHoc->ma_mon }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tên môn học:</strong></td>
                                    <td>{{ $chuongTrinhKhung->monHoc->ten_mon }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số tín chỉ:</strong></td>
                                    <td><span class="badge bg-info">{{ $chuongTrinhKhung->monHoc->so_tin_chi }} TC</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Khoa phụ trách:</strong></td>
                                    <td>{{ $chuongTrinhKhung->monHoc->khoa->ten_khoa ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số tiết lý thuyết:</strong></td>
                                    <td>{{ $chuongTrinhKhung->monHoc->so_tiet_ly_thuyet ?? 0 }} tiết</td>
                                </tr>
                                <tr>
                                    <td><strong>Số tiết thực hành:</strong></td>
                                    <td>{{ $chuongTrinhKhung->monHoc->so_tiet_thuc_hanh ?? 0 }} tiết</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Thông tin trong CTĐT</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Chuyên ngành:</strong></td>
                                    <td>{{ $chuongTrinhKhung->chuyenNganh->ten_chuyen_nganh }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Học kỳ gợi ý:</strong></td>
                                    <td><span class="badge bg-warning">Học kỳ {{ $chuongTrinhKhung->hoc_ky_goi_y }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Loại môn học:</strong></td>
                                    <td>
                                        @php
                                            $loaiLabels = [
                                                'dai_cuong' => 'Đại cương',
                                                'co_so_nganh' => 'Cơ sở ngành',
                                                'chuyen_nganh_bat_buoc' => 'Chuyên ngành bắt buộc',
                                                'chuyen_nganh_tu_chon' => 'Chuyên ngành tự chọn',
                                                'thuc_tap' => 'Thực tập',
                                                'do_an_tot_nghiep' => 'Đồ án tốt nghiệp',
                                            ];
                                        @endphp
                                        {{ $loaiLabels[$chuongTrinhKhung->loai_mon_hoc] ?? $chuongTrinhKhung->loai_mon_hoc }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Yêu cầu:</strong></td>
                                    <td>
                                        @if($chuongTrinhKhung->bat_buoc)
                                            <span class="badge bg-danger">Bắt buộc</span>
                                        @else
                                            <span class="badge bg-secondary">Tự chọn</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Thứ tự học:</strong></td>
                                    <td>{{ $chuongTrinhKhung->thu_tu_hoc ?? 'Không xác định' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($chuongTrinhKhung->ghi_chu)
                        <div class="alert alert-info mt-3">
                            <strong><i class="bi bi-info-circle me-2"></i>Ghi chú:</strong>
                            <p class="mb-0">{{ $chuongTrinhKhung->ghi_chu }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Môn tiên quyết -->
            @if($chuongTrinhKhung->monHoc->monTienQuyet && $chuongTrinhKhung->monHoc->monTienQuyet->isNotEmpty())
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Môn học tiên quyết
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            Bạn cần hoàn thành các môn học sau trước khi đăng ký môn này:
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">STT</th>
                                        <th width="120">Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th width="100">Tín chỉ</th>
                                        <th width="150">Điều kiện</th>
                                        <th width="150">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chuongTrinhKhung->monHoc->monTienQuyet as $tienQuyet)
                                        @php
                                            // Kiểm tra xem sinh viên đã hoàn thành môn tiên quyết chưa
                                            $ketQuaTQ = \App\Models\KetQuaHocTap::with('lopHocPhanSinhVien.lopHocPhan')
                                                ->whereHas('lopHocPhanSinhVien', function ($query) use ($sinhVien, $tienQuyet) {
                                                    $query->where('sinh_vien_id', $sinhVien->id)
                                                        ->whereHas('lopHocPhan', function ($q) use ($tienQuyet) {
                                                            $q->where('mon_hoc_id', $tienQuyet->mon_hoc_tien_quyet_id);
                                                        });
                                                })
                                                ->first();
                                            $daDatTQ = $ketQuaTQ && ($ketQuaTQ->diem_he_10 ?? 0) >= ($tienQuyet->diem_toi_thieu ?? 4.0);
                                        @endphp
                                        <tr class="{{ $daDatTQ ? 'table-success' : '' }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $tienQuyet->monHocTienQuyet->ma_mon }}</strong></td>
                                            <td>{{ $tienQuyet->monHocTienQuyet->ten_mon }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $tienQuyet->monHocTienQuyet->so_tin_chi }} TC
                                                </span>
                                            </td>
                                            <td>
                                                Điểm ≥ {{ number_format($tienQuyet->diem_toi_thieu ?? 4.0, 1) }}
                                            </td>
                                            <td>
                                                @if($daDatTQ)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Đã đạt
                                                    </span>
                                                    ({{ number_format($ketQuaTQ->diem_he_10, 1) }})
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-x-circle"></i> Chưa đạt
                                                    </span>
                                                    @if($ketQuaTQ)
                                                        ({{ number_format($ketQuaTQ->diem_he_10, 1) }})
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Kết quả học tập của sinh viên -->
            <div class="card">
                <div class="card-header {{ $ketQuaHocTap ? 'bg-info' : 'bg-secondary' }} text-white">
                    <h5 class="mb-0">
                        <i class="bi {{ $ketQuaHocTap ? 'bi-clipboard-data' : 'bi-clipboard' }} me-2"></i>
                        Kết quả học tập của bạn
                    </h5>
                </div>
                <div class="card-body">
                    @if($ketQuaHocTap)
                            @php
                                $daDat = ($ketQuaHocTap->diem_he_10 ?? 0) >= 4.0;
                            @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="200"><strong>Học kỳ:</strong></td>
                                        <td>{{ $ketQuaHocTap->lopHocPhanSinhVien->lopHocPhan->hocKy->ten_hoc_ky ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lớp học phần:</strong></td>
                                        <td>{{ $ketQuaHocTap->lopHocPhanSinhVien->lopHocPhan->ma_lop_hp ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="200"><strong>Điểm tổng kết:</strong></td>
                                        <td>
                                            <h4 class="mb-0 {{ $daDat ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($ketQuaHocTap->diem_he_10, 1) }}
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Điểm chữ:</strong></td>
                                        <td>
                                            @php
                                                $diem = $ketQuaHocTap->diem_he_10;
                                                if ($diem >= 8.5) $diemChu = 'A';
                                                elseif ($diem >= 7.0) $diemChu = 'B';
                                                elseif ($diem >= 5.5) $diemChu = 'C';
                                                elseif ($diem >= 4.0) $diemChu = 'D';
                                                else $diemChu = 'F';
                                            @endphp
                                            <span class="badge bg-{{ $daDat ? 'success' : 'danger' }} fs-5">{{ $diemChu }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            @if($daDat)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle-fill"></i> Đã đạt
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle-fill"></i> Chưa đạt
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($daDat)
                            <div class="alert alert-success mb-0">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Chúc mừng!</strong> Bạn đã hoàn thành môn học này với điểm {{ number_format($ketQuaHocTap->diem_he_10, 1) }}.
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Bạn chưa đạt yêu cầu môn học này. Cần cải thiện để đạt điểm ≥ 4.0.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Bạn chưa học môn này. Hãy đăng ký trong các học kỳ tiếp theo theo đúng học kỳ gợi ý.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mô tả môn học -->
            @if($chuongTrinhKhung->monHoc->mo_ta)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>Mô tả môn học
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $chuongTrinhKhung->monHoc->mo_ta }}</p>
                    </div>
                </div>
            @endif

            <!-- Nút hành động -->
            <div class="text-center mt-4">
                <a href="{{ route('sinh-vien.chuong-trinh-dao-tao.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại CTĐT
                </a>
                @if(!$ketQuaHocTap)
                    <a href="{{ route('sinh-vien.dang-ky-mon-hoc.index') }}" class="btn btn-primary">
                        <i class="bi bi-journal-plus me-2"></i>Đăng ký môn học
                    </a>
                @endif
            </div>
        </section>
    </div>
@endsection

