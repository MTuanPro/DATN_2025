<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Giấy xác nhận sinh viên - {{ $sinhVien->ma_sinh_vien }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.8;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header-left {
            float: left;
            width: 50%;
            text-align: center;
            font-weight: bold;
        }
        .header-right {
            float: right;
            width: 50%;
            text-align: center;
            font-weight: bold;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .title {
            text-align: center;
            margin: 40px 0 30px 0;
        }
        .title h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .content {
            text-align: justify;
            margin: 30px 0;
        }
        .content p {
            margin: 10px 0;
            text-indent: 40px;
        }
        .info {
            margin: 20px 0;
            padding-left: 40px;
        }
        .info p {
            margin: 8px 0;
            text-indent: 0;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        .signature-left {
            float: left;
            width: 50%;
        }
        .signature-right {
            float: right;
            width: 50%;
        }
        .signature p {
            margin: 5px 0;
        }
        .signature .position {
            font-weight: bold;
            text-transform: uppercase;
        }
        .signature .name {
            margin-top: 80px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="header-left">
            TRƯỜNG ĐẠI HỌC ABC<br>
            PHÒNG ĐÀO TẠO
        </div>
        <div class="header-right">
            CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
            Độc lập - Tự do - Hạnh phúc
        </div>
    </div>

    <div class="title">
        <p style="font-style: italic; margin-bottom: 5px;">Số: ......../GXN-ĐT</p>
        <h1>GIẤY XÁC NHẬN</h1>
        <p style="font-style: italic;">V/v Xác nhận sinh viên đang học tập tại trường</p>
    </div>

    <div class="content">
        <p style="text-align: center; font-weight: bold; text-indent: 0;">
            TRƯỜNG ĐẠI HỌC ABC - PHÒNG ĐÀO TẠO
        </p>
        <p>
            Căn cứ Quyết định thành lập Trường Đại học ABC;
        </p>
        <p>
            Căn cứ Quy chế đào tạo đại học và cao đẳng hệ chính quy theo hệ thống tín chỉ hiện hành;
        </p>
        <p>
            Căn cứ hồ sơ và kết quả học tập của sinh viên;
        </p>
        <p style="text-align: center; font-weight: bold; text-transform: uppercase;">
            GIẤY XÁC NHẬN
        </p>
    </div>

    <div class="info">
        <p><strong>Họ và tên:</strong> {{ $sinhVien->ho_ten }}</p>
        <p><strong>Ngày sinh:</strong> {{ $sinhVien->ngay_sinh ? \Carbon\Carbon::parse($sinhVien->ngay_sinh)->format('d/m/Y') : 'N/A' }}</p>
        <p><strong>Giới tính:</strong> {{ $sinhVien->gioi_tinh == 'nam' ? 'Nam' : 'Nữ' }}</p>
        <p><strong>Nơi sinh:</strong> {{ $sinhVien->noi_sinh ?? 'N/A' }}</p>
        <p><strong>CMND/CCCD:</strong> {{ $sinhVien->cccd ?? 'N/A' }}</p>
        <p><strong>Mã số sinh viên:</strong> {{ $sinhVien->ma_sinh_vien }}</p>
        <p><strong>Lớp:</strong> {{ $sinhVien->lop->ten_lop ?? 'N/A' }}</p>
        <p><strong>Ngành học:</strong> {{ $sinhVien->lop->nganh->ten_nganh ?? 'N/A' }}</p>
        <p><strong>Khoa:</strong> {{ $sinhVien->lop->nganh->khoa->ten_khoa ?? 'N/A' }}</p>
        <p><strong>Khóa học:</strong> {{ $sinhVien->khoa_hoc ?? 'N/A' }}</p>
        @if($hocKyHienTai)
        <p><strong>Học kỳ hiện tại:</strong> {{ $hocKyHienTai->ten_hoc_ky }} - {{ $hocKyHienTai->nam_hoc }}</p>
        @endif
        <p><strong>Tổng số tín chỉ tích lũy:</strong> {{ $tongTinChiDat }} tín chỉ</p>
    </div>

    <div class="content">
        <p>
            Hiện đang là sinh viên chính thức, đang học tập tại Trường Đại học ABC.
        </p>
        <p>
            Nhà trường xác nhận để sinh viên sử dụng cho mục đích: 
            <strong>______________________________________</strong>
        </p>
        <p style="text-indent: 0; text-align: right; margin-right: 80px;">
            <em>Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</em>
        </p>
    </div>

    <div class="signature clearfix">
        <div class="signature-left">
            <p class="position">Sinh viên</p>
            <p style="font-style: italic;">(Ký và ghi rõ họ tên)</p>
            <p class="name">{{ $sinhVien->ho_ten }}</p>
        </div>
        <div class="signature-right">
            <p class="position">Trưởng phòng đào tạo</p>
            <p style="font-style: italic;">(Ký tên và đóng dấu)</p>
            <p class="name">&nbsp;</p>
        </div>
    </div>
</body>
</html>
