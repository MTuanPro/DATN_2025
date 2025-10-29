@extends('layouts.layout-daotao')

@section('title', 'Cấu hình Đầu điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cấu hình Đầu điểm</h3>
                    <p class="text-subtitle text-muted">Lớp: {{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->ten_lop_hp }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Cấu hình điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin lớp học phần -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Môn học:</strong> {{ $lopHocPhan->monHoc->ten_mon }}</p>
                            <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }} -
                                {{ $lopHocPhan->hocKy->nam_hoc }}</p>
                        </div>
                        <div class="col-md-4 text-end">

                            <h4>Tổng tỷ lệ:
                                <span class="badge {{ $summary['total_percentage'] == 100 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $summary['total_percentage'] }}%
                                </span>
                            </h4>
                            <p class="text-muted">Còn lại: {{ $summary['remaining_percentage'] }}%</p>

                            <h4>Tổng tỷ lệ: <span
                                    class="badge {{ $tongTyLe == 100 ? 'bg-success' : 'bg-warning' }}">{{ $tongTyLe }}%</span>
                            </h4>
                            <p class="text-muted">Còn lại: {{ $tyLeConLai }}%</p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thêm Đầu điểm</h5>
                </div>
                <div class="card-body">
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


                    @if ($summary['remaining_percentage'] > 0)

                    @if ($tyLeConLai > 0)

                        <form action="{{ route('dao-tao.lop-hoc-phan.cau-hinh-diem.store', $lopHocPhan->id) }}"
                            method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tên đầu điểm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_dau_diem"
                                        class="form-control @error('ten_dau_diem') is-invalid @enderror"
                                        placeholder="VD: Chuyên cần, Giữa kỳ..." required>
                                    @error('ten_dau_diem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tỷ lệ % <span class="text-danger">*</span></label>
                                    <input type="number" name="ty_le"
                                        class="form-control @error('ty_le') is-invalid @enderror" step="0.01"
                                        min="0.01" max="{{ $tyLeConLai }}" placeholder="10" required>
                                    @error('ty_le')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Max: {{ $tyLeConLai }}%</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Số cột <span class="text-danger">*</span></label>
                                    <input type="number" name="so_cot"
                                        class="form-control @error('so_cot') is-invalid @enderror" min="1"
                                        max="10" value="1" required>
                                    @error('so_cot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" name="ghi_chu" class="form-control" placeholder="Ghi chú...">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Thêm đầu điểm
                                </button>
                                <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Đã cấu hình đầy đủ 100% tỷ lệ điểm.
                            <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-sm btn-secondary">Quay
                                lại</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Danh sách đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Đầu điểm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên đầu điểm</th>
                                    <th>Tỷ lệ %</th>
                                    <th>Số cột điểm</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhan->cauHinhDauDiem as $index => $cau_hinh)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $cau_hinh->ten_dau_diem }}</strong></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $cau_hinh->ty_le }}%</span>
                                        </td>
                                        <td>{{ $cau_hinh->so_cot }} cột</td>
                                        <td>{{ $cau_hinh->ghi_chu ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('dao-tao.cau-hinh-diem.destroy', $cau_hinh->id) }}"
                                                method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có cấu hình đầu điểm nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($lopHocPhan->cauHinhDauDiem->count() > 0)
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="2" class="text-end">TỔNG:</th>
                                        <th><span

                                                class="badge bg-{{ $summary['total_percentage'] == 100 ? 'success' : 'warning' }}">
                                                {{ $summary['total_percentage'] }}%
                                            </span>

                                        </th>
                                        <th colspan="3">
                                            @if ($summary['is_complete'])

                                                class="badge bg-{{ $tongTyLe == 100 ? 'success' : 'warning' }}">{{ $tongTyLe }}%</span>
                                        </th>
                                        <th colspan="3">
                                            @if ($tongTyLe == 100)

                                                <span class="text-success"><i class="bi bi-check-circle"></i> Đã đủ
                                                    100%</span>
                                            @else
                                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Còn
                                                    thiếu {{ $tyLeConLai }}%</span>
                                            @endif
                                        </th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            // Validation tỷ lệ % realtime
            document.querySelector('input[name="ty_le"]').addEventListener('input', function() {
                const max = parseFloat(this.max);
                const val = parseFloat(this.value);
                if (val > max) {
                    this.value = max;
                    alert('Tỷ lệ % không được vượt quá ' + max + '%');
                }
            });
        </script>
    @endpush
@endsection
