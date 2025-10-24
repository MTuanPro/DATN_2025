@extends('layouts.layout-daotao')

@section('title', 'Chương trình khung')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chương trình khung (CTĐT)</h3>
                    <p class="text-subtitle text-muted">Quản lý chương trình đào tạo theo chuyên ngành</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chương trình khung</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Chọn chuyên ngành -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Chọn Chuyên ngành</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dao-tao.chuong-trinh-khung.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-10">
                                <select name="chuyen_nganh_id" class="form-select" required>
                                    <option value="">-- Chọn chuyên ngành --</option>
                                    @foreach ($chuyenNganhs as $cn)
                                        <option value="{{ $cn->id }}"
                                            {{ $selectedChuyenNganhId == $cn->id ? 'selected' : '' }}>
                                            {{ $cn->nganh->khoa->ten_khoa }} - {{ $cn->nganh->ten_nganh }} -
                                            {{ $cn->ten_chuyen_nganh }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Xem CTĐT
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if ($selectedChuyenNganhId)
                <!-- Thống kê -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Tổng môn học</h6>
                                        <h3 class="mb-0">{{ $thongKe['tong_mon_hoc'] }}</h3>
                                    </div>
                                    <div class="avatar bg-primary">
                                        <i class="bi bi-book-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Tổng tín chỉ</h6>
                                        <h3 class="mb-0">{{ $thongKe['tong_tin_chi'] }}</h3>
                                    </div>
                                    <div class="avatar bg-success">
                                        <i class="bi bi-calculator-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">TC Bắt buộc</h6>
                                        <h3 class="mb-0">{{ $thongKe['tin_chi_bat_buoc'] }}</h3>
                                        <small class="text-muted">{{ $thongKe['mon_bat_buoc'] }} môn</small>
                                    </div>
                                    <div class="avatar bg-danger">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">TC Tự chọn</h6>
                                        <h3 class="mb-0">{{ $thongKe['tin_chi_tu_chon'] }}</h3>
                                        <small class="text-muted">{{ $thongKe['mon_tu_chon'] }} môn</small>
                                    </div>
                                    <div class="avatar bg-warning">
                                        <i class="bi bi-list-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách môn học theo học kỳ -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách môn học trong CTĐT</h5>
                        <a href="{{ route('dao-tao.chuong-trinh-khung.create', ['chuyen_nganh_id' => $selectedChuyenNganhId]) }}"
                            class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm môn học
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($chuongTrinhKhung && $chuongTrinhKhung->count() > 0)
                            @for ($hocKy = 1; $hocKy <= 8; $hocKy++)
                                @if (isset($chuongTrinhKhung[$hocKy]) && $chuongTrinhKhung[$hocKy]->count() > 0)
                                    <div class="mb-4">
                                        <h5 class="bg-light p-2 rounded">
                                            <i class="bi bi-calendar3"></i> Học kỳ {{ $hocKy }}
                                            <span class="badge bg-primary">
                                                {{ $chuongTrinhKhung[$hocKy]->sum(function ($item) {return $item->monHoc->so_tin_chi;}) }}
                                                TC
                                            </span>
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">STT</th>
                                                        <th width="10%">Mã môn</th>
                                                        <th width="25%">Tên môn</th>
                                                        <th width="8%">TC</th>
                                                        <th width="12%">Loại môn</th>
                                                        <th width="10%">Bắt buộc</th>
                                                        <th width="8%">Thứ tự</th>
                                                        <th width="15%">Ghi chú</th>
                                                        <th width="7%">Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($chuongTrinhKhung[$hocKy] as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td><strong>{{ $item->monHoc->ma_mon }}</strong></td>
                                                            <td>{{ $item->monHoc->ten_mon }}</td>
                                                            <td><span
                                                                    class="badge bg-info">{{ $item->monHoc->so_tin_chi }}</span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $loaiLabels = [
                                                                        'dai_cuong' => 'Đại cương',
                                                                        'co_so_nganh' => 'Cơ sở ngành',
                                                                        'chuyen_nganh_bat_buoc' => 'CN bắt buộc',
                                                                        'chuyen_nganh_tu_chon' => 'CN tự chọn',
                                                                        'thuc_tap' => 'Thực tập',
                                                                        'do_an_tot_nghiep' => 'ĐATN',
                                                                    ];
                                                                @endphp
                                                                <small>{{ $loaiLabels[$item->loai_mon_hoc] ?? $item->loai_mon_hoc }}</small>
                                                            </td>
                                                            <td>
                                                                @if ($item->bat_buoc)
                                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                                @else
                                                                    <span class="badge bg-warning">Tự chọn</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{ $item->thu_tu_hoc ?? '-' }}</td>
                                                            <td><small>{{ $item->ghi_chu ?? '-' }}</small></td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('dao-tao.chuong-trinh-khung.edit', $item->id) }}"
                                                                        class="btn btn-sm btn-warning" title="Sửa">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('dao-tao.chuong-trinh-khung.destroy', $item->id) }}"
                                                                        method="POST"
                                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa môn này khỏi CTĐT?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-danger" title="Xóa">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Chưa có môn học nào trong CTĐT.
                                <a
                                    href="{{ route('dao-tao.chuong-trinh-khung.create', ['chuyen_nganh_id' => $selectedChuyenNganhId]) }}">
                                    Thêm môn học
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
