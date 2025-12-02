<?php $__env->startSection('title', 'Lịch Coi Thi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Lịch coi thi</h3>
                <p class="text-subtitle text-muted">Danh sách ca thi bạn được phân công giám thị</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch coi thi</li>
                    </ol>
                </nav>
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
                <form action="<?php echo e(route('giangvien.lich-thi.lich-coi-thi')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tháng</label>
                                <input type="month" name="thang" class="form-control" value="<?php echo e(request('thang')); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trạng thái</label>
                                <select name="da_coi" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="0" <?php echo e(request('da_coi') === '0' ? 'selected' : ''); ?>>Sắp coi</option>
                                    <option value="1" <?php echo e(request('da_coi') === '1' ? 'selected' : ''); ?>>Đã coi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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
                                <th>Lớp HP</th>
                                <th>Loại thi</th>
                                <th>Ngày thi</th>
                                <th>Giờ</th>
                                <th>Phòng</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $lichCoiThis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lichThi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($lichThi->ngay_thi < now()->toDateString() ? 'table-secondary' : ''); ?>">
                                <td><?php echo e($lichCoiThis->firstItem() + $index); ?></td>
                                <td>
                                    <?php echo e($lichThi->lopHocPhan->monHoc->ten_mon); ?>

                                    <br><small class="text-muted"><?php echo e($lichThi->lopHocPhan->monHoc->ma_mon); ?></small>
                                </td>
                                <td><?php echo e($lichThi->lopHocPhan->ma_lop); ?></td>
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
                                <td><?php echo e($lichThi->gio_bat_dau); ?><br><?php echo e($lichThi->gio_ket_thuc); ?></td>
                                <td><?php echo e($lichThi->phongHoc->ten_phong); ?></td>
                                <td>
                                    <?php
                                        $giangVien = Auth::user()->giangVien;
                                    ?>
                                    <?php if($lichThi->giam_thi_1_id == $giangVien->id): ?>
                                        <span class="badge bg-primary">Giám thị 1</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Giám thị 2</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($lichThi->ngay_thi < now()->toDateString()): ?>
                                        <span class="badge bg-success">Đã coi</span>
                                    <?php elseif($lichThi->ngay_thi->isToday()): ?>
                                        <span class="badge bg-warning">Hôm nay</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Sắp tới</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('giangvien.lich-thi.show', $lichThi)); ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    <i class="bi bi-inbox"></i> Không có lịch coi thi nào
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?php echo e($lichCoiThis->links()); ?>

                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/lich-thi/lich-coi-thi.blade.php ENDPATH**/ ?>