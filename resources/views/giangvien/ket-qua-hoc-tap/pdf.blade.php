<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điểm - {{ $lopHocPhan->ma_lop }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 10px 0;
            font-size: 18px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px 0;
        }

        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        table.diem {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.diem th,
        table.diem td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 11px;
        }

        table.diem th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table.diem td.text-left {
            text-align: left;
            padding-left: 8px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        .footer .sign-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        .footer .sign-box .title {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>TRƯỜNG ĐẠI HỌC ABC</h2>
        <h2>BẢNG ĐIỂM TỔNG KẾT</h2>
        <p style="margin-top: 5px;">Học kỳ: {{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Mã lớp học phần:</td>
            <td>{{ $lopHocPhan->ma_lop_hp }}</td>
        </tr>
        <tr>
            <td>Môn học:</td>
            <td>{{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}</td>
        </tr>
        <tr>
            <td>Số tín chỉ:</td>
            <td>{{ $lopHocPhan->monHoc->so_tin_chi }}</td>
        </tr>
        <tr>
            <td>Giảng viên:</td>
            <td>
                @foreach ($lopHocPhan->giangViens as $gv)
                    {{ $gv->ho_ten }}@if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Sĩ số:</td>
            <td>{{ $danhSachDiem->count() }} sinh viên</td>
        </tr>
    </table>

    <table class="diem">
        <thead>
            <tr>
                <th style="width: 30px;">STT</th>
                <th style="width: 80px;">Mã SV</th>
                <th style="width: 150px;">Họ và tên</th>
                <th style="width: 80px;">Lớp HC</th>
                @if ($cauHinh->chuyen_can_ty_le > 0)
                    <th style="width: 40px;">CC<br>({{ $cauHinh->chuyen_can_ty_le }}%)</th>
                @endif
                @if ($cauHinh->giua_ky_ty_le > 0)
                    <th style="width: 40px;">GK<br>({{ $cauHinh->giua_ky_ty_le }}%)</th>
                @endif
                @if ($cauHinh->cuoi_ky_ty_le > 0)
                    <th style="width: 40px;">CK<br>({{ $cauHinh->cuoi_ky_ty_le }}%)</th>
                @endif
                @if ($cauHinh->thuc_hanh_ty_le > 0)
                    <th style="width: 40px;">TH<br>({{ $cauHinh->thuc_hanh_ty_le }}%)</th>
                @endif
                @if ($cauHinh->tieu_luan_ty_le > 0)
                    <th style="width: 40px;">TL<br>({{ $cauHinh->tieu_luan_ty_le }}%)</th>
                @endif
                <th style="width: 45px;">Hệ 10</th>
                <th style="width: 40px;">Hệ 4</th>
                <th style="width: 40px;">Chữ</th>
                <th style="width: 60px;">Kết quả</th>
            </tr>
        </thead>
        <tbody>
            @forelse($danhSachDiem as $index => $dk)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dk->sinhVien->ma_sinh_vien }}</td>
                    <td class="text-left">{{ $dk->sinhVien->ho_ten }}</td>
                    <td>{{ $dk->sinhVien->nganh->ten_nganh ?? 'N/A' ?? '-' }}</td>
                    @if ($cauHinh->chuyen_can_ty_le > 0)
                        <td>{{ $dk->diem->diem_chuyen_can ?? '-' }}</td>
                    @endif
                    @if ($cauHinh->giua_ky_ty_le > 0)
                        <td>{{ $dk->diem->diem_giua_ky ?? '-' }}</td>
                    @endif
                    @if ($cauHinh->cuoi_ky_ty_le > 0)
                        <td>{{ $dk->diem->diem_cuoi_ky ?? '-' }}</td>
                    @endif
                    @if ($cauHinh->thuc_hanh_ty_le > 0)
                        <td>{{ $dk->diem->diem_thuc_hanh ?? '-' }}</td>
                    @endif
                    @if ($cauHinh->tieu_luan_ty_le > 0)
                        <td>{{ $dk->diem->diem_tieu_luan ?? '-' }}</td>
                    @endif
                    <td>
                        @if ($dk->diem_tong_ket)
                            <strong>{{ $dk->diem_tong_ket['diem_he_10'] }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($dk->diem_tong_ket)
                            {{ $dk->diem_tong_ket['diem_he_4'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($dk->diem_tong_ket)
                            {{ $dk->diem_tong_ket['diem_chu'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($dk->diem_tong_ket)
                            @if ($dk->diem_tong_ket['qua_mon'])
                                Đạt
                            @else
                                Không đạt
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13">Chưa có sinh viên</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-box">
            <div class="title">Giảng viên</div>
            <div>(Ký và ghi rõ họ tên)</div>
        </div>
    </div>
</body>

</html>
