@extends('layouts.layout-daotao')

@section('title', 'Phân công Giảng dạy')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Phân công Giảng dạy</h3>
                    <p class="text-subtitle text-muted">Lớp: {{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->ten_lop_hp }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Phân công GV</li>
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
                        <div class="col-md-6">
                            <p><strong>Môn học:</strong> {{ $lopHocPhan->monHoc->ten_mon }}</p>
                            <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }} -
                                {{ $lopHocPhan->hocKy->nam_hoc }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sĩ số:</strong> {{ $lopHocPhan->so_luong_dang_ky }}/{{ $lopHocPhan->suc_chua }}</p>
                            <p><strong>Hình thức:</strong>
                                @if ($lopHocPhan->hinh_thuc == 'offline')
                                    <span class="badge bg-secondary">Offline</span>
                                @elseif($lopHocPhan->hinh_thuc == 'online')
                                    <span class="badge bg-primary">Online</span>
                                @else
                                    <span class="badge bg-info">Hybrid</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form phân công -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thêm Giảng viên</h5>
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

                    <form action="{{ route('dao-tao.lop-hoc-phan.phan-cong.store', $lopHocPhan->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                                <select name="giang_vien_id"
                                    class="form-select @error('giang_vien_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn giảng viên --</option>
                                    @foreach ($giangViens as $gv)
                                        <option value="{{ $gv->id }}">{{ $gv->ma_giang_vien }} -
                                            {{ $gv->ho_ten }}</option>
                                    @endforeach
                                </select>
                                @error('giang_vien_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                <select name="vai_tro" class="form-select @error('vai_tro') is-invalid @enderror" required>
                                    <option value="giang_vien_chinh">Giảng viên chính</option>
                                    <option value="giang_vien_phu">Giảng viên phụ</option>
                                    <option value="tro_giang">Trợ giảng</option>
                                </select>
                                @error('vai_tro')
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
                                <i class="bi bi-plus-circle"></i> Phân công
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách giảng viên đã phân công -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách Giảng viên</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã GV</th>
                                    <th>Họ tên</th>
                                    <th>Vai trò</th>
                                    <th>Ngày phân công</th>
                                    <th>Người phân công</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhan->lopHocPhanGiangVien as $index => $pc)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $pc->giangVien->ma_giang_vien }}</td>
                                        <td>{{ $pc->giangVien->ho_ten }}</td>
                                        <td>
                                            @if ($pc->vai_tro == 'giang_vien_chinh')
                                                <span class="badge bg-primary">Giảng viên chính</span>
                                            @elseif($pc->vai_tro == 'giang_vien_phu')
                                                <span class="badge bg-info">Giảng viên phụ</span>
                                            @else
                                                <span class="badge bg-secondary">Trợ giảng</span>
                                            @endif
                                        </td>
                                        <td>{{ $pc->ngay_phan_cong ? date('d/m/Y', strtotime($pc->ngay_phan_cong)) : 'N/A' }}
                                        </td>
                                        <td>{{ $pc->nguoiPhanCong->name ?? 'N/A' }}</td>
                                        <td>{{ $pc->phan_cong_giang_day ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('dao-tao.phan-cong.destroy', $pc->id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
                                        <td colspan="8" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có giảng viên nào được phân công</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
