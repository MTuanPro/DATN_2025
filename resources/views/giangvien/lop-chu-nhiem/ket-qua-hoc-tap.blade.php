@extends('layouts.layout-giangvien')

@section('title', 'Kết quả học tập - Lớp ' . $lop->ma_lop)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kết quả học tập - Lớp {{ $lop->ma_lop }}</h3>
                    <p class="text-subtitle text-muted">Theo dõi kết quả học tập sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.lop-chu-nhiem.index') }}">Lớp chủ
                                    nhiệm</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('giangvien.lop-chu-nhiem.show', $lop->id) }}">{{ $lop->ma_lop }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kết quả học tập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thống kê tổng quan -->
            <div class="row mb-4">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['tong_sinh_vien'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="iconly-boldStar"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Điểm TB lớp</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['diem_tb_lop'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon success">
                                        <i class="iconly-boldTicket-Star"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Xuất sắc/Giỏi</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['xuat_sac'] + $thongKe['gioi'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon danger">
                                        <i class="iconly-boldDanger"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Yếu/Kém</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['yeu'] + $thongKe['kem'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', $lop->id) }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        @foreach ($hocKys as $hk)
                                            <option value="{{ $hk->id }}"
                                                {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Xếp loại</label>
                                    <select name="xep_loai" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="xuat_sac" {{ request('xep_loai') == 'xuat_sac' ? 'selected' : '' }}>
                                            Xuất sắc</option>
                                        <option value="gioi" {{ request('xep_loai') == 'gioi' ? 'selected' : '' }}>Giỏi
                                        </option>
                                        <option value="kha" {{ request('xep_loai') == 'kha' ? 'selected' : '' }}>Khá
                                        </option>
                                        <option value="trung_binh"
                                            {{ request('xep_loai') == 'trung_binh' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="yeu" {{ request('xep_loai') == 'yeu' ? 'selected' : '' }}>Yếu
                                        </option>
                                        <option value="kem" {{ request('xep_loai') == 'kem' ? 'selected' : '' }}>Kém
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="da_cong_bo" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="1" {{ request('da_cong_bo') == '1' ? 'selected' : '' }}>Đã công
                                            bố</option>
                                        <option value="0" {{ request('da_cong_bo') == '0' ? 'selected' : '' }}>Chưa
                                            công bố</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm sinh viên</label>
                                    <input type="text" name="search" class="form-control" placeholder="Mã SV, Họ tên..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bảng kết quả -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách kết quả học tập</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>Học kỳ</th>
                                    <th>
                                        <a
                                            href="{{ route('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', array_merge(request()->all(), ['sort_by' => 'tong_tin_chi_dang_ky', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                            TC đăng ký
                                            @if (request('sort_by') == 'tong_tin_chi_dang_ky')
                                                <i
                                                    class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a
                                            href="{{ route('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', array_merge(request()->all(), ['sort_by' => 'tong_tin_chi_dat', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                            TC đạt
                                            @if (request('sort_by') == 'tong_tin_chi_dat')
                                                <i
                                                    class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a
                                            href="{{ route('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', array_merge(request()->all(), ['sort_by' => 'diem_trung_binh_he_10', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                            ĐTB Hệ 10
                                            @if (request('sort_by') == 'diem_trung_binh_he_10')
                                                <i
                                                    class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a
                                            href="{{ route('giangvien.lop-chu-nhiem.ket-qua-hoc-tap', array_merge(request()->all(), ['sort_by' => 'diem_trung_binh_he_4', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}">
                                            ĐTB Hệ 4 (GPA)
                                            @if (request('sort_by') == 'diem_trung_binh_he_4')
                                                <i
                                                    class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Xếp loại</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bangDiems as $index => $bd)
                                    <tr>
                                        <td>{{ $bangDiems->firstItem() + $index }}</td>
                                        <td><strong>{{ $bd->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $bd->sinhVien->ho_ten }}</td>
                                        <td>{{ $bd->hocKy->ten_hoc_ky }} - {{ $bd->hocKy->nam_hoc }}</td>
                                        <td>
                                            <span class="badge bg-light-info">{{ $bd->tong_tin_chi_dang_ky }} TC</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-success">{{ $bd->tong_tin_chi_dat }} TC</span>
                                        </td>
                                        <td>
                                            <strong
                                                class="text-{{ $bd->diem_trung_binh_he_10 >= 8 ? 'success' : ($bd->diem_trung_binh_he_10 >= 5 ? 'warning' : 'danger') }}">
                                                {{ number_format($bd->diem_trung_binh_he_10, 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            <strong
                                                class="text-{{ $bd->diem_trung_binh_he_4 >= 3.2 ? 'success' : ($bd->diem_trung_binh_he_4 >= 2.0 ? 'warning' : 'danger') }}">
                                                {{ number_format($bd->diem_trung_binh_he_4, 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $bd->xep_loai_badge }}">
                                                {{ $bd->xep_loai_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($bd->da_cong_bo)
                                                <span class="badge bg-light-success">
                                                    <i class="bi bi-check-circle"></i> Đã công bố
                                                </span>
                                            @else
                                                <span class="badge bg-light-secondary">
                                                    <i class="bi bi-clock"></i> Chưa công bố
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Chưa có dữ liệu kết quả học tập
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $bangDiems->links() }}
                    </div>
                </div>
            </div>

            <!-- Biểu đồ phân bố xếp loại -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Phân bố xếp loại học tập</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <h6 class="text-muted">Xuất sắc</h6>
                            <h4 class="text-success">{{ $thongKe['xuat_sac'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted">Giỏi</h6>
                            <h4 class="text-info">{{ $thongKe['gioi'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted">Khá</h6>
                            <h4 class="text-primary">{{ $thongKe['kha'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted">Trung bình</h6>
                            <h4 class="text-warning">{{ $thongKe['trung_binh'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted">Yếu</h6>
                            <h4 class="text-danger">{{ $thongKe['yeu'] }}</h4>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-muted">Kém</h6>
                            <h4 class="text-dark">{{ $thongKe['kem'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
