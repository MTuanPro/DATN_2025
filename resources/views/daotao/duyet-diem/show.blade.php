@extends('layouts.layout-daotao')

@section('title', 'Chi tiết bảng điểm')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết bảng điểm</h3>
                <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dao-tao.duyet-diem.index') }}">Duyệt điểm</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Thông tin lớp -->
    <section class="section">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin lớp học phần</h5>
                <div>
                    <a href="{{ route('dao-tao.duyet-diem.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
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
                                <td><span class="badge bg-info">{{ $thongKe['tong_sv'] }} SV</span></td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if ($lopHocPhan->trang_thai_lop === 'da_khoa_diem')
                                        <span class="badge bg-warning"><i class="bi bi-clock"></i> Chờ duyệt</span>
                                    @elseif($lopHocPhan->trang_thai_lop === 'da_duyet_diem')
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                                    @else
                                        <span class="badge bg-info">{{ $lopHocPhan->ten_trang_thai }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Gửi điểm lần 1 (giữa kỳ):</th>
                                <td>
                                    @php
                                        $trangThaiLan1 = $lopHocPhan->trang_thai_gui_diem_lan_1 ?? 'chua_gui';
                                    @endphp
                                    @if($trangThaiLan1 === 'chua_gui')
                                        <span class="badge bg-secondary">Chưa gửi</span>
                                    @elseif($trangThaiLan1 === 'da_gui')
                                        <span class="badge bg-warning">Đã gửi - Chờ duyệt</span>
                                    @elseif($trangThaiLan1 === 'da_duyet')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @elseif($trangThaiLan1 === 'da_tra_ve')
                                        <span class="badge bg-danger">Đã trả về</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Gửi điểm lần 2 (cuối kỳ):</th>
                                <td>
                                    @php
                                        $trangThaiLan2 = $lopHocPhan->trang_thai_gui_diem_lan_2 ?? 'chua_gui';
                                    @endphp
                                    @if($trangThaiLan2 === 'chua_gui')
                                        <span class="badge bg-secondary">Chưa gửi</span>
                                    @elseif($trangThaiLan2 === 'da_gui')
                                        <span class="badge bg-warning">Đã gửi - Chờ duyệt</span>
                                    @elseif($trangThaiLan2 === 'da_duyet')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @elseif($trangThaiLan2 === 'da_tra_ve')
                                        <span class="badge bg-danger">Đã trả về</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Thống kê -->
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Tổng SV</h6>
                                <h3 class="mb-0">{{ $thongKe['tong_sv'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV có điểm</h6>
                                <h3 class="mb-0">{{ $thongKe['sv_co_diem'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV qua môn</h6>
                                <h3 class="mb-0">{{ $thongKe['sv_qua_mon'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">SV không qua</h6>
                                <h3 class="mb-0">{{ $thongKe['sv_khong_qua_mon'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                @if($thongKe['diem_tb'])
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info text-center mb-0">
                            <strong>Điểm trung bình lớp: {{ number_format($thongKe['diem_tb'], 2) }}</strong>
                        </div>
                    </div>
                </div>
                @endif
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

    <!-- Bảng điểm -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-table"></i> Bảng điểm sinh viên</h5>
                @if($choPhepSuaDiem && $daDuyetCa2Lan)
                <div>
                    <span class="badge bg-info me-2"><i class="bi bi-pencil"></i> Chế độ sửa điểm (phúc khảo)</span>
                    <button type="button" class="btn btn-sm btn-success" onclick="luuTatCaDiemDaoTao()">
                        <i class="bi bi-save"></i> Lưu tất cả điểm
                    </button>
                </div>
                @elseif($lopHocPhan->trang_thai_lop === 'da_khoa_diem')
                @php
                    // Ưu tiên sử dụng lan_gui_diem từ database
                    $lanDangChoDuyet = $lopHocPhan->lan_gui_diem;
                    
                    if (!$lanDangChoDuyet) {
                        // Nếu chưa có, xác định dựa trên trạng thái
                        $trangThaiLan1 = $lopHocPhan->trang_thai_gui_diem_lan_1 ?? 'chua_gui';
                        $trangThaiLan2 = $lopHocPhan->trang_thai_gui_diem_lan_2 ?? 'chua_gui';
                        
                        // 1. Nếu lần 1 đã gửi và lần 2 chưa gửi → đang chờ duyệt lần 1
                        if ($trangThaiLan1 === 'da_gui' && ($trangThaiLan2 === 'chua_gui' || $trangThaiLan2 === 'da_tra_ve' || !$trangThaiLan2)) {
                            $lanDangChoDuyet = 1;
                        }
                        // 2. Nếu lần 1 đã duyệt và lần 2 đã gửi → đang chờ duyệt lần 2
                        elseif ($trangThaiLan1 === 'da_duyet' && $trangThaiLan2 === 'da_gui') {
                            $lanDangChoDuyet = 2;
                        }
                        // 3. Nếu cả 2 đều đã gửi → ưu tiên duyệt lần 2
                        elseif ($trangThaiLan1 === 'da_gui' && $trangThaiLan2 === 'da_gui') {
                            $lanDangChoDuyet = 2;
                        }
                        // 4. Nếu chỉ lần 2 đã gửi
                        elseif ($trangThaiLan2 === 'da_gui') {
                            $lanDangChoDuyet = 2;
                        }
                        // 5. Mặc định là lần 1 nếu lần 1 đã gửi
                        elseif ($trangThaiLan1 === 'da_gui') {
                            $lanDangChoDuyet = 1;
                        } else {
                            $lanDangChoDuyet = 1; // Mặc định
                        }
                    }
                    
                    $lanText = $lanDangChoDuyet == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
                @endphp
                <div>
                    <span class="badge bg-info me-2">Đang chờ duyệt {{ $lanText }}</span>
                    <button type="button" class="btn btn-sm btn-success" onclick="duyetDiem('phe_duyet')">
                        <i class="bi bi-check-circle"></i> Phê duyệt {{ $lanText }}
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="duyetDiem('tra_ve')">
                        <i class="bi bi-arrow-counterclockwise"></i> Trả về {{ $lanText }}
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                @if ($cauHinhs->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Chưa có cấu hình đầu điểm cho lớp này.
                    </div>
                @elseif($sinhViens->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="bi bi-inbox"></i> Chưa có sinh viên nào trong lớp học phần.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
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
                                    <th width="80" class="text-center" rowspan="2">Kết quả</th>
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
                                @foreach($sinhViens as $index => $lhpsv)
                                @php
                                    $nhapDiemSV = $nhapDiems->get($lhpsv->id) ?? collect();
                                    $ketQua = $lhpsv->ketQuaHocTap;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $lhpsv->sinhVien->ma_sinh_vien }}</td>
                                    <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                                    @foreach($cauHinhs as $cauHinh)
                                        @for($cot = 1; $cot <= $cauHinh->so_cot; $cot++)
                                            @php
                                                $diem = $nhapDiemSV->where('cau_hinh_id', $cauHinh->id)
                                                    ->where('cot_diem', $cot)
                                                    ->first();
                                            @endphp
                                            <td class="text-center">
                                                @if($choPhepSuaDiem && $daDuyetCa2Lan)
                                                    <input type="number" 
                                                        class="form-control form-control-sm text-center diem-input-daotao" 
                                                        min="0" max="10" step="0.01"
                                                        data-lhpsv-id="{{ $lhpsv->id }}"
                                                        data-cau-hinh-id="{{ $cauHinh->id }}"
                                                        data-cot-diem="{{ $cot }}"
                                                        value="{{ $diem ? $diem->diem_so : '' }}"
                                                        placeholder="-"
                                                        style="width: 70px; margin: 0 auto;">
                                                @else
                                                    @if($diem)
                                                        <strong>{{ number_format($diem->diem_so, 1) }}</strong>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endfor
                                    @endforeach
                                    <td class="text-center">
                                        @if($ketQua && $ketQua->diem_he_10 !== null)
                                            <strong class="text-primary">{{ number_format($ketQua->diem_he_10, 2) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ketQua)
                                            @if($ketQua->qua_mon)
                                                <span class="badge bg-success">Qua môn</span>
                                            @else
                                                <span class="badge bg-danger">Không qua</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function duyetDiem(hanhDong) {
        // Xác định lần gửi điểm đang duyệt
        const trangThaiLan1 = '{{ $lopHocPhan->trang_thai_gui_diem_lan_1 ?? "chua_gui" }}';
        const trangThaiLan2 = '{{ $lopHocPhan->trang_thai_gui_diem_lan_2 ?? "chua_gui" }}';
        const lanGuiDiem = {{ $lopHocPhan->lan_gui_diem ?? 'null' }};
        
        let lanGui = 1;
        
        // Ưu tiên sử dụng lan_gui_diem từ database nếu có
        if (lanGuiDiem !== null) {
            lanGui = lanGuiDiem;
        } else {
            // Logic xác định lần gửi điểm dựa trên trạng thái:
            // 1. Nếu lần 1 đã gửi (da_gui) và lần 2 chưa gửi → đang chờ duyệt lần 1
            if (trangThaiLan1 === 'da_gui' && (trangThaiLan2 === 'chua_gui' || trangThaiLan2 === 'da_tra_ve' || !trangThaiLan2)) {
                lanGui = 1;
            }
            // 2. Nếu lần 1 đã duyệt và lần 2 đã gửi → đang chờ duyệt lần 2
            else if (trangThaiLan1 === 'da_duyet' && trangThaiLan2 === 'da_gui') {
                lanGui = 2;
            }
            // 3. Nếu cả 2 đều đã gửi → ưu tiên duyệt lần 2
            else if (trangThaiLan1 === 'da_gui' && trangThaiLan2 === 'da_gui') {
                lanGui = 2;
            }
            // 4. Nếu chỉ lần 2 đã gửi (trường hợp đặc biệt)
            else if (trangThaiLan2 === 'da_gui') {
                lanGui = 2;
            }
            // 5. Mặc định là lần 1 nếu lần 1 đã gửi
            else if (trangThaiLan1 === 'da_gui') {
                lanGui = 1;
            }
        }
        
        const lanGuiText = lanGui == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';
        
        if (hanhDong === 'phe_duyet') {
            Swal.fire({
                title: 'Xác nhận phê duyệt điểm ' + lanGuiText,
                text: lanGui == 2 
                    ? 'Bạn có chắc muốn phê duyệt điểm ' + lanGuiText + '? Điểm sẽ được công bố cho sinh viên và không thể sửa nữa.'
                    : 'Bạn có chắc muốn phê duyệt điểm ' + lanGuiText + '? Sau khi duyệt, giảng viên vẫn có thể sửa để chuẩn bị gửi lần 2.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, phê duyệt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    guiDuyetDiem(hanhDong, '', lanGui);
                }
            });
        } else {
            Swal.fire({
                title: 'Trả về điểm',
                html: '<input id="lyDoTraVe" class="swal2-input" placeholder="Nhập lý do trả về">',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Trả về',
                cancelButtonText: 'Hủy',
                preConfirm: () => {
                    const lyDo = document.getElementById('lyDoTraVe').value;
                    if (!lyDo) {
                        Swal.showValidationMessage('Vui lòng nhập lý do trả về');
                        return false;
                    }
                    return { ly_do_tra_ve: lyDo };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    guiDuyetDiem(hanhDong, result.value.ly_do_tra_ve, lanGui);
                }
            });
        }
    }

    function guiDuyetDiem(hanhDong, lyDoTraVe = '', lanGui = null) {
        const data = {
            hanh_dong: hanhDong,
            _token: '{{ csrf_token() }}'
        };

        if (hanhDong === 'tra_ve' && lyDoTraVe) {
            data.ly_do_tra_ve = lyDoTraVe;
        }
        
        if (lanGui) {
            data.lan_gui = lanGui;
        }

        fetch('{{ route("dao-tao.duyet-diem.duyet", $lopHocPhan->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Thành công!', data.message, 'success')
                    .then(() => {
                        window.location.href = '{{ route("dao-tao.duyet-diem.index") }}';
                    });
            } else {
                Swal.fire('Lỗi!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi duyệt điểm', 'error');
        });
    }

    @if($choPhepSuaDiem && $daDuyetCa2Lan)
    // Xử lý sửa điểm cho đào tạo
    $(document).on('change', '.diem-input-daotao', function() {
        const input = $(this);
        const lhpsvId = input.data('lhpsv-id');
        const cauHinhId = input.data('cau-hinh-id');
        const cotDiem = input.data('cot-diem');
        const diemSo = parseFloat(input.val()) || null;

        // Validate
        if (diemSo !== null && (diemSo < 0 || diemSo > 10)) {
            Swal.fire('Lỗi!', 'Điểm phải từ 0 đến 10', 'error');
            input.focus();
            return;
        }

        // Gửi request cập nhật điểm
        fetch('{{ route("dao-tao.duyet-diem.sua-diem", $lopHocPhan->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                lop_hoc_phan_sinh_vien_id: lhpsvId,
                cau_hinh_id: cauHinhId,
                cot_diem: cotDiem,
                diem_so: diemSo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật điểm tổng kết nếu có
                if (data.diem_tong_ket !== undefined) {
                    const row = input.closest('tr');
                    const diemTkCell = row.find('td').eq(-2); // Cột điểm TK
                    if (data.diem_tong_ket !== null) {
                        diemTkCell.html('<strong class="text-primary">' + parseFloat(data.diem_tong_ket).toFixed(2) + '</strong>');
                    } else {
                        diemTkCell.html('<span class="text-muted">-</span>');
                    }
                }
            } else {
                Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra khi cập nhật điểm', 'error');
                // Revert giá trị
                input.val(input.data('old-value') || '');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi cập nhật điểm', 'error');
            input.val(input.data('old-value') || '');
        });
    });

    // Lưu giá trị cũ khi focus
    $(document).on('focus', '.diem-input-daotao', function() {
        $(this).data('old-value', $(this).val());
    });

    // Lưu tất cả điểm
    function luuTatCaDiemDaoTao() {
        const inputs = $('.diem-input-daotao');
        let hasChanges = false;
        const diemData = [];

        inputs.each(function() {
            const input = $(this);
            const lhpsvId = input.data('lhpsv-id');
            const cauHinhId = input.data('cau-hinh-id');
            const cotDiem = input.data('cot-diem');
            const inputValue = input.val().trim();
            
            // Chỉ xử lý nếu ô có giá trị (không trống)
            if (inputValue === '') {
                return true; // Continue to next iteration
            }
            
            const diemSo = parseFloat(inputValue);

            if (isNaN(diemSo) || diemSo < 0 || diemSo > 10) {
                Swal.fire('Lỗi!', 'Có điểm không hợp lệ (phải từ 0 đến 10)', 'error');
                return false;
            }

            diemData.push({
                lop_hoc_phan_sinh_vien_id: lhpsvId,
                cau_hinh_id: cauHinhId,
                cot_diem: cotDiem,
                diem_so: diemSo
            });
        });

        if (diemData.length === 0) {
            Swal.fire('Thông báo', 'Không có điểm nào để lưu', 'info');
            return;
        }

        Swal.fire({
            title: 'Xác nhận',
            text: 'Bạn có chắc muốn lưu tất cả điểm đã sửa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Có, lưu tất cả',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("dao-tao.duyet-diem.luu-tat-ca-diem", $lopHocPhan->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ diem_data: diemData })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message || 'Đã lưu tất cả điểm thành công', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Lỗi!', data.message || 'Có lỗi xảy ra khi lưu điểm', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Lỗi!', 'Có lỗi xảy ra khi lưu điểm', 'error');
                });
            }
        });
    }
    @endif
</script>
@endpush
@endsection

