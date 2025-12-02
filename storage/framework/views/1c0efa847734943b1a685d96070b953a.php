<?php $__env->startSection('title', 'Báo cáo giảng dạy'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Báo cáo giảng dạy cá nhân</h3>
                <p class="text-subtitle text-muted">Thống kê và phân tích hoạt động giảng dạy</p>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Tổng lớp</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($tongLop); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Buổi đã dạy</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($buoiDaDay); ?>/<?php echo e($tongBuoiHoc); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-clipboard-check-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Điểm danh</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($tongDiemDanh); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Tỷ lệ có mặt</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($tyLeCoMat); ?>%</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách báo cáo -->
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon bg-primary me-3">
                            <i class="bi bi-graph-up text-white"></i>
                        </div>
                        <h5 class="mb-0">Tiến độ giảng dạy</h5>
                    </div>
                    <p class="text-muted">Thống kê số buổi đã dạy/tổng buổi theo từng lớp học phần</p>
                    <a href="<?php echo e(route('giangvien.bao-cao.tien-do')); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-eye"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon bg-success me-3">
                            <i class="bi bi-clipboard-data text-white"></i>
                        </div>
                        <h5 class="mb-0">Báo cáo điểm danh</h5>
                    </div>
                    <p class="text-muted">Thống kê tỷ lệ có mặt, vắng, đi trễ theo từng lớp</p>
                    <a href="<?php echo e(route('giangvien.bao-cao.diem-danh')); ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-eye"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon bg-warning me-3">
                            <i class="bi bi-bar-chart-fill text-white"></i>
                        </div>
                        <h5 class="mb-0">Phân tích điểm</h5>
                    </div>
                    <p class="text-muted">Phân bố điểm và tỷ lệ qua môn của sinh viên</p>
                    <a href="<?php echo e(route('giangvien.bao-cao.phan-tich-diem')); ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-eye"></i> Xem báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách lớp giảng dạy -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lớp giảng dạy</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Sĩ số</th>
                                    <th>Số buổi</th>
                                    <th>Tiến độ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lopHocPhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($lop->ma_lop_hp); ?></strong></td>
                                    <td><?php echo e($lop->monHoc->ten_mon ?? ''); ?></td>
                                    <td>
                                        <?php if($lop->hocKy): ?>
                                        <?php echo e($lop->hocKy->ten_hoc_ky); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($lop->lopHocPhanSinhVien ? $lop->lopHocPhanSinhVien->count() : 0); ?></td>
                                    <td><?php echo e($lop->lichHocChiTiet ? $lop->lichHocChiTiet->count() : 0); ?></td>
                                    <td>
                                        <?php
                                            $tongBuoi = $lop->lichHocChiTiet ? $lop->lichHocChiTiet->count() : 0;
                                            $daDayCount = $lop->lichHocChiTiet ? $lop->lichHocChiTiet->where('trang_thai', 'da_day')->count() : 0;
                                            $tiLe = $tongBuoi > 0 ? round(($daDayCount / $tongBuoi) * 100, 2) : 0;
                                        ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?php echo e($tiLe >= 75 ? 'success' : ($tiLe >= 50 ? 'warning' : 'danger')); ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo e($tiLe); ?>%">
                                                <?php echo e($tiLe); ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Chưa có lớp giảng dạy</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/bao-cao/index.blade.php ENDPATH**/ ?>