@extends('layouts.layout-giangvien')

@section('title', 'Nhập điểm')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Nhập điểm</h3>
                <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.nhap-diem.index') }}">Nhập điểm</a></li>
                        <li class="breadcrumb-item active">Nhập điểm</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Thông tin lớp -->
    <section class="section">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mã lớp:</th>
                                <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td>{{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}</td>
                            </tr>
                            <tr>
                                <th>Số tín chỉ:</th>
                                <td>{{ $lopHocPhan->monHoc->so_tin_chi }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Học kỳ:</th>
                                <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</td>
                            </tr>
                            <tr>
                                <th>Số sinh viên:</th>
                                <td><span class="badge bg-info">{{ $sinhViens->count() }} SV</span></td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if ($daKhoaDiem)
                                        <span class="badge bg-danger"><i class="bi bi-lock"></i> Đã khóa điểm</span>
                                    @else
                                        <span class="badge bg-success"><i class="bi bi-unlock"></i> Đang mở</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cấu hình đầu điểm -->
    @if (!$cauHinhs->isEmpty())
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Cấu hình đầu điểm</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($cauHinhs as $cauHinh)
                        <div class="col-md-4 mb-2">
                            <div class="alert alert-info mb-0">
                                <strong>{{ $cauHinh->ten_dau_diem }}:</strong> {{ $cauHinh->ty_le }}%
                                @if($cauHinh->so_cot > 1)
                                    <small>({{ $cauHinh->so_cot }} cột)</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Form nhập điểm -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Nhập điểm sinh viên</h5>
                @if(!$daKhoaDiem)
                <div>
                    <button type="button" class="btn btn-sm btn-success" onclick="luuTatCaDiem()">
                        <i class="bi bi-save"></i> Lưu tất cả
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                @if ($cauHinhs->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có cấu hình đầu điểm cho lớp này. Vui lòng liên hệ phòng đào tạo.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableDiem">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" rowspan="2">STT</th>
                                    <th width="100" rowspan="2">MSSV</th>
                                    <th width="200" rowspan="2">Họ tên</th>
                                    @foreach($cauHinhs as $cauHinh)
                                        <th class="text-center" colspan="{{ $cauHinh->so_cot }}">
                                            {{ $cauHinh->ten_dau_diem }}<br>
                                            <small class="text-muted">({{ $cauHinh->ty_le }}%)</small>
                                        </th>
                                    @endforeach
                                    <th width="80" class="text-center" rowspan="2">Điểm TK</th>
                                    @if(!$daKhoaDiem)
                                    <th width="100" class="text-center" rowspan="2">Thao tác</th>
                                    @endif
                                </tr>
                                <tr>
                                    @foreach($cauHinhs as $cauHinh)
                                        @for($cot = 1; $cot <= $cauHinh->so_cot; $cot++)
                                            <th width="70" class="text-center">
                                                @if($cauHinh->so_cot > 1)
                                                    Cột {{ $cot }}
                                                @else
                                                    Điểm
                                                @endif
                                            </th>
                                        @endfor
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sinhViens as $index => $lhpsv)
                                @php
                                    // Lấy tất cả điểm đã nhập của sinh viên này
                                    $diemDaNhap = \App\Models\NhapDiem::where('lop_hoc_phan_sinh_vien_id', $lhpsv->id)->get();
                                    $diemMap = [];
                                    foreach($diemDaNhap as $diem) {
                                        $key = $diem->cau_hinh_id . '_' . $diem->cot_diem;
                                        $diemMap[$key] = $diem->diem_so;
                                    }
                                    
                                    // Lấy điểm tổng kết
                                    $ketQua = $lhpsv->ketQuaHocTap;
                                @endphp
                                <tr data-sv-id="{{ $lhpsv->id }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><strong>{{ $lhpsv->sinhVien->ma_sinh_vien }}</strong></td>
                                    <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                    
                                    @foreach($cauHinhs as $cauHinh)
                                        @for($cot = 1; $cot <= $cauHinh->so_cot; $cot++)
                                            @php
                                                $key = $cauHinh->id . '_' . $cot;
                                                $value = $diemMap[$key] ?? '';
                                            @endphp
                                            <td class="text-center">
                                                @if($daKhoaDiem)
                                                    {{ $value !== '' ? number_format($value, 2) : '-' }}
                                                @else
                                                    <input type="number" 
                                                        class="form-control form-control-sm text-center diem-input" 
                                                        min="0" max="10" step="0.01"
                                                        data-sv-id="{{ $lhpsv->id }}"
                                                        data-cau-hinh-id="{{ $cauHinh->id }}"
                                                        data-cot-diem="{{ $cot }}"
                                                        value="{{ $value }}"
                                                        placeholder="-">
                                                @endif
                                            </td>
                                        @endfor
                                    @endforeach
                                    
                                    <td class="text-center">
                                        <strong class="diem-tk-{{ $lhpsv->id }}">
                                            @if($ketQua && $ketQua->diem_he_10)
                                                {{ number_format($ketQua->diem_he_10, 2) }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </td>
                                    
                                    @if(!$daKhoaDiem)
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary btn-luu-sv" 
                                            data-sv-id="{{ $lhpsv->id }}">
                                            <i class="bi bi-save"></i> Lưu
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('giangvien.nhap-diem.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        @if(!$daKhoaDiem)
                        <button type="button" class="btn btn-success" onclick="luuTatCaDiem()">
                            <i class="bi bi-save-fill"></i> Lưu tất cả điểm
                        </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // Lưu điểm từng sinh viên
    document.querySelectorAll('.btn-luu-sv').forEach(btn => {
        btn.addEventListener('click', function() {
            const svId = this.dataset.svId;
            luuDiemSinhVien(svId);
        });
    });

    function luuDiemSinhVien(svId) {
        const row = document.querySelector(`tr[data-sv-id="${svId}"]`);
        const inputs = row.querySelectorAll('.diem-input');
        
        if (inputs.length === 0) return;
        
        // Tạo mảng promise để lưu từng điểm
        const promises = [];
        
        inputs.forEach(input => {
            const value = input.value;
            if (value !== '' && value !== null) {
                const data = {
                    lop_hoc_phan_sinh_vien_id: parseInt(svId),
                    cau_hinh_id: parseInt(input.dataset.cauHinhId),
                    cot_diem: parseInt(input.dataset.cotDiem),
                    diem_so: parseFloat(value)
                };
                
                promises.push(
                    fetch('{{ route("giangvien.nhap-diem.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                );
            }
        });
        
        if (promises.length === 0) {
            Swal.fire('Thông báo', 'Chưa có điểm nào để lưu', 'info');
            return;
        }
        
        // Hiển thị loading
        Swal.fire({
            title: 'Đang lưu điểm...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        Promise.all(promises)
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                const hasError = results.some(r => !r.success);
                
                if (hasError) {
                    const errorMsg = results.find(r => !r.success)?.message || 'Có lỗi xảy ra';
                    Swal.fire('Lỗi!', errorMsg, 'error');
                } else {
                    Swal.fire('Thành công!', 'Đã lưu điểm sinh viên', 'success')
                        .then(() => location.reload());
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi lưu điểm', 'error');
            });
    }

    function luuTatCaDiem() {
        const rows = document.querySelectorAll('#tableDiem tbody tr');
        const promises = [];
        
        rows.forEach(row => {
            const inputs = row.querySelectorAll('.diem-input');
            
            inputs.forEach(input => {
                const value = input.value;
                if (value !== '' && value !== null) {
                    const data = {
                        lop_hoc_phan_sinh_vien_id: parseInt(input.dataset.svId),
                        cau_hinh_id: parseInt(input.dataset.cauHinhId),
                        cot_diem: parseInt(input.dataset.cotDiem),
                        diem_so: parseFloat(value)
                    };
                    
                    promises.push(
                        fetch('{{ route("giangvien.nhap-diem.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(data)
                        })
                    );
                }
            });
        });
        
        if (promises.length === 0) {
            Swal.fire('Thông báo', 'Chưa có điểm nào để lưu', 'info');
            return;
        }
        
        // Hiển thị loading
        Swal.fire({
            title: 'Đang lưu điểm...',
            html: `Đang lưu ${promises.length} điểm...`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        Promise.all(promises)
            .then(responses => Promise.all(responses.map(r => r.json())))
            .then(results => {
                const hasError = results.some(r => !r.success);
                const successCount = results.filter(r => r.success).length;
                
                if (hasError) {
                    const errorMsg = results.find(r => !r.success)?.message || 'Có lỗi xảy ra';
                    Swal.fire('Lỗi!', `Đã lưu ${successCount}/${results.length} điểm. Lỗi: ${errorMsg}`, 'error');
                } else {
                    Swal.fire('Thành công!', `Đã lưu tất cả ${successCount} điểm`, 'success')
                        .then(() => location.reload());
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi lưu điểm', 'error');
            });
    }
</script>
@endpush
@endsection
