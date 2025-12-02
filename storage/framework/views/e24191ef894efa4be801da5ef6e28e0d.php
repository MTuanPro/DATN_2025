<?php $__env->startSection('title', 'Quản lý Ca học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Quản lý Ca học</h3>
                    <p class="text-subtitle text-muted">Thiết lập thời gian học cho các ca trong ngày</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ca học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <section class="section">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="bi bi-clock-history text-primary"></i> Quản lý Ca học
                                    </h5>
                                    <p class="text-muted mb-0 small">
                                        Thiết lập và quản lý khung giờ học cho các ca trong ngày
                                    </p>
                                </div>
                                <div class="mt-3 mt-md-0">
                                    <a href="<?php echo e(route('dao-tao.ca-hoc.create')); ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Thêm Ca học mới
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                        <i class="bi bi-clock fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Tổng số ca học</h6>
                                    <h3 class="mb-0"><?php echo e($caHocList->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Đang hoạt động</h6>
                                    <h3 class="mb-0"><?php echo e($caHocList->where('trang_thai', true)->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                        <i class="bi bi-pause-circle fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Không hoạt động</h6>
                                    <h3 class="mb-0"><?php echo e($caHocList->where('trang_thai', false)->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                        <i class="bi bi-calendar3 fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 text-muted small">Ca học/ngày</h6>
                                    <h3 class="mb-0"><?php echo e($caHocList->where('trang_thai', true)->count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul text-primary"></i> Danh sách Ca học
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">

                    <?php $__empty_1 = true; $__currentLoopData = $caHocList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $caHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border-bottom <?php echo e($index === 0 ? 'border-top' : ''); ?>">
                            <div class="p-4 hover-bg-light">
                                <div class="row align-items-center">
                                    <!-- STT & Icon -->
                                    <div class="col-auto">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <span class="fs-5 fw-bold text-primary"><?php echo e($caHoc->thu_tu); ?></span>
                                        </div>
                                    </div>

                                    <!-- Thông tin ca học -->
                                    <div class="col-lg-3 col-md-4">
                                        <h6 class="mb-1 fw-bold"><?php echo e($caHoc->ten_ca); ?></h6>
                                        <p class="mb-0 small text-muted">
                                            <i class="bi bi-list-ol"></i> Thứ tự: <?php echo e($caHoc->thu_tu); ?>

                                        </p>
                                    </div>

                                    <!-- Thời gian -->
                                    <div class="col-lg-3 col-md-4 mt-3 mt-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="text-center me-2">
                                                <div class="badge bg-light text-dark border px-3 py-2">
                                                    <i class="bi bi-clock text-primary"></i>
                                                    <div class="mt-1 fw-bold"><?php echo e(date('H:i', strtotime($caHoc->gio_bat_dau))); ?></div>
                                                </div>
                                            </div>
                                            <div class="mx-2">
                                                <i class="bi bi-arrow-right text-muted"></i>
                                            </div>
                                            <div class="text-center ms-2">
                                                <div class="badge bg-light text-dark border px-3 py-2">
                                                    <i class="bi bi-clock-fill text-success"></i>
                                                    <div class="mt-1 fw-bold"><?php echo e(date('H:i', strtotime($caHoc->gio_ket_thuc))); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            $start = \Carbon\Carbon::parse($caHoc->gio_bat_dau);
                                            $end = \Carbon\Carbon::parse($caHoc->gio_ket_thuc);
                                            $diff = $start->diffInMinutes($end);
                                        ?>
                                        <div class="mt-2">
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <i class="bi bi-hourglass-split"></i> <?php echo e($diff); ?> phút
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Trạng thái & Ghi chú -->
                                    <div class="col-lg-3 col-md-4 mt-3 mt-lg-0">
                                        <div class="mb-2">
                                            <?php if($caHoc->trang_thai): ?>
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle"></i> Đang hoạt động
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary px-3 py-2">
                                                    <i class="bi bi-pause-circle"></i> Tạm dừng
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($caHoc->ghi_chu): ?>
                                            <p class="mb-0 small text-muted">
                                                <i class="bi bi-sticky"></i> <?php echo e(Str::limit($caHoc->ghi_chu, 40)); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-lg-3 col-md-12 mt-3 mt-lg-0 text-lg-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('dao-tao.ca-hoc.edit', $caHoc->id)); ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil-square"></i> Sửa
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo e($caHoc->id); ?>)"
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i> Xóa
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-<?php echo e($caHoc->id); ?>" 
                                              action="<?php echo e(route('dao-tao.ca-hoc.destroy', $caHoc->id)); ?>" 
                                              method="POST" 
                                              style="display: none;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted">Chưa có ca học nào</h5>
                            <p class="text-muted mb-4">Bắt đầu bằng cách thêm ca học mới cho hệ thống</p>
                            <a href="<?php echo e(route('dao-tao.ca-hoc.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Thêm Ca học đầu tiên
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .card {
        border-radius: 10px;
    }
    
    .badge {
        font-weight: 500;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(id) {
        if (confirm('⚠️ Bạn có chắc chắn muốn xóa ca học này?\n\nLưu ý: Việc xóa ca học có thể ảnh hưởng đến các lịch học đã được xếp.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/ca-hoc/index.blade.php ENDPATH**/ ?>