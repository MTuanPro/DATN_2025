<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch thi - {{ $sinhVien->ma_sinh_vien }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 20px;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .schedule-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>TRƯỜNG ĐẠI HỌC ABC</h3>
        <h1>Lịch thi học kỳ</h1>
        <p><em>Ngày in: {{ now()->format('d/m/Y H:i') }}</em></p>
    </div>

    <table class="info-table">
        <tr>
            <td width="150"><strong>Mã sinh viên:</strong></td>
            <td>{{ $sinhVien->ma_sinh_vien }}</td>
            <td width="150"><strong>Lớp:</strong></td>
            <td>{{ $sinhVien->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Họ và tên:</strong></td>
            <td>{{ $sinhVien->ho_ten }}</td>
            <td><strong>Ngành:</strong></td>
            <td>{{ $sinhVien->chuyenNganh->nganh->ten_nganh ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="schedule-table">
        <thead>
            <tr>
                <th width="30">STT</th>
                <th width="200">Môn học</th>
                <th width="80">Loại thi</th>
                <th width="80">Ngày thi</th>
                <th width="80">Giờ thi</th>
                <th width="80">Phòng thi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lichThis as $index => $lichThi)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $lichThi->lopHocPhan->monHoc->ten_mon }}</strong><br>
                    <small>{{ $lichThi->lopHocPhan->monHoc->ma_mon }} - {{ $lichThi->lopHocPhan->ma_lop }}</small>
                </td>
                <td style="text-align: center;">
                    @if($lichThi->loai_thi == 'giua_ky')
                        <span class="badge badge-info">Giữa kỳ</span>
                    @elseif($lichThi->loai_thi == 'cuoi_ky')
                        <span class="badge badge-danger">Cuối kỳ</span>
                    @else
                        <span class="badge badge-warning">Thi lại</span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $lichThi->ngay_thi->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $lichThi->gio_bat_dau }}<br>{{ $lichThi->gio_ket_thuc }}</td>
                <td style="text-align: center;"><strong>{{ $lichThi->phongHoc->ten_phong }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Không có lịch thi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Lưu ý quan trọng:</strong></p>
        <ul>
            <li>Sinh viên phải có mặt trước giờ thi <strong>15 phút</strong></li>
            <li>Mang theo <strong>thẻ sinh viên</strong> và <strong>CMND/CCCD</strong></li>
            <li>Không mang tài liệu, điện thoại vào phòng thi (trừ khi được phép)</li>
            <li>Tắt điện thoại trước khi vào phòng thi</li>
            <li>Nghiêm túc thực hiện quy chế thi</li>
        </ul>
    </div>

    <div class="footer">
        <p>--- Hết ---</p>
        <p>Tài liệu được in từ hệ thống S-MIS</p>
    </div>
</body>
</html>
