@extends('layouts.layout-giangvien')

@section('title', 'Kết quả học tập')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kết quả học tập</h3>
                    <p class="text-subtitle text-muted">Xem kết quả học tập sinh viên các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kết quả học tập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('giangvien.ket-qua-hoc-tap.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Học kỳ</label>
                                <select name="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả học kỳ --</option>
                                    @foreach ($hocKys as $hocKy)
                                        <option value="{{ $hocKy->id }}"
                                            {{ request('hoc_ky_id') == $hocKy->id ? 'selected' : '' }}>
                                            {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="mo_lop" {{ request('trang_thai') == 'mo_lop' ? 'selected' : '' }}>Mở
                                        lớp</option>
                                    <option value="dang_hoc" {{ request('trang_thai') == 'dang_hoc' ? 'selected' : '' }}>
                                        Đang học</option>
                                    <option value="ket_thuc" {{ request('trang_thai') == 'ket_thuc' ? 'selected' : '' }}>
                                        Kết thúc</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Mã lớp, tên lớp, môn học..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Danh sách lớp học phần -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lớp học phần</h4>
                </div>
                <div class="card-body">
                    @if ($lopHocPhans->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Không tìm thấy lớp học phần nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã lớp</th>
                                        <th>Môn học</th>
                                        <th>Học kỳ</th>
                                        <th>Sĩ số</th>
                                        <th>Trạng thái</th>
                                        <th>Điểm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lopHocPhans as $index => $lop)
                                        <tr>
                                            <td>{{ $lopHocPhans->firstItem() + $index }}</td>
                                            <td><strong>{{ $lop->ma_lop_hp ?? 'N/A' }}</strong></td>
                                            <td>
                                                @if($lop->monHoc)
                                                    {{ $lop->monHoc->ma_mon }} - {{ $lop->monHoc->ten_mon }}<br>
                                                    <small class="text-muted">{{ $lop->monHoc->so_tin_chi }} TC</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lop->hocKy)
                                                    {{ $lop->hocKy->ten_hoc_ky }}<br>
                                                    <small class="text-muted">{{ $lop->hocKy->nam_hoc }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $lop->so_sinh_vien }} SV</span>
                                            </td>
                                            <td>
                                                @if ($lop->trang_thai_lop == 'mo_lop' || $lop->trang_thai_lop == 'mo_dang_ky')
                                                    <span class="badge bg-secondary">Mở lớp</span>
                                                @elseif($lop->trang_thai_lop == 'dang_hoc')
                                                    <span class="badge bg-primary">Đang học</span>
                                                @elseif($lop->trang_thai_lop == 'ket_thuc')
                                                    <span class="badge bg-success">Kết thúc</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $lop->trang_thai_lop ?? 'N/A' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="mb-1">
                                                    @if ($lop->da_nhap_diem)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> {{ $lop->sv_da_nhap }}/{{ $lop->so_sinh_vien }} SV
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="bi bi-exclamation-circle"></i> Chưa nhập
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($lop->so_sinh_vien > 0)
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar 
                                                        @if($lop->ty_le_nhap >= 100) bg-success
                                                        @elseif($lop->ty_le_nhap >= 70) bg-info
                                                        @elseif($lop->ty_le_nhap >= 30) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                        role="progressbar" 
                                                        style="width: {{ $lop->ty_le_nhap }}%;" 
                                                        aria-valuenow="{{ $lop->ty_le_nhap }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $lop->ty_le_nhap }}% đã nhập</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('giangvien.nhap-diem.show', $lop->id) }}"
                                                        class="btn btn-warning" title="Nhập điểm">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="{{ route('giangvien.ket-qua-hoc-tap.show', $lop->id) }}"
                                                        class="btn btn-primary" title="Xem bảng điểm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('giangvien.ket-qua-hoc-tap.phan-tich', $lop->id) }}"
                                                        class="btn btn-info" title="Phân tích điểm">
                                                        <i class="bi bi-graph-up"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $lopHocPhans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
