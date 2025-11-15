<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Thời khóa biểu - {{ $sinhVien->ma_sinh_vien }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            font-weight: bold;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px;
        }
        table.schedule {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table.schedule th,
        table.schedule td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: top;
        }
        table.schedule th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .class-info {
            font-size: 10px;
            line-height: 1.3;
            text-align: left;
            padding: 3px !important;
        }
        .class-name {
            font-weight: bold;
            color: #000;
        }
        .class-room {
            color: #666;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>THỜI KHÓA BIỂU</h1>
        <p>{{ $hocKy->ten_hoc_ky }} - Năm học {{ $hocKy->nam_hoc }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="50%"><strong>Họ và tên:</strong> {{ $sinhVien->ho_ten }}</td>
                <td width="50%"><strong>MSSV:</strong> {{ $sinhVien->ma_sinh_vien }}</td>
            </tr>
            <tr>
                <td><strong>Lớp:</strong> {{ $sinhVien->lop->ten_lop ?? 'N/A' }}</td>
                <td><strong>Khoa:</strong> {{ $sinhVien->lop->nganh->khoa->ten_khoa ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table class="schedule">
        <thead>
            <tr>
                <th width="8%">Tiết/Thứ</th>
                <th width="13%">Thứ 2</th>
                <th width="13%">Thứ 3</th>
                <th width="13%">Thứ 4</th>
                <th width="13%">Thứ 5</th>
                <th width="13%">Thứ 6</th>
                <th width="13%">Thứ 7</th>
                <th width="14%">Chủ nhật</th>
            </tr>
        </thead>
        <tbody>
            @for($tiet = 1; $tiet <= 12; $tiet++)
                <tr>
                    <td><strong>{{ $tiet }}</strong></td>
                    @for($thu = 2; $thu <= 8; $thu++)
                        <td class="class-info">
                            @if(isset($tkb[$thu][$tiet]))
                                @foreach($tkb[$thu][$tiet] as $lich)
                                    <div style="margin-bottom: 5px;">
                                        <div class="class-name">{{ $lich->lopHocPhan->monHoc->ten_mon }}</div>
                                        <div class="class-room">
                                            {{ $lich->phongHoc->ten_phong ?? 'N/A' }}
                                        </div>
                                        @if($lich->giangVien)
                                            <div style="font-size: 9px;">GV: {{ $lich->giangVien->ho_ten }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                &nbsp;
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        <p><em>Ngày in: {{ date('d/m/Y H:i') }}</em></p>
    </div>
</body>
</html>
