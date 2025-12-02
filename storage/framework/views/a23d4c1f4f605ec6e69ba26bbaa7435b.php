<?php $__env->startSection('title', 'Lịch sử điểm danh'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch sử điểm danh</h3>
                    <p class="text-subtitle text-muted">Xem lịch sử điểm danh các lớp học phần</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lịch sử điểm danh</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="<?php echo e(route('sinh-vien.diem-danh.index')); ?>" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="hoc_ky_id" class="form-label mb-1">Học kỳ</label>
                            <select name="hoc_ky_id" id="hoc_ky_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- Tất cả học kỳ --</option>
                                <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($hk->id); ?>" <?php echo e($hocKyId == $hk->id ? 'selected' : ''); ?>>
                                        <?php echo e($hk->ten_hoc_ky); ?> - <?php echo e($hk->nam_hoc); ?>

                                        <?php if($hk->la_hoc_ky_hien_tai): ?> (Hiện tại) <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php if($lopHocPhans->isEmpty()): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Không tìm thấy lớp học phần nào trong học kỳ này.
                        </div>
                    </div>
                </div>
            <?php else: ?>
                
                <?php $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lhp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $thongKe = $thongKeData[$lhp->id] ?? [
                            'tong_buoi' => 0,
                            'co_mat' => 0,
                            'vang' => 0,
                            'di_tre' => 0,
                            'nghi_phep' => 0,
                            'ty_le' => 0
                        ];
                    ?>

                    <div class="card mb-4">
                        
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-journal-text text-primary"></i>
                                        <?php echo e($lhp->lopHocPhan->monHoc->ten_mon); ?> (<?php echo e($lhp->lopHocPhan->monHoc->ma_mon); ?>) - <?php echo e($lhp->lopHocPhan->ma_lop_hp); ?>

                                    </h6>
                                    <small class="text-muted">
                                        <span class="me-3">
                                            <i class="bi bi-person"></i>
                                            <?php if($lhp->lopHocPhan->giangVienChinh && $lhp->lopHocPhan->giangVienChinh->giangVien): ?>
                                                <?php echo e($lhp->lopHocPhan->giangVienChinh->giangVien->ho_ten); ?>

                                            <?php else: ?>
                                                Chưa phân công
                                            <?php endif; ?>
                                        </span>
                                    </small>
                                </div>
                                <?php if($thongKe['tong_buoi'] > 0): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="printTable('table-<?php echo e($lhp->id); ?>')">
                                            <i class="bi bi-printer"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyTable('table-<?php echo e($lhp->id); ?>')">
                                            <i class="bi bi-clipboard"></i> Copy
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="exportToExcel('table-<?php echo e($lhp->id); ?>', '<?php echo e($lhp->lopHocPhan->ma_lop_hp); ?>')">
                                            <i class="bi bi-file-earmark-excel"></i> Excel
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="exportToCSV('table-<?php echo e($lhp->id); ?>', '<?php echo e($lhp->lopHocPhan->ma_lop_hp); ?>')">
                                            <i class="bi bi-filetype-csv"></i> CSV
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="exportToPDF('table-<?php echo e($lhp->id); ?>', '<?php echo e($lhp->lopHocPhan->monHoc->ten_mon); ?>', '<?php echo e($lhp->lopHocPhan->ma_lop_hp); ?>')">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <?php if($thongKe['tong_buoi'] == 0): ?>
                                <div class="p-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> Chưa có dữ liệu điểm danh cho lớp này.
                                    </div>
                                </div>
                            <?php else: ?>
                                
                                <div class="px-4 py-3 border-bottom bg-light">
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Vắng: <span class="text-danger"><?php echo e($thongKe['vang']); ?>/<?php echo e($thongKe['tong_buoi']); ?> (<?php echo e(round(($thongKe['vang']/$thongKe['tong_buoi'])*100, 0)); ?>%)</span> trên tổng số buổi điểm danh</strong>
                                            <span class="float-end">
                                                <strong>Tỷ lệ chuyên cần: 
                                                    <span class="
                                                        <?php if($thongKe['ty_le'] >= 90): ?> text-success
                                                        <?php elseif($thongKe['ty_le'] >= 80): ?> text-warning
                                                        <?php else: ?> text-danger
                                                        <?php endif; ?>
                                                    "><?php echo e($thongKe['ty_le']); ?>%</span>
                                                </strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0" id="table-<?php echo e($lhp->id); ?>">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">STT</th>
                                                <th>Ngày</th>
                                                <th>Tiết</th>
                                                <th>Phòng học</th>
                                                <th>Giảng viên</th>
                                                <th>Trạng thái</th>
                                                <th>Thời gian điểm danh</th>
                                                <th>Ghi chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $diemDanhList = \App\Models\DiemDanh::where('lop_hoc_phan_sinh_vien_id', $lhp->id)
                                                    ->with(['lichHocChiTiet.phongHoc', 'lichHocChiTiet.giangVien'])
                                                    ->join('lich_hoc_chi_tiet', 'diem_danh.lich_hoc_chi_tiet_id', '=', 'lich_hoc_chi_tiet.id')
                                                    ->select('diem_danh.*', 'lich_hoc_chi_tiet.ngay_hoc', 'lich_hoc_chi_tiet.tiet_bat_dau', 'lich_hoc_chi_tiet.tiet_ket_thuc')
                                                    ->orderBy('lich_hoc_chi_tiet.ngay_hoc', 'desc')
                                                    ->get();
                                            ?>
                                            
                                            <?php $__currentLoopData = $diemDanhList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($index + 1); ?></td>
                                                    <td>
                                                        <?php echo e(\Carbon\Carbon::parse($dd->ngay_hoc)->format('d/m/Y')); ?> - 
                                                        <?php echo e(\Carbon\Carbon::parse($dd->ngay_hoc)->translatedFormat('l')); ?>

                                                    </td>
                                                    <td><?php echo e($dd->tiet_bat_dau); ?></td>
                                                    <td>
                                                        <?php if($dd->lichHocChiTiet && $dd->lichHocChiTiet->phongHoc): ?>
                                                            <?php echo e($dd->lichHocChiTiet->phongHoc->ten_phong); ?>

                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($dd->lichHocChiTiet && $dd->lichHocChiTiet->giangVien): ?>
                                                            <?php echo e($dd->lichHocChiTiet->giangVien->ho_ten); ?>

                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($dd->trang_thai == 'co_mat'): ?>
                                                            <span class="text-success fw-bold">Present</span>
                                                        <?php elseif($dd->trang_thai == 'vang'): ?>
                                                            <span class="text-danger fw-bold">Absent</span>
                                                        <?php elseif($dd->trang_thai == 'di_tre'): ?>
                                                            <span class="text-warning fw-bold">Late</span>
                                                        <?php elseif($dd->trang_thai == 'nghi_phep'): ?>
                                                            <span class="text-info fw-bold">Excused</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($dd->thoi_gian_diem_danh): ?>
                                                            <?php echo e(\Carbon\Carbon::parse($dd->thoi_gian_diem_danh)->format('H:i d/m/Y')); ?>

                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($dd->ghi_chu ?? '-'); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>

                                
                                <div class="px-4 py-2 border-top bg-light">
                                    <small class="text-muted">
                                        Đang xem 1 đến <?php echo e($diemDanhList->count()); ?> trong tổng số <?php echo e($thongKe['tong_buoi']); ?> bản ghi
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    @media print {
        .btn-group, .breadcrumb, nav, .card-header .btn-group {
            display: none !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
// Print table
function printTable(tableId) {
    var printContents = document.getElementById(tableId).outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = '<table class="table table-bordered">' + printContents + '</table>';
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

// Copy table
function copyTable(tableId) {
    var table = document.getElementById(tableId);
    var range = document.createRange();
    range.selectNode(table);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    alert('Đã sao chép bảng vào clipboard!');
}

// Export to Excel
function exportToExcel(tableId, fileName) {
    var table = document.getElementById(tableId);
    var wb = XLSX.utils.table_to_book(table, {sheet: "Điểm danh"});
    XLSX.writeFile(wb, 'DiemDanh_' + fileName + '.xlsx');
}

// Export to CSV
function exportToCSV(tableId, fileName) {
    var table = document.getElementById(tableId);
    var csv = [];
    var rows = table.querySelectorAll('tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(','));
    }
    
    var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    var downloadLink = document.createElement('a');
    downloadLink.download = 'DiemDanh_' + fileName + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

// Export to PDF
function exportToPDF(tableId, monHoc, maLop) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    
    doc.setFont("helvetica");
    doc.setFontSize(16);
    doc.text('LỊCH SỬ ĐIỂM DANH', 148, 15, { align: 'center' });
    
    doc.setFontSize(11);
    doc.text('Môn: ' + monHoc, 14, 25);
    doc.text('Lớp: ' + maLop, 14, 32);
    
    const table = document.getElementById(tableId);
    doc.autoTable({
        html: table,
        startY: 38,
        theme: 'grid',
        styles: { fontSize: 8, font: 'helvetica' },
        headStyles: { fillColor: [66, 139, 202] }
    });
    
    doc.save('DiemDanh_' + maLop + '.pdf');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/diem-danh/index.blade.php ENDPATH**/ ?>