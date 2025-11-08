@extends('layouts.layout-giangvien')

@section('title', 'Quản lý buổi học')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Quản lý buổi học</h3>
            <a href="{{ route('giangvien.buoi-hoc.history') }}" class="btn btn-info">
                <i class="bi bi-clock-history"></i> Lịch sử đã dạy
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách buổi học</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('giangvien.buoi-hoc.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="lop_hoc_phan_id" class="form-label">Lớp học phần</label>
                                <select name="lop_hoc_phan_id" id="lop_hoc_phan_id" class="form-select">
                                    <option value="">-- Tất cả lớp --</option>
                                    @foreach($lopHocPhans as $lhp)
                                        <option value="{{ $lhp->id }}" {{ $lopHocPhanId == $lhp->id ? 'selected' : '' }}>
                                            {{ $lhp->ma_lop_hp }} - {{ $lhp->monHoc->ten_mon ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="trang_thai" class="form-label">Trạng thái</label>
                                <select name="trang_thai" id="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="chua_day" {{ $trangThai == 'chua_day' ? 'selected' : '' }}>Chưa dạy</option>
                                    <option value="dang_day" {{ $trangThai == 'dang_day' ? 'selected' : '' }}>Đang dạy</option>
                                    <option value="da_day" {{ $trangThai == 'da_day' ? 'selected' : '' }}>Đã dạy</option>
                                    <option value="huy" {{ $trangThai == 'huy' ? 'selected' : '' }}>Hủy</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tu_ngay" class="form-label">Từ ngày</label>
                                <input type="date" name="tu_ngay" id="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                            </div>
                            <div class="col-md-2">
                                <label for="den_ngay" class="form-label">Đến ngày</label>
                                <input type="date" name="den_ngay" id="den_ngay" class="form-control" value="{{ $denNgay }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                    <a href="{{ route('giangvien.buoi-hoc.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Xóa bộ lọc
                                    </a>
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
                                    <th>Ngày học</th>
                                    <th>Tiết</th>
                                    <th>Giờ</th>
                                    <th>Lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Phòng</th>
                                    <th>Nội dung</th>
                                    <th>Tài liệu</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buoiHocs as $index => $buoiHoc)
                                    <tr>
                                        <td>{{ $buoiHocs->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $buoiHoc->ngay_hoc->format('d/m/Y') }}</strong><br>
                                            <small class="text-muted">{{ $buoiHoc->ngay_hoc->dayName }}</small>
                                        </td>
                                        <td>{{ $buoiHoc->tiet_bat_dau }} - {{ $buoiHoc->tiet_ket_thuc }}</td>
                                        <td>
                                            <small>{{ $buoiHoc->gio_bat_dau }} - {{ $buoiHoc->gio_ket_thuc }}</small>
                                        </td>
                                        <td>{{ $buoiHoc->lopHocPhan->ma_lop_hp }}</td>
                                        <td>{{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</td>
                                        <td>{{ $buoiHoc->phongHoc->ten_phong ?? 'N/A' }}</td>
                                        <td>
                                            @if($buoiHoc->noi_dung_giang_day)
                                                <small>{{ Str::limit($buoiHoc->noi_dung_giang_day, 30) }}</small>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($buoiHoc->tai_lieu_dinh_kem)
                                                <i class="bi bi-file-earmark-check text-success" title="Có tài liệu"></i>
                                            @else
                                                <i class="bi bi-dash text-muted"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($buoiHoc->trang_thai == 'chua_day')
                                                <span class="badge bg-secondary">Chưa dạy</span>
                                            @elseif($buoiHoc->trang_thai == 'dang_day')
                                                <span class="badge bg-warning">Đang dạy</span>
                                            @elseif($buoiHoc->trang_thai == 'da_day')
                                                <span class="badge bg-success">Đã dạy</span>
                                            @elseif($buoiHoc->trang_thai == 'huy')
                                                <span class="badge bg-danger">Hủy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('giangvien.buoi-hoc.edit', $buoiHoc->id) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Cập nhật">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có buổi học nào.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($buoiHocs->hasPages())
                        <div class="mt-3">
                            {{ $buoiHocs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    // Auto submit on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select.form-select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
