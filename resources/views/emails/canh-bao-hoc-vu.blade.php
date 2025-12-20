<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảnh Báo Học Vụ</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box.danger {
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        .alert-box.critical {
            background-color: #d1ecf1;
            border-left-color: #0c5460;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .info-table td:first-child {
            font-weight: 600;
            width: 40%;
            color: #495057;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-dark {
            background-color: #343a40;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 600;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .warning-text {
            color: #856404;
            font-weight: 600;
            margin: 15px 0;
        }
        .steps {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .steps h3 {
            margin-top: 0;
            color: #007bff;
        }
        .steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .steps li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">⚠️</div>
            <h1>THÔNG BÁO CẢNH BÁO HỌC VỤ</h1>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">
                Hệ thống Quản lý Sinh viên - S-MIS
            </p>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 16px; margin-bottom: 20px;">
                Kính gửi <strong>{{ $canhBao->sinhVien->ho_ten }}</strong>,
            </p>

            <p>
                Phòng Đào tạo thông báo bạn đã nhận được 
                <strong style="color: #dc3545;">cảnh báo học vụ</strong> 
                với nội dung như sau:
            </p>

            <!-- Alert Box -->
            @php
                $mucDoClass = match($canhBao->muc_do) {
                    'canh_cao' => '',
                    'dinh_chi' => 'danger',
                    'buoc_thoi_hoc' => 'critical',
                    default => ''
                };
                $mucDoText = match($canhBao->muc_do) {
                    'canh_cao' => 'Cảnh cáo',
                    'dinh_chi' => 'Đình chỉ học tập',
                    'buoc_thoi_hoc' => 'Buộc thôi học',
                    default => $canhBao->muc_do
                };
                $loaiText = match($canhBao->loai_canh_bao) {
                    'diem_thap' => 'Điểm trung bình thấp',
                    'vang_nhieu' => 'Vắng học nhiều',
                    'no_hoc_phi' => 'Nợ học phí',
                    'hoc_ky_lien_tiep' => 'Học kỳ liên tiếp không đạt',
                    default => $canhBao->loai_canh_bao
                };
            @endphp

            <div class="alert-box {{ $mucDoClass }}">
                <strong>⚠️ Mức độ:</strong> 
                <span class="badge badge-{{ $canhBao->muc_do == 'canh_cao' ? 'warning' : 'danger' }}">
                    {{ $mucDoText }}
                </span>
            </div>

            <!-- Thông tin cảnh báo -->
            <table class="info-table">
                <tr>
                    <td>Mã sinh viên:</td>
                    <td><strong>{{ $canhBao->sinhVien->ma_sinh_vien }}</strong></td>
                </tr>
                <tr>
                    <td>Lớp:</td>
                    <td>{{ $canhBao->sinhVien->nganh->ten_nganh ?? 'N/A' ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Học kỳ:</td>
                    <td>{{ $canhBao->hocKy->ten_hoc_ky }} - {{ $canhBao->hocKy->nam_hoc }}</td>
                </tr>
                <tr>
                    <td>Loại cảnh báo:</td>
                    <td><strong style="color: #dc3545;">{{ $loaiText }}</strong></td>
                </tr>
                <tr>
                    <td>Lý do:</td>
                    <td style="color: #dc3545; font-weight: 600;">{{ $canhBao->ly_do }}</td>
                </tr>
                <tr>
                    <td>Ngày cảnh báo:</td>
                    <td>{{ $canhBao->ngay_canh_bao->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            @if($canhBao->ghi_chu)
            <p style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 3px solid #6c757d;">
                <strong>📝 Ghi chú:</strong><br>
                {{ $canhBao->ghi_chu }}
            </p>
            @endif

            <!-- Hướng dẫn xử lý -->
            <div class="steps">
                <h3>📋 Hướng dẫn xử lý:</h3>
                <ul>
                    @if($canhBao->loai_canh_bao == 'diem_thap')
                        <li>Liên hệ với giảng viên chủ nhiệm để được tư vấn học tập</li>
                        <li>Đăng ký học lại các môn chưa đạt trong học kỳ tới</li>
                        <li>Tham gia các buổi học phụ đạo (nếu có)</li>
                        <li>Cải thiện phương pháp học tập, tăng cường thời gian tự học</li>
                    @elseif($canhBao->loai_canh_bao == 'vang_nhieu')
                        <li>Tham gia đầy đủ các buổi học còn lại</li>
                        <li>Nộp đơn xin phép học lại môn (nếu đã vắng quá 50%)</li>
                        <li>Liên hệ với giảng viên để được hỗ trợ</li>
                        <li>Chủ động học bù các bài đã vắng</li>
                    @elseif($canhBao->loai_canh_bao == 'no_hoc_phi')
                        <li><strong style="color: #dc3545;">Đóng học phí ngay lập tức</strong> để tránh bị khóa tài khoản</li>
                        <li>Liên hệ Phòng Tài chính nếu có khó khăn: <a href="mailto:taichinh@smis.edu.vn">taichinh@smis.edu.vn</a></li>
                        <li>Xem xét đăng ký hỗ trợ học phí (nếu đủ điều kiện)</li>
                        <li>Liên hệ gia đình để được hỗ trợ tài chính</li>
                    @elseif($canhBao->loai_canh_bao == 'hoc_ky_lien_tiep')
                        <li><strong style="color: #dc3545;">Liên hệ ngay với Phòng Đào tạo</strong></li>
                        <li>Gặp giảng viên chủ nhiệm để được tư vấn</li>
                        <li>Xem xét bảo lưu học tập nếu cần thiết</li>
                        <li>Xây dựng kế hoạch học tập cụ thể cho học kỳ tiếp theo</li>
                    @endif
                </ul>
            </div>

            @if($canhBao->muc_do != 'canh_cao')
            <p class="warning-text">
                ⚠️ <strong>Lưu ý quan trọng:</strong> 
                Đây là mức cảnh báo nghiêm trọng. Nếu không khắc phục, bạn có thể bị đình chỉ học tập hoặc buộc thôi học theo quy định của nhà trường.
            </p>
            @endif

            <!-- Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('sinhvien.canh-bao-hoc-vu.index') }}" class="btn">
                    Xem Chi Tiết Cảnh Báo
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ:
            </p>
            <ul style="font-size: 14px; color: #6c757d;">
                <li><strong>Phòng Đào tạo:</strong> daotao@smis.edu.vn</li>
                <li><strong>Giảng viên chủ nhiệm:</strong> {{ $canhBao->sinhVien->nganh->ten_nganh ?? 'N/A'->ho_ten ?? 'N/A' }}</li>
                <li><strong>Hotline:</strong> 024.xxxx.xxxx</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                Email này được gửi tự động từ <strong>Hệ thống Quản lý Sinh viên S-MIS</strong>
            </p>
            <p style="margin: 0; font-size: 12px;">
                © 2025 S-MIS. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
