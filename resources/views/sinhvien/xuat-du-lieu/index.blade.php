@extends('layouts.layout-sinhvien')

@section('title', 'Xuất dữ liệu')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Xuất dữ liệu</h3>
                    <p class="text-subtitle text-muted">Xuất bảng điểm, thời khóa biểu và giấy xác nhận sinh viên</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Xuất dữ liệu</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                {{-- Xuất bảng điểm --}}
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-text"></i> Xuất bảng điểm
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Xuất bảng điểm học tập theo học kỳ dưới định dạng Excel hoặc PDF</p>
                            
                            <form id="formBangDiem">
                                <div class="mb-3">
                                    <label for="hoc_ky_bang_diem" class="form-label">Chọn học kỳ</label>
                                    <select name="hoc_ky_id" id="hoc_ky_bang_diem" class="form-select">
                                        <option value="">Tất cả học kỳ</option>
                                        @foreach($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ $hk->la_hoc_ky_hien_tai ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                                @if($hk->la_hoc_ky_hien_tai) (Hiện tại) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" onclick="xuatBangDiem('excel')">
                                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="xuatBangDiem('pdf')">
                                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Xuất TKB --}}
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar3"></i> Xuất thời khóa biểu
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Xuất thời khóa biểu cá nhân theo học kỳ dưới định dạng PDF</p>
                            
                            <form id="formTKB">
                                <div class="mb-3">
                                    <label for="hoc_ky_tkb" class="form-label">Chọn học kỳ <span class="text-danger">*</span></label>
                                    <select name="hoc_ky_id" id="hoc_ky_tkb" class="form-select" required>
                                        <option value="">-- Chọn học kỳ --</option>
                                        @foreach($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ $hk->la_hoc_ky_hien_tai ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }} - {{ $hk->nam_hoc }}
                                                @if($hk->la_hoc_ky_hien_tai) (Hiện tại) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="button" class="btn btn-primary" onclick="xuatTKB()">
                                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Giấy xác nhận SV --}}
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-person"></i> Giấy xác nhận SV
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Xuất giấy xác nhận sinh viên đang theo học</p>
                            
                            <div class="alert alert-info">
                                <small>
                                    <i class="bi bi-info-circle"></i>
                                    Giấy xác nhận sẽ bao gồm thông tin cá nhân, thông tin học tập và xác nhận của nhà trường
                                </small>
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('sinh-vien.xuat-du-lieu.giay-xac-nhan.pdf') }}" 
                                   class="btn btn-info" 
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Xuất giấy xác nhận
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông tin hướng dẫn --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Hướng dẫn sử dụng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-success">
                                <i class="bi bi-1-circle-fill"></i> Xuất bảng điểm
                            </h6>
                            <ul class="text-muted small">
                                <li>Chọn học kỳ muốn xuất (hoặc chọn "Tất cả học kỳ")</li>
                                <li>Click "Xuất Excel" để tải file Excel</li>
                                <li>Click "Xuất PDF" để xem/in file PDF</li>
                                <li>File bao gồm: Điểm QT, GK, CK, điểm tổng kết và GPA</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary">
                                <i class="bi bi-2-circle-fill"></i> Xuất TKB
                            </h6>
                            <ul class="text-muted small">
                                <li>Chọn học kỳ muốn xuất</li>
                                <li>Click "Xuất PDF" để xem/in thời khóa biểu</li>
                                <li>File hiển thị lịch học theo thứ và tiết</li>
                                <li>Bao gồm: Môn học, giảng viên, phòng học</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-info">
                                <i class="bi bi-3-circle-fill"></i> Giấy xác nhận
                            </h6>
                            <ul class="text-muted small">
                                <li>Click "Xuất giấy xác nhận" để tải file</li>
                                <li>File PDF có giá trị pháp lý</li>
                                <li>Sử dụng cho: Xin visa, vay ngân hàng, v.v.</li>
                                <li>In ra và đóng dấu tại phòng Đào tạo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
function xuatBangDiem(type) {
    const hocKyId = document.getElementById('hoc_ky_bang_diem').value;
    const url = type === 'excel' 
        ? '{{ route("sinh-vien.xuat-du-lieu.bang-diem.excel") }}'
        : '{{ route("sinh-vien.xuat-du-lieu.bang-diem.pdf") }}';
    
    const fullUrl = hocKyId ? url + '?hoc_ky_id=' + hocKyId : url;
    window.open(fullUrl, '_blank');
}

function xuatTKB() {
    const hocKyId = document.getElementById('hoc_ky_tkb').value;
    
    if (!hocKyId) {
        alert('Vui lòng chọn học kỳ!');
        return;
    }
    
    const url = '{{ route("sinh-vien.xuat-du-lieu.tkb.pdf") }}?hoc_ky_id=' + hocKyId;
    window.open(url, '_blank');
}
</script>
@endpush
