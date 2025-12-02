<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <h3>Dashboard - Quản trị viên</h3>
    </div>
    <div class="page-content">
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
                                        <h6 class="text-muted font-semibold">Tổng tài khoản</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalUsers); ?></h6>
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
                                        <div class="stats-icon teal">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalStudents ?? 0); ?></h6>
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
                                                <div class="stats-icon indigo">
                                                    <i class="bi bi-person-bounding-box"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Giảng viên</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo e($totalLecturers ?? 0); ?></h6>
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
                                                <div class="stats-icon cyan">
                                                    <i class="bi bi-journal-bookmark-fill"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Lớp học phần</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo e($totalLopHocPhan ?? 0); ?></h6>
                                            </div>
                                        </div>
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
                                        <h6 class="font-extrabold mb-0"><?php echo e($activeUsers); ?></h6>
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
                                        <h6 class="text-muted font-semibold">Tài khoản khóa</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($lockedUsers); ?></h6>
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
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Chưa xác thực email</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($unverifiedUsers); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Đăng ký môn học (Top 8 môn có nhiều đăng ký nhất)</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-registrations" style="height:320px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Phân bố điểm</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-grade-distribution" style="height:280px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tỷ lệ Đỗ/Trượt</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-pass-fail" style="height:280px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tình hình học phí</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4 text-center">
                                        <h6 class="text-muted">Tổng học phí</h6>
                                        <h4 class="text-primary"><?php echo e(number_format($hocPhiTong ?? 0)); ?></h4>
                                        <small class="text-muted">VND</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6 class="text-muted">Đã thu</h6>
                                        <h4 class="text-success"><?php echo e(number_format($hocPhiDaThu ?? 0)); ?></h4>
                                        <small class="text-muted">VND</small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6 class="text-muted">Còn lại</h6>
                                        <h4 class="text-danger"><?php echo e(number_format($hocPhiConLai ?? 0)); ?></h4>
                                        <small class="text-muted">VND</small>
                                    </div>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <?php
                                        $tong = $hocPhiTong ?? 0;
                                        $daThu = $hocPhiDaThu ?? 0;
                                        $pct = $tong > 0 ? round(($daThu / $tong) * 100, 2) : 0;
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($pct); ?>%">
                                        <strong><?php echo e($pct); ?>%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Cảnh báo học vụ</h4>
                                <span class="badge bg-warning"><?php echo e($canhBaoChuaXuLy ?? 0); ?> chưa xử lý</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã SV</th>
                                                <th>Sinh viên</th>
                                                <th>Loại cảnh báo</th>
                                                <th>Mức độ</th>
                                                <th>Ngày</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $recentWarnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><strong><?php echo e($w->sinhVien?->ma_sinh_vien ?? '-'); ?></strong></td>
                                                    <td><?php echo e($w->sinhVien?->ho_ten ?? 'N/A'); ?></td>
                                                    <td><?php echo e($w->getLoaiCanhBaoLabelAttribute() ?? $w->loai_canh_bao); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo e($w->muc_do_badge ?? 'secondary'); ?>">
                                                            <?php echo e($w->getMucDoLabelAttribute()); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($w->ngay_canh_bao?->format('d/m/Y') ?? '-'); ?></td>
                                                    <td>
                                                        <?php if($w->da_xu_ly): ?>
                                                            <span class="badge bg-success">Đã xử lý</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Chưa xử lý</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Không có cảnh báo nào</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="bi bi-shield-fill-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Vai trò</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalRoles); ?></h6>
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
                                        <div class="stats-icon purple">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Quyền</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalPermissions); ?></h6>
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
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Admin</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalAdmins); ?></h6>
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
                                            <i class="bi bi-person-workspace"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Phòng Đào tạo</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalDaoTao); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Thống kê người dùng theo vai trò</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Vai trò</th>
                                                <th>Số lượng</th>
                                                <th>Tỷ lệ</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $usersByRole; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo e($role->ten_vai_tro); ?></span>
                                                    </td>
                                                    <td class="font-semibold"><?php echo e($role->total); ?></td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-success"
                                                                style="width: <?php echo e($totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0); ?>%">
                                                            </div>
                                                        </div>
                                                        <span
                                                            class="text-muted small"><?php echo e($totalUsers > 0 ? round(($role->total / $totalUsers) * 100, 1) : 0); ?>%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle"></i> Hoạt động
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card">
                            <div class="card-header">
                                <h4>Người dùng mới trong 7 ngày</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <h2 class="mb-0 me-3"><?php echo e($newUsersThisWeek); ?></h2>
                                    <span class="badge bg-success">
                                        <i class="bi bi-arrow-up"></i> Người dùng mới
                                    </span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <tbody>
                                            <?php $__currentLoopData = $userCreationStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e(\Carbon\Carbon::parse($stat->date)->format('d/m/Y')); ?>

                                                    </td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-primary"
                                                                style="width: <?php echo e($newUsersThisWeek > 0 ? round(($stat->total / $newUsersThisWeek) * 100) : 0); ?>%">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong><?php echo e($stat->total); ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        
                        <div class="card">
                            <div class="card-header">
                                <h4>Đăng nhập gần đây</h4>
                            </div>
                            <div class="card-content pb-4">
                                <?php $__empty_1 = true; $__currentLoopData = $recentLogins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="recent-message d-flex px-4 py-3">
                                        <div class="avatar avatar-lg">
                                            <img src="<?php echo e(asset('assets/images/faces/1.jpg')); ?>" alt="Avatar">
                                        </div>
                                        <div class="name ms-3">
                                            <h6 class="mb-1"><?php echo e($user->name); ?></h6>
                                            <p class="text-muted mb-0 small"><?php echo e($user->email); ?></p>
                                            <p class="text-muted mb-0 small">
                                                <i class="bi bi-clock"></i>
                                                <?php echo e($user->lan_dang_nhap_cuoi ? \Carbon\Carbon::parse($user->lan_dang_nhap_cuoi)->diffForHumans() : 'Chưa đăng nhập'); ?>

                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="px-4 py-3 text-center text-muted">
                                        Chưa có hoạt động đăng nhập
                                    </div>
                                <?php endif; ?>
                                <div class="px-4">
                                    <a href="<?php echo e(route('admin.users.index')); ?>"
                                        class="btn btn-sm btn-block btn-outline-primary">
                                        <i class="bi bi-arrow-right-circle"></i> Xem tất cả
                                    </a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card">
                            <div class="card-header">
                                <h4>Thông tin hệ thống</h4>
                            </div>
                            <div class="card-content pb-4">
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-primary">
                                        <i class="bi bi-info-circle text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Phiên bản</h6>
                                        <p class="text-muted mb-0">S-MIS v1.0.0</p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-success">
                                        <i class="bi bi-code-square text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Laravel</h6>
                                        <p class="text-muted mb-0"><?php echo e(app()->version()); ?></p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-warning">
                                        <i class="bi bi-filetype-php text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">PHP Version</h6>
                                        <p class="text-muted mb-0"><?php echo e(phpversion()); ?></p>
                                    </div>
                                </div>
                                <div class="recent-message d-flex px-4 py-3">
                                    <div class="avatar avatar-lg bg-info">
                                        <i class="bi bi-calendar-check text-white"></i>
                                    </div>
                                    <div class="name ms-3">
                                        <h6 class="mb-1">Ngày triển khai</h6>
                                        <p class="text-muted mb-0">21/10/2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Data from server
    const regLabels = <?php echo json_encode($registrationLabels ?? [], 15, 512) ?>;
    const regSeries = <?php echo json_encode($registrationSeries ?? [], 15, 512) ?>;

    const gradeLabels = <?php echo json_encode($gradeLabels ?? [], 15, 512) ?>;
    const gradeSeries = <?php echo json_encode($gradeSeries ?? [], 15, 512) ?>;

    const passFailLabels = <?php echo json_encode($passFail['labels'] ?? ['Qua môn', 'Không qua'], 512) ?>;
    const passFailSeries = <?php echo json_encode($passFail['series'] ?? [0, 0], 512) ?>;

    // Registrations bar chart
    (function(){
        const options = {
            chart: { type: 'bar', height: 300 },
            series: [{ name: 'Đăng ký', data: regSeries }],
            xaxis: { categories: regLabels },
            colors: ['#435ebe']
        };
        const chart = new ApexCharts(document.querySelector('#chart-registrations'), options);
        chart.render();
    })();

    // Grade distribution donut
    (function(){
        const options = {
            chart: { type: 'donut', height: 260 },
            series: gradeSeries,
            labels: gradeLabels,
            colors: ['#28a745','#20c997','#17a2b8','#ffc107','#fd7e14','#dc3545','#6c757d','#343a40'],
            legend: { position: 'bottom' }
        };
        const chart = new ApexCharts(document.querySelector('#chart-grade-distribution'), options);
        chart.render();
    })();

    // Pass / Fail chart
    (function(){
        const options = {
            chart: { type: 'donut', height: 260 },
            series: passFailSeries,
            labels: passFailLabels,
            colors: ['#198754','#dc3545'],
            legend: { position: 'bottom' }
        };
        const chart = new ApexCharts(document.querySelector('#chart-pass-fail'), options);
        chart.render();
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.layout-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>