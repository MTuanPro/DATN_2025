@extends('layouts.layout-daotao')

@section('title', 'Chi tiết Lớp học phần - ' . $lopHocPhan->ma_lop_hp)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chi tiết Lớp học phần</h3>
                    <p class="text-subtitle text-muted">
                        {{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->ten_lop_hp }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thông tin lớp học phần --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Thông tin lớp học phần
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã lớp HP:</strong> {{ $lopHocPhan->ma_lop_hp }}</p>
                            <p><strong>Tên lớp HP:</strong> {{ $lopHocPhan->ten_lop_hp }}</p>
                            <p><strong>Môn học:</strong> {{ $lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</p>
                            <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sĩ số:</strong> 
                                <span class="badge bg-info">
                                    {{ $lopHocPhan->so_luong_thuc_te ?? $lopHocPhan->so_luong_dang_ky }}/{{ $lopHocPhan->suc_chua }}
                                </span>
                            </p>
                            <p><strong>Hình thức:</strong> 
                                @if ($lopHocPhan->hinh_thuc == 'offline')
                                    <span class="badge bg-secondary">Offline</span>
                                @elseif($lopHocPhan->hinh_thuc == 'online')
                                    <span class="badge bg-primary">Online</span>
                                @else
                                    <span class="badge bg-info">Hybrid</span>
                                @endif
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                @if ($lopHocPhan->trang_thai_lop == 'mo_dang_ky')
                                    <span class="badge bg-success">Mở đăng ký</span>
                                @elseif($lopHocPhan->trang_thai_lop == 'dang_hoc')
                                    <span class="badge bg-primary">Đang học</span>
                                @elseif($lopHocPhan->trang_thai_lop == 'ket_thuc')
                                    <span class="badge bg-secondary">Kết thúc</span>
                                @else
                                    <span class="badge bg-danger">Hủy</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Lịch theo Phòng học --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> Lịch theo Phòng học
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lichTheoPhong->count() > 0)
                    @foreach ($lichTheoPhong as $phongGroup)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-door-open"></i> Phòng: <strong>{{ $phongGroup['phong'] }}</strong>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày học</th>
                                            <th>Thứ</th>
                                            <th>Ca</th>
                                            <th>Giờ</th>
                                            <th>Tiết</th>
                                            <th>Giảng viên</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($phongGroup['lich_hocs'] as $index => $lich)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ \Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($lich->caHoc)
                                                        <span class="badge bg-primary">{{ $lich->caHoc->ten_ca }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        Tiết {{ $lich->tiet_bat_dau }}
                                                        @if ($lich->tiet_ket_thuc != $lich->tiet_bat_dau)
                                                            - {{ $lich->tiet_ket_thuc }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $lich->giangVien->ho_ten ?? 'Chưa phân công' }}</td>
                                                <td>
                                                    @if ($lich->trang_thai == 'chua_day')
                                                        <span class="badge bg-secondary">Chưa dạy</span>
                                                    @elseif ($lich->trang_thai == 'dang_day')
                                                        <span class="badge bg-info">Đang dạy</span>
                                                    @elseif ($lich->trang_thai == 'da_day')
                                                        <span class="badge bg-success">Đã dạy</span>
                                                    @else
                                                        <span class="badge bg-danger">Hủy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form
                                                        action="{{ route('dao-tao.lich-chi-tiet.destroy', $lich->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa buổi học này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Chưa có lịch học theo phòng</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lịch theo Giảng viên --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge"></i> Lịch theo Giảng viên
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lichTheoGiangVien->count() > 0)
                    @foreach ($lichTheoGiangVien as $gvGroup)
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="bi bi-person"></i> Giảng viên: <strong>{{ $gvGroup['giang_vien'] }}</strong>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Ngày học</th>
                                            <th>Thứ</th>
                                            <th>Ca</th>
                                            <th>Giờ</th>
                                            <th>Tiết</th>
                                            <th>Phòng</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($gvGroup['lich_hocs'] as $index => $lich)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ \Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($lich->caHoc)
                                                        <span class="badge bg-primary">{{ $lich->caHoc->ten_ca }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        Tiết {{ $lich->tiet_bat_dau }}
                                                        @if ($lich->tiet_ket_thuc != $lich->tiet_bat_dau)
                                                            - {{ $lich->tiet_ket_thuc }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $lich->phongHoc->ten_phong ?? 'Chưa phân phòng' }}</td>
                                                <td>
                                                    @if ($lich->trang_thai == 'chua_day')
                                                        <span class="badge bg-secondary">Chưa dạy</span>
                                                    @elseif ($lich->trang_thai == 'dang_day')
                                                        <span class="badge bg-info">Đang dạy</span>
                                                    @elseif ($lich->trang_thai == 'da_day')
                                                        <span class="badge bg-success">Đã dạy</span>
                                                    @else
                                                        <span class="badge bg-danger">Hủy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form
                                                        action="{{ route('dao-tao.lich-chi-tiet.destroy', $lich->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa buổi học này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">Chưa có lịch học theo giảng viên</p>
                    </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

