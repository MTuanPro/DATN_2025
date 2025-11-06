<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên lớp {{ $lop->ma_lop }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0;
            font-size: 11px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #f0f0f0;
            padding: 6px 4px;
            border: 1px solid #333;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        table td {
            padding: 5px 4px;
            border: 1px solid #666;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .signature {
            display: inline-block;
            text-align: center;
            margin-top: 10px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DANH SÁCH SINH VIÊN LỚP {{ $lop->ma_lop }}</h2>
        <p>{{ $lop->ten_lop }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%"><strong>Khóa học:</strong> {{ $lop->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</td>
            <td width="50%"><strong>Ngày xuất:</strong> {{ $ngayXuat }}</td>
        </tr>
        <tr>
            <td><strong>Ngành:</strong> {{ $lop->nganh->ten_nganh ?? 'N/A' }}</td>
            <td><strong>Sĩ số:</strong> {{ $sinhViens->count() }} sinh viên</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Giảng viên chủ nhiệm:</strong> {{ $giangVien->ho_ten }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="3%">STT</th>
                <th width="10%">Mã SV</th>
                <th width="17%">Họ tên</th>
                <th width="8%">Ngày sinh</th>
                <th width="7%">Giới tính</th>
                <th width="7%">Kỳ</th>
                <th width="17%">Email</th>
                <th width="11%">SĐT</th>
                <th width="12%">Chuyên ngành</th>
                <th width="8%">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sinhViens as $index => $sv)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sv->ma_sinh_vien }}</td>
                    <td>{{ $sv->ho_ten }}</td>
                    <td class="text-center">{{ $sv->ngay_sinh ? $sv->ngay_sinh->format('d/m/Y') : '' }}</td>
                    <td class="text-center">{{ ucfirst($sv->gioi_tinh) }}</td>
                    <td class="text-center">{{ $sv->ky_hien_tai }}</td>
                    <td>{{ $sv->email }}</td>
                    <td>{{ $sv->so_dien_thoai }}</td>
                    <td>{{ $sv->chuyenNganh->ten_chuyen_nganh ?? 'Chưa chọn' }}</td>
                    <td class="text-center">{{ $sv->trangThaiHocTap->ten_trang_thai ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p><em>{{ now()->format('d/m/Y') }}</em></p>
            <p><strong>GIẢNG VIÊN CHỦ NHIỆM</strong></p>
            <p class="signature-line"><strong>{{ $giangVien->ho_ten }}</strong></p>
        </div>
    </div>
</body>

</html>
