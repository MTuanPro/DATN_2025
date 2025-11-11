<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tieuDe }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .header .date {
            font-size: 11px;
            font-style: italic;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tbody tr:hover {
            background-color: #f0f0f0;
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
            font-size: 11px;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signature-name {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TRƯỜNG ĐẠI HỌC ABC</h1>
        <h2>{{ $tieuDe }}</h2>
        <p class="date">Ngày xuất: {{ $ngayXuat }}</p>
    </div>

    @if($loaiBaoCao === 'sinh-vien')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Mã SV</th>
                    <th style="width: 25%">Họ tên</th>
                    <th style="width: 20%">Khoa</th>
                    <th style="width: 20%">Ngành</th>
                    <th style="width: 15%">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $sv)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $sv->ma_sinh_vien }}</td>
                        <td>{{ $sv->ho_ten }}</td>
                        <td>{{ $sv->chuyenNganh->nganh->khoa->ten_khoa ?? '' }}</td>
                        <td>{{ $sv->chuyenNganh->nganh->ten_nganh ?? '' }}</td>
                        <td>{{ $sv->trangThaiHocTap->ten_trang_thai ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($loaiBaoCao === 'ket-qua')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Mã SV</th>
                    <th style="width: 25%">Họ tên</th>
                    <th style="width: 30%">Môn học</th>
                    <th class="text-center" style="width: 10%">Điểm TB</th>
                    <th class="text-center" style="width: 15%">Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $kq)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $kq->lopHocPhanSinhVien->sinhVien->ma_sinh_vien ?? '' }}</td>
                        <td>{{ $kq->lopHocPhanSinhVien->sinhVien->ho_ten ?? '' }}</td>
                        <td>{{ $kq->lopHocPhanSinhVien->lopHocPhan->monHoc->ten_mon ?? '' }}</td>
                        <td class="text-center">{{ number_format($kq->diem_tong_ket, 2) }}</td>
                        <td class="text-center">{{ $kq->qua_mon ? 'Đạt' : 'Không đạt' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($loaiBaoCao === 'diem-danh')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Mã SV</th>
                    <th style="width: 25%">Họ tên</th>
                    <th style="width: 15%">Lớp HP</th>
                    <th class="text-center" style="width: 15%">Ngày học</th>
                    <th class="text-center" style="width: 15%">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $dd)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $dd->sinhVien->ma_sinh_vien ?? '' }}</td>
                        <td>{{ $dd->sinhVien->ho_ten ?? '' }}</td>
                        <td>{{ $dd->lichHocChiTiet->lopHocPhan->ma_lop_hp ?? '' }}</td>
                        <td class="text-center">{{ $dd->lichHocChiTiet->ngay_hoc ?? '' }}</td>
                        <td class="text-center">
                            @if($dd->trang_thai === 'co_mat')
                                Có mặt
                            @elseif($dd->trang_thai === 'vang')
                                Vắng
                            @elseif($dd->trang_thai === 'di_tre')
                                Đi trễ
                            @elseif($dd->trang_thai === 'nghi_phep')
                                Nghỉ phép
                            @else
                                {{ $dd->trang_thai }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($loaiBaoCao === 'hoc-phi')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Mã SV</th>
                    <th style="width: 25%">Họ tên</th>
                    <th style="width: 20%">Học kỳ</th>
                    <th class="text-right" style="width: 17%">Tổng phí (đ)</th>
                    <th class="text-right" style="width: 18%">Đã đóng (đ)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $hp)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $hp->sinhVien->ma_sinh_vien ?? '' }}</td>
                        <td>{{ $hp->sinhVien->ho_ten ?? '' }}</td>
                        <td>{{ $hp->hocKy->ten_hoc_ky ?? '' }}</td>
                        <td class="text-right">{{ number_format($hp->tong_hoc_phi) }}</td>
                        <td class="text-right">{{ number_format($hp->da_dong) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($loaiBaoCao === 'canh-bao')
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%">STT</th>
                    <th style="width: 15%">Mã SV</th>
                    <th style="width: 25%">Họ tên</th>
                    <th style="width: 15%">Loại</th>
                    <th class="text-center" style="width: 15%">Mức độ</th>
                    <th class="text-center" style="width: 15%">Ngày CB</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $cb)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $cb->sinhVien->ma_sinh_vien ?? '' }}</td>
                        <td>{{ $cb->sinhVien->ho_ten ?? '' }}</td>
                        <td>{{ $cb->loai_canh_bao }}</td>
                        <td class="text-center">{{ $cb->muc_do }}</td>
                        <td class="text-center">{{ $cb->ngay_canh_bao }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Tổng số bản ghi: <strong>{{ count($items) }}</strong></p>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p class="signature-title">Người lập báo cáo</p>
            <p class="signature-name">(Ký và ghi rõ họ tên)</p>
        </div>
        <div class="signature-box">
            <p class="signature-title">Trưởng phòng Đào tạo</p>
            <p class="signature-name">(Ký và ghi rõ họ tên)</p>
        </div>
    </div>
</body>
</html>
