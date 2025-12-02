<?php $__env->startSection('title', 'Lịch dạy cá nhân'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch dạy cá nhân</h3>
                <p class="text-subtitle text-muted">Quản lý lịch giảng dạy theo ngày/tuần/tháng</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch dạy</li>
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
                <form method="get" action="<?php echo e(route('giangvien.schedule.index')); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Chọn ngày:</label>
                                <input type="date" id="date" name="date" class="form-control" value="<?php echo e(request('date', $date)); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="period">Hiển thị:</label>
                                <select id="period" name="period" class="form-select">
                                    <option value="day" <?php echo e($period == 'day' ? 'selected' : ''); ?>>Ngày</option>
                                    <option value="week" <?php echo e($period == 'week' ? 'selected' : ''); ?>>Tuần</option>
                                    <option value="month" <?php echo e($period == 'month' ? 'selected' : ''); ?>>Tháng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-filter"></i> Lọc
                                </button>
                                <a href="<?php echo e(route('giangvien.schedule.export', ['date' => request('date', $date), 'period' => $period])); ?>" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Xuất CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Lịch giảng dạy</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th>Thứ</th>
                                <th>Tiết</th>
                                <th>Giờ</th>
                                <th>Môn học</th>
                                <th>Lớp HP</th>
                                <th>Phòng</th>
                                <th>Link online</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($ev['date']); ?></td>
                                    <td><?php echo e($ev['weekday']); ?></td>
                                    <td><?php echo e($ev['tiet_bat_dau'] ?? ($ev['tiet'] ?? '')); ?>-<?php echo e($ev['tiet_ket_thuc'] ?? ''); ?></td>
                                    <td><?php echo e($ev['gio_bat_dau'] ?? ''); ?> - <?php echo e($ev['gio_ket_thuc'] ?? ''); ?></td>
                                    <td>
                                        <?php if(!empty($ev['ma_mon'])): ?>
                                            <strong><?php echo e($ev['ma_mon']); ?></strong><br>
                                        <?php endif; ?>
                                        <?php echo e($ev['ten_mon'] ?? 'N/A'); ?>

                                    </td>
                                    <td><?php echo e($ev['lop_hoc_phan'] ?? ''); ?></td>
                                    <td><?php echo e($ev['phong'] ?? ''); ?></td>
                                    <td>
                                        <?php if(!empty($ev['link_online'])): ?>
                                            <a href="<?php echo e($ev['link_online']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-link-45deg"></i> Link
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Không có buổi học trong khoảng thời gian này.</td>
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

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/schedule/index.blade.php ENDPATH**/ ?>