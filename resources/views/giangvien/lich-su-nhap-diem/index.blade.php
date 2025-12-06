@extends('layouts.layout-giangvien')

@section('title', 'Lịch sử nhập điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử nhập điểm</h3>
                    <p class="text-subtitle text-muted">Xem lịch sử các thao tác nhập điểm của bạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.nhap-diem.index') }}">Nhập điểm</a></li>
                            <li class="breadcrumb-item active">Lịch sử nhập điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.lich-su-nhap-diem.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="hoc_ky_id">Học kỳ</label>
                                    <select name="hoc_ky_id" id="hoc_ky_id" class="form-select">
                                        <option value="">Tất cả</option>
                                        @foreach($hocKys as $hocKy)
                                            <option value="{{ $hocKy->id }}" {{ request('hoc_ky_id') == $hocKy->id ? 'selected' : '' }}>
                                                {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lop_hoc_phan_id">Lớp học phần</label>
                                    <select name="lop_hoc_phan_id" id="lop_hoc_phan_id" class="form-select">
                                        <option value="">Tất cả</option>
                                        @foreach($lopHocPhans as $lhp)
                                            <option value="{{ $lhp->id }}" {{ request('lop_hoc_phan_id') == $lhp->id ? 'selected' : '' }}>
                                                {{ $lhp->ma_lop_hp }} - {{ $lhp->monHoc->ten_mon }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="loai_thao_tac">Loại thao tác</label>
                                    <select name="loai_thao_tac" id="loai_thao_tac" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="them" {{ request('loai_thao_tac') == 'them' ? 'selected' : '' }}>Thêm</option>
                                        <option value="sua" {{ request('loai_thao_tac') == 'sua' ? 'selected' : '' }}>Sửa</option>
                                        <option value="xoa" {{ request('loai_thao_tac') == 'xoa' ? 'selected' : '' }}>Xóa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tu_ngay">Từ ngày</label>
                                    <input type="date" name="tu_ngay" id="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="den_ngay">Đến ngày</label>
                                    <input type="date" name="den_ngay" id="den_ngay" class="form-control" value="{{ request('den_ngay') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keyword">Tìm kiếm (Mã SV/Tên SV)</label>
                                    <input type="text" name="keyword" id="keyword" class="form-control" 
                                           placeholder="Nhập mã hoặc tên sinh viên" value="{{ request('keyword') }}">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('giangvien.lich-su-nhap-diem.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lịch sử nhập điểm</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($lichSu->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Không có lịch sử nhập điểm nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Thời gian</th>
                                        <th>Mã SV</th>
                                        <th>Tên sinh viên</th>
                                        <th>Lớp học phần</th>
                                        <th>Môn học</th>
                                        <th>Đầu điểm</th>
                                        <th>Cột điểm</th>
                                        <th>Điểm cũ</th>
                                        <th>Điểm mới</th>
                                        <th>Loại thao tác</th>
                                        <th>Lý do</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lichSu as $index => $ls)
                                        @php
                                            $sinhVien = $ls->lopHocPhanSinhVien->sinhVien ?? null;
                                            $lopHocPhan = $ls->lopHocPhanSinhVien->lopHocPhan ?? null;
                                            $monHoc = $lopHocPhan->monHoc ?? null;
                                            $cauHinh = $ls->cauHinh ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $lichSu->firstItem() + $index }}</td>
                                            <td>
                                                <small>{{ $ls->created_at->format('d/m/Y H:i:s') }}</small>
                                            </td>
                                            <td><strong>{{ $sinhVien->ma_sinh_vien ?? 'N/A' }}</strong></td>
                                            <td>{{ $sinhVien->ho_ten ?? 'N/A' }}</td>
                                            <td>
                                                @if($lopHocPhan)
                                                    <a href="{{ route('giangvien.lich-su-nhap-diem.show', $lopHocPhan->id) }}" 
                                                       class="text-primary">
                                                        {{ $lopHocPhan->ma_lop_hp }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $monHoc->ten_mon ?? 'N/A' }}</td>
                                            <td>{{ $cauHinh->ten_dau_diem ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $ls->cot_diem }}</td>
                                            <td class="text-center">
                                                @if($ls->diem_cu !== null)
                                                    <span class="badge bg-secondary">{{ number_format($ls->diem_cu, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($ls->diem_moi !== null)
                                                    <span class="badge bg-primary">{{ number_format($ls->diem_moi, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->loai_thao_tac == 'them')
                                                    <span class="badge bg-success">Thêm</span>
                                                @elseif($ls->loai_thao_tac == 'sua')
                                                    <span class="badge bg-warning">Sửa</span>
                                                @elseif($ls->loai_thao_tac == 'xoa')
                                                    <span class="badge bg-danger">Xóa</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ls->ly_do)
                                                    <small class="text-muted">{{ Str::limit($ls->ly_do, 30) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lopHocPhan)
                                                    <a href="{{ route('giangvien.lich-su-nhap-diem.show', $lopHocPhan->id) }}" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $lichSu->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        // Tự động load danh sách lớp học phần khi chọn học kỳ
        document.getElementById('hoc_ky_id').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
    @endpush
@endsection

