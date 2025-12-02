@extends('layouts.layout-giangvien')

@section('title', 'Danh sách lớp giảng dạy')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Danh sách lớp giảng dạy</h3>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lớp học phần được phân công</h4>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <form method="GET" action="{{ route('giangvien.lop-giang-day.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hoc_ky_id">Học kỳ</label>
                                    <select name="hoc_ky_id" id="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        @foreach($hocKys as $hocKy)
                                            <option value="{{ $hocKy->id }}" {{ $hocKyId == $hocKy->id ? 'selected' : '' }}>
                                                {{ $hocKy->ten_hoc_ky }} ({{ $hocKy->nam_hoc }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Tên lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Vai trò</th>
                                    <th>Số SV</th>
                                    <th>Tiến độ điểm</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phanCongs as $index => $phanCong)
                                    <tr>
                                        <td>{{ $phanCongs->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $phanCong->lopHocPhan->ma_lop_hp }}</strong>
                                        </td>
                                        <td>{{ $phanCong->lopHocPhan->ten_lop_hp }}</td>
                                        <td>
                                            {{ $phanCong->lopHocPhan->monHoc->ma_mon ?? '' }} - 
                                            {{ $phanCong->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $phanCong->lopHocPhan->hocKy->ten_hoc_ky }}<br>
                                            <small class="text-muted">{{ $phanCong->lopHocPhan->hocKy->nam_hoc }}</small>
                                        </td>
                                        <td>
                                            @if($phanCong->vai_tro == 'giang_vien_chinh')
                                                <span class="badge bg-primary">GV Chính</span>
                                            @elseif($phanCong->vai_tro == 'giang_vien_phu')
                                                <span class="badge bg-info">GV Phụ</span>
                                            @elseif($phanCong->vai_tro == 'tro_giang')
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $phanCong->vai_tro }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary">
                                                {{ $phanCong->lopHocPhan->so_sinh_vien ?? 0 }}/{{ $phanCong->lopHocPhan->suc_chua }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(isset($phanCong->lopHocPhan->ty_le_nhap_diem))
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $phanCong->lopHocPhan->ty_le_nhap_diem >= 100 ? 'bg-success' : 'bg-warning' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $phanCong->lopHocPhan->ty_le_nhap_diem }}%">
                                                        {{ $phanCong->lopHocPhan->sv_co_diem ?? 0 }}/{{ $phanCong->lopHocPhan->so_sinh_vien ?? 0 }} ({{ $phanCong->lopHocPhan->ty_le_nhap_diem }}%)
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($phanCong->lopHocPhan->trang_thai_lop == 'mo_dang_ky')
                                                <span class="badge bg-warning">Mở đăng ký</span>
                                            @elseif($phanCong->lopHocPhan->trang_thai_lop == 'dang_hoc')
                                                <span class="badge bg-success">Đang học</span>
                                            @elseif($phanCong->lopHocPhan->trang_thai_lop == 'ket_thuc')
                                                <span class="badge bg-secondary">Kết thúc</span>
                                            @elseif($phanCong->lopHocPhan->trang_thai_lop == 'huy')
                                                <span class="badge bg-danger">Hủy</span>
                                            @elseif($phanCong->lopHocPhan->trang_thai_lop == 'da_khoa_diem')
                                                <span class="badge bg-danger">Đã khóa điểm</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $phanCong->lopHocPhan->trang_thai_lop }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                            <a href="{{ route('giangvien.lop-giang-day.show', $phanCong->lop_hoc_phan_id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Xem chi tiết">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                                @if(!isset($phanCong->lopHocPhan->da_ket_thuc) || !$phanCong->lopHocPhan->da_ket_thuc)
                                                    <a href="{{ route('giangvien.nhap-diem.show', $phanCong->lop_hoc_phan_id) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Nhập điểm">
                                                        <i class="bi bi-pencil-square"></i> Nhập điểm
                                                    </a>
                                                @endif
                                                <a href="{{ route('giangvien.lop-giang-day.show', $phanCong->lop_hoc_phan_id) }}#results" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Kết quả học tập">
                                                    <i class="bi bi-trophy"></i> Kết quả
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có lớp học phần nào được phân công.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($phanCongs->hasPages())
                        <div class="mt-3">
                            {{ $phanCongs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    // Auto submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form');
        if (filterForm) {
            const selects = filterForm.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });
        }
    });
</script>
@endpush
