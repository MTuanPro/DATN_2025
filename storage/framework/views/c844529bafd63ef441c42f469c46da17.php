<?php $__env->startSection('title', 'Đào tạo Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <h3>Dashboard Đào tạo</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Sinh viên</h6>
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
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Giảng viên</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalTeachers ?? 0); ?></h6>
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
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Lớp học phần</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalClasses ?? 0); ?></h6>
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
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Học phần</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($totalSubjects ?? 0); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Thống kê đăng ký học phần</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-registration"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Cảnh báo học vụ</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Loại</th>
                                                <th>Số lượng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="col-3">
                                                    <p class="font-bold mb-0">Học vụ</p>
                                                </td>
                                                <td class="col-auto">
                                                    <span class="badge bg-danger"><?php echo e($warningsAcademic ?? 0); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-3">
                                                    <p class="font-bold mb-0">Học phí</p>
                                                </td>
                                                <td class="col-auto">
                                                    <span class="badge bg-warning"><?php echo e($warningsTuition ?? 0); ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">
                                            <i class="bi bi-calendar-week"></i> Lịch dạy tuần này
                                        </h4>
                                        <p class="mb-0 mt-1" style="font-size: 0.85rem; opacity: 0.9;">
                                            <i class="bi bi-calendar-range"></i>
                                            Từ <?php echo e(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m/Y')); ?> 
                                            đến <?php echo e(\Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m/Y')); ?>

                                        </p>
                                    </div>
                                    <?php if($lichDayTuanNay && $lichDayTuanNay->count() > 0): ?>
                                        <span class="badge bg-light text-primary" style="font-size: 0.9rem;">
                                            <?php echo e($lichDayTuanNay->count()); ?> buổi học
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if($lichDayTuanNay && $lichDayTuanNay->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">Ngày</th>
                                                    <th style="width: 8%;">Thứ</th>
                                                    <th style="width: 8%;">Ca</th>
                                                    <th style="width: 10%;">Giờ</th>
                                                    <th style="width: 10%;">Phòng</th>
                                                    <th style="width: 10%;">Lớp HP</th>
                                                    <th style="width: 20%;">Môn học</th>
                                                    <th style="width: 14%;">Giảng viên</th>
                                                    <th style="width: 10%;">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $lichDayTuanNay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr style="cursor: pointer;" 
                                                        onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                                        onmouseout="this.style.backgroundColor=''">
                                                        <td>
                                                            <strong class="text-primary">
                                                                <?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y')); ?>

                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info text-white" style="font-size: 0.75rem; padding: 0.35em 0.65em;">
                                                                <?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->locale('vi')->dayName); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if($lich->caHoc): ?>
                                                                <span class="badge bg-primary text-white" style="font-size: 0.75rem; padding: 0.35em 0.65em;">
                                                                    <i class="bi bi-clock"></i> <?php echo e($lich->caHoc->ten_ca); ?>

                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-clock-history text-primary"></i>
                                                                <?php echo e(\Carbon\Carbon::parse($lich->gio_bat_dau)->format('H:i')); ?> - 
                                                                <?php echo e(\Carbon\Carbon::parse($lich->gio_ket_thuc)->format('H:i')); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-building text-success"></i>
                                                                <?php echo e($lich->phongHoc->ten_phong ?? 'Chưa phân phòng'); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong class="text-primary">
                                                                <?php echo e($lich->lopHocPhan->ma_lop_hp ?? 'N/A'); ?>

                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark" style="font-size: 0.9rem;">
                                                                <?php echo e($lich->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-dark">
                                                                <i class="bi bi-person-circle text-warning"></i>
                                                                <?php echo e($lich->giangVien->ho_ten ?? 'Chưa phân công'); ?>

                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if($lich->trang_thai == 'chua_day'): ?>
                                                                <span class="badge bg-secondary text-white">
                                                                    <i class="bi bi-hourglass-split"></i> Chưa dạy
                                                                </span>
                                                            <?php elseif($lich->trang_thai == 'dang_day'): ?>
                                                                <span class="badge bg-info text-white">
                                                                    <i class="bi bi-play-circle"></i> Đang dạy
                                                                </span>
                                                            <?php elseif($lich->trang_thai == 'da_day'): ?>
                                                                <span class="badge bg-success text-white">
                                                                    <i class="bi bi-check-circle"></i> Đã dạy
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger text-white">
                                                                    <i class="bi bi-x-circle"></i> Hủy
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-calendar-x" style="font-size: 4rem; color: #dee2e6;"></i>
                                        <p class="text-muted mt-3 mb-0" style="font-size: 1.1rem;">
                                            Không có lịch dạy trong tuần này
                                        </p>
                                        <small class="text-muted">
                                            Từ <?php echo e(\Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('d/m/Y')); ?> 
                                            đến <?php echo e(\Carbon\Carbon::now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d/m/Y')); ?>

                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Thông báo mới</h4>
                    </div>
                    <div class="card-content pb-4">
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="name ms-4">
                                <h5 class="mb-1">Thông báo mẫu</h5>
                                <h6 class="text-muted mb-0">Nội dung thông báo...</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/dashboard.blade.php ENDPATH**/ ?>