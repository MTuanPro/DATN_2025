<?php $__env->startSection('title', 'Bảng điểm tổng hợp'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Bảng điểm tổng hợp</h3>
                    <p class="text-subtitle text-muted">Tổng hợp kết quả học tập qua các học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.diem.index')); ?>">Kết quả học tập</a>
                            </li>
                            <li class="breadcrumb-item active">Bảng điểm tổng hợp</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Thông tin sinh viên</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Mã sinh viên:</strong></td>
                                    <td><?php echo e($sinhVien->ma_sinh_vien ?? '-'); ?></td>
                                    <td width="150"><strong>Lớp:</strong></td>
                                    <td><?php echo e($sinhVien->lopHanhChinh->ten_lop ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td><?php echo e($sinhVien->user->ho_ten ?? $sinhVien->ho_ten ?? '-'); ?></td>
                                    <td><strong>Khoa:</strong></td>
                                    <td><?php echo e($sinhVien->lopHanhChinh->khoa->ten_khoa ?? '-'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="mb-3">Tổng kết</h5>
                                <div class="mb-2">
                                    <span class="text-muted">GPA tích lũy:</span>
                                    <h3 class="text-primary mb-0"><?php echo e(number_format($gpaTichLuy, 2)); ?></h3>
                                </div>
                                <div>
                                    <span class="text-muted">Tín chỉ đạt:</span>
<h4 class="text-success mb-0"><?php echo e($tongTinChiDat); ?>/<?php echo e($tongTinChiHoc); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="<?php echo e(route('sinh-vien.diem.export-pdf')); ?>" class="btn btn-danger">
                            <i class="bi bi-file-pdf"></i> Xuất PDF
                        </a>
                    </div>
                </div>
            </div>

            
            <?php $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hocKyId => $dsMonHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $hocKy = $dsMonHoc->first()->lopHocPhan->hocKy;
                    $tongTinChi = 0;
                    $tongDiemHe4 = 0;
                    $soMonDat = 0;
                ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar3"></i> <?php echo e($hocKy->ten_hoc_ky); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">STT</th>
                                        <th width="100">Mã môn</th>
                                        <th>Tên môn học</th>
                                        <th width="80" class="text-center">Tín chỉ</th>
                                        <th width="100" class="text-center">Điểm (10)</th>
                                        <th width="100" class="text-center">Điểm (4)</th>
                                        <th width="100" class="text-center">Điểm chữ</th>
                                        <th width="100" class="text-center">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $dsMonHoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $monHoc = $item->lopHocPhan->monHoc;
                                            $ketQua = $item->ketQuaHocTap;
                                            $tinChi = $monHoc->so_tin_chi;
                                            $tongTinChi += $tinChi;

                                            if ($ketQua) {
                                                $tongDiemHe4 += $ketQua->diem_he_4 * $tinChi;
                                                if ($ketQua->qua_mon) {
                                                    $soMonDat++;
                                                }
}
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($index + 1); ?></td>
                                            <td><strong><?php echo e($monHoc->ma_mon); ?></strong></td>
                                            <td><?php echo e($monHoc->ten_mon); ?></td>
                                            <td class="text-center"><?php echo e($tinChi); ?></td>
                                            <td class="text-center">
                                                <?php if($ketQua && $ketQua->diem_he_10): ?>
                                                    <strong
                                                        class="text-primary"><?php echo e(number_format($ketQua->diem_he_10, 2)); ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($ketQua && $ketQua->diem_he_4): ?>
                                                    <?php echo e(number_format($ketQua->diem_he_4, 2)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($ketQua && $ketQua->diem_chu): ?>
                                                    <span class="badge bg-<?php echo e($ketQua->diem_chu_badge); ?> fs-6">
                                                        <?php echo e($ketQua->diem_chu); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($ketQua): ?>
                                                    <?php if($ketQua->qua_mon): ?>
                                                        <span class="badge bg-success">Đạt</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Không đạt</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa có</span>
                                                <?php endif; ?>
                                            </td>
</tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng kết học kỳ:</strong></td>
                                        <td class="text-center"><strong><?php echo e($tongTinChi); ?></strong></td>
                                        <td colspan="2" class="text-center">
                                            <strong>GPA:
                                                <?php echo e($tongTinChi > 0 ? number_format($tongDiemHe4 / $tongTinChi, 2) : '0.00'); ?></strong>
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <strong><?php echo e($soMonDat); ?>/<?php echo e($dsMonHoc->count()); ?> môn đạt</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($monHocs->isEmpty()): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Chưa có kết quả học tập nào được công bố</p>
                        <a href="<?php echo e(route('sinh-vien.diem.index')); ?>" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/diem/bang-diem.blade.php ENDPATH**/ ?>