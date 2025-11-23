<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Biên lai học phí - {{ $hocPhi->sinhVien->ma_sinh_vien }}</title>
    <style>
        @page {
            margin: 10mm 15mm;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .receipt-header {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-top {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 35%;
            text-align: right;
            vertical-align: top;
        }
        .institution-name {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .institution-subtitle {
            font-size: 9pt;
            margin-bottom: 8px;
        }
        .institution-info {
            font-size: 9pt;
            line-height: 1.5;
        }
        .receipt-code {
            font-size: 9pt;
            font-weight: 600;
        }
        .receipt-code .code-number {
            display: inline-block;
            border: 1.5px solid #000;
            padding: 3px 10px;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        
        .receipt-title-section {
            text-align: center;
            margin: 20px 0 15px 0;
        }
        .receipt-title {
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .receipt-subtitle {
            font-size: 10pt;
            font-style: italic;
            margin-bottom: 3px;
        }
        .semester-info {
            font-size: 10pt;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .student-info-section {
            margin: 15px 0;
            border: 1.5px solid #000;
            padding: 12px 15px;
        }
        .info-row-table {
            width: 100%;
        }
        .info-row-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            width: 130px;
            padding-right: 10px;
        }
        .info-value {
            border-bottom: 1px dotted #333;
            padding-bottom: 2px;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1.5px solid #000;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
        }
        .detail-table thead th {
            background-color: #f0f0f0;
            font-weight: 700;
            text-align: center;
            font-size: 10pt;
            text-transform: uppercase;
        }
        .detail-table tbody td {
            font-size: 10pt;
        }
        .detail-table .text-center {
            text-align: center;
        }
        .detail-table .text-right {
            text-align: right;
        }
        .detail-table .subject-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .detail-table .amount {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .detail-table tfoot td {
            font-weight: 700;
            background-color: #f9f9f9;
        }
        
        .summary-section {
            margin: 20px 0;
            float: right;
            width: 400px;
            border: 1.5px solid #000;
        }
        .summary-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-label {
            display: table-cell;
            padding: 8px 12px;
            font-weight: 600;
            width: 60%;
            border-right: 1px solid #000;
        }
        .summary-value {
            display: table-cell;
            padding: 8px 12px;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }
        .summary-total {
            background-color: #e8e8e8;
            font-size: 12pt;
        }
        .summary-remaining {
            background-color: #f5f5f5;
        }
        .summary-paid {
            background-color: #e8f5e9;
        }
        .amount-in-words {
            clear: both;
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
            font-style: italic;
        }
        
        .payment-history-section {
            clear: both;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .section-header {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .signature-section {
            margin-top: 30px;
            clear: both;
        }
        .signature-table {
            width: 100%;
        }
        .signature-box {
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-date {
            font-style: italic;
            margin-bottom: 5px;
            font-size: 10pt;
        }
        .signature-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 60px;
            font-size: 10pt;
        }
        .signature-name {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 9pt;
        }
        
        .receipt-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            color: #333;
        }
        .footer-note {
            font-style: italic;
            margin-bottom: 5px;
        }
        
        .text-bold { font-weight: 700; }
        .text-uppercase { text-transform: uppercase; }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120pt;
            color: rgba(0, 0, 0, 0.03);
            font-weight: 900;
            z-index: -1;
            letter-spacing: 15px;
        }
        
        .status-stamp {
            position: absolute;
            top: 180px;
            right: 80px;
            width: 150px;
            height: 150px;
            border: 4px solid #DC143C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            opacity: 0.7;
        }
        .status-stamp-text {
            color: #DC143C;
            font-size: 20pt;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    @if($hocPhi->so_tien_con_lai == 0)
    <div class="watermark">DA THANH TOAN</div>
    @endif
    
    <div class="receipt-header">
        <div class="header-top">
            <div class="header-left">
                <div class="institution-name">TRUONG DAI HOC ABC</div>
                <div class="institution-subtitle">PHONG TAI CHINH - KE TOAN</div>
                <div class="institution-info">
                    Dia chi: Trinh Van Bo, Bac Tu Liem, Ha Noi<br>
                    Dien thoai: (024) 1234 5678 - Email: ketoan@abc.edu.vn
                </div>
            </div>
            <div class="header-right">
                <div class="receipt-code">
                    Mau so: 01-TT<br>
                    <span style="font-size: 8pt;">(Ban hanh theo TT so 200/2014/TT-BTC)</span><br>
                    <div class="code-number">{{ str_pad($hocPhi->id, 8, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="receipt-title-section">
        <div class="receipt-title">BIEN LAI THU TIEN</div>
        <div class="receipt-subtitle">(Hoc phi va le phi)</div>
        <div class="semester-info">{{ $hocPhi->hocKy->ten_hoc_ky }} - Nam hoc {{ $hocPhi->hocKy->nam_hoc }}</div>
    </div>
    
    @if($hocPhi->so_tien_con_lai == 0)
    <div class="status-stamp">
        <div class="status-stamp-text">DA<br>THANH TOAN</div>
    </div>
    @endif
    
    <div class="student-info-section">
        <table class="info-row-table">
            <tr>
                <td class="info-label">Ho va ten sinh vien:</td>
                <td class="info-value text-bold text-uppercase">{{ $hocPhi->sinhVien->ho_ten }}</td>
                <td class="info-label" style="width: 100px;">Ma SV:</td>
                <td class="info-value text-bold" style="width: 120px;">{{ $hocPhi->sinhVien->ma_sinh_vien }}</td>
            </tr>
            <tr>
                <td class="info-label">Lop:</td>
                <td class="info-value">{{ $hocPhi->sinhVien->lopHanhChinh->ten_lop ?? 'N/A' }}</td>
                <td class="info-label">Khoa hoc:</td>
                <td class="info-value">{{ $hocPhi->sinhVien->lopHanhChinh->khoaHoc->ten_khoa_hoc ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Khoa:</td>
                <td class="info-value" colspan="3">{{ $hocPhi->sinhVien->lopHanhChinh->nganh->khoa->ten_khoa ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Ly do thu tien:</td>
                <td class="info-value text-bold" colspan="3">HOC PHI HOC KY {{ $hocPhi->hocKy->ten_hoc_ky }} - {{ $hocPhi->hocKy->nam_hoc }}</td>
            </tr>
        </table>
    </div>
    
    <table class="detail-table">
        <thead>
            <tr>
                <th width="40">STT</th>
                <th width="80">MA MON HOC</th>
                <th>TEN MON HOC</th>
                <th width="60">SO TC</th>
                <th width="100">DON GIA (d)</th>
                <th width="120">THANH TIEN (d)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $chiTietHienThi = $hocPhi->chiTietHocPhiMon->where('trang_thai', '!=', 'huy');
                $tongTinChi = 0;
            @endphp
            @foreach ($chiTietHienThi as $index => $ct)
                @php $tongTinChi += $ct->so_tin_chi; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center subject-code">{{ $ct->monHoc->ma_mon }}</td>
                    <td>{{ $ct->monHoc->ten_mon }}</td>
                    <td class="text-center">{{ $ct->so_tin_chi }}</td>
                    <td class="text-right amount">{{ number_format($ct->don_gia_tin_chi, 0, ',', '.') }}</td>
                    <td class="text-right amount">{{ number_format($ct->thanh_tien, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if($hocPhi->phi_dich_vu > 0)
            <tr>
                <td colspan="5" style="text-align: right; font-weight: 600;">Phi dich vu (the sinh vien, bao hiem y te...)</td>
                <td class="text-right amount">{{ number_format($hocPhi->phi_dich_vu, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">TONG CONG:</td>
                <td class="text-center">{{ $tongTinChi }} TC</td>
                <td colspan="2" class="text-right amount" style="font-size: 12pt;">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-label">Tong so tien phai nop:</div>
            <div class="summary-value">{{ number_format($hocPhi->tong_so_tien, 0, ',', '.') }} d</div>
        </div>
        @if($hocPhi->so_tien_da_dong > 0)
        <div class="summary-row summary-paid">
            <div class="summary-label">Da thanh toan:</div>
            <div class="summary-value">{{ number_format($hocPhi->so_tien_da_dong, 0, ',', '.') }} d</div>
        </div>
        @endif
        @if($hocPhi->so_tien_con_lai > 0)
        <div class="summary-row summary-remaining">
            <div class="summary-label">Con phai nop:</div>
            <div class="summary-value" style="color: #d32f2f;">{{ number_format($hocPhi->so_tien_con_lai, 0, ',', '.') }} d</div>
        </div>
        @else
        <div class="summary-row summary-total">
            <div class="summary-label" style="text-transform: uppercase;">Trang thai:</div>
            <div class="summary-value" style="color: #388e3c;">DA HOAN TAT</div>
        </div>
        @endif
    </div>
    
    @php
        if (!function_exists('numberToWords')) {
            function numberToWords($number) {
                $ones = ['', 'mot', 'hai', 'ba', 'bon', 'nam', 'sau', 'bay', 'tam', 'chin'];
                $tens = ['', '', 'hai muoi', 'ba muoi', 'bon muoi', 'nam muoi', 'sau muoi', 'bay muoi', 'tam muoi', 'chin muoi'];
                
                if ($number == 0) return 'khong';
                if ($number < 10) return $ones[$number];
                if ($number < 100) {
                    $ten = floor($number / 10);
                    $one = $number % 10;
                    if ($one == 0) return $tens[$ten];
                    if ($one == 5 && $ten > 1) return $tens[$ten] . ' lam';
                    if ($one == 1 && $ten > 1) return $tens[$ten] . ' mot';
                    return $tens[$ten] . ' ' . $ones[$one];
                }
                
                $result = '';
                if ($number >= 1000000000) {
                    $billion = floor($number / 1000000000);
                    $result .= numberToWords($billion) . ' ty ';
                    $number %= 1000000000;
                }
                if ($number >= 1000000) {
                    $million = floor($number / 1000000);
                    $result .= numberToWords($million) . ' trieu ';
                    $number %= 1000000;
                }
                if ($number >= 1000) {
                    $thousand = floor($number / 1000);
                    $result .= numberToWords($thousand) . ' nghin ';
                    $number %= 1000;
                }
                if ($number >= 100) {
                    $hundred = floor($number / 100);
                    $result .= $ones[$hundred] . ' tram ';
                    $number %= 100;
                }
                if ($number > 0) {
                    if ($number < 10) {
                        $result .= 'le ' . $ones[$number];
                    } else {
                        $result .= numberToWords($number);
                    }
                }
                
                return trim($result);
            }
        }
        
        $amountInWords = ucfirst(numberToWords($hocPhi->tong_so_tien)) . ' dong';
    @endphp
    
    <div class="amount-in-words">
        <strong>So tien viet bang chu:</strong> {{ $amountInWords }}
    </div>
    
    @if ($hocPhi->lichSuDongHocPhi->isNotEmpty())
        <div class="payment-history-section">
            <div class="section-header">CHI TIET THANH TOAN</div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th width="40">STT</th>
                        <th width="120">NGAY THANH TOAN</th>
                        <th width="120">SO TIEN (d)</th>
                        <th>HINH THUC THANH TOAN</th>
                        <th width="140">MA GIAO DICH</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hocPhi->lichSuDongHocPhi as $index => $ls)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $ls->ngay_dong->format('d/m/Y H:i') }}</td>
                            <td class="text-right amount">{{ number_format($ls->so_tien_dong, 0, ',', '.') }}</td>
                            <td class="text-center text-uppercase">
                                @if($ls->phuong_thuc_thanh_toan == 'vnpay')
                                    CHUYEN KHOAN QUA VNPAY
                                @elseif($ls->phuong_thuc_thanh_toan == 'tien_mat')
                                    TIEN MAT
                                @else
                                    {{ strtoupper($ls->phuong_thuc_thanh_toan) }}
                                @endif
                            </td>
                            <td class="text-center subject-code">{{ $ls->ma_giao_dich ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-box" style="width: 33%;">
                    <div class="signature-date"><em>Ngay {{ now()->format('d') }} thang {{ now()->format('m') }} nam {{ now()->format('Y') }}</em></div>
                    <div class="signature-title">Sinh vien nop tien</div>
                    <div class="signature-name">(Ky va ghi ro ho ten)</div>
                </td>
                <td class="signature-box" style="width: 34%;">
                    <div class="signature-date">&nbsp;</div>
                    <div class="signature-title">Nguoi thu tien</div>
                    <div class="signature-name">(Ky va ghi ro ho ten)</div>
                </td>
                <td class="signature-box" style="width: 33%;">
                    <div class="signature-date">&nbsp;</div>
                    <div class="signature-title">Ke toan truong</div>
                    <div class="signature-name">(Ky, dong dau va ghi ro ho ten)</div>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="receipt-footer">
        <div class="footer-note">
            <strong>Luu y:</strong> Day la bien lai thu tien dien tu, co gia tri nhu ban chinh. 
            Sinh vien vui long bao quan bien lai de doi chieu khi can thiet.
        </div>
        <div style="font-size: 8pt; margin-top: 5px;">
            In luc: {{ now()->format('d/m/Y H:i:s') }} | 
            Han nop: {{ $hocPhi->han_dong->format('d/m/Y') }} | 
            Lien he: Phong Tai chinh - Ke toan, DT: (024) 1234 5678
        </div>
    </div>
</body>
</html>
