@extends('layouts.layout-giangvien')

@section('title', 'Điểm danh sinh viên')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Điểm danh sinh viên</h3>
                <p class="text-subtitle text-muted">
                    {{ $buoiHoc->lopHocPhan->ma_lop_hp }} - {{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                </p>
            </div>
            <a href="{{ route('giangvien.diem-danh.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin buổi học -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 35%;">Ngày học:</th>
                                    <td><strong class="text-primary">{{ $buoiHoc->ngay_hoc->format('d/m/Y') }} ({{ $buoiHoc->ngay_hoc->dayName }})</strong></td>
                                </tr>
                                <tr>
                                    <th>Tiết:</th>
                                    <td>Tiết {{ $buoiHoc->tiet_bat_dau }} - {{ $buoiHoc->tiet_ket_thuc }}</td>
                                </tr>
                                <tr>
                                    <th>Giờ:</th>
                                    <td>
                                        @if($buoiHoc->gio_bat_dau && $buoiHoc->gio_ket_thuc)
                                            {{ \Carbon\Carbon::parse($buoiHoc->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($buoiHoc->gio_ket_thuc)->format('H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 35%;">Phòng học:</th>
                                    <td>{{ $buoiHoc->phongHoc->ten_phong ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><strong>{{ $sinhViens->count() }}</strong> sinh viên</td>
                                </tr>
                                <tr>
                                    <th>Thời gian sửa:</th>
                                    <td>
                                        @if($coTheSua)
                                            <span class="badge bg-success">Có thể sửa</span>
                                        @else
                                            <span class="badge bg-danger">{{ $thongBaoThoiGian ?? 'Không thể sửa' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form điểm danh -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách sinh viên</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('giangvien.diem-danh.store', $buoiHoc->id) }}" method="POST">
                        @csrf

                        <!-- Toolbar điểm danh nhanh -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><i class="bi bi-lightbulb"></i> Điểm danh nhanh:</strong>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-success" onclick="checkAll('co_mat')">
                                        <i class="bi bi-check-all"></i> Tất cả có mặt
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="checkAll('vang')">
                                        <i class="bi bi-x-circle"></i> Tất cả vắng
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th style="width: 120px;">Mã SV</th>
                                        <th>Họ và tên</th>
                                        <th style="width: 200px;">Lớp học phần</th>
                                        <th style="width: 350px;">Trạng thái</th>
                                        <th style="width: 250px;">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sinhViens as $index => $sv)
                                        @php
                                            $currentStatus = $diemDanhData[$sv->id] ?? 'co_mat';
                                            $currentGhiChu = $diemDanhGhiChu[$sv->id] ?? '';
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $sv->sinhVien->ma_sinh_vien }}</strong></td>
                                            <td>{{ $sv->sinhVien->ho_ten }}</td>
                                            <td>{{ $sv->lopHocPhan->ma_lop_hp ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="diem_danh[{{ $sv->id }}]" 
                                                           id="co_mat_{{ $sv->id }}" value="co_mat" 
                                                           {{ $currentStatus == 'co_mat' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success btn-sm" for="co_mat_{{ $sv->id }}">
                                                        <i class="bi bi-check-circle"></i> Có mặt
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[{{ $sv->id }}]" 
                                                           id="vang_{{ $sv->id }}" value="vang"
                                                           {{ $currentStatus == 'vang' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger btn-sm" for="vang_{{ $sv->id }}">
                                                        <i class="bi bi-x-circle"></i> Vắng
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[{{ $sv->id }}]" 
                                                           id="di_tre_{{ $sv->id }}" value="di_tre"
                                                           {{ $currentStatus == 'di_tre' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-warning btn-sm" for="di_tre_{{ $sv->id }}">
                                                        <i class="bi bi-clock"></i> Đi trễ
                                                    </label>

                                                    <input type="radio" class="btn-check" name="diem_danh[{{ $sv->id }}]" 
                                                           id="nghi_phep_{{ $sv->id }}" value="nghi_phep"
                                                           {{ $currentStatus == 'nghi_phep' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-info btn-sm" for="nghi_phep_{{ $sv->id }}">
                                                        <i class="bi bi-envelope"></i> Nghỉ phép
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="ghi_chu[{{ $sv->id }}]" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Ghi chú..."
                                                       value="{{ $currentGhiChu }}"
                                                       maxlength="500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Tổng số: <strong>{{ $sinhViens->count() }}</strong> sinh viên
                            </div>
                            <div>
                                <a href="{{ route('giangvien.diem-danh.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </a>
                                @if($coTheSua)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Lưu điểm danh
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        function checkAll(status) {
            const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
    @endpush
@endsection
