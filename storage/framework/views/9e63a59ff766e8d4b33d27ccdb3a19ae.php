<?php $__env->startSection('title', 'Giảng viên Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <h3>Dashboard Giảng viên</h3>
        <p class="text-subtitle text-muted">Chào mừng, <?php echo e(auth()->user()->ho_ten); ?></p>
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
                                        <h6 class="text-muted font-semibold">Lớp phụ trách</h6>
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
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldProfile"></i>
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
                                        <div class="stats-icon green">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Buổi học tuần này</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($weekSessions ?? 0); ?></h6>
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
                                        <h6 class="text-muted font-semibold">Cần nhập điểm</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo e($pendingGrades ?? 0); ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center mb-2">
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
                                
                                <form method="GET" action="<?php echo e(route('giangvien.dashboard')); ?>" class="mt-2">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select name="giang_vien_id" class="form-select form-select-sm" 
                                                    onchange="this.form.submit()" style="font-size: 0.85rem;">
                                                <option value="">-- Tất cả giảng viên --</option>
                                                <?php if(isset($giangViens) && $giangViens->count() > 0): ?>
                                                    <?php $__currentLoopData = $giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($gv->id); ?>" 
                                                                <?php echo e(request('giang_vien_id') == $gv->id ? 'selected' : ''); ?>>
                                                            <?php echo e($gv->ma_giang_vien ?? ''); ?> - <?php echo e($gv->ho_ten); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="tim_kiem_giang_vien" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Tìm theo tên hoặc mã GV..." 
                                                   value="<?php echo e(request('tim_kiem_giang_vien')); ?>"
                                                   style="font-size: 0.85rem;">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="btn-group w-100" role="group">
                                                <button type="submit" class="btn btn-light btn-sm">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <a href="<?php echo e(route('giangvien.dashboard')); ?>" 
                                                   class="btn btn-light btn-sm" title="Reset">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Điểm danh gần đây</h4>
                            </div>
                            <div class="card-body">
                                <?php $__empty_1 = true; $__currentLoopData = $diemDanhGanDay ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div>
                                            <h6 class="mb-1"><?php echo e($lich->lopHocPhan->monHoc->ten_mon ?? ''); ?></h6>
                                            <p class="text-muted mb-0 small">
                                                <?php echo e(\Carbon\Carbon::parse($lich->ngay_hoc)->format('d/m/Y')); ?> - 
                                                <?php echo e($lich->diemDanh->count() ?? 0); ?> SV điểm danh
                                            </p>
                                        </div>
                                        <a href="<?php echo e(route('giangvien.diem-danh.show', $lich->id)); ?>" class="btn btn-sm btn-primary">
                                            Xem
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="text-muted mb-0">Chưa có dữ liệu điểm danh</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Thông báo</h4>
                    </div>
                    <div class="card-content pb-4">
                        <?php $__empty_1 = true; $__currentLoopData = $thongBaoMoi ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nguoiNhan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="recent-message d-flex px-4 py-3 border-bottom">
                                <div class="name ms-4">
                                    <h5 class="mb-1"><?php echo e(Str::limit($nguoiNhan->thongBao->tieu_de ?? '', 30)); ?></h5>
                                    <h6 class="text-muted mb-0"><?php echo e(\Carbon\Carbon::parse($nguoiNhan->created_at)->diffForHumans()); ?></h6>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="recent-message d-flex px-4 py-3">
                                <div class="name ms-4">
                                    <h5 class="mb-1">Chưa có thông báo mới</h5>
                                    <h6 class="text-muted mb-0">Bạn đã xem tất cả thông báo</h6>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if(isset($thongBaoMoi) && $thongBaoMoi->count() > 0): ?>
                            <div class="px-4 py-2">
                                <a href="<?php echo e(route('giangvien.thong-bao.index')); ?>" class="btn btn-sm btn-outline-primary w-100">
                                    Xem tất cả
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Lớp chủ nhiệm</h4>
                    </div>
                    <div class="card-content pb-4">
                        <div class="px-4 py-3">
                            <p class="text-muted mb-0"><?php echo e($homeRoomClass ?? 'Chưa có lớp chủ nhiệm'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/dashboard.blade.php ENDPATH**/ ?>