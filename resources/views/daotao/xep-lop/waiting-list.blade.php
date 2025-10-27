@extends('layouts.app-daotao')

@section('title', 'Danh sách chờ')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách chờ (Waiting List)</h3>
                    <p class="text-subtitle text-muted">Sinh viên không xếp được lớp</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.xep-lop.index') }}">Xếp lớp</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Danh sách chờ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('dao-tao.xep-lop.waiting-list') }}" class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route('dao-tao.xep-lop.index') }}" class="btn btn-secondary d-block">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách chờ -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    Sinh viên không xếp được lớp ({{ $waitingList->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if ($waitingList->isEmpty())
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tuyệt vời! Không có sinh viên nào trong danh sách chờ.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <strong>Lưu ý:</strong> Những sinh viên này cần được xếp lớp thủ công hoặc chờ có lớp mở thêm.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày ĐK</th>
                                    <th>Ưu tiên</th>
                                    <th>Lý do thất bại</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($waitingList as $index => $dk)
                                    <tr>
                                        <td>{{ $waitingList->firstItem() + $index }}</td>
                                        <td><code>{{ $dk->sinhVien->ma_sinh_vien }}</code></td>
                                        <td>{{ $dk->sinhVien->ho_ten }}</td>
                                        <td>
                                            @if ($dk->sinhVien->lopHanhChinh)
                                                {{ $dk->sinhVien->lopHanhChinh->ma_lop }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $dk->monHoc->ten_mon }}</strong>
                                            <br><small class="text-muted">{{ $dk->monHoc->ma_mon }}</small>
                                        </td>
                                        <td>{{ $dk->hocKy->ten_hoc_ky }}</td>
                                        <td>{{ $dk->ngay_dang_ky->format('d/m/Y') }}</td>
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
                                            <small class="text-danger">
                                                {{ $dk->ly_do_that_bai ?? 'Không rõ' }}
                                            </small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary btn-xep-thu-cong"
                                                data-dang-ky-id="{{ $dk->id }}"
                                                data-sinh-vien="{{ $dk->sinhVien->ho_ten }}"
                                                data-mon-hoc-id="{{ $dk->mon_hoc_id }}"
                                                data-hoc-ky-id="{{ $dk->hoc_ky_id }}">
                                                <i class="bi bi-pencil"></i> Xếp lớp
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $waitingList->links() }}
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
                // Xếp lớp thủ công - Mở modal
                $('.btn-xep-thu-cong').click(function() {
                    currentDangKyId = $(this).data('dang-ky-id');
                    const tenSinhVien = $(this).data('sinh-vien');
                    const monHocId = $(this).data('mon-hoc-id');
                    const hocKyId = $(this).data('hoc-ky-id');

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
