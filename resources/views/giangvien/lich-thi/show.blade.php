@extends('layouts.giangvien')

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
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.lich-thi.index') }}">Lịch thi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thông tin lịch thi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Lớp học phần:</th>
                                <td><strong>{{ $lichThi->lopHocPhan->ma_lop }}</strong></td>
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
                                <th width="180">Ngày thi:</th>
                                <td><strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Giờ thi:</th>
                                <td>{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</td>
                            </tr>
                            <tr>
                                <th>Phòng thi:</th>
                                <td>{{ $lichThi->phongHoc->ten_phong }}</td>
                            </tr>
                            <tr>
                                <th>Hình thức:</th>
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
                        </table>
                    </div>
                </div>

                @if($lichThi->link_online)
                <div class="alert alert-info">
                    <strong>Link thi online:</strong> <a href="{{ $lichThi->link_online }}" target="_blank">{{ $lichThi->link_online }}</a>
                </div>
                @endif

                @if($lichThi->ghi_chu)
                <div class="alert alert-warning">
                    <strong>Ghi chú:</strong> {{ $lichThi->ghi_chu }}
                </div>
                @endif
            </div>
        </div>

        <!-- Upload Đề thi & Đáp án -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Đề thi & Đáp án</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Đề thi -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Đề thi:</strong>
                            @if($lichThi->de_thi_file)
                                <div class="mt-2">
                                    <span class="badge bg-success">Đã upload</span>
                                    <a href="{{ route('giangvien.lich-thi.download-de-thi', $lichThi) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-download"></i> Tải xuống
                                    </a>
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-file-earmark-pdf"></i> {{ basename($lichThi->de_thi_file) }}
                                    </div>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="badge bg-secondary">Chưa có</span>
                                </div>
                            @endif
                        </div>
                        <form action="{{ route('giangvien.lich-thi.upload-de-thi', $lichThi) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="de_thi" class="form-control" accept=".pdf,.doc,.docx" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            </div>
                            <small class="text-muted">Chỉ chấp nhận file PDF, DOC, DOCX (Max 10MB)</small>
                        </form>
                    </div>

                    <!-- Đáp án -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Đáp án:</strong>
                            @if($lichThi->dap_an_file)
                                <div class="mt-2">
                                    <span class="badge bg-success">Đã upload</span>
                                    <a href="{{ route('giangvien.lich-thi.download-dap-an', $lichThi) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-download"></i> Tải xuống
                                    </a>
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-file-earmark-pdf"></i> {{ basename($lichThi->dap_an_file) }}
                                    </div>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="badge bg-secondary">Chưa có</span>
                                </div>
                            @endif
                        </div>
                        <form action="{{ route('giangvien.lich-thi.upload-dap-an', $lichThi) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="dap_an" class="form-control" accept=".pdf,.doc,.docx" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            </div>
                            <small class="text-muted">Chỉ chấp nhận file PDF, DOC, DOCX (Max 10MB)</small>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giám thị -->
        @if($isGiamThi)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thông tin giám thị</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Bạn được phân công làm giám thị cho ca thi này.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Giám thị 1:</strong> {{ $lichThi->giamThi1->ho_ten ?? 'Chưa phân công' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Giám thị 2:</strong> {{ $lichThi->giamThi2->ho_ten ?? 'Chưa phân công' }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Danh sách sinh viên -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Danh sách sinh viên dự thi ({{ $lichThi->lopHocPhan->lopHocPhanSinhViens->count() }} sinh viên)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>MSSV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Email</th>
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
