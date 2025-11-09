@extends('layouts.layout-giangvien')

@section('title', 'Cảnh báo học vụ - Giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cảnh báo học vụ</h3>
                    <p class="text-subtitle text-muted">Danh sách cảnh báo học vụ sinh viên trong lớp giảng dạy</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cảnh báo học vụ</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="iconly-boldShow"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Tổng cảnh báo</h6>
                                    <h6 class="font-extrabold mb-0">{{ $tongCanhBao }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon red">
                                        <i class="iconly-boldDanger"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Chưa xử lý</h6>
                                    <h6 class="font-extrabold mb-0">{{ $chuaXuLy }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="iconly-boldTime-Circle"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Đang xử lý</h6>
                                    <h6 class="font-extrabold mb-0">{{ $dangXuLy }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon green">
                                        <i class="iconly-boldTick-Square"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Đã xử lý</h6>
                                    <h6 class="font-extrabold mb-0">{{ $daXuLy }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter Form -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tìm kiếm & Lọc</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.canh-bao-hoc-vu.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="chua_xu_ly"
                                            {{ request('trang_thai') == 'chua_xu_ly' ? 'selected' : '' }}>Chưa xử lý
                                        </option>
                                        <option value="dang_xu_ly"
                                            {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý
                                        </option>
                                        <option value="da_xu_ly" {{ request('trang_thai') == 'da_xu_ly' ? 'selected' : '' }}>
                                            Đã xử lý</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mức độ</label>
                                    <select name="muc_do" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="canh_cao" {{ request('muc_do') == 'canh_cao' ? 'selected' : '' }}>
                                            Cảnh cáo</option>
                                        <option value="dinh_chi" {{ request('muc_do') == 'dinh_chi' ? 'selected' : '' }}>
                                            Đình chỉ</option>
                                        <option value="buoc_thoi_hoc"
                                            {{ request('muc_do') == 'buoc_thoi_hoc' ? 'selected' : '' }}>Buộc thôi học
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Loại cảnh báo</label>
                                    <select name="loai" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="diem_thap" {{ request('loai') == 'diem_thap' ? 'selected' : '' }}>
                                            Điểm thấp</option>
                                        <option value="vang_nhieu" {{ request('loai') == 'vang_nhieu' ? 'selected' : '' }}>
                                            Vắng nhiều</option>
                                        <option value="no_hoc_phi" {{ request('loai') == 'no_hoc_phi' ? 'selected' : '' }}>
                                            Nợ học phí</option>
                                        <option value="hoc_ky_lien_tiep"
                                            {{ request('loai') == 'hoc_ky_lien_tiep' ? 'selected' : '' }}>Học kỳ liên tiếp
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm sinh viên</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Mã SV hoặc tên..." value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('giangvien.canh-bao-hoc-vu.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Table -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách cảnh báo</h5>
                </div>
                <div class="card-body">
                    @if ($canhBaoList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Sinh viên</th>
                                        <th>Loại</th>
                                        <th>Mức độ</th>
                                        <th>Lý do</th>
                                        <th>Ngày cảnh báo</th>
                                        <th>Trạng thái</th>
                                        <th>Người tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($canhBaoList as $index => $canhBao)
                                        <tr>
                                            <td>{{ $canhBaoList->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $canhBao->sinhVien->ma_sinh_vien ?? 'N/A' }}</strong><br>
                                                <small>{{ $canhBao->sinhVien->user->name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if ($canhBao->loai == 'diem_thap')
                                                    <span class="badge bg-warning">Điểm thấp</span>
                                                @elseif($canhBao->loai == 'vang_nhieu')
                                                    <span class="badge bg-info">Vắng nhiều</span>
                                                @elseif($canhBao->loai == 'no_hoc_phi')
                                                    <span class="badge bg-danger">Nợ học phí</span>
                                                @else
                                                    <span class="badge bg-secondary">Học kỳ liên tiếp</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($canhBao->muc_do == 'canh_cao')
                                                    <span class="badge bg-warning">Cảnh cáo</span>
                                                @elseif($canhBao->muc_do == 'dinh_chi')
                                                    <span class="badge bg-danger">Đình chỉ</span>
                                                @else
                                                    <span class="badge bg-dark">Buộc thôi học</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ Str::limit($canhBao->ly_do, 50) }}</small>
                                            </td>
                                            <td>
                                                {{ $canhBao->ngay_canh_bao ? $canhBao->ngay_canh_bao->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if ($canhBao->trang_thai == 'chua_xu_ly')
                                                    <span class="badge bg-secondary">Chưa xử lý</span>
                                                @elseif($canhBao->trang_thai == 'dang_xu_ly')
                                                    <span class="badge bg-primary">Đang xử lý</span>
                                                @else
                                                    <span class="badge bg-success">Đã xử lý</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $canhBao->nguoiTao->name ?? 'Hệ thống' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('giangvien.canh-bao-hoc-vu.show', $canhBao->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $canhBaoList->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Không có cảnh báo học vụ nào.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
