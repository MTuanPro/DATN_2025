@extends('layouts.layout-admin')

@section('title', 'Map Vai trò - Quyền')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Map Vai trò - Quyền</h3>
                    <p class="text-subtitle text-muted">Quản lý quyền cho từng vai trò theo nhóm người dùng (Actor)</p>
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
            <!-- Hướng dẫn -->
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Mỗi quyền chỉ có thể gán cho vai trò có cùng nhóm người dùng (Actor).
                Ví dụ: Quyền "Thêm khoa" chỉ có thể gán cho vai trò thuộc nhóm "Phòng đào tạo", không thể gán cho "Admin".
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ma trận Quyền theo Vai trò</h5>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-circle-fill text-success"></i> Checkbox xanh: Quyền phù hợp với Actor của vai trò
                        <i class="bi bi-circle-fill text-secondary ms-3"></i> Checkbox xám: Quyền không phù hợp (bị disable)
                    </p>
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
                                            @if ($nhomQuyen->quyens->count() > 0)
                                                <th colspan="{{ $nhomQuyen->quyens->count() }}"
                                                    class="text-center bg-light">
                                                    {{ $nhomQuyen->ten_nhom }}
                                                </th>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th></th>
                                        @foreach ($nhomQuyens as $nhomQuyen)
                                            @foreach ($nhomQuyen->quyens as $quyen)
                                                <th class="text-center" style="min-width: 100px;">
                                                    <small>{{ $quyen->ten_quyen }}</small>
                                                    <br>
                                                    @foreach ($quyen->actors as $actorRecord)
                                                        @php
                                                            $actorColors = [
                                                                'admin' => 'danger',
                                                                'dao_tao' => 'info',
                                                                'giang_vien' => 'warning',
                                                                'sinh_vien' => 'success',
                                                            ];
                                                            $color = $actorColors[$actorRecord->actor] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $color }}"
                                                            style="font-size: 0.65em;">
                                                            {{ $actors[$actorRecord->actor] ?? $actorRecord->actor }}
                                                        </span>
                                                    @endforeach
                                                </th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vaiTros as $vaiTro)
                                        @php
                                            $vaiTroActorColor =
                                                [
                                                    'admin' => 'danger',
                                                    'dao_tao' => 'info',
                                                    'giang_vien' => 'warning',
                                                    'sinh_vien' => 'success',
                                                ][$vaiTro->actor] ?? 'secondary';
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">
                                                {{ $vaiTro->ten_vai_tro }}
                                                <br>
                                                <small class="text-muted">({{ $vaiTro->ma_vai_tro }})</small>
                                                @if ($vaiTro->actor)
                                                    <br>
                                                    <span class="badge bg-{{ $vaiTroActorColor }}">
                                                        {{ $actors[$vaiTro->actor] ?? $vaiTro->actor }}
                                                    </span>
                                                @endif
                                            </td>
                                            @foreach ($nhomQuyens as $nhomQuyen)
                                                @foreach ($nhomQuyen->quyens as $quyen)
                                                    @php
                                                        // Kiểm tra quyền có phù hợp với actor của vai trò không
                                                        $quyenActors = $quyen->actors->pluck('actor')->toArray();
                                                        $isCompatible =
                                                            empty($quyenActors) ||
                                                            empty($vaiTro->actor) ||
                                                            in_array($vaiTro->actor, $quyenActors);
                                                        $isChecked = in_array($quyen->id, $matrix[$vaiTro->id] ?? []);
                                                    @endphp
                                                    <td class="text-center {{ !$isCompatible ? 'bg-light' : '' }}">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[{{ $vaiTro->id }}][]"
                                                                value="{{ $quyen->id }}"
                                                                id="permission_{{ $vaiTro->id }}_{{ $quyen->id }}"
                                                                data-role="{{ $vaiTro->id }}"
                                                                data-permission="{{ $quyen->id }}"
                                                                {{ $isChecked ? 'checked' : '' }}
                                                                {{ !$isCompatible ? 'disabled' : '' }}
                                                                title="{{ !$isCompatible ? 'Quyền không phù hợp với Actor của vai trò này' : '' }}">
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
                    @php
                        $actorColor =
                            [
                                'admin' => 'danger',
                                'dao_tao' => 'info',
                                'giang_vien' => 'warning',
                                'sinh_vien' => 'success',
                            ][$vaiTro->actor] ?? 'secondary';
                    @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card border-{{ $actorColor }}">
                            <div class="card-body">
                                <h6 class="card-title">
                                    {{ $vaiTro->ten_vai_tro }}
                                    @if ($vaiTro->actor)
                                        <span class="badge bg-{{ $actorColor }} float-end">
                                            {{ $actors[$vaiTro->actor] ?? $vaiTro->actor }}
                                        </span>
                                    @endif
                                </h6>
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
                const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');

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
