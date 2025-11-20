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
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
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
                            @php
                                // Kiểm tra xem học kỳ có được mở đăng ký không
                                $hocKyMoDangKy = \App\Models\HocKy::where('la_hoc_ky_hien_tai', true)
                                    ->where('dang_mo_dang_ky', true)
                                    ->first();
                            @endphp
                            @if ($hocKyMoDangKy && $hocKy->id == $hocKyMoDangKy->id)
                                @if (now()->between($hocKy->ngay_bat_dau_dang_ky, $hocKy->ngay_ket_thuc_dang_ky))
                                    <span class="badge bg-success">Đang mở đăng ký</span>
                                @elseif(now() < $hocKy->ngay_bat_dau_dang_ky)
                                    <span class="badge bg-warning">Chưa đến thời gian</span>
                                @else
                                    <span class="badge bg-danger">Đã hết thời gian</span>
                                @endif
                            @else
                                <span class="badge bg-danger">Đã đóng</span>
                                <small class="d-block text-muted mt-1">Học kỳ chưa được mở đăng ký</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debug Info -->
            @if(isset($debugInfo) || config('app.debug'))
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">🔍 Thông tin Debug</h6>
                </div>
                <div class="card-body">
                    @if(isset($debugInfo))
                        @if(isset($debugInfo['hoc_ky_hien_tai']))
                            <p class="mb-1"><strong>Học kỳ hiện tại:</strong> {{ $debugInfo['hoc_ky_hien_tai'] }}</p>
                            <p class="mb-1"><strong>Học kỳ mở đăng ký:</strong> {{ $debugInfo['hoc_ky_mo_dang_ky'] }}</p>
                        @else
                            <p class="mb-1"><strong>Học kỳ ID:</strong> {{ $debugInfo['hoc_ky_id'] ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Tổng lớp đang mở:</strong> {{ $debugInfo['tong_lop_dang_mo'] ?? 0 }} lớp</p>
                            <p class="mb-1"><strong>Tổng môn có lớp mở:</strong> {{ $debugInfo['tong_mon_co_lop_mo'] ?? 0 }} môn</p>
                            <p class="mb-1"><strong>Tổng CTK của chuyên ngành:</strong> {{ $debugInfo['tong_chuong_trinh_khung'] ?? 0 }} môn</p>
                            <p class="mb-1"><strong>CTK có lớp mở:</strong> {{ $debugInfo['chuong_trinh_khung_co_lop_mo'] ?? 0 }} môn</p>
                            <p class="mb-1"><strong>Chuyên ngành ID:</strong> {{ $debugInfo['chuyen_nganh_id'] ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Chuyên ngành:</strong> {{ $debugInfo['chuyen_nganh'] ?? 'Chưa có' }}</p>
                        @endif
                    @else
                        <p class="mb-1"><strong>Học kỳ ID:</strong> {{ $hocKy->id ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Tổng lớp đang mở:</strong> {{ $lopHocPhans->count() }} môn học</p>
                        <p class="mb-1"><strong>Môn trong CTK:</strong> {{ $chuongTrinhKhung->count() }} môn</p>
                        <p class="mb-1"><strong>Chuyên ngành SV:</strong> {{ $sinhVien->chuyenNganh->ten_chuyen_nganh ?? 'Chưa có' }}</p>
                    @endif
                    @if($lopHocPhans->isNotEmpty())
                        <details class="mt-2">
                            <summary class="text-primary" style="cursor: pointer;">Xem danh sách lớp đang mở</summary>
                            <ul class="mt-2">
                                @foreach($lopHocPhans as $monId => $lops)
                                    <li>Môn ID {{ $monId }}: {{ $lops->first()->monHoc->ten_mon ?? 'N/A' }} - {{ $lops->count() }} lớp</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </div>
            </div>
            @endif

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
                                            <td>
                                                @php
                                                    // Lấy học kỳ gợi ý từ CTK hoặc object
                                                    $hocKyGoiY = null;
                                                    if (is_object($ct)) {
                                                        // Nếu là model ChuongTrinhKhung
                                                        if (method_exists($ct, 'getAttribute')) {
                                                            $hocKyGoiY = $ct->hoc_ky_goi_y;
                                                        } else {
                                                            // Nếu là stdClass object
                                                            $hocKyGoiY = $ct->hoc_ky_goi_y ?? null;
                                                        }
                                                    } else {
                                                        $hocKyGoiY = $ct['hoc_ky_goi_y'] ?? null;
                                                    }
                                                @endphp
                                                @if ($hocKyGoiY && $hocKyGoiY > 0)
                                                    <span class="badge bg-info text-white">Kỳ {{ $hocKyGoiY }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa xác định</span>
                                                @endif
                                            </td>
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
                                                        $dangKyId = $dangKyCollection->firstWhere('mon_hoc_id', $monHoc->id)?->id;
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-danger btn-huy-dang-ky"
                                                        data-dang-ky-id="{{ $dangKyId }}">
                                                        <i class="bi bi-x-circle"></i> Hủy
                                                    </button>
                                                @elseif($daQua)
                                                    <small class="text-muted">Đã qua môn</small>
                                                @elseif($lopHPs->isEmpty())
                                                    <small class="text-muted">Chưa mở lớp</small>
                                                @else
                                                    @if(config('app.debug'))
                                                        <small class="text-warning" title="daQua:{{$daQua?'Y':'N'}} daDK:{{$daDangKy?'Y':'N'}} lops:{{$lopHPs->count()}}">
                                                            Debug
                                                        </small>
                                                    @endif
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
        <!-- Modal for registration confirmation -->
        <div class="modal fade" id="dangKyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận đăng ký môn học</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="modal-mon-ten" class="fw-bold"></p>
                        <p> Tín chỉ: <span id="modal-mon-tc"></span> </p>
                        <div id="modal-error" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" id="confirm-dang-ky" class="btn btn-primary">Đăng ký</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function() {
                let selectedMonId = null;
                let selectedTinChi = 0;

                function showError(text) {
                    $('#modal-error').removeClass('d-none').text(text);
                }

                function clearError() {
                    $('#modal-error').addClass('d-none').text('');
                }

                // Open modal when clicking Đăng ký
                $('.btn-dang-ky').on('click', function() {
                    selectedMonId = $(this).data('mon-hoc-id');
                    const tenMon = $(this).data('ten-mon');
selectedTinChi = parseInt($(this).data('tin-chi')) || 0;
                    const tongTinChi = {{ $tongTinChiDaDangKy }};
                    const tinChiToiDa = {{ $tinChiToiDa }};

                    if ((tongTinChi + selectedTinChi) > tinChiToiDa) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Vượt quá số tín chỉ!',
                            text: `Bạn đã đăng ký ${tongTinChi} TC. Môn này có ${selectedTinChi} TC sẽ vượt quá giới hạn ${tinChiToiDa} TC.`
                        });
                        return;
                    }

                    clearError();
                    $('#modal-mon-ten').text(tenMon);
                    $('#modal-mon-tc').text(selectedTinChi + ' TC');
                    const modal = new bootstrap.Modal(document.getElementById('dangKyModal'));
                    modal.show();
                });

                // Confirm from modal
                $('#confirm-dang-ky').on('click', function() {
                    if (!selectedMonId) return;

                    const $btn = $(this);
                    $btn.prop('disabled', true).text('Đang xử lý...');

                    $.ajax({
                        url: '{{ route('sinh-vien.dang-ky-mon-hoc.store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            mon_hoc_id: selectedMonId,
                            hoc_ky_id: {{ $hocKy->id }}
                        }
                    }).done(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message
                        }).then(() => location.reload());
                    }).fail(function(xhr) {
                        console.error('Lỗi đăng ký:', xhr);
                        let message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                        
                        // Hiển thị chi tiết lỗi nếu có
                        if (xhr.responseJSON?.errors && Array.isArray(xhr.responseJSON.errors)) {
                            message += '\n\n' + xhr.responseJSON.errors.join('\n');
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Không thể đăng ký',
                            text: message,
                            html: message.replace(/\n/g, '<br>'),
                            width: '500px'
                        });
                        
                        // Đóng modal
                        bootstrap.Modal.getInstance(document.getElementById('dangKyModal'))?.hide();
                    }).always(function() {
                        $btn.prop('disabled', false).text('Đăng ký');
                    });
                });

                // Hủy đăng ký (keeps existing behaviour)
                $('.btn-huy-dang-ky').on('click', function() {
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
                                data: { _token: '{{ csrf_token() }}' }
                            }).done(function(response) {
                                Swal.fire({ icon: 'success', title: 'Đã hủy!', text: response.message })
                                    .then(() => location.reload());
                            }).fail(function(xhr) {
                                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra!';
                                Swal.fire({ icon: 'error', title: 'Lỗi', text: message });
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection