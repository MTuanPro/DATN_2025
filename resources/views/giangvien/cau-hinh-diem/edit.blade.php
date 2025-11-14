@extends('layouts.layout-giangvien')

@section('title', 'Sửa đầu điểm')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Sửa đầu điểm</h3>
                <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.cau-hinh-diem.index') }}">Cấu hình điểm</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.cau-hinh-diem.show', $lopHocPhan->id) }}">Chi tiết</a></li>
                        <li class="breadcrumb-item active">Chỉnh sửa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5>Chỉnh sửa đầu điểm: <strong>{{ $cauHinh->ten_dau_diem }}</strong></h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Tỷ lệ hiện tại: <strong>{{ $cauHinh->ty_le }}%</strong> | 
                    Tỷ lệ còn lại (không tính đầu điểm này): <strong>{{ $tyLeConLai }}%</strong> |
                    Tối đa có thể cấu hình: <strong>{{ $tyLeConLai + $cauHinh->ty_le }}%</strong>
                </div>

                <form action="{{ route('giangvien.cau-hinh-diem.update', $cauHinh->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ten_dau_diem">Tên đầu điểm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ten_dau_diem') is-invalid @enderror" 
                                    id="ten_dau_diem" name="ten_dau_diem" 
                                    value="{{ old('ten_dau_diem', $cauHinh->ten_dau_diem) }}"
                                    placeholder="VD: Chuyên cần, Giữa kỳ, Cuối kỳ, Bài tập, Tiểu luận...">
                                @error('ten_dau_diem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nhập tên mô tả cho đầu điểm</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ty_le">Tỷ lệ (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('ty_le') is-invalid @enderror" 
                                    id="ty_le" name="ty_le" 
                                    value="{{ old('ty_le', $cauHinh->ty_le) }}"
                                    min="1" max="{{ $tyLeConLai + $cauHinh->ty_le }}" step="0.1"
                                    placeholder="VD: 10, 20, 30...">
                                @error('ty_le')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tối đa: {{ $tyLeConLai + $cauHinh->ty_le }}%</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="so_cot">Số cột điểm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('so_cot') is-invalid @enderror" 
                                    id="so_cot" name="so_cot" 
                                    value="{{ old('so_cot', $cauHinh->so_cot) }}"
                                    min="1" max="10"
                                    placeholder="VD: 1, 2, 3...">
                                @error('so_cot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Số lần nhập điểm (VD: Bài tập có 3 cột = 3 lần làm bài)</small>
                            </div>
                        </div>
                    </div>

                    @if($cauHinh->nhapDiems->count() > 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Lưu ý:</strong> Đầu điểm này đã có {{ $cauHinh->nhapDiems->count() }} điểm đã nhập. 
                            Thay đổi số cột hoặc tỷ lệ có thể ảnh hưởng đến kết quả tính điểm.
                        </div>
                    @endif

                    <div class="alert alert-secondary">
                        <strong>Gợi ý các đầu điểm phổ biến:</strong>
                        <ul class="mb-0">
                            <li><strong>Chuyên cần (10-20%):</strong> Điểm danh, tham gia lớp - 1 cột</li>
                            <li><strong>Bài tập (10-30%):</strong> Bài tập về nhà - 2-5 cột</li>
                            <li><strong>Giữa kỳ (20-30%):</strong> Kiểm tra giữa kỳ - 1 cột</li>
                            <li><strong>Cuối kỳ (40-60%):</strong> Thi cuối kỳ - 1 cột</li>
                            <li><strong>Thực hành (10-30%):</strong> Bài lab, thực hành - 2-4 cột</li>
                            <li><strong>Tiểu luận (20-40%):</strong> Đề tài, báo cáo - 1-2 cột</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật
                        </button>
                        <a href="{{ route('giangvien.cau-hinh-diem.show', $lopHocPhan->id) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
