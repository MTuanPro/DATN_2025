@extends('layouts.layout-giangvien')

@section('title', 'Danh sách sinh viên đủ điều kiện thi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách sinh viên đủ điều kiện thi</h3>
                    <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('giangvien.nhap-diem.index') }}">Nhập điểm</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('giangvien.nhap-diem.show', $lopHocPhan->id) }}">{{ $lopHocPhan->ma_lop_hp }}</a>
                            </li>
                            <li class="breadcrumb-item active">Danh sách thi</li>
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
                                    <th>Tổng sinh viên:</th>
                                    <td><span
                                            class="badge bg-info">{{ count($danhSachDuDieuKien) + count($danhSachKhongDuDieuKien) }}
                                            SV</span></td>
                                </tr>
                                <tr>
                                    <th>Đủ điều kiện thi:</th>
                                    <td><span class="badge bg-success">{{ count($danhSachDuDieuKien) }} SV</span></td>
                                </tr>
                                <tr>
                                    <th>Không đủ điều kiện:</th>
                                    <td><span class="badge bg-danger">{{ count($danhSachKhongDuDieuKien) }} SV</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Điều kiện đi thi -->
        <section class="section">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Điều kiện đủ để đi thi</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <ul class="mb-0">
                            <li>Điểm trung bình các đầu điểm đã nhập (không tính thi cuối kỳ) <strong>≥ 5.0</strong></li>
                            <li>Tỷ lệ vắng <strong>≤ 20%</strong> số buổi đã điểm danh</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Danh sách đủ điều kiện -->
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-success"><i class="bi bi-check-circle"></i> Sinh viên đủ điều kiện thi
                        ({{ count($danhSachDuDieuKien) }})</h5>
                    <div>
                        <button onclick="exportExcel('du-dieu-kien')" class="btn btn-sm btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                        </button>
                        <button onclick="printList('du-dieu-kien')" class="btn btn-sm btn-primary">
                            <i class="bi bi-printer"></i> In danh sách
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if (count($danhSachDuDieuKien) > 0)

                        <!-- Form chọn lịch thi và thêm sinh viên -->
                        <div class="alert alert-info mb-3">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Chọn lịch thi để thêm sinh viên:</strong></label>
                                    <select id="lichThiSelect" class="form-select">
                                        <option value="">-- Chọn lịch thi --</option>
                                        @foreach ($lichThis as $lichThi)
                                            <option value="{{ $lichThi->id }}">
                                                {{ $lichThi->loai_thi }} -
                                                {{ \Carbon\Carbon::parse($lichThi->ngay_thi)->format('d/m/Y') }} -
                                                {{ $lichThi->caHoc ? $lichThi->caHoc->ten_ca : 'N/A' }} -
                                                Phòng: {{ $lichThi->phongThi ? $lichThi->phongThi->ten_phong : 'N/A' }}
                                                ({{ $lichThi->so_sinh_vien_du_thi ?? 0 }} SV)
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (count($lichThis) == 0)
                                        <small class="text-danger">Chưa có lịch thi nào cho lớp này.</small>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" onclick="themTatCaVaoLichThi()">
                                        <i class="bi bi-plus-circle"></i> Thêm tất cả ({{ count($danhSachDuDieuKien) }} SV)
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning" onclick="themDaChonVaoLichThi()">
                                        <i class="bi bi-check2-square"></i> Thêm đã chọn
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="table-du-dieu-kien">
                                <thead class="table-success">
                                    <tr>
                                        <th width="50" class="text-center">
                                            <input type="checkbox" id="checkAll" onclick="toggleCheckAll()">
                                        </th>
                                        <th width="50" class="text-center">STT</th>
                                        <th width="100">MSSV</th>
                                        <th width="200">Họ tên</th>
                                        <th width="100" class="text-center">Điểm TB<br><small>(chưa tính thi)</small>
                                        </th>
                                        <th width="100" class="text-center">Số buổi vắng</th>
                                        <th width="100" class="text-center">Tỷ lệ vắng</th>
                                        <th class="text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($danhSachDuDieuKien as $index => $item)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="sv-checkbox"
                                                    value="{{ $item['lhpsv']->sinh_vien_id }}">
                                            </td>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $item['lhpsv']->sinhVien->ma_sinh_vien }}</strong></td>
                                            <td>{{ $item['lhpsv']->sinhVien->ho_ten }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success">{{ number_format($item['diem_trung_binh'], 2) }}</span>
                                            </td>
                                            <td class="text-center">{{ $item['so_buoi_vang'] }} / {{ $item['tong_buoi'] }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success">{{ number_format($item['ty_le_vang'], 2) }}%</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đủ điều
                                                    kiện</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Chưa có sinh viên nào đủ điều kiện thi.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Danh sách không đủ điều kiện -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-danger"><i class="bi bi-x-circle"></i> Sinh viên không đủ điều kiện thi
                        ({{ count($danhSachKhongDuDieuKien) }})</h5>
                </div>
                <div class="card-body">
                    @if (count($danhSachKhongDuDieuKien) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="table-khong-du-dieu-kien">
                                <thead class="table-danger">
                                    <tr>
                                        <th width="50" class="text-center">STT</th>
                                        <th width="100">MSSV</th>
                                        <th width="200">Họ tên</th>
                                        <th width="100" class="text-center">Điểm TB<br><small>(chưa tính thi)</small>
                                        </th>
                                        <th width="100" class="text-center">Số buổi vắng</th>
                                        <th width="100" class="text-center">Tỷ lệ vắng</th>
                                        <th>Lý do</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($danhSachKhongDuDieuKien as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $item['lhpsv']->sinhVien->ma_sinh_vien }}</strong></td>
                                            <td>{{ $item['lhpsv']->sinhVien->ho_ten }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $item['diem_trung_binh'] >= 5 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ number_format($item['diem_trung_binh'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $item['so_buoi_vang'] }} /
                                                {{ $item['tong_buoi'] }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $item['ty_le_vang'] <= 20 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ number_format($item['ty_le_vang'], 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @foreach ($item['ly_do'] as $lyDo)
                                                    <span class="badge bg-danger me-1">{{ $lyDo }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Tất cả sinh viên đều đủ điều kiện thi.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="mb-3">
            <a href="{{ route('giangvien.nhap-diem.show', $lopHocPhan->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle check all
            function toggleCheckAll() {
                const checkAll = document.getElementById('checkAll');
                const checkboxes = document.querySelectorAll('.sv-checkbox');
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
            }

            // Thêm tất cả sinh viên đủ điều kiện vào lịch thi
            function themTatCaVaoLichThi() {
                const lichThiId = document.getElementById('lichThiSelect').value;

                if (!lichThiId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn lịch thi',
                        text: 'Vui lòng chọn lịch thi trước khi thêm sinh viên'
                    });
                    return;
                }

                const sinhVienIds = [];
                document.querySelectorAll('.sv-checkbox').forEach(cb => {
                    sinhVienIds.push(cb.value);
                });

                themVaoLichThi(lichThiId, sinhVienIds);
            }

            // Thêm sinh viên đã chọn vào lịch thi
            function themDaChonVaoLichThi() {
                const lichThiId = document.getElementById('lichThiSelect').value;

                if (!lichThiId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn lịch thi',
                        text: 'Vui lòng chọn lịch thi trước khi thêm sinh viên'
                    });
                    return;
                }

                const sinhVienIds = [];
                document.querySelectorAll('.sv-checkbox:checked').forEach(cb => {
                    sinhVienIds.push(cb.value);
                });

                if (sinhVienIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn sinh viên',
                        text: 'Vui lòng chọn ít nhất một sinh viên'
                    });
                    return;
                }

                themVaoLichThi(lichThiId, sinhVienIds);
            }

            // Gọi API thêm sinh viên vào lịch thi
            function themVaoLichThi(lichThiId, sinhVienIds) {
                Swal.fire({
                    title: 'Xác nhận',
                    text: `Bạn muốn thêm ${sinhVienIds.length} sinh viên vào danh sách thi?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Thêm',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route('giangvien.nhap-diem.them-vao-danh-sach-thi', $lopHocPhan->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    lich_thi_id: lichThiId,
                                    sinh_vien_ids: sinhVienIds
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        text: data.message,
                                        timer: 2000
                                    }).then(() => {
                                        // Bỏ chọn tất cả checkbox
                                        document.getElementById('checkAll').checked = false;
                                        document.querySelectorAll('.sv-checkbox').forEach(cb => cb.checked =
                                            false);
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi',
                                    text: 'Có lỗi xảy ra: ' + error.message
                                });
                            });
                    }
                });
            }

            // Xuất Excel
            function exportExcel(type) {
                const tableId = type === 'du-dieu-kien' ? 'table-du-dieu-kien' : 'table-khong-du-dieu-kien';
                const table = document.getElementById(tableId);

                if (!table) {
                    alert('Không tìm thấy bảng dữ liệu');
                    return;
                }

                // Tạo workbook và worksheet
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);

                // Thêm worksheet vào workbook
                const sheetName = type === 'du-dieu-kien' ? 'Đủ điều kiện' : 'Không đủ điều kiện';
                XLSX.utils.book_append_sheet(wb, ws, sheetName);

                // Xuất file
                const fileName = `Danh_sach_${type}_{{ $lopHocPhan->ma_lop_hp }}_${new Date().getTime()}.xlsx`;
                XLSX.writeFile(wb, fileName);
            }

            // In danh sách
            function printList(type) {
                const tableId = type === 'du-dieu-kien' ? 'table-du-dieu-kien' : 'table-khong-du-dieu-kien';
                const table = document.getElementById(tableId);

                if (!table) {
                    alert('Không tìm thấy bảng dữ liệu');
                    return;
                }

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Danh sách sinh viên ${type === 'du-dieu-kien' ? 'đủ' : 'không đủ'} điều kiện thi</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h2 { text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                    th { background-color: #f0f0f0; font-weight: bold; }
                    .text-center { text-align: center; }
                    @media print {
                        button { display: none; }
                    }
                </style>
            </head>
            <body>
                <h2>DANH SÁCH SINH VIÊN ${type === 'du-dieu-kien' ? 'ĐỦ' : 'KHÔNG ĐỦ'} ĐIỀU KIỆN THI</h2>
                <p><strong>Lớp:</strong> {{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
                <p><strong>Học kỳ:</strong> {{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</p>
                ${table.outerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                    }
                <\/script>
            </body>
            </html>
        `);
                printWindow.document.close();
            }
        </script>

        <!-- Thêm thư viện XLSX cho xuất Excel -->
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    @endpush
@endsection
