<<<<<<< HEAD

@extends('layouts.layout-daotao')

@extends('layouts.app-daotao')
=======
@extends('layouts.layout-daotao')
>>>>>>> origin/main


@section('title', 'Danh sách sinh viên lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách sinh viên</h3>
                    <p class="text-subtitle text-muted">Lớp: <strong>{{ $lopHocPhan->ma_lop_hoc_phan }}</strong></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.xep-lop.index') }}">Xếp lớp</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Danh sách lớp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
         <!-- test code -->
        <!-- Thông tin lớp học phần -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Thông tin lớp học phần</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Mã lớp:</strong></td>
                                <td><code>{{ $lopHocPhan->ma_lop_hoc_phan }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Môn học:</strong></td>
                                <td>{{ $lopHocPhan->monHoc->ten_mon }} ({{ $lopHocPhan->monHoc->ma_mon }})</td>
                            </tr>
                            <tr>
                                <td><strong>Số tín chỉ:</strong></td>
                                <td>{{ $lopHocPhan->monHoc->tin_chi }} TC</td>
                            </tr>
                            <tr>
                                <td><strong>Học kỳ:</strong></td>
                                <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Thông tin sĩ số</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Sĩ số tối đa:</strong></td>
                                <td><span class="badge bg-info">{{ $lopHocPhan->so_luong_toi_da }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Sĩ số hiện tại:</strong></td>
                                <td>
                                    <span
                                        class="badge {{ $lopHocPhan->so_luong_hien_tai >= $lopHocPhan->so_luong_toi_da ? 'bg-danger' : 'bg-success' }}">
                                        {{ $lopHocPhan->so_luong_hien_tai }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Còn trống:</strong></td>
                                <td>
                                    @php
                                        $conTrong = $lopHocPhan->so_luong_toi_da - $lopHocPhan->so_luong_hien_tai;
                                    @endphp
                                    <span class="badge {{ $conTrong > 0 ? 'bg-warning' : 'bg-secondary' }}">
                                        {{ max(0, $conTrong) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tỷ lệ lấp đầy:</strong></td>
                                <td>
                                    @php
                                        $tiLe = ($lopHocPhan->so_luong_hien_tai / $lopHocPhan->so_luong_toi_da) * 100;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $tiLe >= 100 ? 'bg-danger' : ($tiLe >= 80 ? 'bg-warning' : 'bg-success') }}"
                                            role="progressbar" style="width: {{ min(100, $tiLe) }}%">
                                            {{ number_format($tiLe, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('dao-tao.xep-lop.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button class="btn btn-success" onclick="exportExcel()">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </button>
                </div>
            </div>
        </div>
 <!-- //  test code -->
        <!-- Danh sách sinh viên -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Danh sách sinh viên ({{ $sinhViens->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if ($sinhViens->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Lớp này chưa có sinh viên nào.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tableSinhVien">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Lớp hành chính</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Ngày xếp lớp</th>
                                    <th>Phương thức</th>
                                    <th>Trạng thái</th>
                                    <th>Người duyệt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sinhViens as $index => $lhpsv)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><code>{{ $lhpsv->sinhVien->ma_sinh_vien }}</code></td>
                                        <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                        <td>
                                            @if ($lhpsv->sinhVien->lopHanhChinh)
                                                {{ $lhpsv->sinhVien->lopHanhChinh->ma_lop }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $lhpsv->ngay_dang_ky->format('d/m/Y') }}</td>
                                        <td>{{ $lhpsv->ngay_xep_lop ? $lhpsv->ngay_xep_lop->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @if ($lhpsv->phuong_thuc_xep == 'tu_dong')
                                                <span class="badge bg-info">Tự động</span>
                                            @else
                                                <span class="badge bg-warning">Thủ công</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($lhpsv->trang_thai)
                                                @case('da_xep_lop')
                                                    <span class="badge bg-info">Đã xếp lớp</span>
                                                @break

                                                @case('dang_hoc')
                                                    <span class="badge bg-primary">Đang học</span>
                                                @break

                                                @case('da_hoan_thanh')
                                                    <span class="badge bg-success">Đã hoàn thành</span>
                                                @break

                                                @case('bo_hoc')
                                                    <span class="badge bg-warning">Bỏ học</span>
                                                @break

                                                @case('huy_dang_ky')
                                                    <span class="badge bg-danger">Hủy đăng ký</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">{{ $lhpsv->trang_thai }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if ($lhpsv->nguoiDuyet)
                                                {{ $lhpsv->nguoiDuyet->ho_ten }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array($lhpsv->trang_thai, ['da_xep_lop', 'dang_hoc']))
                                                <button class="btn btn-sm btn-danger btn-xoa-khoi-lop"
                                                    data-id="{{ $lhpsv->id }}"
                                                    data-sinh-vien="{{ $lhpsv->sinhVien->ho_ten }}">
                                                    <i class="bi bi-trash"></i>
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
    </div>

    @push('scripts')
        <script>
            function exportExcel() {
                window.location.href = "{{ route('dao-tao.xep-lop.danh-sach-lop', $lopHocPhan->id) }}?export=excel";
            }

            $(document).ready(function() {
                // Xóa sinh viên khỏi lớp
                $('.btn-xoa-khoi-lop').click(function() {
                    const lhpsvId = $(this).data('id');
                    const tenSinhVien = $(this).data('sinh-vien');

                    Swal.fire({
                        title: 'Xác nhận xóa',
                        text: `Bạn có chắc chắn muốn xóa sinh viên ${tenSinhVien} khỏi lớp này?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Gọi API xóa (cần implement trong controller)
                            $.ajax({
                                url: `/dao-tao/xep-lop/xoa-khoi-lop/${lhpsvId}`,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
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
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: xhr.responseJSON?.message ||
                                            'Có lỗi xảy ra!'
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
