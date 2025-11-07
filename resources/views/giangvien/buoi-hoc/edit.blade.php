@extends('layouts.layout-giangvien')

@section('title', 'Cập nhật buổi học')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>Cập nhật buổi học</h3>
                <p class="text-subtitle text-muted">
                    {{ $buoiHoc->lopHocPhan->ma_lop_hp }} - {{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}
                </p>
            </div>
            <a href="{{ route('giangvien.buoi-hoc.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <!-- Thông tin buổi học -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin buổi học</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Ngày học:</th>
                                    <td><strong>{{ $buoiHoc->ngay_hoc->format('d/m/Y') }} ({{ $buoiHoc->ngay_hoc->dayName }})</strong></td>
                                </tr>
                                <tr>
                                    <th>Tiết:</th>
                                    <td>Tiết {{ $buoiHoc->tiet_bat_dau }} - {{ $buoiHoc->tiet_ket_thuc }}</td>
                                </tr>
                                <tr>
                                    <th>Giờ:</th>
                                    <td>{{ $buoiHoc->gio_bat_dau }} - {{ $buoiHoc->gio_ket_thuc }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;">Lớp học phần:</th>
                                    <td>{{ $buoiHoc->lopHocPhan->ma_lop_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td>{{ $buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phòng học:</th>
                                    <td>{{ $buoiHoc->phongHoc->ten_phong ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form cập nhật -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cập nhật thông tin</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
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

                    <form action="{{ route('giangvien.buoi-hoc.update', $buoiHoc->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="trang_thai" id="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                                    <option value="">-- Chọn trạng thái --</option>
                                    <option value="chua_day" {{ old('trang_thai', $buoiHoc->trang_thai) == 'chua_day' ? 'selected' : '' }}>
                                        Chưa dạy
                                    </option>
                                    <option value="dang_day" {{ old('trang_thai', $buoiHoc->trang_thai) == 'dang_day' ? 'selected' : '' }}>
                                        Đang dạy
                                    </option>
                                    <option value="da_day" {{ old('trang_thai', $buoiHoc->trang_thai) == 'da_day' ? 'selected' : '' }}>
                                        Đã dạy
                                    </option>
                                    <option value="huy" {{ old('trang_thai', $buoiHoc->trang_thai) == 'huy' ? 'selected' : '' }}>
                                        Hủy
                                    </option>
                                </select>
                                @error('trang_thai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="noi_dung_giang_day" class="form-label">Nội dung giảng dạy</label>
                                <textarea name="noi_dung_giang_day" id="noi_dung_giang_day" rows="5" 
                                          class="form-control @error('noi_dung_giang_day') is-invalid @enderror"
                                          placeholder="Nhập nội dung giảng dạy của buổi học...">{{ old('noi_dung_giang_day', $buoiHoc->noi_dung_giang_day) }}</textarea>
                                @error('noi_dung_giang_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tối đa 1000 ký tự</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="tai_lieu" class="form-label">Tài liệu đính kèm</label>
                                
                                @if($buoiHoc->tai_lieu_dinh_kem)
                                    <div class="alert alert-info mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-earmark-text"></i>
                                                <strong>Tài liệu hiện tại:</strong>
                                                <a href="{{ route('giangvien.buoi-hoc.download-tai-lieu', $buoiHoc->id) }}" 
                                                   class="text-decoration-none" target="_blank">
                                                    {{ basename($buoiHoc->tai_lieu_dinh_kem) }}
                                                </a>
                                            </div>
                                            <form action="{{ route('giangvien.buoi-hoc.delete-tai-lieu', $buoiHoc->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" name="tai_lieu" id="tai_lieu" 
                                       class="form-control @error('tai_lieu') is-invalid @enderror"
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                                @error('tai_lieu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Chấp nhận: PDF, Word, PowerPoint, Excel, ZIP. Tối đa 10MB.
                                </small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="ghi_chu" class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" id="ghi_chu" rows="3" 
                                          class="form-control @error('ghi_chu') is-invalid @enderror"
                                          placeholder="Ghi chú thêm về buổi học (nếu có)...">{{ old('ghi_chu', $buoiHoc->ghi_chu) }}</textarea>
                                @error('ghi_chu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tối đa 500 ký tự</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('giangvien.buoi-hoc.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
