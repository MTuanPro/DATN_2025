@extends('layouts.layout-giangvien')

@section('title', 'Danh sách sinh viên lớp ' . $lop->ma_lop)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách sinh viên lớp {{ $lop->ma_lop }}</h3>
                    <p class="text-subtitle text-muted">{{ $lop->ten_lop }}</p>
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
                            <li class="breadcrumb-item active" aria-current="page">Sinh viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">Danh sách sinh viên</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('giangvien.lop-chu-nhiem.export-excel', $lop->id) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </a>
                                <a href="{{ route('giangvien.lop-chu-nhiem.export-pdf', $lop->id) }}"
                                    class="btn btn-sm btn-danger">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form action="{{ route('giangvien.lop-chu-nhiem.sinh-vien', $lop->id) }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm (Mã SV, Họ tên, Email, SĐT)..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="gioi_tinh" class="form-select">
                                    <option value="">-- Giới tính --</option>
                                    <option value="nam" {{ request('gioi_tinh') == 'nam' ? 'selected' : '' }}>Nam
                                    </option>
                                    <option value="nu" {{ request('gioi_tinh') == 'nu' ? 'selected' : '' }}>Nữ</option>
                                    <option value="khac" {{ request('gioi_tinh') == 'khac' ? 'selected' : '' }}>Khác
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="trang_thai_id" class="form-select">
                                    <option value="">-- Trạng thái --</option>
                                    @foreach ($trangThais as $tt)
                                        <option value="{{ $tt->id }}"
                                            {{ request('trang_thai_id') == $tt->id ? 'selected' : '' }}>
                                            {{ $tt->ten_trang_thai }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group w-100" role="group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('giangvien.lop-chu-nhiem.sinh-vien', $lop->id) }}"
                                        class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Thống kê nhanh -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Tổng số: <strong>{{ $sinhViens->total() }}</strong> sinh viên
                        @if (request('search') || request('gioi_tinh') || request('trang_thai_id'))
                            (đã lọc)
                        @endif
                    </div>

                    <!-- Danh sách sinh viên -->
                    <div class="list-group">
                        @forelse($sinhViens as $index => $sv)
                            <div
                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="badge bg-info me-3" style="min-width: 40px;">
                                        {{ $sinhViens->firstItem() + $index }}</div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center">
                                            <strong class="me-2">{{ $sv->ma_sinh_vien }}</strong>
                                            <span>{{ $sv->ho_ten }}</span>
                                        </div>
                                        <small class="text-muted">{{ explode('@', $sv->email)[1] ?? $sv->email }}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary">Kỳ {{ $sv->ky_hien_tai }}</span>
                                    @if ($sv->trangThaiHocTap)
                                        @if ($sv->trangThaiHocTap->ten_trang_thai == 'Đang học')
                                            <span class="badge bg-success">Đang học</span>
                                        @elseif($sv->trangThaiHocTap->ten_trang_thai == 'Bảo lưu')
                                            <span class="badge bg-warning">Bảo lưu</span>
                                        @elseif($sv->trangThaiHocTap->ten_trang_thai == 'Thôi học')
                                            <span class="badge bg-danger">Thôi học</span>
                                        @else
                                            <span class="badge bg-info">{{ $sv->trangThaiHocTap->ten_trang_thai }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#modalChiTiet{{ $sv->id }}" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Chi tiết -->
                            <div class="modal fade" id="modalChiTiet{{ $sv->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chi tiết sinh viên: {{ $sv->ho_ten }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Thông tin cá nhân</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td width="40%"><strong>Mã SV:</strong></td>
                                                            <td>{{ $sv->ma_sinh_vien }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Họ tên:</strong></td>
                                                            <td>{{ $sv->ho_ten }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Ngày sinh:</strong></td>
                                                            <td>{{ $sv->ngay_sinh ? $sv->ngay_sinh->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Giới tính:</strong></td>
                                                            <td>{{ ucfirst($sv->gioi_tinh) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Email:</strong></td>
                                                            <td>{{ $sv->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>SĐT:</strong></td>
                                                            <td>{{ $sv->so_dien_thoai }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>CCCD:</strong></td>
                                                            <td>{{ $sv->can_cuoc_cong_dan }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Thông tin học tập</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td width="40%"><strong>Lớp:</strong></td>
                                                            <td>{{ $lop->ma_lop }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Khóa học:</strong></td>
                                                            <td>{{ $sv->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Ngành:</strong></td>
                                                            <td>{{ $sv->nganh->ten_nganh ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Chuyên ngành:</strong></td>
                                                            <td>{{ $sv->chuyenNganh->ten_chuyen_nganh ?? 'Chưa chọn' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Kỳ hiện tại:</strong></td>
                                                            <td>Kỳ {{ $sv->ky_hien_tai }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Trạng thái:</strong></td>
                                                            <td>{{ $sv->trangThaiHocTap->ten_trang_thai ?? 'N/A' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="text-primary">Địa chỉ</h6>
                                            <p>
                                                {{ implode(', ', array_filter([$sv->so_nha_duong, $sv->phuong_xa, $sv->quan_huyen, $sv->tinh_thanh])) ?:
                                                    'Chưa cập nhật' }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Không tìm thấy sinh viên nào</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Hiển thị {{ $sinhViens->firstItem() ?? 0 }} - {{ $sinhViens->lastItem() ?? 0 }}
                            trong tổng số {{ $sinhViens->total() }} sinh viên
                        </div>
                        <div>
                            {{ $sinhViens->links() }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('giangvien.lop-chu-nhiem.show', $lop->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại thống kê lớp
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
