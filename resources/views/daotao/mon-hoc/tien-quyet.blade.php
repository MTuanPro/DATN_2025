@extends('layouts.layout-daotao')

@section('title', 'Quản lý Môn tiên quyết')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Môn tiên quyết</h3>
                    <p class="text-subtitle text-muted">{{ $monHoc->ma_mon }} - {{ $monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('daotao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.mon-hoc.index') }}">Môn học</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Môn tiên quyết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Thêm môn tiên quyết -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Thêm Môn tiên quyết</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('dao-tao.mon-hoc.tien-quyet.store', $monHoc->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mon_tien_quyet_id" class="form-label">Môn tiên quyết <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('mon_tien_quyet_id') is-invalid @enderror"
                                        id="mon_tien_quyet_id" name="mon_tien_quyet_id" required>
                                        <option value="">-- Chọn môn tiên quyết --</option>
                                        @foreach ($danhSachMonHoc as $mon)
                                            <option value="{{ $mon->id }}"
                                                {{ old('mon_tien_quyet_id') == $mon->id ? 'selected' : '' }}>
                                                {{ $mon->ma_mon }} - {{ $mon->ten_mon }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mon_tien_quyet_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="loai_tien_quyet" class="form-label">Loại tiên quyết <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('loai_tien_quyet') is-invalid @enderror"
                                        id="loai_tien_quyet" name="loai_tien_quyet" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="bat_buoc"
                                            {{ old('loai_tien_quyet') == 'bat_buoc' ? 'selected' : '' }}>Bắt buộc</option>
                                        <option value="khuyen_nghi"
                                            {{ old('loai_tien_quyet') == 'khuyen_nghi' ? 'selected' : '' }}>Khuyến nghị
                                        </option>
                                    </select>
                                    @error('loai_tien_quyet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="dieu_kien_qua_mon" class="form-label">Điều kiện qua môn <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('dieu_kien_qua_mon') is-invalid @enderror"
                                        id="dieu_kien_qua_mon" name="dieu_kien_qua_mon" required>
                                        <option value="1" {{ old('dieu_kien_qua_mon') == '1' ? 'selected' : '' }}>Phải
                                            qua môn</option>
                                        <option value="0" {{ old('dieu_kien_qua_mon') == '0' ? 'selected' : '' }}>
                                            Không yêu cầu</option>
                                    </select>
                                    @error('dieu_kien_qua_mon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle"></i> Thêm
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú</label>
                                    <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu" rows="2"
                                        placeholder="Ghi chú về môn tiên quyết...">{{ old('ghi_chu') }}</textarea>
                                    @error('ghi_chu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách môn tiên quyết hiện tại -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Danh sách Môn tiên quyết của {{ $monHoc->ma_mon }}</h5>
                </div>
                <div class="card-body">
                    @if ($monHoc->monTienQuyet->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn</th>
                                        <th>Tín chỉ</th>
                                        <th>Loại tiên quyết</th>
                                        <th>Điều kiện</th>
                                        <th>Ghi chú</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monHoc->monTienQuyet as $tienQuyet)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $tienQuyet->ma_mon }}</strong></td>
                                            <td>{{ $tienQuyet->ten_mon }}</td>
                                            <td><span class="badge bg-primary">{{ $tienQuyet->so_tin_chi }} TC</span></td>
                                            <td>
                                                @if ($tienQuyet->pivot->loai_tien_quyet == 'bat_buoc')
                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                @else
                                                    <span class="badge bg-warning">Khuyến nghị</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($tienQuyet->pivot->dieu_kien_qua_mon)
                                                    <span class="badge bg-success">Phải qua môn</span>
                                                @else
                                                    <span class="badge bg-secondary">Không yêu cầu</span>
                                                @endif
                                            </td>
                                            <td>{{ $tienQuyet->pivot->ghi_chu ?? '-' }}</td>
                                            <td>
                                                <form
                                                    action="{{ route('dao-tao.mon-hoc.tien-quyet.destroy', [$monHoc->id, $tienQuyet->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa môn tiên quyết này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có môn tiên quyết nào cho môn học này.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Danh sách các môn cần môn này làm tiên quyết -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">Các môn học cần {{ $monHoc->ma_mon }} làm tiên quyết</h5>
                </div>
                <div class="card-body">
                    @if ($monHoc->monCanMonNay->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã môn</th>
                                        <th>Tên môn</th>
                                        <th>Tín chỉ</th>
                                        <th>Loại tiên quyết</th>
                                        <th>Điều kiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monHoc->monCanMonNay as $mon)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $mon->ma_mon }}</strong></td>
                                            <td>{{ $mon->ten_mon }}</td>
                                            <td><span class="badge bg-primary">{{ $mon->so_tin_chi }} TC</span></td>
                                            <td>
                                                @if ($mon->pivot->loai_tien_quyet == 'bat_buoc')
                                                    <span class="badge bg-danger">Bắt buộc</span>
                                                @else
                                                    <span class="badge bg-warning">Khuyến nghị</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($mon->pivot->dieu_kien_qua_mon)
                                                    <span class="badge bg-success">Phải qua môn</span>
                                                @else
                                                    <span class="badge bg-secondary">Không yêu cầu</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Chưa có môn học nào cần môn này làm tiên quyết.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Nút quay lại -->
            <div class="mb-3">
                <a href="{{ route('dao-tao.mon-hoc.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </section>
    </div>
@endsection
