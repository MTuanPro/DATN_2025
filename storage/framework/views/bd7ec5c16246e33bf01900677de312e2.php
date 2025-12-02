<?php $__env->startSection('title', 'Danh sách Học kỳ'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Danh sách Học kỳ</h3>
                    <p class="text-subtitle text-muted">Quản lý học kỳ trong hệ thống</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học kỳ</li>
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
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách Học kỳ</h5>
                        <a href="<?php echo e(route('dao-tao.hoc-ky.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Thêm Học kỳ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form action="<?php echo e(route('dao-tao.hoc-ky.index')); ?>" method="GET" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm theo tên học kỳ hoặc năm học..." value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên học kỳ</th>
                                    <th>Năm học</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Thời gian đăng ký</th>
                                    <th class="text-center">Hiện tại</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hocKy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="<?php echo e($hocKy->la_hoc_ky_hien_tai ? 'table-success' : ''); ?>">
                                        <td><?php echo e($hocKys->firstItem() + $index); ?></td>
                                        <td><strong><?php echo e($hocKy->ten_hoc_ky); ?></strong></td>
                                        <td><?php echo e($hocKy->nam_hoc); ?></td>
                                        <td><?php echo e($hocKy->ngay_bat_dau->format('d/m/Y')); ?></td>
                                        <td><?php echo e($hocKy->ngay_ket_thuc->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php if($hocKy->ngay_bat_dau_dang_ky && $hocKy->ngay_ket_thuc_dang_ky): ?>
                                                <div>
                                                    <?php echo e($hocKy->ngay_bat_dau_dang_ky->format('d/m/Y')); ?>

                                                    -
                                                    <?php echo e($hocKy->ngay_ket_thuc_dang_ky->format('d/m/Y')); ?>

                                                </div>
                                                <?php
                                                    $now = now();
                                                    $batDau = $hocKy->ngay_bat_dau_dang_ky;
                                                    $ketThuc = $hocKy->ngay_ket_thuc_dang_ky;
                                                ?>
                                                <?php if($hocKy->dang_mo_dang_ky ?? false): ?>
                                                    <small class="text-success fw-bold">✓ Đang mở</small>
                                                <?php else: ?>
                                                    <?php if($now < $batDau): ?>
                                                        <small class="text-warning">Chưa mở</small>
                                                    <?php elseif($now > $ketThuc): ?>
                                                        <small class="text-danger">Đã đóng</small>
                                                    <?php else: ?>
                                                        <small class="text-secondary">Đã đóng</small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <small class="text-muted">Chưa thiết lập</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($hocKy->la_hoc_ky_hien_tai): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Hiện tại
                                                </span>
                                                <br>
                                                <?php if($hocKy->ngay_bat_dau_dang_ky && $hocKy->ngay_ket_thuc_dang_ky): ?>
                                                    <form action="<?php echo e(route('dao-tao.hoc-ky.mo-dang-ky', $hocKy->id)); ?>"
                                                        method="POST" class="d-inline mt-1">
                                                        <?php echo csrf_field(); ?>
                                                        <?php if($hocKy->dang_mo_dang_ky ?? false): ?>
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Bạn có chắc muốn đóng đăng ký?')">
                                                                <i class="bi bi-lock"></i> Đóng đăng ký
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="submit" class="btn btn-sm btn-info"
                                                                onclick="return confirm('Bạn có chắc muốn mở đăng ký?')">
                                                                <i class="bi bi-unlock"></i> Mở đăng ký
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <form action="<?php echo e(route('dao-tao.hoc-ky.set-hien-tai', $hocKy->id)); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Đặt làm học kỳ hiện tại?')">
                                                        Đặt hiện tại
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('dao-tao.hoc-ky.edit', $hocKy->id)); ?>"
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if(!$hocKy->la_hoc_ky_hien_tai): ?>
                                                    <form action="<?php echo e(route('dao-tao.hoc-ky.destroy', $hocKy->id)); ?>"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa học kỳ này?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có học kỳ nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-3">
                        <?php echo e($hocKys->links()); ?>

                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/hoc-ky/index.blade.php ENDPATH**/ ?>