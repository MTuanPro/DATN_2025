<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biên lai thanh toán học phí</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h2 {
            background-color: #f3f4f6;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .info-label {
            width: 40%;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            width: 60%;
            color: #333;
        }
        .amount-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
        }
        .amount-box .label {
            font-size: 14px;
            color: #92400e;
            margin-bottom: 10px;
        }
        .amount-box .value {
            font-size: 32px;
            font-weight: bold;
            color: #b45309;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .note {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            font-size: 11px;
            color: #991b1b;
        }
        .qr-code-placeholder {
            width: 120px;
            height: 120px;
            border: 2px dashed #ccc;
            display: inline-block;
            text-align: center;
            line-height: 120px;
            color: #999;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>BIÊN LAI THANH TOÁN HỌC PHÍ</h1>
            <p>TRƯỜNG ĐẠI HỌC S-MIS</p>
            <p>Mã biên lai: <strong>{{ $lichSu->ma_giao_dich }}</strong></p>
        </div>

        <!-- Thông tin sinh viên -->
        <div class="info-section">
            <h2>THÔNG TIN SINH VIÊN</h2>
            <div class="info-row">
                <div class="info-label">Mã sinh viên:</div>
                <div class="info-value"><strong>{{ $hocPhi->sinhVien->ma_sinh_vien }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Họ và tên:</div>
                <div class="info-value"><strong>{{ $hocPhi->sinhVien->ho_ten }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lớp hành chính:</div>
                <div class="info-value">{{ $hocPhi->sinhVien->lopHanhChinh->ma_lop ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Học kỳ:</div>
                <div class="info-value"><strong>{{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</strong></div>
            </div>
        </div>

        <!-- Thông tin thanh toán -->
        <div class="info-section">
            <h2>THÔNG TIN THANH TOÁN</h2>
            <div class="info-row">
                <div class="info-label">Mã giao dịch:</div>
                <div class="info-value"><strong>{{ $lichSu->ma_giao_dich }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Ngày thanh toán:</div>
                <div class="info-value"><strong>{{ \Carbon\Carbon::parse($lichSu->ngay_dong)->format('d/m/Y H:i') }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Phương thức thanh toán:</div>
                <div class="info-value">
                    @if($lichSu->phuong_thuc_thanh_toan == 'tien_mat')
                        Tiền mặt
                    @elseif($lichSu->phuong_thuc_thanh_toan == 'chuyen_khoan')
                        Chuyển khoản
                        @if($lichSu->ngan_hang)
                            ({{ $lichSu->ngan_hang }})
                        @endif
                    @elseif($lichSu->phuong_thuc_thanh_toan == 'VNPay')
                        VNPay
                    @else
                        {{ $lichSu->phuong_thuc_thanh_toan }}
                    @endif
                </div>
            </div>
            @if($lichSu->nguoiThu)
            <div class="info-row">
                <div class="info-label">Người thu:</div>
                <div class="info-value">{{ $lichSu->nguoiThu->ho_ten ?? 'N/A' }}</div>
            </div>
            @endif
        </div>

        <!-- Số tiền -->
        <div class="amount-box">
            <div class="label">SỐ TIỀN ĐÃ THANH TOÁN</div>
            <div class="value">{{ number_format($lichSu->so_tien_dong, 0, ',', '.') }} đ</div>
        </div>

        <!-- Chi tiết học phí -->
        <div class="info-section">
            <h2>CHI TIẾT HỌC PHÍ</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Môn học</th>
                        <th>Số tín chỉ</th>
                        <th>Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $chiTietHocPhi = \App\Models\ChiTietHocPhiMon::where('hoc_phi_hoc_ky_id', $hocPhi->id)->get();
                        $stt = 1;
                    @endphp
                    @foreach($chiTietHocPhi as $ct)
                        <tr>
                            <td class="text-center">{{ $stt++ }}</td>
                            <td>{{ $ct->monHoc->ma_mon }} - {{ $ct->monHoc->ten_mon }}</td>
                            <td class="text-center">{{ $ct->so_tin_chi }}</td>
                            <td class="text-right">{{ number_format($ct->don_gia_tin_chi, 0, ',', '.') }} đ</td>
                            <td class="text-right"><strong>{{ number_format($ct->thanh_tien, 0, ',', '.') }} đ</strong></td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="text-right"><strong>Phí dịch vụ:</strong></td>
                        <td class="text-right"><strong>{{ number_format($hocPhi->phi_dich_vu, 0, ',', '.') }} đ</strong></td>
                    </tr>
                    <tr style="background-color: #dbeafe;">
                        <td colspan="4" class="text-right"><strong>TỔNG CỘNG:</strong></td>
                        <td class="text-right"><strong style="font-size: 16px;">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Đã thanh toán:</strong></td>
                        <td class="text-right" style="color: #059669;"><strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Còn lại:</strong></td>
                        <td class="text-right" style="color: #dc2626;"><strong>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($lichSu->ghi_chu)
        <div class="note">
            <strong>Ghi chú:</strong> {{ $lichSu->ghi_chu }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div><strong>NGƯỜI NỘP TIỀN</strong></div>
                    <div class="signature-line">
                        {{ $hocPhi->sinhVien->ho_ten }}
                    </div>
                </div>
                <div class="signature-box">
                    <div><strong>NGƯỜI THU TIỀN</strong></div>
                    <div class="signature-line">
                        {{ $lichSu->nguoiThu->ho_ten ?? 'Phòng Đào tạo' }}
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 30px; font-size: 11px; color: #666;">
                <p>Biên lai này có giá trị xác nhận thanh toán học phí.</p>
                <p>Vui lòng lưu giữ biên lai này để đối chiếu khi cần thiết.</p>
                <p>Ngày in: {{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

