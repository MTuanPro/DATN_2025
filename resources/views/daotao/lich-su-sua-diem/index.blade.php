@extends('layouts.layout-daotao')

@section('title', 'Lịch sử sửa điểm')

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
                            <li class="breadcrumb-item active">Lịch sử sửa điểm</li>
                        </ol>
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
                <form method="GET" action="{{ route('dao-tao.lich-su-sua-diem.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Học kỳ</label>
                            <select name="hoc_ky_id" class="form-select" id="hocKySelect">
                                <option value="">-- Tất cả --</option>
                                @foreach($hocKys as $hk)
                                    <option value="{{ $hk->id }}" {{ request('hoc_ky_id') == $hk->id ? 'selected' : '' }}>
                                        {{ $hk->ten_hoc_ky }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Lớp học phần</label>
                            <select name="lop_hoc_phan_id" class="form-select" id="lopHocPhanSelect">
                                <option value="">-- Tất cả --</option>
                                @foreach($lopHocPhans as $lhp)
                                    <option value="{{ $lhp->id }}" {{ request('lop_hoc_phan_id') == $lhp->id ? 'selected' : '' }}>
                                        {{ $lhp->ma_lop }} - {{ $lhp->monHoc->ten_mon }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Loại thao tác</label>
                            <select name="loai_thao_tac" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="them" {{ request('loai_thao_tac') == 'them' ? 'selected' : '' }}>Thêm điểm</option>
                                <option value="sua" {{ request('loai_thao_tac') == 'sua' ? 'selected' : '' }}>Sửa điểm</option>
                                <option value="xoa" {{ request('loai_thao_tac') == 'xoa' ? 'selected' : '' }}>Xóa điểm</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control" value="{{ request('tu_ngay') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="{{ request('den_ngay') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tìm kiếm sinh viên</label>
                            <input type="text" name="keyword" class="form-control" 
                                   placeholder="Nhập mã sinh viên hoặc tên sinh viên..." 
                                   value="{{ request('keyword') }}">
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('dao-tao.lich-su-sua-diem.index') }}" class="btn btn-secondary">
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
                    <i class="bi bi-list-ul"></i> Danh sách lịch sử ({{ $lichSu->total() }} bản ghi)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th width="10%">Thời gian</th>
                                <th width="12%">Sinh viên</th>
                                <th width="15%">Môn học</th>
                                <th width="12%">Đầu điểm</th>
                                <th width="8%" class="text-center">Điểm cũ</th>
                                <th width="8%" class="text-center">Điểm mới</th>
                                <th width="10%" class="text-center">Loại</th>
                                <th width="12%">Người sửa</th>
                                <th width="8%">Lý do</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichSu as $index => $ls)
                                <tr>
                                    <td class="text-center">{{ $lichSu->firstItem() + $index }}</td>
                                    <td>
                                        <small>{{ $ls->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ls->lopHocPhanSinhVien->sinhVien->ma_sinh_vien }}</strong><br>
                                        <small>{{ $ls->lopHocPhanSinhVien->sinhVien->ho_ten }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ls->lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon }}</strong><br>
                                        <small>{{ $ls->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</small><br>
                                        <small class="text-muted">Lớp: {{ $ls->lopHocPhanSinhVien->lopHocPhan->ma_lop }}</small>
                                    </td>
                                    <td>
                                        {{ $ls->cauHinh->ten_dau_diem }}
                                        @if($ls->cauHinh->so_cot > 1)
                                            <br><small class="text-muted">Cột {{ $ls->cot_diem }}</small>
                                        @endif
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
                                            <span class="text-muted">Hệ thống</span>
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
                                        <p class="mt-2">Không có lịch sử sửa điểm</p>
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

@push('scripts')
<script>
    // Khi chọn học kỳ, load lại danh sách lớp học phần
    document.getElementById('hocKySelect').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>
@endpush
