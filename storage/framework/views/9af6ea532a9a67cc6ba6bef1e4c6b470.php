<?php $__env->startSection('title', 'Danh sách Nợ quá hạn'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Nợ quá hạn</h3>
                    <p class="text-subtitle text-muted">Sinh viên nợ học phí quá hạn - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nợ quá hạn</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Họ tên</th>
                                    <th>Học kỳ</th>
                                    <th>Hạn đóng</th>
                                    <th>Số tiền nợ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $hocPhis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($hocPhis->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($hp->sinhVien->ma_sinh_vien); ?></strong></td>
                                        <td><?php echo e($hp->sinhVien->ho_ten); ?></td>
                                        <td><?php echo e($hp->hocKy->ten_hoc_ky); ?></td>
                                        <td><span class="text-danger"><?php echo e($hp->han_dong->format('d/m/Y')); ?></span></td>
                                        <td><strong class="text-danger"><?php echo e(number_format($hp->so_tien_con_lai, 0, ',', '.')); ?> đ</strong></td>
                                        <td>
                                            <a href="<?php echo e(route('dao-tao.hoc-phi.show', $hp->id)); ?>" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-check-circle" style="font-size: 3rem; color: green;"></i>
                                            <p class="text-muted mt-2">Không có sinh viên nợ quá hạn</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($hocPhis->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($hocPhis->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-phi/overdue.blade.php ENDPATH**/ ?>