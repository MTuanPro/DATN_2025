<?php $__env->startSection('title', 'Map Vai trò - Quyền'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Map Vai trò - Quyền</h3>
                    <p class="text-subtitle text-muted">Quản lý quyền cho từng vai trò</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Map Vai trò - Quyền</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ma trận Quyền theo Vai trò</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.vai-tro-quyen.update-matrix')); ?>" method="POST"
                        id="permissionMatrixForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="200">Vai trò \ Quyền</th>
                                        <?php $__currentLoopData = $nhomQuyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nhomQuyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th colspan="<?php echo e($nhomQuyen->quyens->count()); ?>" class="text-center bg-light">
                                                <?php echo e($nhomQuyen->ten_nhom); ?>

                                            </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <?php $__currentLoopData = $nhomQuyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nhomQuyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $__currentLoopData = $nhomQuyen->quyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="text-center" style="min-width: 100px;">
                                                    <small><?php echo e($quyen->ten_quyen); ?></small>
                                                </th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaiTro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="fw-bold">
                                                <?php echo e($vaiTro->ten_vai_tro); ?>

                                                <br>
                                                <small class="text-muted">(<?php echo e($vaiTro->ma_vai_tro); ?>)</small>
                                            </td>
                                            <?php $__currentLoopData = $nhomQuyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nhomQuyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $nhomQuyen->quyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[<?php echo e($vaiTro->id); ?>][]"
                                                                value="<?php echo e($quyen->id); ?>"
                                                                id="permission_<?php echo e($vaiTro->id); ?>_<?php echo e($quyen->id); ?>"
                                                                data-role="<?php echo e($vaiTro->id); ?>"
                                                                data-permission="<?php echo e($quyen->id); ?>"
                                                                <?php echo e(in_array($quyen->id, $matrix[$vaiTro->id] ?? []) ? 'checked' : ''); ?>>
                                                        </div>
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu thay đổi
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Làm mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="row mt-3">
                <?php $__currentLoopData = $vaiTros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaiTro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo e($vaiTro->ten_vai_tro); ?></h6>
                                <p class="text-muted mb-2">
                                    <small><?php echo e($vaiTro->mo_ta); ?></small>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <span>Số quyền:</span>
                                    <span class="badge bg-info"><?php echo e(count($matrix[$vaiTro->id] ?? [])); ?> quyền</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // AJAX update khi click checkbox (tùy chọn)
                const checkboxes = document.querySelectorAll('.permission-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const roleId = this.dataset.role;
                        const permissionId = this.dataset.permission;
                        const isChecked = this.checked;

                        // Có thể thêm AJAX call để update real-time
                        console.log(`Role ${roleId} - Permission ${permissionId}: ${isChecked}`);
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/vai-tro-quyen/index.blade.php ENDPATH**/ ?>