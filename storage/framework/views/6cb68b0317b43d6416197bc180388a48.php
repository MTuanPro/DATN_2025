<?php $__env->startSection('title', 'Học phí của tôi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Học phí của tôi</h3>
                    <p class="text-subtitle text-muted">Xem công nợ học phí - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học phí</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Tổng học phí</h6>
                            <h3><?php echo e(number_format($tongHocPhi, 0, ',', '.')); ?> đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Đã đóng</h6>
                            <h3><?php echo e(number_format($daDong, 0, ',', '.')); ?> đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6>Còn lại</h6>
                            <h3><?php echo e(number_format($conLai, 0, ',', '.')); ?> đ</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Học kỳ</th>
                                    <th>Tín chỉ</th>
                                    <th>Tổng học phí</th>
                                    <th>Đã đóng</th>
                                    <th>Còn lại</th>
                                    <th>Hạn đóng</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $hocPhis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($hocPhis->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($hp->hocKy->ten_hoc_ky); ?> - <?php echo e($hp->hocKy->nam_hoc); ?></strong></td>
                                        <td><span class="badge bg-primary"><?php echo e($hp->tong_tin_chi_dang_ky); ?> TC</span></td>
                                        <td><?php echo e(number_format($hp->tong_so_tien, 0, ',', '.')); ?> đ</td>
                                        <td class="text-success"><?php echo e(number_format($hp->so_tien_da_dong, 0, ',', '.')); ?> đ</td>
                                        <td class="text-danger"><?php echo e(number_format($hp->so_tien_con_lai, 0, ',', '.')); ?> đ</td>
                                        <td><?php echo e($hp->han_dong->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php if($hp->trang_thai == 'da_nop_du'): ?>
                                                <span class="badge bg-success">Đã nộp đủ</span>
                                            <?php elseif($hp->trang_thai == 'qua_han'): ?>
                                                <span class="badge bg-danger">Quá hạn</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Chưa nộp đủ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('sinh-vien.hoc-phi.show', $hp->id)); ?>" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <p class="text-muted">Chưa có dữ liệu học phí</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($hocPhis->hasPages()): ?>
                        <?php echo e($hocPhis->links()); ?>

                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/hoc-phi/index.blade.php ENDPATH**/ ?>