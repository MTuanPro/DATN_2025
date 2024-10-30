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

    <!-- Thông báo khi đào tạo trả về -->
    @if($lopHocPhan->trang_thai_lop === 'dang_hoc' && $lopHocPhan->ly_do_tra_ve)
    <section class="section">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Điểm đã được trả về để chỉnh sửa</h5>
            <p class="mb-0"><strong>Lý do:</strong> {{ $lopHocPhan->ly_do_tra_ve }}</p>
            <p class="mb-0 mt-2">Vui lòng chỉnh sửa điểm và gửi lại cho đào tạo để duyệt.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </section>
    @endif

    <!-- Thông báo khi đã duyệt nhưng vẫn cho phép sửa -->
    @if(isset($daDuyetDiem) && $daDuyetDiem && $dangDienRa)
    <section class="section">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Điểm đã được duyệt</h5>
            <p class="mb-0">Điểm đã được đào tạo duyệt. Bạn vẫn có thể chỉnh sửa điểm và gửi lại cho đào tạo phê duyệt lại.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </section>
    @endif

    <!-- Thông báo khi lớp đã kết thúc -->
    @if(isset($daKetThuc) && $daKetThuc)
    <section class="section">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Lớp học phần đã kết thúc</h5>
            <p class="mb-0">
                <strong>Trạng thái:</strong> <span class="badge bg-danger">{{ $lopHocPhan->ten_trang_thai }}</span>
                @if($lopHocPhan->ngay_ket_thuc)
                    <br><strong>Ngày kết thúc:</strong> {{ $lopHocPhan->ngay_ket_thuc->format('d/m/Y') }}
                @endif
            </p>
            <p class="mb-0 mt-2"><strong>Bạn không thể sửa điểm sau khi lớp học phần đã kết thúc.</strong> Vui lòng liên hệ phòng đào tạo nếu cần chỉnh sửa.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </section>
    @endif

    <!-- Form nhập điểm -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Nhập điểm sinh viên</h5>
                @if(!$daKhoaDiem && $dangDienRa)
                <div class="d-flex gap-2">
                    <a href="{{ route('giangvien.nhap-diem.download-template', $lopHocPhan->id) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-download"></i> Tải template Excel
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showImportModal()">
                        <i class="bi bi-upload"></i> Import Excel
                    </button>
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
                                    @if(!$daKhoaDiem && $dangDienRa)
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
                                                @if($daKhoaDiem || $daKetThuc)
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
                                    
                                    @if(!$daKhoaDiem && $dangDienRa)
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
                        @if(!$daKhoaDiem && $dangDienRa)
                        <button type="button" class="btn btn-success" onclick="luuTatCaDiem()">
                            <i class="bi bi-save-fill"></i> Lưu tất cả điểm
                        </button>
                        @if($laGiangVienChinh)
                        <button type="button" class="btn btn-primary" onclick="guiDiemChoDaoTao()">
                            <i class="bi bi-send"></i> 
                            @if(isset($daDuyetDiem) && $daDuyetDiem)
                                Gửi lại điểm cho đào tạo
                            @elseif($lopHocPhan->trang_thai_lop === 'dang_hoc' && $lopHocPhan->ly_do_tra_ve)
                                Gửi lại điểm cho đào tạo
                            @else
                                Gửi điểm cho đào tạo
                            @endif
                        </button>
                        @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // Cấu hình đầu điểm từ PHP
    const cauHinhs = @json($cauHinhs->map(function($cauHinh) {
        return [
            'id' => $cauHinh->id,
            'ty_le' => (float)$cauHinh->ty_le,
            'so_cot' => (int)$cauHinh->so_cot
        ];
    })->values());

    // Tính điểm TK tạm thời dựa trên điểm đã nhập
    function tinhDiemTKTamThoi(svId) {
        const row = document.querySelector(`tr[data-sv-id="${svId}"]`);
        if (!row) return null;

        let tongDiem = 0;
        let tongTyLe = 0;

        // Duyệt qua từng cấu hình đầu điểm
        cauHinhs.forEach(cauHinh => {
            const diems = [];
            
            // Lấy tất cả điểm của đầu điểm này
            for (let cot = 1; cot <= cauHinh.so_cot; cot++) {
                const input = row.querySelector(`input[data-cau-hinh-id="${cauHinh.id}"][data-cot-diem="${cot}"]`);
                if (input && input.value !== '' && input.value !== null) {
                    const diem = parseFloat(input.value);
                    if (!isNaN(diem) && diem >= 0 && diem <= 10) {
                        diems.push(diem);
                    }
                }
            }

            // Chỉ tính nếu đủ cột
            if (diems.length >= cauHinh.so_cot) {
                // Tính trung bình các cột
                const diemTrungBinh = diems.reduce((sum, d) => sum + d, 0) / diems.length;

                // Nhân với tỷ lệ %
                tongDiem += diemTrungBinh * (cauHinh.ty_le / 100);
                tongTyLe += cauHinh.ty_le;
            }
        });

        // Nếu không có điểm nào thì trả về null
        if (tongTyLe === 0) {
            return null;
        }

        // Tính điểm tạm thời (chia lại theo tỷ lệ đã có)
        const diemTamThoi = (tongDiem / tongTyLe) * 100;

        // Làm tròn 2 chữ số
        return Math.round(diemTamThoi * 100) / 100;
    }

    // Lấy điểm TK từ server sau khi lưu
    function capNhatDiemTK(svId) {
        console.log('Cap nhat diem TK for svId:', svId);
        
        // Delay nhỏ để đảm bảo database đã commit
        setTimeout(() => {
            const url = '{{ route("giangvien.nhap-diem.get-diem-tk") }}';
            console.log('Fetching diem TK from:', url);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lop_hoc_phan_sinh_vien_id: parseInt(svId)
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Response error:', text);
                        throw new Error('Network response was not ok: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Diem TK response:', data);
                if (data.success) {
                    // Thử nhiều cách selector
                    let diemTKElement = document.querySelector(`.diem-tk-${svId}`);
                    if (!diemTKElement) {
                        // Thử tìm trong row
                        const row = document.querySelector(`tr[data-sv-id="${svId}"]`);
                        if (row) {
                            diemTKElement = row.querySelector(`.diem-tk-${svId}`);
                        }
                    }
                    
                    if (diemTKElement) {
                        const oldValue = diemTKElement.textContent;
                        diemTKElement.textContent = data.diem_tk;
                        
                        // Nếu là điểm tạm thời, thêm style đặc biệt
                        if (data.is_tam_thoi) {
                            diemTKElement.style.color = '#ff9800';
                            diemTKElement.style.fontWeight = 'bold';
                            diemTKElement.title = 'Điểm tạm thời (chưa đủ tất cả đầu điểm)';
                        } else {
                            diemTKElement.style.color = '#28a745';
                            diemTKElement.style.fontWeight = 'bold';
                            diemTKElement.title = 'Điểm chính thức';
                        }
                        
                        setTimeout(() => {
                            if (data.is_tam_thoi) {
                                diemTKElement.style.color = '#ff9800';
                            } else {
                                diemTKElement.style.color = '';
                            }
                            diemTKElement.style.fontWeight = '';
                        }, 2000);
                        console.log('Updated diem TK for svId:', svId, 'from:', oldValue, 'to:', data.diem_tk, 'is_tam_thoi:', data.is_tam_thoi);
                    } else {
                        console.error('Không tìm thấy element .diem-tk-' + svId);
                        console.log('Available elements:', document.querySelectorAll('[class*="diem-tk"]'));
                    }
                } else {
                    console.error('API returned success: false', data);
                }
            })
            .catch(error => {
                console.error('Error fetching diem TK:', error);
            });
        }, 500); // Tăng delay lên 500ms để đảm bảo database đã commit
    }

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
            const value = input.value.trim();
            // Gửi cả giá trị rỗng để xóa điểm cũ
            const data = {
                lop_hoc_phan_sinh_vien_id: parseInt(svId),
                cau_hinh_id: parseInt(input.dataset.cauHinhId),
                cot_diem: parseInt(input.dataset.cotDiem),
                diem_so: value === '' || value === null || value === '-' ? null : parseFloat(value)
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
                    Swal.fire('Thành công!', 'Đã lưu điểm sinh viên', 'success');
                    
                    // Tính và hiển thị điểm TK tạm thời ngay lập tức
                    const diemTamThoi = tinhDiemTKTamThoi(svId);
                    const diemTKElement = document.querySelector(`.diem-tk-${svId}`);
                    if (diemTKElement && diemTamThoi !== null) {
                        diemTKElement.textContent = diemTamThoi.toFixed(2);
                        diemTKElement.style.color = '#ff9800';
                        diemTKElement.style.fontWeight = 'bold';
                        diemTKElement.title = 'Điểm tạm thời (chưa đủ tất cả đầu điểm)';
                    }
                    
                    // Sau đó cập nhật từ server (có thể là điểm chính thức nếu đủ điểm)
                    setTimeout(() => {
                        capNhatDiemTK(svId);
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi lưu điểm', 'error');
            });
    }

    function luuTatCaDiem() {
        console.log('luuTatCaDiem called');
        const rows = document.querySelectorAll('#tableDiem tbody tr');
        console.log('Found rows:', rows.length);
        
        if (rows.length === 0) {
            Swal.fire('Thông báo', 'Không tìm thấy dữ liệu sinh viên', 'info');
            return;
        }
        
        const promises = [];
        const svIds = new Set(); // Lưu danh sách svId đã gửi request
        
        rows.forEach(row => {
            const inputs = row.querySelectorAll('.diem-input');
            
            inputs.forEach(input => {
                const value = input.value.trim();
                const svId = parseInt(input.dataset.svId);
                
                // Gửi cả giá trị rỗng để xóa điểm cũ
                const data = {
                    lop_hoc_phan_sinh_vien_id: svId,
                    cau_hinh_id: parseInt(input.dataset.cauHinhId),
                    cot_diem: parseInt(input.dataset.cotDiem),
                    diem_so: value === '' || value === null || value === '-' ? null : parseFloat(value)
                };
                
                // Đánh dấu sinh viên này có điểm được lưu/xóa
                svIds.add(svId);
                
                promises.push(
                    fetch('{{ route("giangvien.nhap-diem.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    }).then(response => {
                        return response.json().then(result => {
                            return { result, svId };
                        });
                    })
                );
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
            .then(results => {
                const hasError = results.some(r => !r.result.success);
                const successCount = results.filter(r => r.result.success).length;
                
                // Lấy danh sách sinh viên đã lưu điểm thành công
                const svIds = new Set();
                results.forEach(({ result, svId }) => {
                    if (result.success && svId) {
                        svIds.add(svId);
                    }
                });
                
                if (hasError) {
                    const errorMsg = results.find(r => !r.result.success)?.result?.message || 'Có lỗi xảy ra';
                    Swal.fire('Lỗi!', `Đã lưu ${successCount}/${results.length} điểm. Lỗi: ${errorMsg}`, 'error');
                } else {
                    Swal.fire('Thành công!', `Đã lưu tất cả ${successCount} điểm`, 'success');
                }
                
                // Cập nhật điểm TK cho từng sinh viên đã lưu điểm thành công
                svIds.forEach(svId => {
                    // Tính và hiển thị điểm TK tạm thời ngay lập tức
                    const diemTamThoi = tinhDiemTKTamThoi(svId);
                    const diemTKElement = document.querySelector(`.diem-tk-${svId}`);
                    if (diemTKElement && diemTamThoi !== null) {
                        diemTKElement.textContent = diemTamThoi.toFixed(2);
                        diemTKElement.style.color = '#ff9800';
                        diemTKElement.style.fontWeight = 'bold';
                        diemTKElement.title = 'Điểm tạm thời (chưa đủ tất cả đầu điểm)';
                    }
                    
                    // Sau đó cập nhật từ server
                    setTimeout(() => {
                        capNhatDiemTK(svId);
                    }, 500);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi lưu điểm', 'error');
            });
    }

    // Gửi điểm cho đào tạo
    function guiDiemChoDaoTao() {
        @if(isset($daDuyetDiem) && $daDuyetDiem)
        const title = 'Xác nhận gửi lại điểm';
        const text = 'Bạn có chắc muốn gửi lại điểm cho đào tạo để duyệt lại? Điểm sẽ được chuyển sang trạng thái chờ duyệt.';
        const confirmText = 'Có, gửi lại điểm';
        @else
        const title = 'Xác nhận gửi điểm';
        const text = 'Bạn có chắc muốn gửi điểm cho đào tạo để duyệt? Sau khi gửi, bạn sẽ không thể sửa điểm cho đến khi đào tạo duyệt hoặc trả về.';
        const confirmText = 'Có, gửi điểm';
        @endif
        
        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gửi request
                fetch('{{ route("giangvien.nhap-diem.gui-dao-tao", $lopHocPhan->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Thành công!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        // Nếu cần confirm (còn sinh viên chưa có điểm)
                        if (data.can_confirm) {
                            Swal.fire({
                                title: 'Xác nhận',
                                text: data.message,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Vẫn gửi',
                                cancelButtonText: 'Hủy'
                            }).then((confirmResult) => {
                                if (confirmResult.isConfirmed) {
                                    // Gửi lại với confirm
                                    fetch('{{ route("giangvien.nhap-diem.gui-dao-tao", $lopHocPhan->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ confirm: true })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Thành công!', data.message, 'success')
                                                .then(() => location.reload());
                                        } else {
                                            Swal.fire('Lỗi!', data.message, 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi gửi điểm', 'error');
                                    });
                                }
                            });
                        } else {
                            Swal.fire('Lỗi!', data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Lỗi!', 'Có lỗi xảy ra khi gửi điểm', 'error');
                });
            }
        });
    }

    // Hiển thị modal import Excel
    function showImportModal() {
        Swal.fire({
            title: 'Import điểm từ Excel',
            html: `
                <form id="importForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Chọn file Excel</label>
                        <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Chấp nhận: .xlsx, .xls (Tối đa 5MB)</small>
                    </div>
                    <div class="alert alert-info">
                        <strong><i class="bi bi-info-circle"></i> Lưu ý:</strong>
                        <ul class="mb-0">
                            <li>File phải có định dạng giống template Excel</li>
                            <li>Dòng đầu tiên là header (STT, MSSV, Họ tên, ...)</li>
                            <li>Dữ liệu bắt đầu từ dòng thứ 2</li>
                            <li>Điểm phải trong khoảng 0-10</li>
                            <li>MSSV phải khớp với danh sách sinh viên</li>
                        </ul>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Import',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            didOpen: () => {
                // Focus vào input file
                document.getElementById('excelFile').focus();
            },
            preConfirm: () => {
                const fileInput = document.getElementById('excelFile');
                if (!fileInput.files || !fileInput.files[0]) {
                    Swal.showValidationMessage('Vui lòng chọn file Excel');
                    return false;
                }
                return fileInput.files[0];
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                importExcel(result.value);
            }
        });
    }

    // Import Excel
    function importExcel(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'Đang import điểm...',
            html: 'Vui lòng đợi trong giây lát',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("giangvien.nhap-diem.import-excel", $lopHocPhan->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = data.message;
                if (data.errors && data.errors.length > 0) {
                    message += '\n\nLỗi:\n' + data.errors.slice(0, 5).join('\n');
                    if (data.total_errors > 5) {
                        message += `\n... và ${data.total_errors - 5} lỗi khác`;
                    }
                }
                Swal.fire({
                    title: 'Thành công!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Lỗi!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi import Excel', 'error');
        });
    }
</script>
@endpush
@endsection



<!-- 
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
    </section> -->


<!-- 
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

    Thông tin lớp
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
    </section> -->

    <!-- Cấu hình đầu điểm -->
    <!-- @if (!$cauHinhs->isEmpty())
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
    @endif -->