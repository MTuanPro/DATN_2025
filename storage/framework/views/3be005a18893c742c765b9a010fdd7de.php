<?php $__env->startSection('title', 'Quản lý Nhóm quyền'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Nhóm quyền</h3>
                    <p class="text-subtitle text-muted">Danh sách tất cả nhóm quyền trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nhóm quyền</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Danh sách Nhóm quyền</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="<?php echo e(route('admin.nhom-quyen.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm nhóm quyền
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="<?php echo e(route('admin.nhom-quyen.index')); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm theo mã, tên hoặc mô tả..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Mã nhóm quyền</th>
                                    <th>Tên nhóm quyền</th>
                                    <th>Mô tả</th>
                                    <th>Số quyền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $nhomQuyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nhomQuyen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><code><?php echo e($nhomQuyen->ma_nhom); ?></code></td>
                                        <td><strong><?php echo e($nhomQuyen->ten_nhom); ?></strong></td>
                                        <td><?php echo e(Str::limit($nhomQuyen->mo_ta, 50)); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo e($nhomQuyen->quyens_count); ?> quyền</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('admin.nhom-quyen.edit', $nhomQuyen)); ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.nhom-quyen.destroy', $nhomQuyen)); ?>" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm quyền này?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">Không có nhóm quyền nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($nhomQuyens->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/nhom-quyen/index.blade.php ENDPATH**/ ?>