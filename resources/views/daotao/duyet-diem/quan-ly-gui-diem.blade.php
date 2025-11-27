@extends('layouts.layout-daotao')

@section('title', 'Quản lý mở/đóng gửi điểm')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý mở/đóng gửi điểm</h3>
                    <p class="text-subtitle text-muted">Quản lý việc mở/đóng gửi điểm cho giảng viên (lần 1 và lần 2)</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dao-tao.duyet-diem.index') }}">Duyệt điểm</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Quản lý gửi điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Filter --}}
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('dao-tao.duyet-diem.quan-ly-gui-diem') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Tất cả học kỳ --</option>
                                        @foreach ($hocKys as $hk)
                                            <option value="{{ $hk->id }}" {{ $hocKyId == $hk->id ? 'selected' : '' }}>
                                                {{ $hk->ten_hoc_ky }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8 text-end">
                                <button type="button" class="btn btn-success" onclick="moGuiDiemHangLoat(1)">
                                    <i class="bi bi-unlock"></i> Mở gửi điểm lần 1 (hàng loạt)
                                </button>
                                <button type="button" class="btn btn-success" onclick="moGuiDiemHangLoat(2)">
                                    <i class="bi bi-unlock"></i> Mở gửi điểm lần 2 (hàng loạt)
                                </button>
                                <button type="button" class="btn btn-danger" onclick="dongGuiDiemHangLoat(1)">
                                    <i class="bi bi-lock"></i> Đóng gửi điểm lần 1 (hàng loạt)
                                </button>
                                <button type="button" class="btn btn-danger" onclick="dongGuiDiemHangLoat(2)">
                                    <i class="bi bi-lock"></i> Đóng gửi điểm lần 2 (hàng loạt)
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danh sách lớp học phần --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll()">
                                    </th>
                                    <th>#</th>
                                    <th>Mã lớp HP</th>
                                    <th>Tên lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Gửi điểm lần 1</th>
                                    <th>Gửi điểm lần 2</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lopHocPhans as $index => $lhp)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox-lhp" value="{{ $lhp->id }}" onchange="toggleDeleteButton()">
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $lhp->ma_lop_hp }}</strong></td>
                                        <td>{{ $lhp->ten_lop_hp }}</td>
                                        <td>{{ $lhp->monHoc->ten_mon ?? '-' }}</td>
                                        <td>{{ $lhp->hocKy->ten_hoc_ky ?? '-' }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-gui-diem" 
                                                    type="checkbox" 
                                                    data-lhp-id="{{ $lhp->id }}"
                                                    data-lan="1"
                                                    {{ $lhp->cho_phep_gui_diem_lan_1 ? 'checked' : '' }}
                                                    onchange="capNhatTrangThaiGuiDiem({{ $lhp->id }}, 1, this.checked)">
                                            </div>
                                            <small class="text-muted d-block">
                                                @if($lhp->trang_thai_gui_diem_lan_1 === 'chua_gui')
                                                    Chưa gửi
                                                @elseif($lhp->trang_thai_gui_diem_lan_1 === 'da_gui')
                                                    <span class="badge bg-warning">Đã gửi</span>
                                                @elseif($lhp->trang_thai_gui_diem_lan_1 === 'da_duyet')
                                                    <span class="badge bg-success">Đã duyệt</span>
                                                @elseif($lhp->trang_thai_gui_diem_lan_1 === 'da_tra_ve')
                                                    <span class="badge bg-danger">Đã trả về</span>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-gui-diem" 
                                                    type="checkbox" 
                                                    data-lhp-id="{{ $lhp->id }}"
                                                    data-lan="2"
                                                    {{ $lhp->cho_phep_gui_diem_lan_2 ? 'checked' : '' }}
                                                    onchange="capNhatTrangThaiGuiDiem({{ $lhp->id }}, 2, this.checked)">
                                            </div>
                                            <small class="text-muted d-block">
                                                @if($lhp->trang_thai_gui_diem_lan_2 === 'chua_gui')
                                                    Chưa gửi
                                                @elseif($lhp->trang_thai_gui_diem_lan_2 === 'da_gui')
                                                    <span class="badge bg-warning">Đã gửi</span>
                                                @elseif($lhp->trang_thai_gui_diem_lan_2 === 'da_duyet')
                                                    <span class="badge bg-success">Đã duyệt</span>
                                                @elseif($lhp->trang_thai_gui_diem_lan_2 === 'da_tra_ve')
                                                    <span class="badge bg-danger">Đã trả về</span>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($lhp->trang_thai_gui_diem_lan_1 === 'da_duyet' && $lhp->trang_thai_gui_diem_lan_2 === 'da_duyet')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                        type="checkbox" 
                                                        {{ $lhp->cho_phep_sua_diem_sau_duyet ? 'checked' : '' }}
                                                        onchange="choPhepSuaDiemSauDuyet({{ $lhp->id }}, this.checked)">
                                                    <label class="form-check-label">
                                                        Cho phép sửa điểm (phúc khảo)
                                                    </label>
                                                </div>
                                            @else
                                                <span class="text-muted">Chờ duyệt cả 2 lần</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleCheckAll() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.checkbox-lhp');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
        }

        function capNhatTrangThaiGuiDiem(lopHocPhanId, lan, choPhep) {
            const field = lan == 1 ? 'cho_phep_gui_diem_lan_1' : 'cho_phep_gui_diem_lan_2';
            
            fetch(`{{ url('dao-tao/duyet-diem') }}/${lopHocPhanId}/cap-nhat-trang-thai-gui-diem`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ [field]: choPhep })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success');
                } else {
                    Swal.fire('Lỗi!', data.message, 'error');
                    // Revert checkbox
                    const checkbox = document.querySelector(`input[data-lhp-id="${lopHocPhanId}"][data-lan="${lan}"]`);
                    if (checkbox) checkbox.checked = !choPhep;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
                // Revert checkbox
                const checkbox = document.querySelector(`input[data-lhp-id="${lopHocPhanId}"][data-lan="${lan}"]`);
                if (checkbox) checkbox.checked = !choPhep;
            });
        }

        function choPhepSuaDiemSauDuyet(lopHocPhanId, choPhep) {
            fetch(`{{ url('dao-tao/duyet-diem') }}/${lopHocPhanId}/cho-phep-sua-diem-sau-duyet`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cho_phep_sua_diem_sau_duyet: choPhep })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success');
                } else {
                    Swal.fire('Lỗi!', data.message, 'error');
                    // Revert checkbox
                    const checkbox = document.querySelector(`input[onchange*="choPhepSuaDiemSauDuyet(${lopHocPhanId}"]`);
                    if (checkbox) checkbox.checked = !choPhep;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra', 'error');
            });
        }

        function moGuiDiemHangLoat(lan) {
            const checkboxes = document.querySelectorAll('.checkbox-lhp:checked');
            if (checkboxes.length === 0) {
                Swal.fire('Cảnh báo', 'Vui lòng chọn ít nhất một lớp học phần', 'warning');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const field = lan == 1 ? 'cho_phep_gui_diem_lan_1' : 'cho_phep_gui_diem_lan_2';
            const lanText = lan == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';

            Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc muốn mở gửi điểm ${lanText} cho ${ids.length} lớp đã chọn?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có, mở',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("dao-tao.duyet-diem.cap-nhat-trang-thai-gui-diem-hang-loat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lop_hoc_phan_ids: ids,
                            [field]: true
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Thành công!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi!', data.message, 'error');
                        }
                    });
                }
            });
        }

        function dongGuiDiemHangLoat(lan) {
            const checkboxes = document.querySelectorAll('.checkbox-lhp:checked');
            if (checkboxes.length === 0) {
                Swal.fire('Cảnh báo', 'Vui lòng chọn ít nhất một lớp học phần', 'warning');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const field = lan == 1 ? 'cho_phep_gui_diem_lan_1' : 'cho_phep_gui_diem_lan_2';
            const lanText = lan == 1 ? 'lần 1 (giữa kỳ)' : 'lần 2 (cuối kỳ)';

            Swal.fire({
                title: 'Xác nhận',
                text: `Bạn có chắc muốn đóng gửi điểm ${lanText} cho ${ids.length} lớp đã chọn?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Có, đóng',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("dao-tao.duyet-diem.cap-nhat-trang-thai-gui-diem-hang-loat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lop_hoc_phan_ids: ids,
                            [field]: false
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Thành công!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi!', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection

