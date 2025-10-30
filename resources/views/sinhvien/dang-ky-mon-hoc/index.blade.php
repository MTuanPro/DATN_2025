@extends('layouts.layout-sinhvien')

@section('title', 'Đăng ký môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Học kỳ:
                        {{ $hocKy ? $hocKy->ten_hoc_ky . ' - ' . $hocKy->nam_hoc : 'N/A' }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinhvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Đăng ký môn học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (!$hocKy)
            <div class="alert alert-warning">
                <h4 class="alert-heading">Thông báo</h4>
                <p>{{ $message ?? 'Hiện tại không có học kỳ nào mở đăng ký môn học.' }}</p>
            </div>
        @else
            <!-- Thông tin đăng ký -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Thời gian đăng ký</h6>
                            <p class="mb-0">
                                <strong>Từ:</strong> {{ $hocKy->ngay_bat_dau_dang_ky->format('d/m/Y') }}<br>
                                <strong>Đến:</strong> {{ $hocKy->ngay_ket_thuc_dang_ky->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Số tín chỉ</h6>
                            <p class="mb-0">
                                <strong>Đã đăng ký:</strong> {{ $tongTinChiDaDangKy }} TC<br>
                                <strong>Tối đa:</strong> {{ $tinChiToiDa }} TC
                            </p>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar {{ $tongTinChiDaDangKy >= $tinChiToiDa ? 'bg-danger' : 'bg-primary' }}"
                                    style="width: {{ ($tongTinChiDaDangKy / $tinChiToiDa) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Trạng thái</h6>
                            @if (now()->between($hocKy->ngay_bat_dau_dang_ky, $hocKy->ngay_ket_thuc_dang_ky))
                                <span class="badge bg-success">Đang mở đăng ký</span>
                            @elseif(now() < $hocKy->ngay_bat_dau_dang_ky)
                                <span class="badge bg-warning">Chưa mở</span>
                            @else
                                <span class="badge bg-danger">Đã đóng</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách môn học -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách môn học có thể đăng ký</h5>
                </div>
                <div class="card-body">
                    @if ($chuongTrinhKhung->isEmpty())
                        <div class="alert alert-info">
                            Chưa có chương trình khung cho chuyên ngành của bạn.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã MH</th>
                                        <th>Tên môn học</th>
                                        <th>Tín chỉ</th>
                                        <th>Học kỳ gợi ý</th>
                                        <th>Lớp học phần</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($chuongTrinhKhung as $ct)
                                        @php
                                            $monHoc = $ct->monHoc;
                                            $daDangKy = in_array($monHoc->id, $monDaDangKy);
                                            $daHoc = in_array($monHoc->id, $monDaHoc);
                                            $daQua = in_array($monHoc->id, $monDaQua);
                                            $lopHPs = $lopHocPhans[$monHoc->id] ?? collect();
                                        @endphp
                                        <tr>
                                            <td><code>{{ $monHoc->ma_mon }}</code></td>
                                            <td>
                                                <strong>{{ $monHoc->ten_mon }}</strong>
                                                @if ($ct->bat_buoc)
                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                @endif
                                            </td>
                                            <td>{{ $monHoc->so_tin_chi }}</td>
                                            <td>Kỳ {{ $ct->hoc_ky_goi_y }}</td>
                                            <td>
                                                @if ($lopHPs->isEmpty())
                                                    <span class="text-muted">Chưa mở lớp</span>
                                                @else
                                                    <small class="text-primary">{{ $lopHPs->count() }} lớp</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($daQua)
                                                    <span class="badge bg-success">Đã qua môn</span>
                                                @elseif($daHoc)
                                                    <span class="badge bg-warning">Đang học</span>
                                                @elseif($daDangKy)
                                                    <span class="badge bg-info">Đã đăng ký</span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa học</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$daQua && !$daDangKy && !$lopHPs->isEmpty())
                                                    <button type="button" class="btn btn-sm btn-primary btn-dang-ky"
                                                        data-mon-hoc-id="{{ $monHoc->id }}"
                                                        data-ten-mon="{{ $monHoc->ten_mon }}"
                                                        data-tin-chi="{{ $monHoc->so_tin_chi }}">
                                                        <i class="bi bi-plus-circle"></i> Đăng ký
                                                    </button>
                                                @elseif($daDangKy)
                                                    @php
                                                        $dangKyId = $dangKyCollection->firstWhere(
                                                            'mon_hoc_id',
                                                            $monHoc->id,
                                                        )?->id;
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-danger btn-huy-dang-ky"
                                                        data-dang-ky-id="{{ $dangKyId }}">
                                                        <i class="bi bi-x-circle"></i> Hủy
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Đăng ký môn học
                $('.btn-dang-ky').click(function() {
                    const monHocId = $(this).data('mon-hoc-id');
                    const tenMon = $(this).data('ten-mon');
                    const tinChi = $(this).data('tin-chi');
                    const tongTinChi = {{ $tongTinChiDaDangKy }};
                    const tinChiToiDa = {{ $tinChiToiDa }};

                    if ((tongTinChi + tinChi) > tinChiToiDa) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Vượt quá số tín chỉ!',
                            text: `Bạn đã đăng ký ${tongTinChi} TC. Môn này có ${tinChi} TC sẽ vượt quá giới hạn ${tinChiToiDa} TC.`
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Xác nhận đăng ký',
                        text: `Bạn muốn đăng ký môn: ${tenMon} (${tinChi} TC)?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng ký',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('sinhvien.dang-ky-mon-hoc.store') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    mon_hoc_id: monHocId,
                                    hoc_ky_id: {{ $hocKy->id }}
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: response.message
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    const message = xhr.responseJSON?.message ||
                                        'Có lỗi xảy ra!';
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: message
                                    });
                                }
                            });
                        }
                    });
                });

                // Hủy đăng ký
                $('.btn-huy-dang-ky').click(function() {
                    const dangKyId = $(this).data('dang-ky-id');

                    Swal.fire({
                        title: 'Xác nhận hủy',
                        text: 'Bạn có chắc muốn hủy đăng ký môn này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hủy đăng ký',
                        cancelButtonText: 'Không',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/sinh-vien/dang-ky-mon-hoc/${dangKyId}`,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Đã hủy!',
                                        text: response.message
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    const message = xhr.responseJSON?.message ||
                                        'Có lỗi xảy ra!';
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: message
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
