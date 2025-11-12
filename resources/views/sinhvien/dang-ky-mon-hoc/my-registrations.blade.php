@extends('layouts.layout-sinhvien')

@section('title', 'Lịch sử đăng ký môn học')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử đăng ký môn học</h3>
                    <p class="text-subtitle text-muted">Xem lại các môn đã đăng ký</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dang-ky-mon-hoc.index') }}">Đăng ký môn
                                    học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lịch sử</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('sinh-vien.dang-ky-mon-hoc.my-registrations') }}" class="row g-3">
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
                        <label class="form-label">Trạng thái</label>
                        <select name="trang_thai" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="cho_xep_lop" {{ request('trang_thai') == 'cho_xep_lop' ? 'selected' : '' }}>Chờ
                                xếp lớp</option>
                            <option value="da_xep_lop" {{ request('trang_thai') == 'da_xep_lop' ? 'selected' : '' }}>Đã xếp
                                lớp</option>
                            <option value="that_bai" {{ request('trang_thai') == 'that_bai' ? 'selected' : '' }}>Thất bại
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">
                            <i class="bi bi-filter"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Chờ xếp lớp</h6>
                        <h3 class="text-warning">{{ $thongKe['cho_xep_lop'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Đã xếp lớp</h6>
                        <h3 class="text-success">{{ $thongKe['da_xep_lop'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Thất bại</h6>
                        <h3 class="text-danger">{{ $thongKe['that_bai'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Tổng tín chỉ</h6>
                        <h3 class="text-primary">{{ $thongKe['tong_tin_chi'] }} TC</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách đăng ký -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Lịch sử đăng ký ({{ $registrations->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if ($registrations->isEmpty())
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Bạn chưa có lịch sử đăng ký nào.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Môn học</th>
                                    <th>Tín chỉ</th>
                                    <th>Học kỳ</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Ưu tiên</th>
                                    <th>Trạng thái</th>
                                    <th>Lớp học phần</th>
                                    <th>Lý do thất bại</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registrations as $index => $dk)
                                    <tr>
                                        <td>{{ $registrations->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $dk->monHoc->ten_mon }}</strong>
                                            <br><small class="text-muted">{{ $dk->monHoc->ma_mon }}</small>
                                        </td>
                                        <td>{{ $dk->monHoc->tin_chi }} TC</td>
                                        <td>{{ $dk->hocKy->ten_hoc_ky }}</td>
                                        <td>{{ $dk->ngay_dang_ky->format('d/m/Y H:i') }}</td>
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
                                            @switch($dk->trang_thai)
                                                @case('cho_xep_lop')
                                                    <span class="badge bg-warning">Chờ xếp lớp</span>
                                                @break

                                                @case('da_xep_lop')
                                                    <span class="badge bg-success">Đã xếp lớp</span>
                                                @break

                                                @case('that_bai')
                                                    <span class="badge bg-danger">Thất bại</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if ($dk->lopHocPhanSinhVien)
                                                <code>{{ $dk->lopHocPhanSinhVien->lopHocPhan->ma_lop_hoc_phan }}</code>
                                            @else
                                                <span class="text-muted">Chưa xếp</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dk->trang_thai == 'that_bai' && $dk->ly_do_that_bai)
                                                <small class="text-danger">{{ $dk->ly_do_that_bai }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $registrations->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
