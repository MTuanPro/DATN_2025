<?php $__env->startSection('title', 'Mẫu thông báo tự động'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Mẫu thông báo tự động</h3>
                    <p class="text-subtitle text-muted">Quản lý các mẫu thông báo tự động của hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mẫu thông báo tự động</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Danh sách mẫu thông báo</h4>
                        <a href="<?php echo e(route('admin.mau-thong-bao.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tạo mẫu mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Loại thông báo</th>
                                    <th>Tiêu đề mẫu</th>
                                    <th>Đối tượng</th>
                                    <th>Mức độ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $mauThongBaos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mau): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e($mau->loai_thong_bao); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($mau->tieu_de_mau); ?></td>
                                        <td><?php echo e($mau->doi_tuong_mac_dinh ?? 'Tất cả'); ?></td>
                                        <td>
                                            <?php if($mau->muc_do_uu_tien == 'rat_quan_trong'): ?>
                                                <span class="badge bg-danger">Rất quan trọng</span>
                                            <?php elseif($mau->muc_do_uu_tien == 'quan_trong'): ?>
                                                <span class="badge bg-warning">Quan trọng</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Bình thường</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($mau->kich_hoat): ?>
                                                <span class="badge bg-success">Bật</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tắt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('admin.mau-thong-bao.edit', $mau->id)); ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Chưa có mẫu thông báo nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/mau-thong-bao/index.blade.php ENDPATH**/ ?>