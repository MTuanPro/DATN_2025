@extends('layouts.layout-sinhvien')

@section('title', 'Điểm danh hôm nay')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Điểm danh hôm nay</h3>
                    <p class="text-subtitle text-muted">Điểm danh các lớp học hôm nay</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Điểm danh</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Thông tin quan trọng --}}
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-info-circle-fill"></i> Lưu ý quan trọng</h5>
            <ul class="mb-0">
                <li><strong>Thời gian điểm danh:</strong> Chỉ được điểm danh trong <strong class="text-danger">40 phút đầu</strong> kể từ khi bắt đầu lớp học</li>
                <li><strong>Đến muộn:</strong> Nếu điểm danh sau 15 phút sẽ được tính là <span class="badge bg-warning">Đi muộn</span></li>
                <li><strong>Quá giờ:</strong> Nếu quá 40 phút, bạn cần yêu cầu điểm danh bù từ giảng viên</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Thời gian hiện tại --}}
        <div class="card mb-4 shadow-sm border-primary">
            <div class="card-body bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-fill text-primary"></i> 
                            <strong>Thời gian hiện tại:</strong>
                            <span class="text-primary" id="current-time">{{ $now->format('H:i:s - d/m/Y') }}</span>
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <a href="{{ route('sinh-vien.thoi-khoa-bieu.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-calendar3"></i> Xem thời khóa biểu
                        </a>
                        <a href="{{ route('sinh-vien.diem-danh.lich-su') }}" class="btn btn-outline-info">
                            <i class="bi bi-clock-history"></i> Lịch sử
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách lịch học hôm nay --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-check"></i> Lịch học hôm nay ({{ $now->format('d/m/Y') }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if ($lichHocHomNay->isEmpty())
                    <div class="alert alert-info m-4 mb-0">
                        <i class="bi bi-info-circle"></i> Bạn không có lịch học nào hôm nay.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="80">#</th>
                                    <th>Môn học</th>
                                    <th>Phòng</th>
                                    <th>Giảng viên</th>
                                    <th width="150">Thời gian</th>
                                    <th width="180">Trạng thái</th>
                                    <th width="150" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lichHocHomNay as $index => $lich)
                                    <tr>
                                        <td class="align-middle text-center">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            @if ($lich->lopHocPhan && $lich->lopHocPhan->monHoc)
                                                <strong class="text-primary">{{ $lich->lopHocPhan->monHoc->ten_mon }}</strong><br>
                                                <small class="text-muted">{{ $lich->lopHocPhan->monHoc->ma_mon }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($lich->phongHoc)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-geo-alt"></i> {{ $lich->phongHoc->ten_phong }}
                                                </span>
                                            @else
                                                <span class="text-muted">Chưa xếp</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($lich->giangVien)
                                                <i class="bi bi-person-fill text-primary"></i> {{ $lich->giangVien->ho_ten }}
                                            @else
                                                <span class="text-muted">Chưa phân công</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if (isset($lich->thoi_gian_bat_dau))
                                                <strong>{{ $lich->thoi_gian_bat_dau->format('H:i') }}</strong> - 
                                                {{ $lich->thoi_gian_bat_dau->copy()->addMinutes(90)->format('H:i') }}
                                                <br>
                                                <small class="text-muted">Điểm danh đến: {{ $lich->thoi_gian_ket_thuc_diem_danh->format('H:i') }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($lich->diemDanh)
                                                @if ($lich->diemDanh->trang_thai === 'co_mat')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Đã điểm danh
                                                    </span>
                                                @elseif ($lich->diemDanh->trang_thai === 'muon')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock-fill"></i> Đi muộn
                                                    </span>
                                                @elseif ($lich->diemDanh->trang_thai === 'vang_co_phep')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-file-check-fill"></i> Vắng có phép
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $lich->diemDanh->trang_thai }}</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $lich->diemDanh->thoi_gian_diem_danh->format('H:i') }}</small>
                                            @elseif ($lich->co_the_diem_danh)
                                                <span class="badge bg-primary pulse">
                                                    <i class="bi bi-circle-fill"></i> Có thể điểm danh
                                                </span>
                                                <br>
                                                <small class="text-danger" data-countdown="{{ $lich->thoi_gian_ket_thuc_diem_danh->timestamp }}">
                                                    Còn {{ $lich->phut_con_lai }} phút
                                                </small>
                                            @elseif ($lich->qua_gio_diem_danh)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle-fill"></i> Quá giờ
                                                </span>
                                                <br>
                                                <small class="text-muted">Yêu cầu điểm danh bù</small>
                                            @elseif ($lich->chua_den_gio)
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-hourglass-split"></i> Chưa đến giờ
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($lich->diemDanh)
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-check-circle"></i> Đã điểm danh
                                                </button>
                                            @elseif ($lich->co_the_diem_danh)
                                                <form action="{{ route('sinh-vien.diem-danh.store', $lich->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Xác nhận điểm danh?')">
                                                        <i class="bi bi-check-circle-fill"></i> Điểm danh
                                                    </button>
                                                </form>
                                            @elseif ($lich->qua_gio_diem_danh)
                                                @if ($lich->yeuCauDiemDanhBu)
                                                    <span class="badge bg-info">
                                                        Đã gửi yêu cầu<br>
                                                        <small>({{ $lich->yeuCauDiemDanhBu->trang_thai }})</small>
                                                    </span>
                                                @else
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalYeuCau{{ $lich->id }}">
                                                        <i class="bi bi-file-earmark-text"></i> Yêu cầu bù
                                                    </button>
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    Chưa đến giờ
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Modal yêu cầu điểm danh bù --}}
                                    @if ($lich->qua_gio_diem_danh && !$lich->yeuCauDiemDanhBu)
                                        <div class="modal fade" id="modalYeuCau{{ $lich->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Yêu cầu điểm danh bù</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('sinh-vien.diem-danh.yeu-cau-diem-danh-bu.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="lich_hoc_chi_tiet_id" value="{{ $lich->id }}">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Lý do <span class="text-danger">*</span></label>
                                                                <textarea name="ly_do" class="form-control" rows="4" required 
                                                                    placeholder="Nhập lý do vắng mặt hoặc đến muộn..."></textarea>
                                                            </div>
                                                            <div class="alert alert-warning">
                                                                <small>
                                                                    <i class="bi bi-info-circle"></i> 
                                                                    Yêu cầu của bạn sẽ được giảng viên xem xét và phê duyệt.
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Cập nhật thời gian hiện tại
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + 
                             ' - ' + now.toLocaleDateString('vi-VN');
            document.getElementById('current-time').textContent = timeString;
        }

        // Cập nhật countdown cho các lớp có thể điểm danh
        function updateCountdowns() {
            document.querySelectorAll('[data-countdown]').forEach(element => {
                const endTime = parseInt(element.dataset.countdown) * 1000;
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance > 0) {
                    const minutes = Math.floor(distance / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    element.textContent = `Còn ${minutes} phút ${seconds} giây`;
                } else {
                    element.textContent = 'Hết giờ';
                    element.classList.remove('text-danger');
                    element.classList.add('text-muted');
                    
                    // Reload trang sau 2 giây khi hết giờ
                    setTimeout(() => location.reload(), 2000);
                }
            });
        }

        // Update mỗi giây
        setInterval(() => {
            updateCurrentTime();
            updateCountdowns();
        }, 1000);

        // Run ngay khi load
        updateCurrentTime();
        updateCountdowns();
    </script>
    @endpush
@endsection
