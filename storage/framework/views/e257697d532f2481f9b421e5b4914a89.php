<?php $__env->startSection('title', 'Báo cáo & Thống kê'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo & Thống kê</h3>
                    <p class="text-subtitle text-muted">Tổng quan hệ thống quản lý</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Báo cáo</li>
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
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('admin.reports.index')); ?>" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="<?php echo e($startDate); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-filter"></i> Lọc
                                        </button>
                                        <a href="<?php echo e(route('admin.reports.export')); ?>" class="btn btn-success">
                                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        
        <section class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Tổng người dùng</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e(number_format($totalUsers)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Mới trong kỳ</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e(number_format($usersInPeriod)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Đang hoạt động</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e(number_format($activeUsers)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="bi bi-lock-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Bị khóa</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e(number_format($lockedUsers)); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="row">
            <div class="col-12 col-lg-8">
                
                <div class="card">
                    <div class="card-header">
                        <h4>Phân bố người dùng theo vai trò</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vai trò</th>
                                        <th>Mã vai trò</th>
                                        <th>Số lượng</th>
                                        <th>Tỷ lệ %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><span class="badge bg-primary"><?php echo e($role->ten_vai_tro); ?></span></td>
                                            <td><code><?php echo e($role->ma_vai_tro); ?></code></td>
                                            <td class="font-semibold"><?php echo e(number_format($role->total)); ?></td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success"
                                                        style="width: <?php echo e($totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0); ?>%">
                                                    </div>
                                                </div>
                                                <span
                                                    class="small"><?php echo e($totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0); ?>%</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Tổng cộng:</td>
                                        <td><?php echo e(number_format($totalUsers)); ?></td>
                                        <td>100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h4>Hoạt động đăng nhập theo ngày</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Số lượt đăng nhập</th>
                                        <th>Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $loginsByDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $login): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e(\Carbon\Carbon::parse($login->date)->format('d/m/Y')); ?></td>
                                            <td><strong><?php echo e($login->total); ?></strong></td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-info"
                                                        style="width: <?php echo e($loginsByDay->max('total') > 0 ? round(($login->total / $loginsByDay->max('total')) * 100) : 0); ?>%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Chưa có dữ liệu đăng nhập
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                
                <div class="card">
                    <div class="card-header">
                        <h4>Xác thực Email</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Đã xác thực</span>
                                <span class="badge bg-success"><?php echo e(number_format($verifiedUsers)); ?></span>
                            </div>
                            <div class="progress progress-sm mb-3">
                                <div class="progress-bar bg-success"
                                    style="width: <?php echo e($totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100) : 0); ?>%">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Chưa xác thực</span>
                                <span class="badge bg-warning"><?php echo e(number_format($unverifiedUsers)); ?></span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning"
                                    style="width: <?php echo e($totalUsers > 0 ? round(($unverifiedUsers / $totalUsers) * 100) : 0); ?>%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h4>Đăng nhập gần đây</h4>
                    </div>
                    <div class="card-content pb-4">
                        <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="recent-message d-flex px-4 py-2">
                                <div class="avatar avatar-md">
                                    <img src="<?php echo e(asset('assets/images/faces/1.jpg')); ?>" alt="Avatar">
                                </div>
                                <div class="name ms-3">
                                    <h6 class="mb-0"><?php echo e($user->name); ?></h6>
                                    <p class="text-muted mb-0 small"><?php echo e($user->email); ?></p>
                                    <p class="text-muted mb-0 small">
                                        <i class="bi bi-clock"></i>
                                        <?php echo e(\Carbon\Carbon::parse($user->lan_dang_nhap_cuoi)->diffForHumans()); ?>

                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="px-4 py-3 text-center text-muted">
                                Chưa có hoạt động
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h4>Báo cáo chi tiết</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('admin.reports.users')); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-people"></i> Báo cáo Người dùng
                            </a>
                            <a href="<?php echo e(route('admin.reports.permissions')); ?>" class="btn btn-outline-success">
                                <i class="bi bi-shield-check"></i> Báo cáo Phân quyền
                            </a>
                            <a href="<?php echo e(route('admin.reports.export')); ?>" class="btn btn-outline-info">
                                <i class="bi bi-download"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>