<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Báo cáo giảng dạy</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BÁO CÁO GIẢNG DẠY CÁ NHÂN</h2>
        <p>{{ $loaiBaoCao === 'tien-do' ? 'Tiến độ giảng dạy' : ($loaiBaoCao === 'diem-danh' ? 'Điểm danh' : 'Phân tích điểm') }}</p>
    </div>

    <div class="info">
        <p><strong>Giảng viên:</strong> {{ $giangVien->ho_ten }}</p>
        <p><strong>Mã giảng viên:</strong> {{ $giangVien->ma_giang_vien }}</p>
        <p><strong>Ngày xuất:</strong> {{ $ngayXuat }}</p>
    </div>

    @if($loaiBaoCao === 'tien-do')
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã lớp</th>
                <th>Môn học</th>
                <th class="text-center">Tổng buổi</th>
                <th class="text-center">Đã dạy</th>
                <th class="text-center">Tiến độ (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($thongKe as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item['lop']->ma_lop_hp }}</td>
                <td>{{ $item['lop']->monHoc->ten_mon ?? '' }}</td>
                <td class="text-center">{{ $item['tong_buoi'] }}</td>
                <td class="text-center">{{ $item['da_day'] }}</td>
                <td class="text-center">{{ $item['ti_le'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
