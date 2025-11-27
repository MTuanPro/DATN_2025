@extends('layouts.layout-daotao')

@section('title', 'Danh sách Lớp hành chính')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Lớp hành chính</h3>
                    <p class="text-subtitle text-muted">Quản lý lớp hành chính - PHASE 3</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lớp hành chính</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filters & Actions -->
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('dao-tao.lop-hanh-chinh.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tìm kiếm mã lớp, tên lớp..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="khoa_hoc_id" class="form-select">
                                        <option value="">-- Tất cả khóa học --</option>
                                        @foreach ($khoaHocs as $khoaHoc)
                                            <option value="{{ $khoaHoc->id }}"
                                                {{ request('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                                                {{ $khoaHoc->ten_khoa_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="nganh_id" class="form-select">
                                        <option value="">-- Tất cả ngành --</option>
                                        @foreach ($nganhs as $nganh)
                                            <option value="{{ $nganh->id }}"
                                                {{ request('nganh_id') == $nganh->id ? 'selected' : '' }}>
                                                {{ $nganh->ma_nganh }} - {{ $nganh->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <button type="button" id="btnXoaChon" class="btn btn-danger shadow-sm" style="display: none;" onclick="xoaNhieuLopHanhChinh()">
                                    <i class="bi bi-trash me-1"></i> Xóa đã chọn
                                </button>
                                <form action="{{ route('dao-tao.lop-hanh-chinh.sync-si-so') }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn đồng bộ lại sức chứa cho tất cả các lớp?')">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary shadow-sm" title="Đồng bộ lại sức chứa từ số lượng sinh viên thực tế">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Đồng bộ sức chứa
                                    </button>
                                </form>
                                <a href="{{ route('dao-tao.lop-hanh-chinh.create') }}" class="btn btn-success shadow-sm" title="Thêm lớp hành chính mới">
                                    <i class="bi bi-plus-circle me-1"></i> Thêm lớp mới
                                </a>
                                <a href="{{ route('dao-tao.lop-hanh-chinh.show-import-form') }}" class="btn btn-warning shadow-sm text-white" title="Import lớp hành chính từ Excel">
                                    <i class="bi bi-upload me-1"></i> Import Excel
                                </a>
                                <a href="{{ route('dao-tao.lop-hanh-chinh.export', request()->query()) }}" class="btn btn-info shadow-sm text-white" title="Xuất danh sách lớp hành chính ra Excel">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng dữ liệu -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th>Mã lớp</th>
                                    <th>Tên lớp</th>
                                    <th>Khóa học</th>
                                    <th>Ngành</th>
                                    <th>GVCN</th>
                                    <th>Sức chứa</th>
                                    <th>Sĩ số</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lopHanhChinh as $index => $lop)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-lop-hanh-chinh" value="{{ $lop->id }}" onchange="toggleDeleteButton()">
                                        </td>
                                        <td>{{ $lopHanhChinh->firstItem() + $index }}</td>
                                        <td><strong>{{ $lop->ma_lop }}</strong></td>
                                        <td>{{ $lop->ten_lop }}</td>
                                        <td>
                                            @if ($lop->khoaHoc)
                                                <span class="badge bg-primary">{{ $lop->khoaHoc->ten_khoa_hoc }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lop->nganh)
                                                {{ $lop->nganh->ma_nganh }} - {{ $lop->nganh->ten_nganh }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lop->giangVienChuNhiem)
                                                {{ $lop->giangVienChuNhiem->ho_ten }}
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $lop->si_so }} SV</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $lop->sinhVien->count() }} SV</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dao-tao.lop-hanh-chinh.show', $lop->id) }}"
                                                    class="btn btn-sm btn-info" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.lop-hanh-chinh.edit', $lop->id) }}"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.lop-hanh-chinh.destroy', $lop->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa lớp này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $lopHanhChinh->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Form ẩn để xóa nhiều -->
    <form id="formXoaNhieu" action="{{ route('dao-tao.lop-hanh-chinh.destroy-multiple') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="idsToDelete">
    </form>

    <script>
        function toggleCheckAll() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.checkbox-lop-hanh-chinh');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
            toggleDeleteButton();
        }

        function toggleDeleteButton() {
            const checkboxes = document.querySelectorAll('.checkbox-lop-hanh-chinh:checked');
            const btnXoaChon = document.getElementById('btnXoaChon');
            if (checkboxes.length > 0) {
                btnXoaChon.style.display = 'inline-block';
            } else {
                btnXoaChon.style.display = 'none';
            }
            // Cập nhật checkbox "Chọn tất cả"
            const allCheckboxes = document.querySelectorAll('.checkbox-lop-hanh-chinh');
            const checkAll = document.getElementById('checkAll');
            if (allCheckboxes.length > 0) {
                checkAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }

        function xoaNhieuLopHanhChinh() {
            const checkboxes = document.querySelectorAll('.checkbox-lop-hanh-chinh:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một lớp hành chính để xóa!');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const count = ids.length;

            if (!confirm(`Bạn có chắc chắn muốn xóa ${count} lớp hành chính đã chọn? Hành động này không thể hoàn tác!`)) {
                return;
            }

            document.getElementById('idsToDelete').value = ids.join(',');
            document.getElementById('formXoaNhieu').submit();
        }
    </script>
@endsection
