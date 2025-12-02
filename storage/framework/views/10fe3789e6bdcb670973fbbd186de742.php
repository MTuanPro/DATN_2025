<?php $__env->startSection('title', 'Báo cáo Phân quyền'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Phân quyền</h3>
                    <p class="text-subtitle text-muted">Chi tiết vai trò và quyền hạn</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.reports.index')); ?>">Báo cáo</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Phân quyền</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        
        <section class="row">
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="bi bi-shield-fill-check"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Tổng Vai trò</h6>
                                <h6 class="font-extrabold mb-0"><?php echo e($totalRoles); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="bi bi-key-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Tổng Quyền</h6>
                                <h6 class="font-extrabold mb-0"><?php echo e($totalPermissions); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Nhóm quyền</h6>
                                <h6 class="font-extrabold mb-0"><?php echo e($permissionsByGroup->count()); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Phân bố quyền theo nhóm</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nhóm quyền</th>
                                    <th>Số lượng quyền</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $permissionsByGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><span class="badge bg-info"><?php echo e($group->ten_nhom); ?></span></td>
                                        <td class="font-semibold"><?php echo e($group->total); ?></td>
                                        <td>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary"
                                                    style="width: <?php echo e($totalPermissions > 0 ? round(($group->total / $totalPermissions) * 100, 1) : 0); ?>%">
                                                </div>
                                            </div>
                                            <span
                                                class="small"><?php echo e($totalPermissions > 0 ? round(($group->total / $totalPermissions) * 100, 1) : 0); ?>%</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết quyền theo vai trò</h4>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <span class="badge bg-primary"><?php echo e($role->ten_vai_tro); ?></span>
                                <small class="text-muted ms-2">(<?php echo e($role->quyen->count()); ?> quyền)</small>
                            </h5>
                            <?php if($role->quyen->count() > 0): ?>
                                <div class="row">
                                    <?php
                                        $groupedPermissions = $role->quyen->groupBy('nhomQuyen.ten_nhom');
                                    ?>
                                    <?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <i class="bi bi-folder2-open"></i> <?php echo e($groupName); ?>

                                                    </h6>
                                                    <ul class="list-unstyled mb-0">
                                                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="mb-1">
                                                                <i class="bi bi-check-circle text-success"></i>
                                                                <?php echo e($permission->ten_quyen); ?>

                                                                <code class="small"><?php echo e($permission->ma_quyen); ?></code>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Vai trò này chưa được gán quyền nào.</p>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/reports/permissions.blade.php ENDPATH**/ ?>