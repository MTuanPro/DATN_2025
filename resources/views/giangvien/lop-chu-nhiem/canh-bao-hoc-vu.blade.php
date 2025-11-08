@extends('layouts.layout-giangvien')

@section('title', 'Cảnh báo học vụ - Lớp ' . $lop->ma_lop)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cảnh báo học vụ - Lớp {{ $lop->ma_lop }}</h3>
                    <p class="text-subtitle text-muted">Theo dõi cảnh báo học vụ sinh viên</p>
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
                            <li class="breadcrumb-item active" aria-current="page">Cảnh báo học vụ</li>
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
                                    <div class="stats-icon red">
                                        <i class="iconly-boldDanger"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Tổng cảnh báo</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['tong_canh_bao'] }}</h6>
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
                                    <div class="stats-icon orange">
                                        <i class="iconly-boldInfo-Square"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Chưa xử lý</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['chua_xu_ly'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-2 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="bi bi-exclamation-circle"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Cảnh cáo</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['canh_cao'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-2 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon red">
                                        <i class="bi bi-x-circle"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Đình chỉ</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['dinh_chi'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-2 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="bi bi-person-x"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Thôi học</h6>
                                    <h6 class="font-extrabold mb-0">{{ $thongKe['buoc_thoi_hoc'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('giangvien.lop-chu-nhiem.canh-bao-hoc-vu', $lop->id) }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select">
                                        <option value="">-- Tất cả --</option>
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
                                    <label>Mức độ</label>
                                    <select name="muc_do" class="form-select">
                                        <option value="">-- Tất cả --</option>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Loại cảnh báo</label>
                                    <select name="loai_canh_bao" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="diem_thap"
                                            {{ request('loai_canh_bao') == 'diem_thap' ? 'selected' : '' }}>Điểm thấp
                                        </option>
                                        <option value="vang_nhieu"
                                            {{ request('loai_canh_bao') == 'vang_nhieu' ? 'selected' : '' }}>Vắng nhiều
                                        </option>
                                        <option value="no_hoc_phi"
                                            {{ request('loai_canh_bao') == 'no_hoc_phi' ? 'selected' : '' }}>Nợ học phí
                                        </option>
                                        <option value="hoc_ky_lien_tiep"
                                            {{ request('loai_canh_bao') == 'hoc_ky_lien_tiep' ? 'selected' : '' }}>Kém
                                            nhiều
                                            HK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="da_xu_ly" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="0" {{ request('da_xu_ly') == '0' ? 'selected' : '' }}>Chưa xử
                                            lý
                                        </option>
                                        <option value="1" {{ request('da_xu_ly') == '1' ? 'selected' : '' }}>Đã xử lý
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tìm kiếm sinh viên</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Mã SV, Họ tên..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bảng cảnh báo -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách cảnh báo học vụ</h4>
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
                                    <th>Loại cảnh báo</th>
                                    <th>Mức độ</th>
                                    <th>Lý do</th>
                                    <th>Ngày cảnh báo</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($canhBaos as $index => $cb)
                                    <tr>
                                        <td>{{ $canhBaos->firstItem() + $index }}</td>
                                        <td><strong>{{ $cb->sinhVien->ma_sinh_vien }}</strong></td>
                                        <td>{{ $cb->sinhVien->ho_ten }}</td>
                                        <td>
                                            <small>{{ $cb->hocKy->ten_hoc_ky }}<br>{{ $cb->hocKy->nam_hoc }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-info">
                                                {{ $cb->loai_canh_bao_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $cb->muc_do_badge }}">
                                                {{ $cb->muc_do_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($cb->ly_do, 50) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $cb->ngay_canh_bao->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            @if ($cb->da_xu_ly)
                                                <span class="badge bg-light-success">
                                                    <i class="bi bi-check-circle"></i> Đã xử lý
                                                </span>
                                                @if ($cb->ngay_xu_ly)
                                                    <br><small
                                                        class="text-muted">{{ $cb->ngay_xu_ly->format('d/m/Y') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-light-warning">
                                                    <i class="bi bi-clock"></i> Chưa xử lý
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
                                                <p class="mt-2">Không có cảnh báo học vụ nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $canhBaos->links() }}
                    </div>
                </div>
            </div>

            @if ($canhBaos->where('da_xu_ly', false)->count() > 0)
                <!-- Hướng dẫn xử lý -->
                <div class="alert alert-warning mt-4">
                    <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Lưu ý cho GVCN</h5>
                    <p class="mb-0">Có <strong>{{ $canhBaos->where('da_xu_ly', false)->count() }}</strong> sinh viên
                        chưa
                        được xử lý cảnh báo học vụ. Vui lòng:</p>
                    <ul class="mb-0 mt-2">
                        <li>Liên hệ trực tiếp với sinh viên để tìm hiểu nguyên nhân</li>
                        <li>Tư vấn và hỗ trợ sinh viên cải thiện kết quả học tập</li>
                        <li>Báo cáo với phòng Đào tạo về các trường hợp nghiêm trọng</li>
                        <li>Liên hệ phụ huynh nếu cần thiết</li>
                    </ul>
                </div>
            @endif
        </section>
    </div>
@endsection
