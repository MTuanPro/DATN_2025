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
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(($tab ?? 'schedule') == 'schedule' ? 'active' : ''); ?>" 
                        id="schedule-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#schedule" 
                        type="button" 
                        role="tab"
                        onclick="window.location.href='<?php echo e(route('giangvien.schedule.index', ['tab' => 'schedule'])); ?>'">
                    <i class="bi bi-calendar-check"></i> Lịch dạy
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e(($tab ?? 'schedule') == 'attendance' ? 'active' : ''); ?>" 
                        id="attendance-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#attendance" 
                        type="button" 
                        role="tab"
                        onclick="window.location.href='<?php echo e(route('giangvien.schedule.index', ['tab' => 'attendance'])); ?>'">
                    <i class="bi bi-clipboard-check"></i> Điểm danh
                </button>
            </li>
        </ul>

        <div class="tab-content" id="scheduleTabContent">
            <!-- Tab Lịch dạy -->
            <div class="tab-pane fade <?php echo e(($tab ?? 'schedule') == 'schedule' ? 'show active' : ''); ?>" 
                 id="schedule" 
                 role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bộ lọc</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="<?php echo e(route('giangvien.schedule.index')); ?>">
                            <input type="hidden" name="tab" value="schedule">
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
            </div>

            <!-- Tab Điểm danh -->
            <div class="tab-pane fade <?php echo e(($tab ?? 'schedule') == 'attendance' ? 'show active' : ''); ?>" 
                 id="attendance" 
                 role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Quản lý điểm danh</h5>
                        <a href="<?php echo e(route('giangvien.diem-danh.report')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-graph-up"></i> Báo cáo điểm danh
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo e(route('giangvien.schedule.index')); ?>" id="filterForm">
                            <input type="hidden" name="tab" value="attendance">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Lớp học phần</label>
                                    <select name="lop_hoc_phan_id" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                        <option value="">-- Tất cả --</option>
                                        <?php if(isset($danhSachLopHocPhan)): ?>
                                            <?php $__currentLoopData = $danhSachLopHocPhan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($lop->id); ?>" <?php echo e(request('lop_hoc_phan_id') == $lop->id ? 'selected' : ''); ?>>
                                                    <?php echo e($lop->ma_lop_hp); ?> - <?php echo e($lop->monHoc->ten_mon); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="trang_thai" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                        <option value="">-- Tất cả --</option>
                                        <option value="chua_day" <?php echo e(request('trang_thai') == 'chua_day' ? 'selected' : ''); ?>>Chưa dạy</option>
                                        <option value="dang_day" <?php echo e(request('trang_thai') == 'dang_day' ? 'selected' : ''); ?>>Đang dạy</option>
                                        <option value="da_day" <?php echo e(request('trang_thai') == 'da_day' ? 'selected' : ''); ?>>Đã dạy</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Từ ngày</label>
                                    <input type="date" name="tu_ngay" class="form-control" value="<?php echo e(request('tu_ngay')); ?>"
                                           onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Đến ngày</label>
                                    <input type="date" name="den_ngay" class="form-control" value="<?php echo e(request('den_ngay')); ?>"
                                           onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <a href="<?php echo e(route('giangvien.schedule.index', ['tab' => 'attendance'])); ?>" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Danh sách buổi học
                            <?php if(isset($buoiHocList)): ?>
                                <span class="badge bg-primary"><?php echo e($buoiHocList->total()); ?></span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($buoiHocList) && $buoiHocList->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Ngày học</th>
                                            <th>Tiết</th>
                                            <th>Lớp HP</th>
                                            <th>Môn học</th>
                                            <th>Phòng</th>
                                            <th>Trạng thái</th>
                                            <th>Thống kê điểm danh</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $buoiHocList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $buoiHoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($buoiHocList->firstItem() + $index); ?></td>
                                                <td>
                                                    <strong><?php echo e($buoiHoc->ngay_hoc->format('d/m/Y')); ?></strong><br>
                                                    <small class="text-muted"><?php echo e($buoiHoc->ngay_hoc->dayName); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo e($buoiHoc->tiet_bat_dau); ?>-<?php echo e($buoiHoc->tiet_ket_thuc); ?>

                                                    </span>
                                                </td>
                                                <td><?php echo e($buoiHoc->lopHocPhan->ma_lop_hp); ?></td>
                                                <td>
                                                    <strong><?php echo e($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo e($buoiHoc->phongHoc->ten_phong ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if($buoiHoc->trang_thai == 'chua_day'): ?>
                                                        <span class="badge bg-secondary">Chưa dạy</span>
                                                    <?php elseif($buoiHoc->trang_thai == 'dang_day'): ?>
                                                        <span class="badge bg-warning">Đang dạy</span>
                                                    <?php elseif($buoiHoc->trang_thai == 'da_day'): ?>
                                                        <span class="badge bg-success">Đã dạy</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Hủy</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($buoiHoc->diem_danh_stats && $buoiHoc->diem_danh_stats->tong > 0): ?>
                                                        <div class="small">
                                                            <span class="text-success">✓ <?php echo e($buoiHoc->diem_danh_stats->co_mat); ?></span> /
                                                            <span class="text-danger">✗ <?php echo e($buoiHoc->diem_danh_stats->vang); ?></span> /
                                                            <span class="text-warning">⏱ <?php echo e($buoiHoc->diem_danh_stats->di_tre); ?></span> /
                                                            <span class="text-info">☂ <?php echo e($buoiHoc->diem_danh_stats->nghi_phep); ?></span>
                                                        </div>
                                                        <small class="text-muted">
                                                            Tổng: <?php echo e($buoiHoc->diem_danh_stats->tong); ?>

                                                        </small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Chưa điểm danh</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('giangvien.diem-danh.show', $buoiHoc->id)); ?>" 
                                                       class="btn btn-sm btn-primary"
                                                       title="Điểm danh">
                                                        <i class="bi bi-clipboard-check"></i> Điểm danh
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Hiển thị <?php echo e($buoiHocList->firstItem()); ?> - <?php echo e($buoiHocList->lastItem()); ?> 
                                    trong tổng <?php echo e($buoiHocList->total()); ?> buổi học
                                </div>
                                <div>
                                    <?php echo e($buoiHocList->appends(request()->query())->links()); ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                Không tìm thấy buổi học nào.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/schedule/index.blade.php ENDPATH**/ ?>