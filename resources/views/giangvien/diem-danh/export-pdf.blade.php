<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo điểm danh - {{ $lopHocPhan->ma_lop_hp }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 20px;
            margin: 10px 0;
            text-transform: uppercase;
        }
        
        .info {
            margin-bottom: 15px;
        }
        
        .info p {
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        
        .signature {
            display: inline-block;
            text-align: center;
            margin-top: 50px;
        }
        
        @media print {
            body {
                margin: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Nút in (ẩn khi in) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #435ebe; color: white; border: none; cursor: pointer; border-radius: 5px;">
            <span style="margin-right: 5px;">🖨️</span> In báo cáo
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">
            Đóng
        </button>
    </div>

    <div class="header">
        <p><strong>TRƯỜNG ĐẠI HỌC ABC</strong></p>
        <p>KHOA CÔNG NGHỆ THÔNG TIN</p>
        <h1>BÁO CÁO ĐIỂM DANH</h1>
    </div>

    <div class="info">
        <p><strong>Lớp học phần:</strong> {{ $lopHocPhan->ma_lop_hp }}</p>
        <p><strong>Môn học:</strong> {{ $lopHocPhan->monHoc->ten_mon }} ({{ $lopHocPhan->monHoc->ma_mon }})</p>
        <p><strong>Tổng số buổi học:</strong> {{ $tongBuoiHoc }}</p>
        <p><strong>Số sinh viên:</strong> {{ count($baoCao) }}</p>
        <p><strong>Ngày xuất báo cáo:</strong> {{ date('d/m/Y H:i') }}</p>
    </div>

    @if(count($baoCao) > 0)
        @php
            $tongCoMat = collect($baoCao)->sum('stats.co_mat');
            $tongVang = collect($baoCao)->sum('stats.vang');
            $tongDiTre = collect($baoCao)->sum('stats.di_tre');
            $tongNghiPhep = collect($baoCao)->sum('stats.nghi_phep');
            $tyLeTrungBinh = collect($baoCao)->avg('ty_le_co_mat');
        @endphp

        <div style="margin: 20px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
            <p><strong>Thống kê tổng quan:</strong></p>
            <p>✓ Tổng có mặt: <strong>{{ $tongCoMat }}</strong> | 
               ✗ Tổng vắng: <strong>{{ $tongVang }}</strong> | 
               ⏱ Tổng đi trễ: <strong>{{ $tongDiTre }}</strong> | 
               📋 Nghỉ phép: <strong>{{ $tongNghiPhep }}</strong></p>
            <p>📊 Tỷ lệ chuyên cần trung bình: <strong>{{ number_format($tyLeTrungBinh, 1) }}%</strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th style="width: 100px;">Mã SV</th>
                    <th class="text-left">Họ và tên</th>
                    <th style="width: 100px;">Lớp HC</th>
                    <th style="width: 60px;">Tổng buổi</th>
                    <th style="width: 60px;">Có mặt</th>
                    <th style="width: 60px;">Vắng</th>
                    <th style="width: 60px;">Đi trễ</th>
                    <th style="width: 70px;">Nghỉ phép</th>
                    <th style="width: 70px;">Tỷ lệ (%)</th>
                    <th style="width: 80px;">Đánh giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($baoCao as $index => $item)
                    @php
                        $tyLe = $item['ty_le_co_mat'];
                        if ($tyLe >= 90) {
                            $danhGia = 'Xuất sắc';
                        } elseif ($tyLe >= 80) {
                            $danhGia = 'Tốt';
                        } elseif ($tyLe >= 70) {
                            $danhGia = 'Khá';
                        } elseif ($tyLe >= 60) {
                            $danhGia = 'Trung bình';
                        } else {
                            $danhGia = 'Yếu';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item['sinh_vien']->ma_sinh_vien }}</strong></td>
                        <td class="text-left">{{ $item['sinh_vien']->ho_ten }}</td>
                        <td>{{ $item['sinh_vien']->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
                        <td><strong>{{ $item['tong_buoi_hoc'] }}</strong></td>
                        <td><strong>{{ $item['stats']->co_mat }}</strong></td>
                        <td>{{ $item['stats']->vang }}</td>
                        <td>{{ $item['stats']->di_tre }}</td>
                        <td>{{ $item['stats']->nghi_phep }}</td>
                        <td><strong>{{ $tyLe }}%</strong></td>
                        <td class="{{ $tyLe < 60 ? 'text-danger' : '' }}">{{ $danhGia }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; padding: 10px; border: 1px solid #ffc107; background: #fff3cd;">
            <p><strong>Ghi chú:</strong></p>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li><strong>Xuất sắc:</strong> ≥ 90% - Chuyên cần tốt</li>
                <li><strong>Tốt:</strong> 80-89% - Chuyên cần khá tốt</li>
                <li><strong>Khá:</strong> 70-79% - Chuyên cần ổn định</li>
                <li><strong>Trung bình:</strong> 60-69% - Cần cải thiện</li>
                <li><strong>Yếu:</strong> &lt; 60% - Cần quan tâm đặc biệt (màu đỏ)</li>
            </ul>
        </div>
    @else
        <p style="text-align: center; color: #999; padding: 20px;">Không có dữ liệu điểm danh.</p>
    @endif

    <div class="footer">
        <div class="signature">
            <p><em>{{ date('d/m/Y') }}</em></p>
            <p><strong>Giảng viên</strong></p>
            <p style="margin-top: 60px;">(Ký và ghi rõ họ tên)</p>
        </div>
    </div>
</body>
</html>
