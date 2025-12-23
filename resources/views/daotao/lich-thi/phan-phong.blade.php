@extends('layouts.layout-daotao')

@section('title', 'Phân Phòng Thi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Phân Phòng Thi</h4>
            <p class="text-muted mb-0">
                {{ $lichThi->lopHocPhan->monHoc->ten_mon }} - 
                {{ $lichThi->lopHocPhan->ma_lop }}
            </p>
        </div>
        <a href="{{ route('dao-tao.lich-thi.show', $lichThi) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông tin lịch thi -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($lichThi->ngay_thi)->format('d/m/Y') }} 
                        - {{ \Carbon\Carbon::parse($lichThi->gio_bat_dau)->format('H:i') }} đến 
                        {{ \Carbon\Carbon::parse($lichThi->gio_ket_thuc)->format('H:i') }}</p>
                    <p><strong>Phòng mặc định:</strong> {{ $lichThi->phongThi->ten_phong ?? 'Chưa chọn' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Hình thức:</strong> 
                        <span class="badge {{ $lichThi->hinh_thuc === 'offline' ? 'bg-primary' : 'bg-success' }}">
                            {{ $lichThi->hinh_thuc === 'offline' ? 'Thi tại trường' : 'Thi trực tuyến' }}
                        </span>
                    </p>
                    <p><strong>Tổng số sinh viên:</strong> {{ $lichThi->lichThiSinhViens->count() }} sinh viên</p>
                </div>
                <div class="col-md-4">
                    @php
                        $phongDangDung = $lichThi->lichThiSinhViens->groupBy('phong_thi_id')->filter(fn($items, $key) => $key !== null);
                    @endphp
                    <p><strong>Số phòng đang dùng:</strong> 
                        <span class="badge bg-info">{{ $phongDangDung->count() }} phòng</span>
                    </p>
                    @if($phongDangDung->count() > 0)
                        <small class="text-muted">
                            @foreach($phongDangDung as $phongId => $items)
                                @php $phong = $items->first()->phongThi; @endphp
                                <div>• {{ $phong->ten_phong ?? 'N/A' }}: {{ $items->count() }} SV</div>
                            @endforeach
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form chọn phòng -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Chuyển sinh viên sang phòng khác</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dao-tao.lich-thi.cap-nhat-phong', $lichThi) }}" method="POST" id="formPhanPhong">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Chọn phòng thi đích:</label>
                        <select name="phong_thi_id" class="form-select" required id="selectPhong">
                            <option value="">-- Chọn phòng --</option>
                            
                            @php
                                $phongDangDung = $lichThi->lichThiSinhViens->pluck('phong_thi_id')->unique()->filter();
                            @endphp
                            
                            @if($phongHocs->isNotEmpty())
                                <optgroup label="📍 Phòng đang sử dụng cho lịch thi này">
                                    @foreach($phongHocs as $phong)
                                        @php
                                            $isDangDung = $phongDangDung->contains($phong->id);
                                            $isPhongMacDinh = $phong->id == $lichThi->phong_thi_id;
                                            $soSinhVien = $lichThi->lichThiSinhViens->where('phong_thi_id', $phong->id)->count();
                                            $conTrong = $phong->suc_chua - $soSinhVien;
                                        @endphp
                                        <option value="{{ $phong->id }}">
                                            {{ $phong->ten_phong }}
                                            @if($isPhongMacDinh && $isDangDung)
                                                - Mặc định ({{ $soSinhVien }}/{{ $phong->suc_chua }}, còn {{ $conTrong }})
                                            @elseif($isPhongMacDinh)
                                                - Mặc định (Trống {{ $phong->suc_chua }})
                                            @elseif($isDangDung)
                                                - Đang có {{ $soSinhVien }}/{{ $phong->suc_chua }} (còn {{ $conTrong }})
                                            @else
                                                - Trống ({{ $phong->suc_chua }})
                                            @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            
                            @if(isset($phongTrong) && $phongTrong->isNotEmpty())
                                <optgroup label="➕ Phòng trống khác ({{ $phongTrong->count() }})">
                                    @foreach($phongTrong->take(10) as $phong)
                                        <option value="{{ $phong->id }}">
                                            {{ $phong->ten_phong }} - Trống ({{ $phong->suc_chua }} chỗ)
                                        </option>
                                    @endforeach
                                    @if($phongTrong->count() > 10)
                                        <option disabled>... và {{ $phongTrong->count() - 10 }} phòng khác</option>
                                    @endif
                                </optgroup>
                            @endif
                        </select>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Nhóm 1: Phòng đang dùng cho lịch thi này | 
                            Nhóm 2: Phòng trống (không trùng giờ)
                        </small>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100" id="btnChuyenPhong" disabled>
                            <i class="bi bi-arrow-right-circle"></i> Chuyển sinh viên đã chọn
                        </button>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    <strong>Hướng dẫn:</strong> Chọn sinh viên ở bảng dưới, sau đó chọn phòng đích và nhấn "Chuyển sinh viên"
                </small>
            </form>
        </div>
    </div>

    <!-- Danh sách sinh viên theo phòng -->
    @if($sinhVienTheoPhong->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Chưa có sinh viên nào được phân công cho lịch thi này.
        </div>
    @else
        @foreach($sinhVienTheoPhong as $phongId => $sinhViens)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-door-open"></i> 
                        @if($phongId)
                            {{ $sinhViens->first()->phongThi->ten_phong }}
                            <span class="badge bg-secondary">{{ $sinhViens->count() }} sinh viên</span>
                        @else
                            Chưa phân phòng
                            <span class="badge bg-warning text-dark">{{ $sinhViens->count() }} sinh viên</span>
                        @endif
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="chonTatCaPhong({{ $phongId ?? 'null' }})">
                        <i class="bi bi-check-all"></i> Chọn tất cả
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input checkbox-all" 
                                               data-phong="{{ $phongId ?? 'null' }}">
                                    </th>
                                    <th>STT</th>
                                    <th>Số báo danh</th>
                                    <th>Mã sinh viên</th>
                                    <th>Họ tên</th>
                                    <th>Lớp hành chính</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sinhViens as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input checkbox-sv" 
                                                   name="sinh_vien_ids[]" 
                                                   value="{{ $item->sinh_vien_id }}"
                                                   data-phong="{{ $phongId ?? 'null' }}"
                                                   form="formPhanPhong">
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->so_bao_danh }}</strong></td>
                                        <td>{{ $item->sinhVien->ma_sinh_vien }}</td>
                                        <td>{{ $item->sinhVien->ho_ten }}</td>
                                        <td>{{ $item->sinhVien->nganh->ten_nganh ?? 'N/A' ?? 'N/A' }}</td>
                                        <td>
                                            @if($item->trang_thai === 'du_thi')
                                                <span class="badge bg-success">Dự thi</span>
                                            @elseif($item->trang_thai === 'vang_co_phep')
                                                <span class="badge bg-warning text-dark">Vắng có phép</span>
                                            @else
                                                <span class="badge bg-danger">Vắng không phép</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Link xem danh sách chi tiết -->
    <div class="text-center mt-4">
        <a href="{{ route('dao-tao.lich-thi.danh-sach-sinh-vien', $lichThi) }}" 
           class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-file-earmark-text"></i> Xem danh sách chi tiết (In/Xuất)
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxAll = document.querySelectorAll('.checkbox-all');
    const checkboxSinhVien = document.querySelectorAll('.checkbox-sv');
    const btnChuyenPhong = document.getElementById('btnChuyenPhong');
    const form = document.getElementById('formPhanPhong');

    // Xử lý chọn tất cả theo phòng
    checkboxAll.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const phongId = this.dataset.phong;
            const checkboxes = document.querySelectorAll(`.checkbox-sv[data-phong="${phongId}"]`);
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateButtonState();
        });
    });

    // Xử lý khi chọn từng sinh viên
    checkboxSinhVien.forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonState);
    });

    // Cập nhật trạng thái nút chuyển phòng
    function updateButtonState() {
        const anyChecked = Array.from(checkboxSinhVien).some(cb => cb.checked);
        btnChuyenPhong.disabled = !anyChecked;
    }

    // Validate trước khi submit
    form.addEventListener('submit', function(e) {
        const checkedCount = Array.from(checkboxSinhVien).filter(cb => cb.checked).length;
        const phongSelect = this.querySelector('[name="phong_thi_id"]');
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một sinh viên!');
            return;
        }

        if (!phongSelect.value) {
            e.preventDefault();
            alert('Vui lòng chọn phòng thi đích!');
            return;
        }

        if (!confirm(`Bạn có chắc chắn muốn chuyển ${checkedCount} sinh viên sang phòng đã chọn?`)) {
            e.preventDefault();
        }
    });
});

// Hàm chọn tất cả theo phòng (gọi từ button)
function chonTatCaPhong(phongId) {
    const checkbox = document.querySelector(`.checkbox-all[data-phong="${phongId}"]`);
    if (checkbox) {
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));
    }
}
</script>
@endpush
@endsection
