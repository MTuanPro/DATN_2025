<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biên lai học phí</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
        }
        .signature {
            float: right;
            text-align: center;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>TRƯỜNG ĐẠI HỌC ABC</h2>
        <h3>BIÊN LAI HỌC PHÍ</h3>
        <p>Học kỳ: {{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</p>
    </div>

    <p><strong>Sinh viên:</strong> {{ $hocPhi->sinhVien->ho_ten }}</p>
    <p><strong>MSSV:</strong> {{ $hocPhi->sinhVien->ma_sinh_vien }}</p>
    <p><strong>Lớp:</strong> {{ $hocPhi->sinhVien->lop->ten_lop ?? 'N/A' }}</p>
    <p><strong>Ngày in:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã môn</th>
                <th>Tên môn học</th>
                <th class="text-center">Số tín chỉ</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hocPhi->chiTietHocPhi as $index => $ct)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $ct->monHoc->ma_mon }}</td>
                    <td>{{ $ct->monHoc->ten_mon }}</td>
                    <td class="text-center">{{ $ct->so_tin_chi }}</td>
                    <td class="text-right">{{ number_format($ct->don_gia_tin_chi, 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($ct->thanh_tien, 0, ',', '.') }} đ</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Tổng học phí:</strong></td>
                <td class="text-right"><strong>{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} đ</strong></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Đã đóng:</strong></td>
                <td class="text-right"><strong>{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} đ</strong></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Còn lại:</strong></td>
                <td class="text-right"><strong>{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} đ</strong></td>
            </tr>
        </tfoot>
    </table>

    @if ($hocPhi->lichSuDongHocPhi->isNotEmpty())
        <h4>Lịch sử thanh toán:</h4>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày đóng</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hocPhi->lichSuDongHocPhi as $index => $ls)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $ls->ngay_dong->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($ls->so_tien, 0, ',', '.') }} đ</td>
                        <td>{{ $ls->phuong_thuc_thanh_toan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div class="signature">
            <p><em>Ngày ... tháng ... năm ...</em></p>
            <p><strong>Người lập biên lai</strong></p>
            <br><br>
            <p>________________________</p>
        </div>
    </div>
</body>
</html>
