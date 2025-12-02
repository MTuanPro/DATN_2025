<?php $__env->startSection('title', 'Cấu hình Học phí'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Cấu hình Học phí</h3>
                    <p class="text-subtitle text-muted">Quản lý cấu hình học phí theo năm học - PHASE 8</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.hoc-phi.index')); ?>">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cấu hình</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông báo -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Content -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách cấu hình</h5>
                        <a href="<?php echo e(route('dao-tao.hoc-phi.cau-hinh.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Thêm cấu hình mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Năm học</th>
                                    <th>Đơn giá/tín chỉ</th>
                                    <th>Phí dịch vụ</th>
                                    <th>Áp dụng từ</th>
                                    <th>Áp dụng đến</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($cauHinhs->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($ch->nam_hoc); ?></strong></td>
                                        <td><?php echo e(number_format($ch->don_gia_tren_tin_chi, 0, ',', '.')); ?> đ</td>
                                        <td><?php echo e(number_format($ch->phi_dich_vu, 0, ',', '.')); ?> đ</td>
                                        <td><?php echo e(\Carbon\Carbon::parse($ch->ap_dung_tu_ngay)->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php if($ch->ap_dung_den_ngay): ?>
                                                <?php echo e(\Carbon\Carbon::parse($ch->ap_dung_den_ngay)->format('d/m/Y')); ?>

                                            <?php else: ?>
                                                <span class="badge bg-info">Vô thời hạn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($ch->isActive()): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đang áp dụng</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Không hoạt động</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo e(route('dao-tao.hoc-phi.cau-hinh.edit', $ch->id)); ?>"
                                                    class="btn btn-warning btn-sm" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.hoc-phi.cau-hinh.destroy', $ch->id)); ?>"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa cấu hình này?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                                <p class="text-muted mt-2">Chưa có cấu hình học phí nào</p>
                                                <a href="<?php echo e(route('dao-tao.hoc-phi.cau-hinh.create')); ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus-circle"></i> Thêm cấu hình đầu tiên
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($cauHinhs->hasPages()): ?>
                        <div class="d-flex justify-content-center">
                            <?php echo e($cauHinhs->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-phi/cau-hinh/index.blade.php ENDPATH**/ ?>