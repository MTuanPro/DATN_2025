@extends('layouts.layout-giangvien')

@section('title', 'Xuất danh sách thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Xuất danh sách thi</h3>
                <p class="text-subtitle text-muted">Chọn lịch thi để xuất danh sách sinh viên đủ điều kiện đi thi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Xuất danh sách thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Bộ lọc -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tìm kiếm & Lọc</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('giangvien.xuat-danh-sach-thi.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hoc_ky_id">Học kỳ</label>
                                <select name="hoc_ky_id" id="hoc_ky_id" class="form-select">
                                    <option value="">-- Tất cả học kỳ --</option>
                                    @foreach($hocKys as $hk)
                                        <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                            {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="loai_thi">Loại thi</label>
                                <select name="loai_thi" id="loai_thi" class="form-select">
                                    <option value="">-- Tất cả loại thi --</option>
                                    <option value="giua_ky" {{ request('loai_thi') == 'giua_ky' ? 'selected' : '' }}>Giữa kỳ</option>
                                    <option value="cuoi_ky" {{ request('loai_thi') == 'cuoi_ky' ? 'selected' : '' }}>Cuối kỳ</option>
                                    <option value="thi_lai" {{ request('loai_thi') == 'thi_lai' ? 'selected' : '' }}>Thi lại</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Tìm kiếm</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Mã môn, tên môn...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách lịch thi -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Danh sách lịch thi ({{ $lichThis->total() }} lịch thi)</h4>
            </div>
            <div class="card-body">
                @if($lichThis->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Chưa có lịch thi nào.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Lớp học phần</th>
                                    <th>Môn học</th>
                                    <th>Loại thi</th>
                                    <th>Ngày thi</th>
                                    <th>Giờ thi</th>
                                    <th>Phòng thi</th>
                                    <th>Học kỳ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lichThis as $index => $lichThi)
                                <tr>
                                    <td>{{ $lichThis->firstItem() + $index }}</td>
                                    <td><strong>{{ $lichThi->lopHocPhan->ma_lop_hp }}</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</strong><br>
                                            <small class="text-muted">{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($lichThi->loai_thi == 'giua_ky')
                                            <span class="badge bg-info">Giữa kỳ</span>
                                        @elseif($lichThi->loai_thi == 'cuoi_ky')
                                            <span class="badge bg-danger">Cuối kỳ</span>
                                        @else
                                            <span class="badge bg-warning">Thi lại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong><br>
                                        <small class="text-muted">{{ $lichThi->ngay_thi->format('l') }}</small>
                                    </td>
                                    <td>
                                        @if($lichThi->caHoc)
                                            <strong>{{ $lichThi->caHoc->ten_ca }}</strong><br>
                                            <small class="text-muted">{{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}</small>
                                        @else
                                            {{ $lichThi->gio_bat_dau }} - {{ $lichThi->gio_ket_thuc }}
                                        @endif
                                    </td>
                                    <td>{{ $lichThi->phongHoc->ten_phong ?? 'Chưa xác định' }}</td>
                                    <td>
                                        {{ $lichThi->lopHocPhan->hocKy->ten_hoc_ky }}<br>
                                        <small class="text-muted">{{ $lichThi->lopHocPhan->hocKy->nam_hoc }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('giangvien.lich-thi.xuat-danh-sach-di-thi', $lichThi) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-file-earmark-check"></i> Xuất danh sách
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lichThis->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

