@extends('layouts.layout-daotao')

@section('title', 'Lịch sử sửa điểm')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0">
                <i class="fas fa-history me-2"></i>Lịch sử sửa điểm
            </h4>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('dao-tao.lich-su-sua-diem.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm theo sinh viên, môn học, cột điểm..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('dao-tao.lich-su-sua-diem.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            @if($lichSuSuaDiem->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="12%">Sinh viên</th>
                                <th width="15%">Môn học</th>
                                <th width="12%">Học kỳ</th>
                                <th width="10%">Cột điểm</th>
                                <th width="8%">Điểm cũ</th>
                                <th width="8%">Điểm mới</th>
                                <th width="10%">Thao tác</th>
                                <th width="12%">Người sửa</th>
                                <th width="10%">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lichSuSuaDiem as $index => $lichSu)
                                <tr>
                                    <td class="text-center">{{ $lichSuSuaDiem->firstItem() + $index }}</td>
                                    <td>
                                        @if($lichSu->lopHocPhanSinhVien && $lichSu->lopHocPhanSinhVien->sinhVien)
                                            <div class="fw-bold">{{ $lichSu->lopHocPhanSinhVien->sinhVien->ma_sinh_vien }}</div>
                                            <small class="text-muted">{{ $lichSu->lopHocPhanSinhVien->sinhVien->ho_ten }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lichSu->lopHocPhanSinhVien && $lichSu->lopHocPhanSinhVien->lopHocPhan && $lichSu->lopHocPhanSinhVien->lopHocPhan->monHoc)
                                            <div class="fw-bold">{{ $lichSu->lopHocPhanSinhVien->lopHocPhan->monHoc->ma_mon }}</div>
                                            <small class="text-muted">{{ $lichSu->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lichSu->lopHocPhanSinhVien && $lichSu->lopHocPhanSinhVien->lopHocPhan && $lichSu->lopHocPhanSinhVien->lopHocPhan->hocKy)
                                            {{ $lichSu->lopHocPhanSinhVien->lopHocPhan->hocKy->ten_hoc_ky }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $lichSu->cot_diem }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($lichSu->diem_cu !== null)
                                            <span class="badge bg-secondary">{{ number_format($lichSu->diem_cu, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($lichSu->diem_moi !== null)
                                            <span class="badge bg-success">{{ number_format($lichSu->diem_moi, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($lichSu->loai_thao_tac === 'them')
                                            <span class="badge bg-success"><i class="fas fa-plus me-1"></i>Thêm</span>
                                        @elseif($lichSu->loai_thao_tac === 'sua')
                                            <span class="badge bg-warning"><i class="fas fa-edit me-1"></i>Sửa</span>
                                        @elseif($lichSu->loai_thao_tac === 'xoa')
                                            <span class="badge bg-danger"><i class="fas fa-trash me-1"></i>Xóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lichSu->nguoiSua)
                                            <div class="fw-bold">{{ $lichSu->nguoiSua->ma_giang_vien }}</div>
                                            <small class="text-muted">{{ $lichSu->nguoiSua->ho_ten }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $lichSu->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $lichSuSuaDiem->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có lịch sử sửa điểm nào.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
