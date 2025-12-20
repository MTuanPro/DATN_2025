@extends('layouts.layout-daotao')

@section('title', 'Lịch theo Giảng viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch theo Giảng viên</h3>
                    <p class="text-subtitle text-muted">Xem lịch dạy theo từng giảng viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lịch theo Giảng viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
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

            {{-- Bộ lọc --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dao-tao.thoi-khoa-bieu.lich-theo-giang-vien') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="giang_vien_id" class="form-label">Giảng viên</label>
                            <select class="form-select" id="giang_vien_id" name="giang_vien_id">
                                <option value="">-- Tất cả giảng viên --</option>
                                @foreach($giangViens as $gv)
                                    <option value="{{ $gv->id }}" {{ request('giang_vien_id') == $gv->id ? 'selected' : '' }}>
                                        {{ $gv->ho_ten }} ({{ $gv->ma_giang_vien }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="hoc_ky_id" class="form-label">Học kỳ</label>
                            <select class="form-select" id="hoc_ky_id" name="hoc_ky_id">
                                <option value="">-- Tất cả học kỳ --</option>
                                @foreach($hocKys as $hk)
                                    <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                        {{ $hk->ten_hoc_ky }} ({{ $hk->nam_hoc }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tu_ngay" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="tu_ngay" name="tu_ngay" value="{{ request('tu_ngay') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="den_ngay" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="den_ngay" name="den_ngay" value="{{ request('den_ngay') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách lịch học --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lịch dạy</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ngày học</th>
                                    <th>Thứ</th>
                                    <th>Tiết</th>
                                    <th>Giờ</th>
                                    <th>Phòng học</th>
                                    <th>Lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Giảng viên</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lichHocs as $index => $lich)
                                    <tr>
                                        <td>{{ $lichHocs->firstItem() + $index }}</td>
                                        <td>{{ $lich->ngay_hoc->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $thu = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                                $thuIndex = $lich->ngay_hoc->dayOfWeek;
                                            @endphp
                                            {{ $thu[$thuIndex] }}
                                        </td>
                                        <td>
                                            Tiết {{ $lich->tiet_bat_dau }}
                                            @if($lich->tiet_ket_thuc != $lich->tiet_bat_dau)
                                                - {{ $lich->tiet_ket_thuc }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($lich->gio_bat_dau && $lich->gio_ket_thuc)
                                                {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $lich->phongHoc->ten_phong ?? 'N/A' }}</strong>
                                            @if($lich->phongHoc && $lich->phongHoc->vi_tri)
                                                <br><small class="text-muted">{{ $lich->phongHoc->vi_tri }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $lich->lopHocPhan->ma_lop_hp ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            {{ $lich->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                                            <br><small class="text-muted">{{ $lich->lopHocPhan->monHoc->ma_mon ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $lich->giangVien->ho_ten ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $lich->giangVien->ma_giang_vien ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @if($lich->trang_thai == 'da_day')
                                                <span class="badge bg-success">Đã dạy</span>
                                            @elseif($lich->trang_thai == 'dang_day')
                                                <span class="badge bg-primary">Đang dạy</span>
                                            @elseif($lich->trang_thai == 'chua_day')
                                                <span class="badge bg-warning">Chưa dạy</span>
                                            @elseif($lich->trang_thai == 'huy')
                                                <span class="badge bg-danger">Hủy</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $lich->trang_thai ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không tìm thấy lịch dạy nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $lichHocs->firstItem() ?? 0 }} - {{ $lichHocs->lastItem() ?? 0 }}
                                trong tổng số {{ $lichHocs->total() }} buổi dạy
                            </small>
                        </div>
                        <div>
                            {{ $lichHocs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

