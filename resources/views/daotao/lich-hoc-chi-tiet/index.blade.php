@extends('layouts.layout-daotao')

@section('title', 'Lịch học chi tiết')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch học chi tiết</h3>
                    <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.lop-hoc-phan.index') }}">Lớp học phần</a>
                            </li>
                            <li class="breadcrumb-item active">Lịch chi tiết</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách lịch học chi tiết</h5>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#generateModal">
                                <i class="bi bi-calendar-check"></i> Tạo tự động
                            </button>
                            <a href="{{ route('dao-tao.lop-hoc-phan.lich-chi-tiet.create', $lopHocPhan) }}"
                                class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm buổi học
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày học</th>
                                    <th>Ca</th>
                                    <th>Giờ</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lichHocs as $lichHoc)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($lichHoc->ngay_hoc)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($lichHoc->caHoc)
                                                <span class="badge bg-info">{{ $lichHoc->caHoc->ten_ca }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($lichHoc->gio_bat_dau)->format('H:i') }} -
                                            {{ Carbon\Carbon::parse($lichHoc->gio_ket_thuc)->format('H:i') }}</td>
                                        <td>{{ $lichHoc->phongHoc->ten_phong ?? '-' }}</td>
                                        <td>{{ $lichHoc->giangVien->ho_ten ?? '-' }}</td>
                                        <td>
                                            @if ($lichHoc->trang_thai == 'chua_day')
                                                <span class="badge bg-secondary">Chưa dạy</span>
                                            @elseif($lichHoc->trang_thai == 'dang_day')
                                                <span class="badge bg-info">Đang dạy</span>
                                            @elseif($lichHoc->trang_thai == 'da_day')
                                                <span class="badge bg-success">Đã dạy</span>
                                            @else
                                                <span class="badge bg-danger">Hủy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.lich-chi-tiet.edit', $lichHoc) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if ($lichHoc->trang_thai != 'huy')
                                                    <form action="{{ route('dao-tao.lich-chi-tiet.cancel', $lichHoc) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy buổi học này?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('dao-tao.lich-chi-tiet.destroy', $lichHoc) }}"
                                                    method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Chưa có lịch học chi tiết nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $lichHocs->links() }}

                    <div class="mt-3">
                        <a href="{{ route('dao-tao.lop-hoc-phan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal tạo lịch tự động -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('dao-tao.lop-hoc-phan.lich-chi-tiet.generate', $lopHocPhan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tạo lịch học tự động</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tạo lịch chi tiết tự động từ lịch cố định đã thiết lập</p>
                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ngay_bat_dau" name="ngay_bat_dau" required>
                        </div>
                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ngay_ket_thuc" name="ngay_ket_thuc" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Tạo lịch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
