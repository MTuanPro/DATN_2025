<?php $__env->startSection('title', 'Lịch theo Phòng học'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Lịch theo Phòng học</h3>
                    <p class="text-subtitle text-muted">Xem lịch học theo từng phòng học</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dao-tao.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lịch theo Phòng học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('dao-tao.thoi-khoa-bieu.lich-theo-phong')); ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="phong_hoc_id" class="form-label">Phòng học</label>
                            <select class="form-select" id="phong_hoc_id" name="phong_hoc_id">
                                <option value="">-- Tất cả phòng --</option>
                                <?php $__currentLoopData = $phongHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ph->id); ?>" <?php echo e(request('phong_hoc_id') == $ph->id ? 'selected' : ''); ?>>
                                        <?php echo e($ph->ten_phong); ?> (<?php echo e($ph->vi_tri ?? 'N/A'); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="hoc_ky_id" class="form-label">Học kỳ</label>
                            <select class="form-select" id="hoc_ky_id" name="hoc_ky_id">
                                <option value="">-- Tất cả học kỳ --</option>
                                <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($hk->id); ?>" <?php echo e(request('hoc_ky_id') == $hk->id ? 'selected' : ''); ?>>
                                        <?php echo e($hk->ten_hoc_ky); ?> (<?php echo e($hk->nam_hoc); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tu_ngay" class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="tu_ngay" name="tu_ngay" value="<?php echo e(request('tu_ngay')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="den_ngay" class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="den_ngay" name="den_ngay" value="<?php echo e(request('den_ngay')); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách lịch học</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ngày học</th>
                                    <th>Thứ</th>
                                    <th>Tiết</th>
                                    <th>Giờ</th>
                                    <th>Phòng học</th>
                                    <th>Lớp HP</th>
                                    <th>Môn học</th>
                                    <th>Giảng viên</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $lichHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lich): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($lichHocs->firstItem() + $index); ?></td>
                                        <td><?php echo e($lich->ngay_hoc->format('d/m/Y')); ?></td>
                                        <td>
                                            <?php
                                                $thu = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                                $thuIndex = $lich->ngay_hoc->dayOfWeek;
                                            ?>
                                            <?php echo e($thu[$thuIndex]); ?>

                                        </td>
                                        <td>
                                            Tiết <?php echo e($lich->tiet_bat_dau); ?>

                                            <?php if($lich->tiet_ket_thuc != $lich->tiet_bat_dau): ?>
                                                - <?php echo e($lich->tiet_ket_thuc); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($lich->gio_bat_dau && $lich->gio_ket_thuc): ?>
                                                <?php echo e($lich->gio_bat_dau->format('H:i')); ?> - <?php echo e($lich->gio_ket_thuc->format('H:i')); ?>

                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($lich->phongHoc->ten_phong ?? 'N/A'); ?></strong>
                                            <?php if($lich->phongHoc && $lich->phongHoc->vi_tri): ?>
                                                <br><small class="text-muted"><?php echo e($lich->phongHoc->vi_tri); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($lich->lopHocPhan->ma_lop_hp ?? 'N/A'); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo e($lich->lopHocPhan->monHoc->ten_mon ?? 'N/A'); ?>

                                            <br><small class="text-muted"><?php echo e($lich->lopHocPhan->monHoc->ma_mon ?? 'N/A'); ?></small>
                                        </td>
                                        <td><?php echo e($lich->giangVien->ho_ten ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if($lich->trang_thai == 'da_day'): ?>
                                                <span class="badge bg-success">Đã dạy</span>
                                            <?php elseif($lich->trang_thai == 'dang_day'): ?>
                                                <span class="badge bg-primary">Đang dạy</span>
                                            <?php elseif($lich->trang_thai == 'chua_day'): ?>
                                                <span class="badge bg-warning">Chưa dạy</span>
                                            <?php elseif($lich->trang_thai == 'huy'): ?>
                                                <span class="badge bg-danger">Hủy</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e($lich->trang_thai ?? 'N/A'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không tìm thấy lịch học nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Hiển thị <?php echo e($lichHocs->firstItem() ?? 0); ?> - <?php echo e($lichHocs->lastItem() ?? 0); ?>

                                trong tổng số <?php echo e($lichHocs->total()); ?> buổi học
                            </small>
                        </div>
                        <div>
                            <?php echo e($lichHocs->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.layout-daotao', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/daotao/thoi-khoa-bieu/lich-theo-phong.blade.php ENDPATH**/ ?>