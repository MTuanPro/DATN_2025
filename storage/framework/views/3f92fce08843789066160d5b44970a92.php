<?php $__env->startSection('title', 'Danh sách Khoa'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Khoa</h3>
                    <p class="text-subtitle text-muted">Danh sách khoa trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Khoa</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tìm kiếm & Lọc</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('dao-tao.khoa.index')); ?>" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tìm kiếm</label>
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Mã khoa, Tên khoa..." value="<?php echo e(request('keyword')); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sắp xếp</label>
                                    <select name="sort" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="id" <?php echo e(request('sort') == 'id' ? 'selected' : ''); ?>>Theo ID
                                        </option>
                                        <option value="ten_khoa" <?php echo e(request('sort') == 'ten_khoa' ? 'selected' : ''); ?>>Theo
                                            Tên</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Thứ tự</label>
                                    <select name="direction" class="form-select">
                                        <option value="asc" <?php echo e(request('direction') == 'asc' ? 'selected' : ''); ?>>Tăng
                                            dần</option>
                                        <option value="desc"
                                            <?php echo e(request('direction', 'asc') == 'desc' ? 'selected' : ''); ?>>Giảm dần</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary icon icon-left">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        <a href="<?php echo e(route('dao-tao.khoa.index')); ?>"
                                            class="btn btn-secondary icon icon-left">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách Khoa</h5>
                    <a href="<?php echo e(route('dao-tao.khoa.create')); ?>" class="btn btn-primary icon icon-left">
                        <i class="bi bi-plus-circle"></i> Thêm Khoa
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã Khoa</th>
                                    <th>Tên Khoa</th>
                                    <th>Trưởng Khoa</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $khoas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $khoa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($khoas->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($khoa->ma_khoa); ?></strong></td>
                                        <td><?php echo e($khoa->ten_khoa); ?></td>
                                        <td><?php echo e($khoa->truong_khoa_id ?? '-'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.khoa.edit', $khoa->id)); ?>"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('dao-tao.khoa.destroy', $khoa->id)); ?>"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa khoa này?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> Không có dữ liệu
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($khoas->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/khoa/index.blade.php ENDPATH**/ ?>