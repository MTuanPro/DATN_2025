<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Thời khóa biểu - <?php echo e($sinhVien->ma_sinh_vien); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
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
            margin-top: 10px;
        }

        table.schedule th,
        table.schedule td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 9px;
        }

        table.schedule th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .subject-name {
            font-weight: bold;
            font-size: 9px;
        }

        .subject-code {
            font-size: 8px;
            color: #666;
        }

        .room {
            font-size: 8px;
            margin-top: 2px;
        }

        .teacher {
            font-size: 8px;
            color: #444;
            margin-top: 2px;
        }

        .ly-thuyet {
            background-color: #e3f2fd;
        }

        .thuc-hanh {
            background-color: #fff9e6;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>TRƯỜNG ĐẠI HỌC ABC</h2>
        <h2>THỜI KHÓA BIỂU</h2>
        <p>Học kỳ: <?php echo e($hocKy->ten_hoc_ky); ?> - Năm học: <?php echo e($hocKy->nam_hoc); ?></p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="25%"><strong>Mã sinh viên:</strong></td>
                <td width="25%"><?php echo e($sinhVien->ma_sinh_vien); ?></td>
                <td width="25%"><strong>Họ tên:</strong></td>
                <td width="25%"><?php echo e($sinhVien->ho_ten); ?></td>
            </tr>
            <tr>
                <td><strong>Lớp:</strong></td>
                <td><?php echo e($sinhVien->lopHanhChinh->ma_lop ?? 'N/A'); ?></td>
                <td><strong>Ngày in:</strong></td>
                <td><?php echo e(now()->format('d/m/Y H:i')); ?></td>
            </tr>
        </table>
    </div>

    <table class="schedule">
        <thead>
            <tr>
                <th width="8%">Tiết</th>
                <th width="13%">Thứ 2</th>
                <th width="13%">Thứ 3</th>
                <th width="13%">Thứ 4</th>
                <th width="13%">Thứ 5</th>
                <th width="13%">Thứ 6</th>
                <th width="13%">Thứ 7</th>
                <th width="14%">CN</th>
            </tr>
        </thead>
        <tbody>
            <?php for($tiet = 1; $tiet <= 12; $tiet++): ?>
                <tr>
                    <td><strong><?php echo e($tiet); ?></strong></td>
                    <?php for($thu = 2; $thu <= 8; $thu++): ?>
                        <?php
                            $cell = $thoiKhoaBieu[$thu][$tiet] ?? null;
                        ?>

                        <?php if($cell === 'span'): ?>
                            
                        <?php elseif($cell): ?>
                            <?php
                                $lichHoc = $cell['lich'];
                                $rowspan = $cell['rowspan'];
                                $loaiLop = $lichHoc->lopHocPhan->loai_lop;
                            ?>
                            <td rowspan="<?php echo e($rowspan); ?>"
                                class="<?php echo e($loaiLop == 'ly_thuyet' ? 'ly-thuyet' : 'thuc-hanh'); ?>">
                                <div class="subject-name"><?php echo e($lichHoc->lopHocPhan->monHoc->ten_mon); ?></div>
                                <div class="subject-code"><?php echo e($lichHoc->lopHocPhan->monHoc->ma_mon); ?></div>
                                <div class="room">
                                    <?php if($lichHoc->lichHocCoDinh && $lichHoc->lichHocCoDinh->phongHoc): ?>
                                        Phòng: <?php echo e($lichHoc->lichHocCoDinh->phongHoc->ten_phong); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="teacher">
                                    <?php if($lichHoc->lichHocCoDinh && $lichHoc->lichHocCoDinh->giangVien): ?>
                                        GV: <?php echo e($lichHoc->lichHocCoDinh->giangVien->ho_ten); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer">
        <p><em>Ghi chú: Màu xanh - Lý thuyết, Màu vàng - Thực hành</em></p>
        <p><strong>Sinh viên</strong></p>
        <br><br>
        <p><?php echo e($sinhVien->ho_ten); ?></p>
    </div>
</body>

</html>
<?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/thoi-khoa-bieu/pdf.blade.php ENDPATH**/ ?>