@extends('layouts.layout-giangvien')

@section('title', 'Lịch Coi Thi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch coi thi</h3>
                <p class="text-subtitle text-muted">Danh sách ca thi bạn được phân công giám thị</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch coi thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('giangvien.lich-thi.lich-coi-thi') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="month" name="thang" class="form-control" value="{{ request('thang') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="da_coi" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="0" {{ request('da_coi') === '0' ? 'selected' : '' }}>Sắp coi</option>
                                    <option value="1" {{ request('da_coi') === '1' ? 'selected' : '' }}>Đã coi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
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

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Môn học</th>
                                <th>Lớp HP</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ</th>
                                <th>Phòng</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichCoiThis as $index => $lichThi)
                            <tr class="{{ $lichThi->ngay_thi < now()->toDateString() ? 'table-secondary' : '' }}">
                                <td>{{ $lichCoiThis->firstItem() + $index }}</td>
                                <td>
                                    {{ $lichThi->lopHocPhan->monHoc->ten_mon }}
                                    <br><small class="text-muted">{{ $lichThi->lopHocPhan->monHoc->ma_mon }}</small>
                                </td>
                                <td>{{ $lichThi->lopHocPhan->ma_lop }}</td>
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
                                    <strong>{{ $lichThi->ngay_thi->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $lichThi->ngay_thi->locale('vi')->isoFormat('dddd') }}</small>
                                </td>
                                <td>{{ $lichThi->gio_bat_dau }}<br>{{ $lichThi->gio_ket_thuc }}</td>
                                <td>{{ $lichThi->phongHoc->ten_phong }}</td>
                                <td>
                                    @php
                                        $giangVien = Auth::user()->giangVien;
                                    @endphp
                                    @if($lichThi->giam_thi_1_id == $giangVien->id)
                                        <span class="badge bg-primary">Giám thị 1</span>
                                    @else
                                        <span class="badge bg-secondary">Giám thị 2</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lichThi->ngay_thi < now()->toDateString())
                                        <span class="badge bg-success">Đã coi</span>
                                    @elseif($lichThi->ngay_thi->isToday())
                                        <span class="badge bg-warning">Hôm nay</span>
                                    @else
                                        <span class="badge bg-info">Sắp tới</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('giangvien.lich-thi.show', $lichThi) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    <i class="bi bi-inbox"></i> Không có lịch coi thi nào
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $lichCoiThis->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
