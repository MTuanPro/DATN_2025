<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bảng điểm - {{ $sinhVien->ma_sinh_vien }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 10px 0;
            font-weight: bold;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px;
        }
        table.grades {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table.grades th,
        table.grades td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        table.grades th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        table.grades td.left {
            text-align: left;
        }
        .summary {
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BẢNG ĐIỂM SINH VIÊN</h1>
        @if($hocKy)
            <p>{{ $hocKy->ten_hoc_ky }} - Năm học {{ $hocKy->nam_hoc }}</p>
        @else
            <p>Tổng hợp tất cả học kỳ</p>
        @endif
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="50%"><strong>Họ và tên:</strong> {{ $sinhVien->ho_ten }}</td>
                <td width="50%"><strong>MSSV:</strong> {{ $sinhVien->ma_sinh_vien }}</td>
            </tr>
            <tr>
                <td><strong>Lớp:</strong> {{ $sinhVien->lop->ten_lop ?? 'N/A' }}</td>
                <td><strong>Khóa học:</strong> {{ $sinhVien->khoa_hoc ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Ngành:</strong> {{ $sinhVien->lop->nganh->ten_nganh ?? 'N/A' }}</td>
                <td><strong>Khoa:</strong> {{ $sinhVien->lop->nganh->khoa->ten_khoa ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table class="grades">
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="10%">Mã môn</th>
                <th width="30%" class="left">Tên môn học</th>
                <th width="5%">TC</th>
                <th width="10%">Điểm QT</th>
                <th width="10%">Điểm GK</th>
                <th width="10%">Điểm CK</th>
                <th width="10%">Điểm TK</th>
                <th width="10%">Điểm chữ</th>
            </tr>
        </thead>
        <tbody>
            @php
                $stt = 1;
            @endphp
            @forelse($ketQuas as $kq)
                @php
                    $monHoc = $kq->lopHocPhanSinhVien->lopHocPhan->monHoc;
                @endphp
                <tr>
                    <td>{{ $stt++ }}</td>
                    <td>{{ $monHoc->ma_mon }}</td>
                    <td class="left">{{ $monHoc->ten_mon }}</td>
                    <td>{{ $monHoc->so_tin_chi }}</td>
                    <td>{{ $kq->diem_qua_trinh ? number_format($kq->diem_qua_trinh, 1) : '-' }}</td>
                    <td>{{ $kq->diem_giua_ky ? number_format($kq->diem_giua_ky, 1) : '-' }}</td>
                    <td>{{ $kq->diem_cuoi_ky ? number_format($kq->diem_cuoi_ky, 1) : '-' }}</td>
                    <td>{{ $kq->diem_he_10 ? number_format($kq->diem_he_10, 2) : '-' }}</td>
                    <td><strong>{{ $kq->diem_chu ?? '-' }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Chưa có kết quả học tập</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p>Tổng số tín chỉ tích lũy: {{ $tongTinChi }}</p>
        <p>Điểm trung bình tích lũy (GPA): {{ number_format($gpa, 2) }}</p>
    </div>

    <div class="footer">
        <p><em>Ngày in: {{ date('d/m/Y H:i') }}</em></p>
    </div>
</body>
</html>
