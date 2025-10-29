@extends('layouts.layout-admin')

@section('title', 'Map Vai trò - Quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Map Vai trò - Quyền</h3>
                    <p class="text-subtitle text-muted">Quản lý quyền cho từng vai trò</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Map Vai trò - Quyền</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ma trận Quyền theo Vai trò</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.vai-tro-quyen.update-matrix') }}" method="POST"
                        id="permissionMatrixForm">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="200">Vai trò \ Quyền</th>
                                        @foreach ($nhomQuyens as $nhomQuyen)
                                            <th colspan="{{ $nhomQuyen->quyens->count() }}" class="text-center bg-light">
                                                {{ $nhomQuyen->ten_nhom }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th></th>
                                        @foreach ($nhomQuyens as $nhomQuyen)
                                            @foreach ($nhomQuyen->quyens as $quyen)
                                                <th class="text-center" style="min-width: 100px;">
                                                    <small>{{ $quyen->ten_quyen }}</small>
                                                </th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vaiTros as $vaiTro)
                                        <tr>
                                            <td class="fw-bold">
                                                {{ $vaiTro->ten_vai_tro }}
                                                <br>
                                                <small class="text-muted">({{ $vaiTro->ma_vai_tro }})</small>
                                            </td>
                                            @foreach ($nhomQuyens as $nhomQuyen)
                                                @foreach ($nhomQuyen->quyens as $quyen)
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[{{ $vaiTro->id }}][]"
                                                                value="{{ $quyen->id }}"
                                                                id="permission_{{ $vaiTro->id }}_{{ $quyen->id }}"
                                                                data-role="{{ $vaiTro->id }}"
                                                                data-permission="{{ $quyen->id }}"
                                                                {{ in_array($quyen->id, $matrix[$vaiTro->id] ?? []) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Làm mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="row mt-3">
                @foreach ($vaiTros as $vaiTro)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $vaiTro->ten_vai_tro }}</h6>
                                <p class="text-muted mb-2">
                                    <small>{{ $vaiTro->mo_ta }}</small>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <span>Số quyền:</span>
                                    <span class="badge bg-info">{{ count($matrix[$vaiTro->id] ?? []) }} quyền</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX update khi click checkbox (tùy chọn)
                const checkboxes = document.querySelectorAll('.permission-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const roleId = this.dataset.role;
                        const permissionId = this.dataset.permission;
                        const isChecked = this.checked;

                        // Có thể thêm AJAX call để update real-time
                        console.log(`Role ${roleId} - Permission ${permissionId}: ${isChecked}`);
                    });
                });
            });
        </script>
    @endpush
@endsection
