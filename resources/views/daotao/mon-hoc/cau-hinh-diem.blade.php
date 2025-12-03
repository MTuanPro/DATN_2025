@extends('layouts.layout-daotao')

@section('title', 'Cấu hình Đầu điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cấu hình Đầu điểm</h3>
                    <p class="text-subtitle text-muted">Môn: {{ $monHoc->ma_mon }} - {{ $monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.mon-hoc.index') }}">Môn học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cấu hình điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thông tin môn học -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Mã môn:</strong> {{ $monHoc->ma_mon }}</p>
                            <p><strong>Tên môn:</strong> {{ $monHoc->ten_mon }}</p>
                            <p><strong>Số tín chỉ:</strong> {{ $monHoc->so_tin_chi }} TC</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h4>Tổng tỷ lệ:
                                <span class="badge {{ $tongTyLe == 100 ? 'bg-success' : 'bg-warning' }}">
                                    {{ number_format($tongTyLe, 1) }}%
                                </span>
                            </h4>
                            <p class="text-muted">Còn lại: {{ number_format($tyLeConLai, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thêm Đầu điểm Mặc định</h5>
                    <p class="text-muted mb-0">
                        <small><i class="bi bi-info-circle"></i> 
                            Cấu hình này sẽ được áp dụng cho tất cả các lớp học phần của môn học này khi tạo mới.
                        </small>
                    </p>
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

                    @if ($tyLeConLai <= 0)
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Lưu ý:</strong> Đã đạt tỷ lệ tối đa 100%. Nếu muốn thêm đầu điểm mới, vui lòng xóa hoặc giảm tỷ lệ của đầu điểm hiện có trước.
                        </div>
                    @endif

                    <form action="{{ route('dao-tao.mon-hoc.cau-hinh-diem.store', $monHoc->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Tên đầu điểm <span class="text-danger">*</span></label>
                                <input type="text" name="ten_dau_diem"
                                    class="form-control @error('ten_dau_diem') is-invalid @enderror"
                                    placeholder="VD: Chuyên cần, Giữa kỳ..." value="{{ old('ten_dau_diem') }}" required>
                                @error('ten_dau_diem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tỷ lệ % <span class="text-danger">*</span></label>
                                <input type="number" name="ty_le"
                                    class="form-control @error('ty_le') is-invalid @enderror" step="0.01"
                                    min="0.01" max="{{ $tyLeConLai > 0 ? 100 : $tyLeConLai + 100 }}"
                                    placeholder="10" value="{{ old('ty_le') }}" required>
                                @error('ty_le')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($tyLeConLai > 0)
                                    <small class="text-muted">Tỷ lệ còn lại: {{ number_format($tyLeConLai, 1) }}%</small>
                                @else
                                    <small class="text-danger">Tỷ lệ đã đạt 100%. Vui lòng xóa hoặc sửa đầu điểm hiện có.</small>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Số cột <span class="text-danger">*</span></label>
                                <input type="number" name="so_cot"
                                    class="form-control @error('so_cot') is-invalid @enderror" min="1"
                                    max="20" placeholder="1" value="{{ old('so_cot', 1) }}" required>
                                @error('so_cot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100" {{ $tyLeConLai <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-plus-circle"></i> Thêm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách đầu điểm -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Đầu điểm Mặc định</h5>
                </div>
                <div class="card-body">
                    @if ($cauHinhs->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Chưa có cấu hình đầu điểm nào. Vui lòng thêm cấu hình đầu điểm mặc định.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên đầu điểm</th>
                                        <th>Tỷ lệ %</th>
                                        <th>Số cột</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cauHinhs as $index => $cauHinh)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $cauHinh->ten_dau_diem }}</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ number_format($cauHinh->ty_le, 1) }}%</span>
                                            </td>
                                            <td>{{ $cauHinh->so_cot }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $cauHinh->id }}">
                                                    <i class="bi bi-pencil"></i> Sửa
                                                </button>
                                                <form action="{{ route('dao-tao.mon-hoc.cau-hinh-diem.destroy', [$monHoc->id, $cauHinh->id]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa đầu điểm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal sửa -->
                                        <div class="modal fade" id="editModal{{ $cauHinh->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Sửa Đầu điểm</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('dao-tao.mon-hoc.cau-hinh-diem.update', [$monHoc->id, $cauHinh->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên đầu điểm <span class="text-danger">*</span></label>
                                                                <input type="text" name="ten_dau_diem" class="form-control"
                                                                    value="{{ $cauHinh->ten_dau_diem }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Tỷ lệ % <span class="text-danger">*</span></label>
                                                                <input type="number" name="ty_le" class="form-control" step="0.01"
                                                                    min="0.01" max="100" value="{{ $cauHinh->ty_le }}" required>
                                                                <small class="text-muted">Tỷ lệ còn lại: {{ number_format($tyLeConLai + $cauHinh->ty_le, 1) }}%</small>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Số cột <span class="text-danger">*</span></label>
                                                                <input type="number" name="so_cot" class="form-control" min="1"
                                                                    max="20" value="{{ $cauHinh->so_cot }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-end mt-3">
                <a href="{{ route('dao-tao.mon-hoc.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </section>
    </div>
@endsection

