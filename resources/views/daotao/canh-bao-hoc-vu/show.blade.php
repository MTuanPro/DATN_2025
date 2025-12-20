@extends('layouts.layout-daotao')

@section('title', 'Chi Tiết Cảnh Báo')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Cảnh báo Học vụ #{{ $canhBao->id }}</h3>
                <p class="text-subtitle text-muted">Thông tin chi tiết và xử lý cảnh báo</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}">Cảnh báo Học vụ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Thông tin sinh viên -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-circle"></i> Thông tin Sinh viên
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                            <h5 class="mt-2 mb-0">{{ $canhBao->sinhVien->ho_ten }}</h5>
                            <p class="text-muted">{{ $canhBao->sinhVien->ma_sinh_vien }}</p>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th width="40%">Lớp:</th>
                                    <td>{{ $canhBao->sinhVien->nganh }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        <a href="mailto:{{ $canhBao->sinhVien->email }}">
                                            {{ $canhBao->sinhVien->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>SĐT:</th>
                                    <td>{{ $canhBao->sinhVien->so_dien_thoai ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh:</th>
                                    <td>{{ $canhBao->sinhVien->ngay_sinh ? $canhBao->sinhVien->ngay_sinh->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="{{ route('dao-tao.sinh-vien.show', $canhBao->sinhVien) }}" class="btn btn-sm btn-outline-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Xem hồ sơ đầy đủ
                        </a>
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Thao tác</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.edit', $canhBao) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-pencil"></i> Sửa cảnh báo
                        </a>
                        
                        @if($canhBao->trang_thai != 'da_xu_ly')
                        <button type="button" class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#xuLyModal">
                            <i class="bi bi-check-circle"></i> Xử lý cảnh báo
                        </button>
                        @endif

                        <button type="button" class="btn btn-success w-100 mb-2" onclick="guiEmailCanhBao()">
                            <i class="bi bi-envelope"></i> Gửi lại email
                        </button>

                        <form action="{{ route('dao-tao.canh-bao-hoc-vu.destroy', $canhBao) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                                <i class="bi bi-trash"></i> Xóa cảnh báo
                            </button>
                        </form>

                        <hr>
                        
                        <a href="{{ route('dao-tao.canh-bao-hoc-vu.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>

            <!-- Thông tin cảnh báo -->
            <div class="col-lg-8">
                <!-- Alert -->
                @php
                    $mucDoText = match($canhBao->muc_do) {
                        'canh_cao' => 'Cảnh cáo',
                        'dinh_chi' => 'Đình chỉ học tập',
                        'buoc_thoi_hoc' => 'Buộc thôi học',
                        default => $canhBao->muc_do
                    };
                    $alertType = match($canhBao->muc_do) {
                        'canh_cao' => 'warning',
                        'dinh_chi' => 'danger',
                        'buoc_thoi_hoc' => 'dark',
                        default => 'info'
                    };
                @endphp
                <div class="alert alert-{{ $alertType }}">
                    <h4 class="alert-heading">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        MỨC ĐỘ: {{ strtoupper($mucDoText) }}
                    </h4>
                    <p class="mb-0"><strong>{{ $canhBao->ly_do }}</strong></p>
                </div>

                <!-- Chi tiết cảnh báo -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin Cảnh báo</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Mã CB:</th>
                                            <td><code>#{{ $canhBao->id }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Học kỳ:</th>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $canhBao->hocKy->ten_hoc_ky }} - {{ $canhBao->hocKy->nam_hoc }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Loại cảnh báo:</th>
                                            <td>
                                                @php
                                                    $loaiText = match($canhBao->loai_canh_bao) {
                                                        'diem_thap' => 'Điểm trung bình thấp',
                                                        'vang_nhieu' => 'Vắng học nhiều',
                                                        'no_hoc_phi' => 'Nợ học phí',
                                                        'hoc_ky_lien_tiep' => 'Học kỳ liên tiếp không đạt',
                                                        default => $canhBao->loai_canh_bao
                                                    };
                                                @endphp
                                                <span class="badge bg-info">{{ $loaiText }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mức độ:</th>
                                            <td>
                                                <span class="badge bg-{{ $canhBao->muc_do == 'buoc_thoi_hoc' ? 'dark' : ($canhBao->muc_do == 'dinh_chi' ? 'danger' : 'warning') }}">
                                                    {{ $mucDoText }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Trạng thái:</th>
                                            <td>
                                                @php
                                                    $ttText = match($canhBao->trang_thai) {
                                                        'chua_xu_ly' => 'Chưa xử lý',
                                                        'dang_xu_ly' => 'Đang xử lý',
                                                        'da_xu_ly' => 'Đã xử lý',
                                                        default => $canhBao->trang_thai
                                                    };
                                                    $ttColor = match($canhBao->trang_thai) {
                                                        'chua_xu_ly' => 'secondary',
                                                        'dang_xu_ly' => 'info',
                                                        'da_xu_ly' => 'success',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $ttColor }}">{{ $ttText }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ngày cảnh báo:</th>
                                            <td>{{ $canhBao->ngay_canh_bao->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Người tạo:</th>
                                            <td>{{ $canhBao->nguoiTao->name ?? 'Hệ thống' }}</td>
                                        </tr>
                                        @if($canhBao->nguoi_xu_ly_id)
                                        <tr>
                                            <th>Người xử lý:</th>
                                            <td>{{ $canhBao->nguoiXuLy->name }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <h6>Lý do cảnh báo:</h6>
                                <p class="text-danger fw-bold">{{ $canhBao->ly_do }}</p>
                            </div>
                            @if($canhBao->ghi_chu)
                            <div class="col-12">
                                <h6>Ghi chú:</h6>
                                <p>{{ $canhBao->ghi_chu }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kết quả xử lý -->
                @if($canhBao->trang_thai == 'da_xu_ly')
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-circle"></i> Kết quả xử lý
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $canhBao->ket_qua_xu_ly }}</p>
                        @if($canhBao->nguoi_xu_ly_id)
                        <hr>
                        <small class="text-muted">
                            Xử lý bởi: <strong>{{ $canhBao->nguoiXuLy->name }}</strong>
                        </small>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Lịch sử cảnh báo của sinh viên -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history"></i> Lịch sử cảnh báo
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $lichSu = \App\Models\CanhBaoHocVu::where('sinh_vien_id', $canhBao->sinh_vien_id)
                                ->where('id', '!=', $canhBao->id)
                                ->orderBy('ngay_canh_bao', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($lichSu->isEmpty())
                        <p class="text-muted mb-0">Sinh viên chưa có cảnh báo nào khác.</p>
                        @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Học kỳ</th>
                                        <th>Loại</th>
                                        <th>Mức độ</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lichSu as $ls)
                                    <tr>
                                        <td>{{ $ls->hocKy->ten_hoc_ky }}</td>
                                        <td>
                                            @php
                                                $loaiLS = match($ls->loai_canh_bao) {
                                                    'diem_thap' => 'Điểm thấp',
                                                    'vang_nhieu' => 'Vắng nhiều',
                                                    'no_hoc_phi' => 'Nợ HP',
                                                    'hoc_ky_lien_tiep' => 'HK liên tiếp',
                                                    default => $ls->loai_canh_bao
                                                };
                                            @endphp
                                            {{ $loaiLS }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ls->muc_do == 'buoc_thoi_hoc' ? 'dark' : ($ls->muc_do == 'dinh_chi' ? 'danger' : 'warning') }}">
                                                {{ match($ls->muc_do) {
                                                    'canh_cao' => 'CC',
                                                    'dinh_chi' => 'ĐC',
                                                    'buoc_thoi_hoc' => 'BTH',
                                                    default => $ls->muc_do
                                                } }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $ttLS = match($ls->trang_thai) {
                                                    'chua_xu_ly' => ['Chưa XL', 'secondary'],
                                                    'dang_xu_ly' => ['Đang XL', 'info'],
                                                    'da_xu_ly' => ['Đã XL', 'success'],
                                                    default => ['N/A', 'secondary']
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $ttLS[1] }}">{{ $ttLS[0] }}</span>
                                        </td>
                                        <td>{{ $ls->ngay_canh_bao->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('dao-tao.canh-bao-hoc-vu.show', $ls) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal xử lý -->
<div class="modal fade" id="xuLyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dao-tao.canh-bao-hoc-vu.xu-ly', $canhBao) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Xử lý Cảnh báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="trang_thai_moi" class="form-label required">Cập nhật trạng thái</label>
                        <select name="trang_thai" id="trang_thai_moi" class="form-select" required>
                            <option value="dang_xu_ly" {{ $canhBao->trang_thai == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="da_xu_ly" {{ $canhBao->trang_thai == 'da_xu_ly' ? 'selected' : '' }}>Đã xử lý</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ket_qua_xu_ly" class="form-label">Kết quả xử lý</label>
                        <textarea name="ket_qua_xu_ly" id="ket_qua_xu_ly" rows="4" class="form-control" placeholder="Mô tả kết quả xử lý...">{{ $canhBao->ket_qua_xu_ly }}</textarea>
                        <small class="form-text text-muted">Ghi rõ các biện pháp đã thực hiện và kết quả đạt được</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu kết quả
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa cảnh báo này?\n\nThao tác này không thể hoàn tác!')) {
        document.getElementById('deleteForm').submit();
    }
}

function guiEmailCanhBao() {
    if (!confirm('Gửi lại email cảnh báo cho sinh viên {{ $canhBao->sinhVien->ho_ten }}?')) {
        return;
    }

    fetch('{{ route("dao-tao.canh-bao-hoc-vu.gui-email", $canhBao) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã gửi email thành công!');
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Không thể gửi email. Vui lòng thử lại sau.');
    });
}
</script>
@endpush

<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endsection
