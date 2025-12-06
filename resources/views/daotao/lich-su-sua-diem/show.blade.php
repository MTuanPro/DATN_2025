@extends('layouts.layout-daotao')

@section('title', 'Lịch sử sửa điểm - ' . $lopHocPhan->ma_lop)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="bi bi-clock-history"></i> Lịch sử sửa điểm
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lich-su-sua-diem.index') }}">Lịch sử sửa điểm</a></li>
                            <li class="breadcrumb-item active">{{ $lopHocPhan->ma_lop }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin lớp học phần -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Thông tin lớp học phần
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Mã lớp:</th>
                                <td><strong class="text-primary">{{ $lopHocPhan->ma_lop }}</strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td>
                                    {{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}
                                    <br><small class="text-muted">{{ $lopHocPhan->monHoc->so_tin_chi }} tín chỉ</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Học kỳ:</th>
                                <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Giảng viên:</th>
                                <td>
                                    @if($lopHocPhan->giangVien)
                                        {{ $lopHocPhan->giangVien->ho_ten }}
                                        <br><small class="text-muted">{{ $lopHocPhan->giangVien->ma_giang_vien }}</small>
                                    @else
                                        <span class="text-muted">Chưa phân công</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if($lopHocPhan->trang_thai_lop == 'chua_mo')
                                        <span class="badge bg-secondary">Chưa mở</span>
                                    @elseif($lopHocPhan->trang_thai_lop == 'dang_mo')
                                        <span class="badge bg-info">Đang mở</span>
                                    @elseif($lopHocPhan->trang_thai_lop == 'da_dong')
                                        <span class="badge bg-warning">Đã đóng</span>
                                    @elseif($lopHocPhan->trang_thai_lop == 'da_gui_diem')
                                        <span class="badge bg-primary">Đã gửi điểm</span>
                                    @elseif($lopHocPhan->trang_thai_lop == 'da_duyet_diem')
                                        <span class="badge bg-success">Đã duyệt điểm</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tổng số lần sửa:</th>
                                <td><strong class="text-danger">{{ $lichSu->total() }}</strong> lần</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-funnel"></i> Bộ lọc
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('dao-tao.lich-su-sua-diem.show', $lopHocPhan->id) }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Loại thao tác</label>
                            <select name="loai_thao_tac" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="them" {{ request('loai_thao_tac') == 'them' ? 'selected' : '' }}>Thêm điểm</option>
                                <option value="sua" {{ request('loai_thao_tac') == 'sua' ? 'selected' : '' }}>Sửa điểm</option>
                                <option value="xoa" {{ request('loai_thao_tac') == 'xoa' ? 'selected' : '' }}>Xóa điểm</option>
                            </select>
                        </div>

                        <div class="col-md-8 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('dao-tao.lich-su-sua-diem.show', $lopHocPhan->id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách lịch sử -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul"></i> Chi tiết lịch sử ({{ $lichSu->total() }} bản ghi)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th width="12%">Thời gian</th>
                                <th width="15%">Sinh viên</th>
                                <th width="15%">Đầu điểm</th>
                                <th width="8%" class="text-center">Điểm cũ</th>
                                <th width="8%" class="text-center">Điểm mới</th>
                                <th width="10%" class="text-center">Thay đổi</th>
                                <th width="10%" class="text-center">Loại</th>
                                <th width="12%">Người sửa</th>
                                <th>Lý do</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichSu as $index => $ls)
                                <tr>
                                    <td class="text-center">{{ $lichSu->firstItem() + $index }}</td>
                                    <td>
                                        {{ $ls->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $ls->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ls->lopHocPhanSinhVien->sinhVien->ma_sinh_vien }}</strong><br>
                                        <small>{{ $ls->lopHocPhanSinhVien->sinhVien->ho_ten }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ls->cauHinh->ten_dau_diem }}</strong>
                                        @if($ls->cauHinh->so_cot > 1)
                                            <br><small class="text-muted">Cột {{ $ls->cot_diem }}</small>
                                        @endif
                                        <br><small class="text-info">Tỷ lệ: {{ $ls->cauHinh->ty_le }}%</small>
                                    </td>
                                    <td class="text-center">
                                        @if($ls->diem_cu !== null)
                                            <strong class="text-danger">{{ number_format($ls->diem_cu, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ls->diem_moi !== null)
                                            <strong class="text-success">{{ number_format($ls->diem_moi, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ls->diem_cu !== null && $ls->diem_moi !== null)
                                            @php
                                                $chenhLech = $ls->diem_moi - $ls->diem_cu;
                                            @endphp
                                            @if($chenhLech > 0)
                                                <span class="badge bg-success">+{{ number_format($chenhLech, 2) }}</span>
                                            @elseif($chenhLech < 0)
                                                <span class="badge bg-danger">{{ number_format($chenhLech, 2) }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ls->loai_thao_tac == 'them')
                                            <span class="badge bg-success">
                                                <i class="bi bi-plus-circle"></i> Thêm
                                            </span>
                                        @elseif($ls->loai_thao_tac == 'sua')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pencil"></i> Sửa
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-trash"></i> Xóa
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ls->nguoiSua && $ls->nguoiSua->giangVien)
                                            <strong>{{ $ls->nguoiSua->giangVien->ma_giang_vien }}</strong><br>
                                            <small>{{ $ls->nguoiSua->giangVien->ho_ten }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ls->ly_do)
                                            <small>{{ $ls->ly_do }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Chưa có lịch sử sửa điểm nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($lichSu->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lichSu->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
