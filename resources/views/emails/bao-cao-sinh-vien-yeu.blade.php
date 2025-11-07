<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo sinh viên chuyên cần yếu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #435ebe 0%, #3949ab 100%);
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
        .summary-box {
            background: #f8f9fa;
            border-left: 4px solid #435ebe;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #435ebe;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #435ebe;
        }
        .stat-card.danger .number {
            color: #dc3545;
        }
        .stat-card.warning .number {
            color: #ffc107;
        }
        .stat-card .label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .student-table th,
        .student-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .student-table th {
            background: #435ebe;
            color: white;
            font-weight: bold;
        }
        .student-table tr:hover {
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-yeu {
            background: #f8d7da;
            color: #721c24;
        }
        .status-kem {
            background: #fff3cd;
            color: #856404;
        }
        .ty-le-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .ty-le-warning {
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
        .action-box {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .action-box h3 {
            margin-top: 0;
            color: #0c5460;
        }
        .action-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="icon">📊</div>
        <h1>BÁO CÁO SINH VIÊN CHUYÊN CẦN YẾU</h1>
        <p style="margin: 10px 0 0 0; font-size: 14px;">Lớp chủ nhiệm</p>
    </div>

    <div class="content">
        <div class="greeting">
            Kính gửi <strong>{{ $giangVien->ho_ten }}</strong>,
        </div>

        <div class="summary-box">
            <h3>📋 Tổng quan:</h3>
            <p>
                Hệ thống phát hiện <strong class="ty-le-danger">{{ count($danhSachSinhVien) }} sinh viên</strong> 
                trong lớp chủ nhiệm của Thầy/Cô có tỷ lệ chuyên cần <strong>dưới mức yêu cầu (< 80%)</strong> 
                tại các lớp học phần đang theo học.
            </p>
        </div>

        @if(count($danhSachSinhVien) > 0)
        <h3>📊 Thống kê chi tiết:</h3>
        
        <table class="student-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã SV</th>
                    <th>Họ tên</th>
                    <th>Lớp học phần</th>
                    <th>Môn học</th>
                    <th>Vắng/Tổng</th>
                    <th>Tỷ lệ CC</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($danhSachSinhVien as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item['sinh_vien']->ma_sinh_vien }}</strong></td>
                    <td>{{ $item['sinh_vien']->ho_ten }}</td>
                    <td>{{ $item['lop_hoc_phan']->ma_lop_hp }}</td>
                    <td>{{ $item['lop_hoc_phan']->monHoc->ten_mon }}</td>
                    <td>
                        <span class="ty-le-danger">{{ $item['thong_ke']['vang'] }}</span> / 
                        {{ $item['thong_ke']['tong_buoi'] }}
                    </td>
                    <td>
                        @if($item['thong_ke']['ty_le'] < 50)
                            <strong class="ty-le-danger">{{ $item['thong_ke']['ty_le'] }}%</strong>
                        @else
                            <strong class="ty-le-warning">{{ $item['thong_ke']['ty_le'] }}%</strong>
                        @endif
                    </td>
                    <td>
                        @if($item['thong_ke']['ty_le'] < 50)
                            <span class="status-badge status-yeu">Yếu</span>
                        @else
                            <span class="status-badge status-kem">Kém</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="action-box">
            <h3>💡 Đề xuất hành động:</h3>
            <ul>
                <li><strong>Gặp gỡ trực tiếp:</strong> Nên gặp và trao đổi với các sinh viên để hiểu rõ nguyên nhân</li>
                <li><strong>Theo dõi thường xuyên:</strong> Kiểm tra tình hình học tập và chuyên cần định kỳ</li>
                <li><strong>Phối hợp với gia đình:</strong> Liên hệ phụ huynh nếu cần thiết</li>
                <li><strong>Tư vấn học tập:</strong> Hướng dẫn sinh viên cải thiện kết quả và thái độ học tập</li>
                <li><strong>Báo cáo phòng Đào tạo:</strong> Các trường hợp nghiêm trọng cần báo cáo để có biện pháp kịp thời</li>
            </ul>
        </div>

        <div class="summary-box">
            <p style="margin: 0;">
                <strong>📌 Lưu ý:</strong> Theo quy định, sinh viên vắng mặt <strong class="ty-le-danger">quá 20%</strong> 
                tổng số buổi học sẽ <strong>KHÔNG được dự thi</strong> môn học. Đề nghị Thầy/Cô quan tâm theo dõi 
                và nhắc nhở các sinh viên cải thiện tỷ lệ chuyên cần.
            </p>
        </div>
        @else
        <div class="summary-box">
            <p style="margin: 0; color: #28a745;">
                ✅ <strong>Tốt!</strong> Hiện tại không có sinh viên nào trong lớp chủ nhiệm có vấn đề về chuyên cần.
            </p>
        </div>
        @endif

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Phòng Đào tạo</strong><br>
            <em>Hệ thống quản lý sinh viên S-MIS</em>
        </p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống S-MIS</p>
        <p>Thời gian gửi: {{ date('d/m/Y H:i:s') }}</p>
        <p style="margin-top: 10px;">
            <small>© {{ date('Y') }} Trường Đại học ABC. All rights reserved.</small>
        </p>
    </div>
</body>
</html>
