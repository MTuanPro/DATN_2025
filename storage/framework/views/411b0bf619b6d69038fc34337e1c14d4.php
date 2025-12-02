<?php $__env->startSection('title', 'Kết quả học tập'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kết quả học tập</h3>
                    <p class="text-subtitle text-muted">Xem điểm các môn học theo học kỳ</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('sinh-vien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kết quả học tập</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">GPA Tích lũy</h6>
                                    <h2 class="mb-0 text-primary"><?php echo e(number_format($gpaTichLuy, 2)); ?></h2>
                                </div>
                                <div class="avatar avatar-xl bg-primary">
                                    <i class="bi bi-trophy text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">GPA Học kỳ</h6>
                                    <h2 class="mb-0 text-success"><?php echo e(number_format($gpaHocKy, 2)); ?></h2>
                                </div>
                                <div class="avatar avatar-xl bg-success">
                                    <i class="bi bi-graph-up text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tín chỉ tích lũy</h6>
                                    <h2 class="mb-0 text-info"><?php echo e($tongTinChiDat); ?></h2>
                                </div>
                                <div class="avatar avatar-xl bg-info">
                                    <i class="bi bi-clipboard-check text-white" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('sinh-vien.diem.index')); ?>">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chọn học kỳ</label>
                                    <select name="hoc_ky_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- Chọn học kỳ --</option>
                                        <?php $__currentLoopData = $hocKys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($hk->id); ?>" <?php echo e($hocKyId == $hk->id ? 'selected' : ''); ?>>
                                                <?php echo e($hk->ten_hoc_ky); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo e(route('sinh-vien.diem.bang-diem')); ?>" class="btn btn-primary">
                                    <i class="bi bi-file-earmark-text"></i> Xem bảng điểm tổng hợp
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Điểm các môn học</h5>
                </div>
                <div class="card-body">
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

                    <?php if(session('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show">
                            <?php echo e(session('info')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã môn</th>
                                    <th>Tên môn học</th>
                                    <th>Tín chỉ</th>
                                    <th class="text-center">Điểm (Hệ 10)</th>
                                    <th class="text-center">Điểm (Hệ 4)</th>
                                    <th class="text-center">Điểm chữ</th>
                                    <th class="text-center">Kết quả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $monHocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($item->lopHocPhan->monHoc->ma_mon); ?></strong></td>
                                        <td><?php echo e($item->lopHocPhan->monHoc->ten_mon); ?></td>
                                        <td class="text-center"><?php echo e($item->lopHocPhan->monHoc->so_tin_chi); ?></td>
                                        <td class="text-center">
                                            <?php if($item->ketQuaHocTap && $item->ketQuaHocTap->diem_he_10): ?>
                                                <strong
                                                    class="text-primary"><?php echo e(number_format($item->ketQuaHocTap->diem_he_10, 2)); ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($item->ketQuaHocTap && $item->ketQuaHocTap->diem_he_4): ?>
                                                <?php echo e(number_format($item->ketQuaHocTap->diem_he_4, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($item->ketQuaHocTap && $item->ketQuaHocTap->diem_chu): ?>
                                                <span class="badge bg-<?php echo e($item->ketQuaHocTap->diem_chu_badge); ?>">
                                                    <?php echo e($item->ketQuaHocTap->diem_chu); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($item->ketQuaHocTap): ?>
                                                <?php if($item->ketQuaHocTap->qua_mon): ?>
                                                    <span class="badge bg-success">Đạt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Không đạt</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('sinh-vien.diem.show', $item->lopHocPhan->id)); ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Chưa có điểm môn học nào trong học kỳ này</p>
                                        </td>
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

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/sinhvien/diem/index.blade.php ENDPATH**/ ?>