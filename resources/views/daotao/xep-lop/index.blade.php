
@extends('layouts.layout-daotao')

@extends('layouts.app-daotao')


@section('title', 'Xếp lớp tự động')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Xếp lớp tự động</h3>
                    <p class="text-subtitle text-muted">Quản lý đăng ký môn học và xếp lớp tự động</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Xếp lớp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Chờ xếp lớp</h6>
                                <h3 class="mb-0 text-warning">{{ $thongKe['cho_xep_lop'] }}</h3>
                            </div>
                            <div class="avatar avatar-xl bg-warning">
                                <i class="bi bi-hourglass-split text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Đã xếp lớp</h6>
                                <h3 class="mb-0 text-success">{{ $thongKe['da_xep_lop'] }}</h3>
                            </div>
                            <div class="avatar avatar-xl bg-success">
                                <i class="bi bi-check-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Thất bại</h6>
                                <h3 class="mb-0 text-danger">{{ $thongKe['that_bai'] }}</h3>
                            </div>
                            <div class="avatar avatar-xl bg-danger">
                                <i class="bi bi-x-circle text-white fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc và thao tác -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('dao-tao.xep-lop.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Học kỳ</label>
                        <select name="hoc_ky_id" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach ($hocKys as $hk)
                                <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                    {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="cho_xep_lop" {{ request('trang_thai') == 'cho_xep_lop' ? 'selected' : '' }}>Chờ
                                xếp lớp</option>
                            <option value="da_xep_lop" {{ request('trang_thai') == 'da_xep_lop' ? 'selected' : '' }}>Đã xếp
                                lớp</option>
                            <option value="that_bai" {{ request('trang_thai') == 'that_bai' ? 'selected' : '' }}>Thất bại
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success d-block" id="btnXepLopTuDong">
                            <i class="bi bi-magic"></i> Xếp lớp tự động
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách đăng ký -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Danh sách đăng ký môn học</h5>
            </div>
            <div class="card-body">
                @if ($dangKys->isEmpty())
                    <div class="alert alert-info mb-0">
                        Không có đăng ký nào.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày ĐK</th>
                                    <th>Ưu tiên</th>
                                    <th>Trạng thái</th>
                                    <th>Lý do</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dangKys as $index => $dk)
                                    <tr>
                                        <td>{{ $dangKys->firstItem() + $index }}</td>
                                        <td><code>{{ $dk->sinhVien->ma_sinh_vien }}</code></td>
                                        <td>{{ $dk->sinhVien->ho_ten }}</td>
                                        <td>
                                            <strong>{{ $dk->monHoc->ten_mon }}</strong>
                                            <br><small class="text-muted">{{ $dk->monHoc->ma_mon }}</small>
                                        </td>
                                        <td>{{ $dk->hocKy->ten_hoc_ky }}</td>
                                        <td>{{ $dk->ngay_dang_ky->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if ($dk->uu_tien >= 100)
                                                <span class="badge bg-danger">{{ $dk->uu_tien }}</span>
                                            @elseif($dk->uu_tien >= 50)
                                                <span class="badge bg-warning">{{ $dk->uu_tien }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $dk->uu_tien }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $dk->trang_thai_badge }}">
                                                {{ $dk->trang_thai_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($dk->ly_do_that_bai)
                                                <small class="text-danger">{{ $dk->ly_do_that_bai }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dk->trang_thai == 'cho_xep_lop')
                                                <button type="button" class="btn btn-sm btn-primary btn-xep-thu-cong"
                                                    data-dang-ky-id="{{ $dk->id }}"
                                                    data-sinh-vien="{{ $dk->sinhVien->ho_ten }}"
                                                    data-mon-hoc-id="{{ $dk->mon_hoc_id }}">
                                                    <i class="bi bi-pencil"></i> Xếp
                                                </button>
                                            @elseif($dk->lopHocPhanSinhVien)
                                                <a href="{{ route('dao-tao.xep-lop.danh-sach-lop', $dk->lopHocPhanSinhVien->lop_hoc_phan_id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Xem lớp
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $dangKys->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal xếp lớp thủ công -->
    <div class="modal fade" id="modalXepLop" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xếp lớp thủ công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Sinh viên:</strong> <span id="tenSinhVien"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Chọn lớp học phần</label>
                        <select id="selectLopHocPhan" class="form-select">
                            <option value="">-- Chọn lớp --</option>
                        </select>
                        <small class="text-muted">Chỉ hiển thị các lớp còn chỗ trống</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="btnXacNhanXepLop">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentDangKyId = null;

            $(document).ready(function() {
                // Xếp lớp tự động
                $('#btnXepLopTuDong').click(function() {
                    const hocKyId = $('select[name="hoc_ky_id"]').val();

                    if (!hocKyId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa chọn học kỳ',
                            text: 'Vui lòng chọn học kỳ trước khi xếp lớp tự động.'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Xác nhận xếp lớp tự động',
                        text: 'Hệ thống sẽ tự động xếp lớp cho tất cả sinh viên đăng ký trong học kỳ này.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xếp lớp',
                        cancelButtonText: 'Hủy',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: '{{ route('dao-tao.xep-lop.auto-assign') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    hoc_ky_id: hocKyId
                                }
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Hoàn tất!',
                                html: result.value.message
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                });

                // Xếp lớp thủ công - Mở modal
                $('.btn-xep-thu-cong').click(function() {
                    currentDangKyId = $(this).data('dang-ky-id');
                    const tenSinhVien = $(this).data('sinh-vien');
                    const monHocId = $(this).data('mon-hoc-id');
                    const hocKyId = $('select[name="hoc_ky_id"]').val();

                    $('#tenSinhVien').text(tenSinhVien);

                    // Load danh sách lớp học phần
                    $.ajax({
                        url: `/dao-tao/xep-lop/lop-hoc-phan-by-mon/${monHocId}`,
                        method: 'GET',
                        data: {
                            hoc_ky_id: hocKyId
                        },
                        success: function(response) {
                            let options = '<option value="">-- Chọn lớp --</option>';
                            response.data.forEach(lop => {
                                options +=
                                    `<option value="${lop.id}">${lop.ma_lop_hoc_phan} (Còn ${lop.con_trong} chỗ)</option>`;
                            });
                            $('#selectLopHocPhan').html(options);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không thể tải danh sách lớp học phần!'
                            });
                        }
                    });

                    $('#modalXepLop').modal('show');
                });

                // Xác nhận xếp lớp thủ công
                $('#btnXacNhanXepLop').click(function() {
                    const lopHocPhanId = $('#selectLopHocPhan').val();

                    if (!lopHocPhanId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa chọn lớp',
                            text: 'Vui lòng chọn lớp học phần.'
                        });
                        return;
                    }

                    $.ajax({
                        url: '{{ route('dao-tao.xep-lop.manual-assign') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            dang_ky_tam_id: currentDangKyId,
                            lop_hoc_phan_id: lopHocPhanId
                        },
                        success: function(response) {
                            $('#modalXepLop').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: xhr.responseJSON?.message || 'Có lỗi xảy ra!'
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
