<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảnh báo chuyên cần</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-box strong {
            color: #856404;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .stats-table th,
        .stats-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .stats-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .danger {
            color: #dc3545;
            font-weight: bold;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            border: 1px solid #dee2e6;
            border-top: none;
            font-size: 14px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #435ebe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .recommendation {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .recommendation h3 {
            margin-top: 0;
            color: #0c5460;
        }
        .recommendation ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">⚠️</div>
        <h1>CẢNH BÁO CHUYÊN CẦN</h1>
    </div>

    <div class="content">
        <div class="greeting">
            Kính gửi sinh viên <strong>{{ $sinhVien->ho_ten }}</strong>,
        </div>

        <div class="alert-box">
            <strong>⚠️ CẢNH BÁO:</strong> Tỷ lệ chuyên cần của bạn đã <strong class="danger">vượt mức cho phép</strong> 
            tại lớp học phần <strong>{{ $lopHocPhan->ma_lop_hp }}</strong>.
        </div>

        <p>
            Hệ thống ghi nhận tỷ lệ vắng mặt của bạn đã <strong class="danger">vượt quá 20%</strong>, 
            có nguy cơ ảnh hưởng đến kết quả học tập và điều kiện dự thi.
        </p>

        <h3>📊 Thống kê điểm danh:</h3>
        <table class="stats-table">
            <tr>
                <th>Lớp học phần</th>
                <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
            </tr>
            <tr>
                <th>Môn học</th>
                <td>{{ $lopHocPhan->monHoc->ten_mon }}</td>
            </tr>
            <tr>
                <th>Tổng số buổi học</th>
                <td><strong>{{ $thongKe['tong_buoi'] }}</strong></td>
            </tr>
            <tr>
                <th>Số buổi có mặt</th>
                <td class="success">{{ $thongKe['co_mat'] }}</td>
            </tr>
            <tr>
                <th>Số buổi vắng</th>
                <td class="danger">{{ $thongKe['vang'] }}</td>
            </tr>
            <tr>
                <th>Số buổi đi trễ</th>
                <td class="warning">{{ $thongKe['di_tre'] }}</td>
            </tr>
            <tr>
                <th>Nghỉ phép</th>
                <td>{{ $thongKe['nghi_phep'] }}</td>
            </tr>
            <tr>
                <th>Tỷ lệ chuyên cần</th>
                <td>
                    <strong class="danger" style="font-size: 18px;">{{ $thongKe['ty_le'] }}%</strong>
                    <span class="danger">(Yêu cầu tối thiểu: 80%)</span>
                </td>
            </tr>
        </table>

        <div class="recommendation">
            <h3>💡 Khuyến nghị:</h3>
            <ul>
                <li>Tham gia đầy đủ các buổi học còn lại</li>
                <li>Liên hệ với giảng viên bộ môn để được tư vấn</li>
                <li>Cải thiện tỷ lệ chuyên cần trước kỳ thi</li>
                <li>Đảm bảo đạt tối thiểu 80% để đủ điều kiện dự thi</li>
            </ul>
        </div>

        <p style="margin-top: 20px;">
            <strong>Lưu ý:</strong> Theo quy định, sinh viên vắng mặt <strong class="danger">quá 20%</strong> 
            tổng số buổi học sẽ <strong>KHÔNG được dự thi</strong> môn học.
        </p>

        <p>
            Nếu có vấn đề gì cần hỗ trợ, vui lòng liên hệ với giảng viên hoặc phòng Đào tạo.
        </p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Phòng Đào tạo</strong><br>
            <em>Hệ thống quản lý sinh viên S-MIS</em>
        </p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống S-MIS</p>
        <p>Vui lòng không trả lời email này</p>
        <p style="margin-top: 10px;">
            <small>© {{ date('Y') }} Trường Đại học ABC. All rights reserved.</small>
        </p>
    </div>
</body>
</html>
