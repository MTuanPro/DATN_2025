@extends('layouts.layout-sinhvien')

@section('title', 'Lớp học phần')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lớp học phần</h3>
                    <p class="text-subtitle text-muted">Danh sách lớp học phần đã đăng ký</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lớp học phần</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Thống kê --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tổng lớp</h6>
                                    <h2 class="mb-0 text-primary">{{ $thongKe['tong_lop'] }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-primary">
                                    <i class="bi bi-book text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Đang học</h6>
                                    <h2 class="mb-0 text-success">{{ $thongKe['dang_hoc'] }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-success">
                                    <i class="bi bi-check-circle text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Đã hoàn thành</h6>
                                    <h2 class="mb-0 text-info">{{ $thongKe['da_hoan_thanh'] }}</h2>
                                </div>
                                <div class="avatar avatar-xl bg-info">
                                    <i class="bi bi-trophy text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bộ lọc --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sinh-vien.lop-hoc-phan.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="hoc_ky_id" class="form-label">Học kỳ</label>
                            <select name="hoc_ky_id" id="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả học kỳ --</option>
                                @foreach($hocKys as $hocKy)
                                    <option value="{{ $hocKy->id }}" 
                                        {{ $selectedHocKy && $selectedHocKy->id == $hocKy->id ? 'selected' : '' }}>
                                        {{ $hocKy->ten_hoc_ky }} - {{ $hocKy->nam_hoc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="trang_thai" class="form-label">Trạng thái</label>
                            <select name="trang_thai" id="trang_thai" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="da_xep_lop" {{ request('trang_thai') == 'da_xep_lop' ? 'selected' : '' }}>Đã xếp lớp</option>
                                <option value="dang_hoc" {{ request('trang_thai') == 'dang_hoc' ? 'selected' : '' }}>Đang học</option>
                                <option value="hoc_lai" {{ request('trang_thai') == 'hoc_lai' ? 'selected' : '' }}>Học lại</option>
                                <option value="da_hoan_thanh" {{ request('trang_thai') == 'da_hoan_thanh' ? 'selected' : '' }}>Đã hoàn thành</option>
                                <option value="bo_hoc" {{ request('trang_thai') == 'bo_hoc' ? 'selected' : '' }}>Bỏ học</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách lớp học phần --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Giảng viên</th>
                                    <th>Tín chỉ</th>
                                    <th>Trạng thái</th>
                                    <th>Điểm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhanSinhViens as $index => $lhpsv)
                                    <tr>
                                        <td>{{ $lopHocPhanSinhViens->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $lhpsv->lopHocPhan->ma_lop_hp }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $lhpsv->lopHocPhan->monHoc->ten_mon }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $lhpsv->lopHocPhan->monHoc->ma_mon }}</small>
                                        </td>
                                        <td>
                                            {{ $lhpsv->lopHocPhan->hocKy->ten_hoc_ky }}
                                            <br>
                                            <small class="text-muted">{{ $lhpsv->lopHocPhan->hocKy->nam_hoc }}</small>
                                        </td>
                                        <td>
                                            @if($lhpsv->lopHocPhan->giangVienChinh && $lhpsv->lopHocPhan->giangVienChinh->giangVien)
                                                {{ $lhpsv->lopHocPhan->giangVienChinh->giangVien->ho_ten }}
                                            @else
                                                <span class="text-muted">Chưa phân công</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $lhpsv->lopHocPhan->monHoc->so_tin_chi }} TC
                                            </span>
                                        </td>
                                        <td>
                                            @if($lhpsv->trang_thai == 'da_xep_lop')
                                                <span class="badge bg-info">Đã xếp lớp</span>
                                            @elseif($lhpsv->trang_thai == 'dang_hoc')
                                                <span class="badge bg-success">Đang học</span>
                                            @elseif($lhpsv->trang_thai == 'hoc_lai')
                                                <span class="badge bg-warning">Học lại</span>
                                            @elseif($lhpsv->trang_thai == 'da_hoan_thanh')
                                                <span class="badge bg-primary">Đã hoàn thành</span>
                                            @elseif($lhpsv->trang_thai == 'bo_hoc')
                                                <span class="badge bg-danger">Bỏ học</span>
                                            @elseif($lhpsv->trang_thai == 'huy_dang_ky')
                                                <span class="badge bg-secondary">Hủy đăng ký</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $lhpsv->trang_thai }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lhpsv->ketQuaHocTap)
                                                <div>
                                                    <strong class="text-primary">{{ $lhpsv->ketQuaHocTap->diem_he_10 }}</strong>
                                                    <span class="text-muted">/10</span>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $lhpsv->ketQuaHocTap->diem_chu }}
                                                    ({{ $lhpsv->ketQuaHocTap->diem_he_4 }}/4)
                                                </small>
                                            @else
                                                <span class="text-muted">Chưa có điểm</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('sinh-vien.lop-hoc-phan.show', $lhpsv->id) }}" 
                                                   class="btn btn-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('sinh-vien.diem.show', $lhpsv->lopHocPhan->id) }}" 
                                                   class="btn btn-primary" title="Xem điểm">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">Chưa có lớp học phần nào</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $lopHocPhanSinhViens->firstItem() ?? 0 }} - {{ $lopHocPhanSinhViens->lastItem() ?? 0 }}
                                trong tổng số {{ $lopHocPhanSinhViens->total() }} lớp học phần
                            </small>
                        </div>
                        <div>
                            {{ $lopHocPhanSinhViens->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

