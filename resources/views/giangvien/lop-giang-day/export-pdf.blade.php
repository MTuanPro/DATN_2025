<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên - {{ $lopHocPhan->ma_lop_hp }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
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

        .header h3 {
            margin: 5px 0;
            font-size: 16px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        td.center {
            text-align: center;
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
            width: 200px;
            display: inline-block;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            In danh sách
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            Đóng
        </button>
    </div>

    <div class="header">
        <h2>DANH SÁCH SINH VIÊN LỚP HỌC PHẦN</h2>
        <h3>{{ $lopHocPhan->ten_lop_hp }}</h3>
        <p>Mã lớp: <strong>{{ $lopHocPhan->ma_lop_hp }}</strong></p>
    </div>

    <table class="info-table" style="border: none;">
        <tr>
            <td style="border: none;"><strong>Môn học:</strong></td>
            <td style="border: none;">{{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Số tín chỉ:</strong></td>
            <td style="border: none;">{{ $lopHocPhan->monHoc->so_tin_chi }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Học kỳ:</strong></td>
            <td style="border: none;">{{ $lopHocPhan->hocKy->ten_hoc_ky }} ({{ $lopHocPhan->hocKy->nam_hoc }})</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Giảng viên:</strong></td>
            <td style="border: none;">{{ $giangVien->ho_ten }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Số sinh viên:</strong></td>
            <td style="border: none;">{{ $sinhViens->count() }} / {{ $lopHocPhan->suc_chua }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Ngày in:</strong></td>
            <td style="border: none;">{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 12%;">Mã SV</th>
                <th style="width: 25%;">Họ và tên</th>
                <th style="width: 15%;">Lớp</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 12%;">Số điện thoại</th>
                <th style="width: 13%;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sinhViens as $index => $lhpsv)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $lhpsv->sinhVien->ma_sinh_vien }}</td>
                    <td>{{ $lhpsv->sinhVien->ho_ten }}</td>
                    <td class="center">{{ $lhpsv->sinhVien->lopHanhChinh->ma_lop ?? 'N/A' }}</td>
                    <td>{{ $lhpsv->sinhVien->email }}</td>
                    <td class="center">{{ $lhpsv->sinhVien->so_dien_thoai ?? '' }}</td>
                    <td class="center">
                        @if($lhpsv->trang_thai == 'da_xep_lop')
                            Đã xếp lớp
                        @elseif($lhpsv->trang_thai == 'dang_hoc')
                            Đang học
                        @elseif($lhpsv->trang_thai == 'da_hoan_thanh')
                            Hoàn thành
                        @elseif($lhpsv->trang_thai == 'bo_hoc')
                            Bỏ học
                        @elseif($lhpsv->trang_thai == 'huy_dang_ky')
                            Hủy
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Chưa có sinh viên nào trong lớp.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p><em>Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</em></p>
            <p><strong>Giảng viên phụ trách</strong></p>
            <p><em>(Ký và ghi rõ họ tên)</em></p>
            <div class="signature-line"></div>
            <p><strong>{{ $giangVien->ho_ten }}</strong></p>
        </div>
    </div>
</body>
</html>
