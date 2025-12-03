@extends('layouts.layout-daotao')

@section('title', 'Chỉnh sửa Học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Chỉnh sửa Học phí</h3>
                    <p class="text-subtitle text-muted">Điều chỉnh học phí sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dao-tao.hoc-phi.update', $hocPhi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">MSSV</label>
                                    <input type="text" class="form-control" value="{{ $hocPhi->sinhVien->ma_sinh_vien }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" value="{{ $hocPhi->sinhVien->ho_ten }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Học kỳ</label>
                                    <input type="text" class="form-control" value="{{ $hocPhi->hocKy->ten_hoc_ky }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hạn đóng <span class="text-danger">*</span></label>
                                    <input type="date" name="han_dong" class="form-control @error('han_dong') is-invalid @enderror" 
                                           value="{{ old('han_dong', $hocPhi->han_dong->format('Y-m-d')) }}" required>
                                    @error('han_dong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Học phí môn học</label>
                                    <input type="text" class="form-control" value="{{ number_format($hocPhi->tong_hoc_phi_mon_hoc, 0, ',', '.') }} đ" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phí dịch vụ <span class="text-danger">*</span></label>
                                    <input type="number" name="phi_dich_vu" class="form-control @error('phi_dich_vu') is-invalid @enderror" 
                                           value="{{ old('phi_dich_vu', $hocPhi->phi_dich_vu) }}" min="0" step="1000" required>
                                    @error('phi_dich_vu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Phí dịch vụ, bảo hiểm</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tổng học phí</label>
                                    <input type="text" class="form-control" id="tong_hoc_phi_display" value="{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Đã đóng</label>
                                    <input type="text" class="form-control" value="{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Còn lại</label>
                                    <input type="text" class="form-control text-danger" id="con_lai_display" value="{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="ghi_chu" class="form-control" rows="3">{{ old('ghi_chu', $hocPhi->ghi_chu) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dao-tao.hoc-phi.show', $hocPhi->id) }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phiDichVuInput = document.querySelector('input[name="phi_dich_vu"]');
            const tongHocPhiDisplay = document.getElementById('tong_hoc_phi_display');
            const conLaiDisplay = document.getElementById('con_lai_display');
            
            const tongHocPhiMonHoc = {{ $hocPhi->tong_hoc_phi_mon_hoc }};
            const soTienDaDong = {{ $hocPhi->so_tien_da_dong }};

            function updateTotals() {
                const phiDichVu = parseFloat(phiDichVuInput.value) || 0;
                const tongHocPhi = tongHocPhiMonHoc + phiDichVu;
                const conLai = tongHocPhi - soTienDaDong;

                tongHocPhiDisplay.value = new Intl.NumberFormat('vi-VN').format(tongHocPhi) + ' đ';
                conLaiDisplay.value = new Intl.NumberFormat('vi-VN').format(conLai) + ' đ';
                
                if (conLai < 0) {
                    conLaiDisplay.classList.add('text-success');
                    conLaiDisplay.classList.remove('text-danger');
                } else {
                    conLaiDisplay.classList.add('text-danger');
                    conLaiDisplay.classList.remove('text-success');
                }
            }

            if (phiDichVuInput) {
                phiDichVuInput.addEventListener('input', updateTotals);
            }
        });
    </script>
@endsection
