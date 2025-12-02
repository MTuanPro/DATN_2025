<?php $__env->startSection('title', 'Lịch Thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch thi cá nhân</h3>
                <p class="text-subtitle text-muted">Xem lịch thi các môn đã đăng ký</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch thi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="iconly-boldShow"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Tổng số ca thi</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($lichThis->total()); ?></h6>
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
                                <i class="iconly-boldCalendar"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Sắp thi</h6>
                            <h6 class="font-extrabold mb-0"><?php echo e($lichThis->where('ngay_thi', '>=', now()->toDateString())->count()); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('sinh-vien.lich-thi.index')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Loại thi</label>
                                <select name="loai_thi" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="giua_ky" <?php echo e(request('loai_thi') == 'giua_ky' ? 'selected' : ''); ?>>Giữa kỳ</option>
                                    <option value="cuoi_ky" <?php echo e(request('loai_thi') == 'cuoi_ky' ? 'selected' : ''); ?>>Cuối kỳ</option>
                                    <option value="thi_lai" <?php echo e(request('loai_thi') == 'thi_lai' ? 'selected' : ''); ?>>Thi lại</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="month" name="thang" class="form-control" value="<?php echo e(request('thang')); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="sap_thi" <?php echo e(request('trang_thai') == 'sap_thi' ? 'selected' : ''); ?>>Sắp thi</option>
                                    <option value="da_thi" <?php echo e(request('trang_thai') == 'da_thi' ? 'selected' : ''); ?>>Đã thi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="<?php echo e(route('sinh-vien.lich-thi.calendar')); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-calendar3"></i> Xem dạng lịch
                </a>
                <a href="<?php echo e(route('sinh-vien.lich-thi.export-pdf')); ?>" class="btn btn-outline-success">
                    <i class="bi bi-file-pdf"></i> Xuất PDF
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Môn học</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ thi</th>
                                <th>Phòng thi</th>
                                <th>Số báo danh</th>
                                <th>Hình thức</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $lichThis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lichThi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($lichThi->ngay_thi->isToday() ? 'table-warning' : ''); ?>">
                                <td><?php echo e($lichThis->firstItem() + $index); ?></td>
                                <td>
                                    <strong><?php echo e($lichThi->lopHocPhan->monHoc->ten_mon); ?></strong>
                                    <br><small class="text-muted"><?php echo e($lichThi->lopHocPhan->monHoc->ma_mon); ?></small>
                                    <br><small class="text-muted">Lớp: <?php echo e($lichThi->lopHocPhan->ma_lop); ?></small>
                                </td>
                                <td>
                                    <?php if($lichThi->loai_thi == 'giua_ky'): ?>
                                        <span class="badge bg-info">Giữa kỳ</span>
                                    <?php elseif($lichThi->loai_thi == 'cuoi_ky'): ?>
                                        <span class="badge bg-danger">Cuối kỳ</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Thi lại</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e($lichThi->ngay_thi->format('d/m/Y')); ?></strong>
                                    <br><small class="text-muted"><?php echo e($lichThi->ngay_thi->locale('vi')->isoFormat('dddd')); ?></small>
                                </td>
                                <td>
                                    <?php echo e($lichThi->gio_bat_dau); ?><br>
                                    <small class="text-muted">đến</small><br>
                                    <?php echo e($lichThi->gio_ket_thuc); ?>

                                </td>
                                <td>
                                    <?php
                                        $thongTinThi = $lichThi->lichThiSinhViens->where('sinh_vien_id', $sinhVien->id ?? auth()->user()->sinhVien->id)->first();
                                    ?>
                                    <?php if($thongTinThi && $thongTinThi->phongThi): ?>
                                        <strong><?php echo e($thongTinThi->phongThi->ten_phong); ?></strong>
                                        <?php if($thongTinThi->phongThi->vi_tri): ?>
                                            <br><small class="text-muted"><?php echo e($thongTinThi->phongThi->vi_tri); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa phân phòng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($thongTinThi): ?>
                                        <span class="badge bg-primary" style="font-size: 1.1em;">
                                            <?php echo e($thongTinThi->so_bao_danh); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($lichThi->hinh_thuc == 'offline'): ?>
                                        <span class="badge bg-secondary">Tại trường</span>
                                    <?php elseif($lichThi->hinh_thuc == 'online'): ?>
                                        <span class="badge bg-primary">Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Kết hợp</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($lichThi->ngay_thi < now()->toDateString()): ?>
                                        <span class="badge bg-success">Đã thi</span>
                                    <?php elseif($lichThi->ngay_thi->isToday()): ?>
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-circle"></i> HÔM NAY
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info">
                                            Còn <?php echo e($lichThi->ngay_thi->diffInDays(now())); ?> ngày
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('sinh-vien.lich-thi.show', $lichThi)); ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2">Không có lịch thi nào</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($lichThis->links()); ?>

                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/lich-thi/index.blade.php ENDPATH**/ ?>