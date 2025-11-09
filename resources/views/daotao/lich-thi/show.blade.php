@extends('layouts.layout-daotao')

@section('title', 'Chi tiết Lịch thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Lịch thi</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.lich-thi.index') }}">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Thông tin lịch thi</h4>
                <div>
                    <a href="{{ route('dao-tao.lich-thi.phan-phong', $lichThi) }}" class="btn btn-info btn-sm">
                        <i class="bi bi-door-open"></i> Phân phòng thi
                    </a>
                    <a href="{{ route('dao-tao.lich-thi.edit', $lichThi) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <form action="{{ route('dao-tao.lich-thi.destroy', $lichThi) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Lớp học phần:</th>
                                <td><strong>{{ $lichThi->lopHocPhan->ma_lop_hp }}</strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td>{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</td>
                            </tr>
                            <tr>
                                <th>Mã môn:</th>
                                <td>{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</td>
                            </tr>
                            <tr>
                                <th>Học kỳ:</th>
                                <td>{{ $lichThi->hocKy->ten_hoc_ky }}</td>
                            </tr>
                            <tr>
                                <th>Loại thi:</th>
                                <td>
                                    @if($lichThi->loai_thi == 'giua_ky')
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    @elseif($lichThi->loai_thi == 'cuoi_ky')
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    @else
                                        <span class="badge bg-warning">Thi lại</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Ngày thi:</th>
                                <td><strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Giờ thi:</th>
                                <td>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</td>
                            </tr>
                            <tr>
                                <th>Phòng thi:</th>
                                <td>{{ $lichThi->phongThi->ten_phong ?? 'Chưa phân phòng' }}</td>
                            </tr>
                            <tr>
                                <th>Số SV dự thi:</th>
                                <td>
                                    <strong>{{ $lichThi->lopHocPhan->lopHocPhanSinhViens->count() }} sinh viên</strong>
                                    @if($lichThi->so_sinh_vien_du_thi && $lichThi->so_sinh_vien_du_thi != $lichThi->lopHocPhan->lopHocPhanSinhViens->count())
                                        <br><small class="text-muted">(Dự kiến: {{ $lichThi->so_sinh_vien_du_thi }})</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Hình thức thi:</th>
                                <td>
                                    @if($lichThi->hinh_thuc == 'offline')
                                        <span class="badge bg-secondary">Thi tại trường</span>
                                    @elseif($lichThi->hinh_thuc == 'online')
                                        <span class="badge bg-primary">Thi trực tuyến</span>
                                    @else
                                        <span class="badge bg-success">Kết hợp</span>
                                    @endif
                                </td>
                            </tr>
                            @if($lichThi->link_online && $lichThi->hinh_thuc != 'offline')
                            <tr>
                                <th>Link thi online:</th>
                                <td><a href="{{ $lichThi->link_online }}" target="_blank">{{ $lichThi->link_online }}</a></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr>

                <h5>Giám thị</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Giám thị 1:</strong> {{ $lichThi->giamThi1->ho_ten ?? 'Chưa phân công' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Giám thị 2:</strong> {{ $lichThi->giamThi2->ho_ten ?? 'Chưa phân công' }}
                    </div>
                </div>

                <hr>

                <h5>Tài liệu</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Đề thi:</strong> 
                        @if($lichThi->de_thi_file)
                            <div class="mt-2">
                                <a href="{{ route('dao-tao.lich-thi.download-de-thi', $lichThi) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Tải xuống
                                </a>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-file-earmark-pdf"></i> {{ basename($lichThi->de_thi_file) }}
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Chưa upload</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Đáp án:</strong> 
                        @if($lichThi->dap_an_file)
                            <div class="mt-2">
                                <a href="{{ route('dao-tao.lich-thi.download-dap-an', $lichThi) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Tải xuống
                                </a>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-file-earmark-pdf"></i> {{ basename($lichThi->dap_an_file) }}
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Chưa upload</span>
                        @endif
                    </div>
                </div>

                @if($lichThi->ghi_chu)
                <hr>
                <h5>Ghi chú</h5>
                <p>{{ $lichThi->ghi_chu }}</p>
                @endif
            </div>
        </div>

        <!-- Danh sách sinh viên dự thi -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Danh sách sinh viên dự thi ({{ $lichThi->lopHocPhan->lopHocPhanSinhViens->count() }} sinh viên)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>MSSV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Email</th>
                                <th>SĐT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lichThi->lopHocPhan->lopHocPhanSinhViens as $index => $lhpsv)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lhpsv->sinhVien->ma_sinh_vien }}</td>
                                <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                <td>{{ $lhpsv->sinhVien->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
                                <td>{{ $lhpsv->sinhVien->email }}</td>
                                <td>{{ $lhpsv->sinhVien->so_dien_thoai }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
