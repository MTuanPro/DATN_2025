@extends('layouts.layout-sinhvien')

@section('title', 'Chi Tiết Cảnh Báo')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Cảnh báo Học vụ</h3>
                <p class="text-subtitle text-muted">Thông tin chi tiết và hướng dẫn xử lý</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sinh-vien.canh-bao-hoc-vu.index') }}">Cảnh báo Học vụ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
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
        <div class="alert alert-{{ $alertType }} alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill"></i> MỨC ĐỘ: {{ strtoupper($mucDoText) }}
            </h4>
            <p>{{ $canhBao->ly_do }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            <!-- Thông tin cảnh báo -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin cảnh báo</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="30%">Mã sinh viên</th>
                                    <td>{{ $canhBao->sinhVien->ma_sinh_vien }}</td>
                                </tr>
                                <tr>
                                    <th>Họ và tên</th>
                                    <td>{{ $canhBao->sinhVien->ho_ten }}</td>
                                </tr>
                                <tr>
                                    <th>Lớp</th>
                                    <td>{{ $canhBao->sinhVien->lop_hanh_chinh }}</td>
                                </tr>
                                <tr>
                                    <th>Học kỳ</th>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $canhBao->hocKy->ten_hoc_ky }} - {{ $canhBao->hocKy->nam_hoc }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Loại cảnh báo</th>
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
                                    <th>Mức độ</th>
                                    <td>
                                        <span class="badge bg-{{ $canhBao->muc_do == 'buoc_thoi_hoc' ? 'dark' : ($canhBao->muc_do == 'dinh_chi' ? 'danger' : 'warning') }}">
                                            {{ $mucDoText }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lý do</th>
                                    <td class="text-danger fw-bold">{{ $canhBao->ly_do }}</td>
                                </tr>
                                @if($canhBao->ghi_chu)
                                <tr>
                                    <th>Ghi chú</th>
                                    <td>{{ $canhBao->ghi_chu }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Trạng thái</th>
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
                                    <th>Ngày cảnh báo</th>
                                    <td>{{ $canhBao->ngay_canh_bao->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @if($canhBao->ket_qua_xu_ly)
                                <tr>
                                    <th>Kết quả xử lý</th>
                                    <td>{{ $canhBao->ket_qua_xu_ly }}</td>
                                </tr>
                                @endif
                                @if($canhBao->nguoi_xu_ly_id)
                                <tr>
                                    <th>Người xử lý</th>
                                    <td>{{ $canhBao->nguoiXuLy->name ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kết quả xử lý -->
                @if($canhBao->trang_thai == 'da_xu_ly' && $canhBao->ket_qua_xu_ly)
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-circle"></i> Kết quả xử lý
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $canhBao->ket_qua_xu_ly }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Hướng dẫn xử lý -->
            <div class="col-lg-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightbulb"></i> Hướng dẫn xử lý
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($canhBao->loai_canh_bao == 'diem_thap')
                        <h6 class="text-primary">Điểm trung bình thấp</h6>
                        <ul>
                            <li>Xem lại kết quả học tập và phân tích nguyên nhân</li>
                            <li>Liên hệ với Giảng viên để được tư vấn về phương pháp học tập</li>
                            <li>Tham gia các lớp học phụ đạo (nếu có)</li>
                            <li>Lập kế hoạch học tập cụ thể cho học kỳ tiếp theo</li>
                            <li>Gặp Giảng viên chủ nhiệm để được hỗ trợ</li>
                        </ul>
                        
                        @elseif($canhBao->loai_canh_bao == 'vang_nhieu')
                        <h6 class="text-primary">Vắng học nhiều</h6>
                        <ul>
                            <li>Kiểm tra lại lịch điểm danh của bạn</li>
                            <li>Nếu có lý do chính đáng (ốm, việc gia đình), cần làm đơn xin phép kèm chứng từ</li>
                            <li>Nộp đơn xin miễn giảm vắng (nếu có chứng từ hợp lệ)</li>
                            <li>Cam kết tham gia học đầy đủ trong các buổi học tiếp theo</li>
                            <li>Liên hệ Phòng Đào tạo để làm thủ tục</li>
                        </ul>
                        
                        @elseif($canhBao->loai_canh_bao == 'no_hoc_phi')
                        <h6 class="text-primary">Nợ học phí</h6>
                        <ul>
                            <li>Kiểm tra số tiền nợ và hạn thanh toán</li>
                            <li>Nếu gặp khó khăn tài chính, liên hệ Phòng Tài vụ để xin gia hạn</li>
                            <li>Xem xét các chương trình hỗ trợ tài chính/học bổng</li>
                            <li>Thanh toán học phí càng sớm càng tốt để tránh bị khóa tài khoản</li>
                            <li><strong>Lưu ý:</strong> Nợ học phí quá hạn sẽ không được thi và đăng ký môn học</li>
                        </ul>
                        
                        @elseif($canhBao->loai_canh_bao == 'hoc_ky_lien_tiep')
                        <h6 class="text-primary">Học kỳ liên tiếp không đạt</h6>
                        <ul>
                            <li>Cần có sự thay đổi lớn về thái độ và phương pháp học tập</li>
                            <li>Gặp Giảng viên chủ nhiệm để được tư vấn định hướng</li>
                            <li>Xem xét lại ngành học và khả năng của bản thân</li>
                            <li>Tham gia các khóa đào tạo kỹ năng học tập</li>
                            <li><strong>Quan trọng:</strong> Nếu tiếp tục không đạt, có thể bị buộc thôi học</li>
                        </ul>
                        @endif

                        <hr>
                        <h6 class="text-danger">Liên hệ hỗ trợ</h6>
                        <p class="mb-2">
                            <i class="bi bi-envelope"></i> Email: <a href="mailto:daotao@smis.edu.vn">daotao@smis.edu.vn</a>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone"></i> Hotline: <a href="tel:024xxxxxxxx">024.xxxx.xxxx</a>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt"></i> Phòng Đào tạo: Tòa nhà A, Tầng 2
                        </p>
                        
                        @if($canhBao->trang_thai == 'chua_xu_ly')
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Vui lòng liên hệ với Phòng Đào tạo trong vòng <strong>7 ngày</strong> kể từ ngày nhận cảnh báo.
                            </small>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick actions -->
                <div class="card mt-3">
                    <div class="card-body">
                        <a href="{{ route('sinh-vien.canh-bao-hoc-vu.index') }}" class="btn btn-secondary w-100 mb-2">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                        <a href="mailto:daotao@smis.edu.vn?subject=Hỏi về cảnh báo {{ $canhBao->id }}" class="btn btn-primary w-100">
                            <i class="bi bi-envelope"></i> Gửi email hỏi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
