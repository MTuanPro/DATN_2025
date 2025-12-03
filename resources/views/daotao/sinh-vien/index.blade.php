@extends('layouts.layout-daotao')

@section('title', 'Danh sách Sinh viên')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Sinh viên</h3>
                    <p class="text-subtitle text-muted">Quản lý sinh viên - PHASE 3</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sinh viên</li>
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
                            <form action="{{ route('dao-tao.sinh-vien.index') }}" method="GET" class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="MSSV, họ tên, email..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="khoa_hoc_id" class="form-select form-select-sm">
                                        <option value="">-- Khóa --</option>
                                        @foreach ($khoaHocs as $kh)
                                            <option value="{{ $kh->id }}"
                                                {{ request('khoa_hoc_id') == $kh->id ? 'selected' : '' }}>
                                                {{ $kh->ten_khoa_hoc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="nganh_id" class="form-select form-select-sm">
                                        <option value="">-- Ngành --</option>
                                        @foreach ($nganhs as $n)
                                            <option value="{{ $n->id }}"
                                                {{ request('nganh_id') == $n->id ? 'selected' : '' }}>
                                                {{ $n->ten_nganh }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="trang_thai_hoc_tap_id" class="form-select form-select-sm">
                                        <option value="">-- Trạng thái --</option>
                                        @foreach ($trangThais as $tt)
                                            <option value="{{ $tt->id }}"
                                                {{ request('trang_thai_hoc_tap_id') == $tt->id ? 'selected' : '' }}>
                                                {{ $tt->ten_trang_thai }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" id="btnXoaChon" class="btn btn-danger btn-sm" style="display: none;" onclick="xoaNhieuSinhVien()">
                                    <i class="bi bi-trash"></i> Xóa đã chọn
                                </button>
                                <a href="{{ route('dao-tao.sinh-vien.show-import-form') }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-file-earmark-excel"></i> Import Excel
                                </a>
                                <a href="{{ route('dao-tao.sinh-vien.create') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle"></i> Thêm SV
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng dữ liệu -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Ngành</th>
                                    <th>Chuyên ngành</th>
                                    <th>Kỳ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sinhViens as $index => $sv)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-sinh-vien" value="{{ $sv->id }}" onchange="toggleDeleteButton()">
                                        </td>
                                        <td>{{ $sinhViens->firstItem() + $index }}</td>
                                        <td><strong>{{ $sv->ma_sinh_vien }}</strong></td>
                                        <td>
                                            {{ $sv->ho_ten }}
                                            <br><small class="text-muted">{{ $sv->email }}</small>
                                        </td>
                                        <td>
                                            @if ($sv->nganh)
                                                {{ $sv->nganh->ten_nganh }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($sv->chuyenNganh)
                                                {{ $sv->chuyenNganh->ten_chuyen_nganh }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-info">Kỳ {{ $sv->ky_hien_tai }}</span></td>
                                        <td>
                                            @if ($sv->trangThaiHocTap)
                                                <span
                                                    class="badge 
                                                    @if ($sv->trangThaiHocTap->ma_trang_thai == 'DANG_HOC') bg-success
                                                    @elseif($sv->trangThaiHocTap->ma_trang_thai == 'BAO_LUU') bg-warning
                                                    @elseif($sv->trangThaiHocTap->ma_trang_thai == 'THOI_HOC') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ $sv->trangThaiHocTap->ten_trang_thai }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('dao-tao.sinh-vien.show', $sv->id) }}"
                                                    class="btn btn-info btn-sm" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('dao-tao.sinh-vien.edit', $sv->id) }}"
                                                    class="btn btn-warning btn-sm" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dao-tao.sinh-vien.destroy', $sv->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Xóa sinh viên sẽ xóa cả tài khoản. Bạn chắc chắn?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Hiển thị {{ $sinhViens->firstItem() ?? 0 }} - {{ $sinhViens->lastItem() ?? 0 }}
                                trong tổng số {{ $sinhViens->total() }} sinh viên
                            </small>
                        </div>
                        <div>
                            {{ $sinhViens->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Form ẩn để xóa nhiều -->
    <form id="formXoaNhieu" action="{{ route('dao-tao.sinh-vien.destroy-multiple') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="idsToDelete">
    </form>

    <script>
        function toggleCheckAll() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
            toggleDeleteButton();
        }

        function toggleDeleteButton() {
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien:checked');
            const btnXoaChon = document.getElementById('btnXoaChon');
            if (checkboxes.length > 0) {
                btnXoaChon.style.display = 'inline-block';
            } else {
                btnXoaChon.style.display = 'none';
            }
            // Cập nhật checkbox "Chọn tất cả"
            const allCheckboxes = document.querySelectorAll('.checkbox-sinh-vien');
            const checkAll = document.getElementById('checkAll');
            if (allCheckboxes.length > 0) {
                checkAll.checked = checkboxes.length === allCheckboxes.length;
            }
        }

        function xoaNhieuSinhVien() {
            const checkboxes = document.querySelectorAll('.checkbox-sinh-vien:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một sinh viên để xóa!');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const count = ids.length;

            if (!confirm(`Bạn có chắc chắn muốn xóa ${count} sinh viên đã chọn? Hành động này sẽ xóa cả tài khoản và không thể hoàn tác!`)) {
                return;
            }

            document.getElementById('idsToDelete').value = ids.join(',');
            document.getElementById('formXoaNhieu').submit();
        }
    </script>
@endsection
