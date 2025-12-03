@extends('layouts.layout-sinhvien')

@section('title', 'Sinh viên Dashboard')

@section('content')
    <div class="page-heading">
        <h3>Dashboard Sinh viên</h3>
        <p class="text-subtitle text-muted">Chào mừng, {{ auth()->user()->ho_ten }}</p>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Tín chỉ đã học</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalCredits ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">GPA</h6>
                                        <h6 class="font-extrabold mb-0">{{ $gpa ?? '0.00' }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Lớp HP đang học</h6>
                                        <h6 class="font-extrabold mb-0">{{ $currentClasses ?? 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Công nợ</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($debt ?? 0) }}đ</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Lịch học 7 ngày tới</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Ca</th>
                                                <th>Học phần</th>
                                                <th>Phòng</th>
                                                <th>Giảng viên</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($weeklyTimetable) && $weeklyTimetable->count() > 0)
                                                @foreach($weeklyTimetable as $lich)
                                                    @php
                                                        $ngayHoc = \Carbon\Carbon::parse($lich->ngay_hoc);
                                                        $monHoc = $lich->lopHocPhan->monHoc ?? null;
                                                        $isToday = $ngayHoc->isToday();
                                                    @endphp
                                                    <tr class="{{ $isToday ? 'table-info' : '' }}">
                                                        <td>
                                                            <strong>{{ $ngayHoc->format('d/m/Y') }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $ngayHoc->locale('vi')->isoFormat('dddd') }}</small>
                                                            @if($isToday)
                                                                <span class="badge bg-primary ms-1">Hôm nay</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lich->caHoc)
                                                                <span class="badge bg-primary">{{ $lich->caHoc->ten_ca }}</span>
                                                            @elseif($lich->gio_bat_dau && $lich->gio_ket_thuc)
                                                                {{ \Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i') }} - 
                                                                {{ \Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $monHoc ? $monHoc->ten_mon : 'N/A' }}</td>
                                                        <td>{{ $lich->phongHoc ? $lich->phongHoc->ten_phong : 'TBA' }}</td>
                                                        <td>{{ $lich->giangVien ? $lich->giangVien->ho_ten : 'TBA' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">Không có lịch học trong 7 ngày tới</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Điểm gần đây</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Học phần</th>
                                                <th>Điểm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($recentGrades) && $recentGrades->count() > 0)
                                                @foreach($recentGrades as $grade)
                                                    @php
                                                        $monHoc = $grade->lopHocPhanSinhVien->lopHocPhan->monHoc ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $monHoc ? $monHoc->ten_mon : 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $grade->diem_chu_badge }}">
                                                                {{ $grade->diem_chu ?? 'N/A' }}
                                                            </span>
                                                            @if($grade->diem_he_10)
                                                                <small class="text-muted ms-1">({{ number_format($grade->diem_he_10, 1) }})</small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2" class="text-center">Chưa có điểm</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Lịch thi 7 ngày tới</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Học phần</th>
                                                <th>Ngày & Giờ thi</th>
                                                <th>Phòng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($upcomingExams) && $upcomingExams->count() > 0)
                                                @foreach($upcomingExams as $exam)
                                                    @php
                                                        $monHoc = $exam->lopHocPhan->monHoc ?? null;
                                                        $ngayThi = \Carbon\Carbon::parse($exam->ngay_thi);
                                                        $isToday = $ngayThi->isToday();
                                                    @endphp
                                                    <tr class="{{ $isToday ? 'table-warning' : '' }}">
                                                        <td>
                                                            {{ $monHoc ? $monHoc->ten_mon : 'N/A' }}
                                                            @if($isToday)
                                                                <span class="badge bg-danger ms-1">Hôm nay</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $ngayThi->format('d/m/Y') }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $ngayThi->locale('vi')->isoFormat('dddd') }}</small>
                                                            @if($exam->gio_bat_dau)
                                                                <br>
                                                                <span class="badge bg-primary">{{ \Carbon\Carbon::parse($exam->gio_bat_dau)->format('H:i') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $exam->phongHoc ? $exam->phongHoc->ten_phong : 'TBA' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">Không có lịch thi trong 7 ngày tới</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Thông tin sinh viên</h4>
                    </div>
                    <div class="card-content pb-4">
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="name ms-4">
                                <h5 class="mb-1">MSSV</h5>
                                <h6 class="text-muted mb-0">{{ $studentCode ?? 'N/A' }}</h6>
                            </div>
                        </div>
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="name ms-4">
                                <h5 class="mb-1">Lớp</h5>
                                <h6 class="text-muted mb-0">{{ $className ?? 'N/A' }}</h6>
                            </div>
                        </div>
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="name ms-4">
                                <h5 class="mb-1">Khóa</h5>
                                <h6 class="text-muted mb-0">{{ $course ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Cảnh báo</h4>
                    </div>
                    <div class="card-content pb-4">
                        @if (isset($warnings) && count($warnings) > 0)
                            @foreach ($warnings as $warning)
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="name ms-4">
                                        <h5 class="mb-1 text-danger">{{ $warning->type }}</h5>
                                        <h6 class="text-muted mb-0">{{ $warning->message }}</h6>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-4 py-3">
                                <p class="text-muted mb-0">Không có cảnh báo</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal hiển thị nhiều thông báo mới nhất --}}
    @if (isset($thongBaoMoiNhat) && $thongBaoMoiNhat->count() > 0)
        <div class="modal fade" id="thongBaoMoiNhatModal" tabindex="-1" aria-labelledby="thongBaoMoiNhatModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h2 class="modal-title fw-bold" id="thongBaoMoiNhatModalLabel" style="font-size: 1.75rem;">
                            THÔNG BÁO
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3 pb-4">
                        <div id="thongBaoCarousel">
                            @foreach ($thongBaoMoiNhat as $index => $nguoiNhan)
                                @if ($nguoiNhan->thongBao)
                                    @php
                                        $thongBao = $nguoiNhan->thongBao;
                                    @endphp
                                    <div class="thong-bao-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" data-thong-bao-id="{{ $thongBao->id }}" data-da-doc="{{ $nguoiNhan->da_doc ? 'true' : 'false' }}">
                                        <div class="notification-content mb-4" style="min-height: 200px; line-height: 1.8; font-size: 1rem;">
                                            {!! nl2br(e($thongBao->noi_dung)) !!}
                                        </div>
                                        @if ($thongBao->file_dinh_kem)
                                            <div class="mb-3">
                                                <a href="{{ Storage::url($thongBao->file_dinh_kem) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-paperclip"></i> Tải file đính kèm
                                                </a>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                            <div>
                                                <a href="{{ route('sinh-vien.thong-bao.show', $thongBao->id) }}" class="btn btn-primary btn-view-detail">Xem Thêm</a>
                                            </div>
                                            <div class="text-muted text-end">
                                                <small>Ngày {{ $thongBao->ngay_gui->format('d') }} tháng {{ $thongBao->ngay_gui->format('m') }} năm {{ $thongBao->ngay_gui->format('Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3 pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="btnPrev" disabled>
                                <i class="bi bi-chevron-left"></i> Trước
                            </button>
                            <span class="text-muted small" id="thongBaoCounter">1 / {{ $thongBaoMoiNhat->count() }}</span>
                            <button type="button" class="btn btn-outline-secondary" id="btnNext">
                                Tiếp <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('thongBaoMoiNhatModal');
                if (!modal) return;

                const items = modal.querySelectorAll('.thong-bao-item');
                const totalItems = items.length;
                let currentIndex = 0;
                const viewedIds = new Set();

                // Debug: Log số lượng thông báo
                console.log('Tổng số thông báo:', totalItems);
                console.log('Modal element:', modal);
                
                // Đếm số thông báo chưa đọc (từ database và sessionStorage)
                let unreadCount = 0;
                items.forEach(item => {
                    const thongBaoId = item.dataset.thongBaoId;
                    const daDoc = item.dataset.daDoc === 'true';
                    
                    // Kiểm tra cả database (da_doc) và sessionStorage
                    const readKey = 'thong_bao_read_' + thongBaoId;
                    const markedAsRead = sessionStorage.getItem(readKey);
                    
                    // Chưa đọc nếu: database chưa đọc VÀ chưa đánh dấu trong sessionStorage
                    if (!daDoc && !markedAsRead) {
                        unreadCount++;
                    }
                });
                
                console.log('Số thông báo chưa đọc:', unreadCount);
                
                // Hiển thị modal nếu có thông báo chưa đọc
                if (totalItems > 0 && unreadCount > 0) {
                    console.log('Có thông báo chưa đọc, đang hiển thị modal...');
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();
                } else if (totalItems > 0) {
                    console.log('Có thông báo nhưng đã đọc hết');
                } else {
                    console.log('Không có thông báo để hiển thị');
                }

                function showItem(index) {
                    items.forEach((item, i) => {
                        item.classList.toggle('active', i === index);
                    });
                    
                    currentIndex = index;
                    updateButtons();
                    updateCounter();
                    updateViewDetailLink();
                    
                    // Đánh dấu đã xem thông báo hiện tại
                    const currentItem = items[index];
                    if (currentItem) {
                        const thongBaoId = currentItem.dataset.thongBaoId;
                        if (thongBaoId && !viewedIds.has(thongBaoId)) {
                            viewedIds.add(thongBaoId);
                            markAsRead(thongBaoId);
                        }
                    }
                }

                function updateButtons() {
                    const btnPrev = document.getElementById('btnPrev');
                    const btnNext = document.getElementById('btnNext');
                    
                    btnPrev.disabled = currentIndex === 0;
                    btnNext.disabled = currentIndex === totalItems - 1;
                }

                function updateCounter() {
                    const counter = document.getElementById('thongBaoCounter');
                    counter.textContent = `${currentIndex + 1} / ${totalItems}`;
                }

                function updateViewDetailLink() {
                    // Không cần cập nhật vì mỗi item đã có link riêng
                }

                function markAsRead(thongBaoId) {
                    fetch(`/sinh-vien/thong-bao/${thongBaoId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        // Đánh dấu đã đọc trong sessionStorage
                        const readKey = 'thong_bao_read_' + thongBaoId;
                        sessionStorage.setItem(readKey, 'true');
                    });
                }

                // Event listeners
                document.getElementById('btnPrev').addEventListener('click', function() {
                    if (currentIndex > 0) {
                        showItem(currentIndex - 1);
                    }
                });

                document.getElementById('btnNext').addEventListener('click', function() {
                    if (currentIndex < totalItems - 1) {
                        showItem(currentIndex + 1);
                    }
                });

                // Đánh dấu đã xem khi đóng modal (không cần thiết nữa vì đã dùng logic chưa đọc)
                modal.addEventListener('hidden.bs.modal', function() {
                    console.log('Modal đã đóng');
                });

                // Hiển thị item đầu tiên
                if (totalItems > 0) {
                    showItem(0);
                }
            });
        </script>
        <style>
            .thong-bao-item {
                display: none;
            }
            .thong-bao-item.active {
                display: block;
            }
            .notification-content {
                white-space: pre-wrap;
                word-wrap: break-word;
            }
            /* Dark mode support for notification modal */
            body.dark-mode .modal-content {
                background-color: #2d3748;
                color: #e2e8f0;
            }
            body.dark-mode .modal-title {
                color: #e2e8f0 !important;
            }
            body.dark-mode .notification-content {
                color: #e2e8f0 !important;
            }
            body.dark-mode .modal-header {
                border-bottom-color: #4a5568;
            }
            body.dark-mode .modal-footer {
                border-top-color: #4a5568;
            }
            body.dark-mode .border-top {
                border-top-color: #4a5568 !important;
            }
            body.dark-mode .text-muted {
                color: #a0aec0 !important;
            }
            body.dark-mode .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }
        </style>
        @endpush
    @endif
@endsection
