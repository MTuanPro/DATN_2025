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
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tỷ lệ điểm danh</h6>
                                    <?php
                                        $tongTyLe = 0;
                                        $soMon = 0;
                                        foreach ($monHocs as $mh) {
                                            if (isset($mh->ty_le_co_mat)) {
                                                $tongTyLe += $mh->ty_le_co_mat;
                                                $soMon++;
                                            }
                                        }
                                        $tyLeTrungBinh = $soMon > 0 ? round($tongTyLe / $soMon, 1) : 0;
                                    ?>
                                    <h2 class="mb-0 text-warning"><?php echo e($tyLeTrungBinh); ?>%</h2>
                                </div>
                                <div class="avatar avatar-xl bg-warning">
                                    <i class="bi bi-person-check text-white" style="font-size: 2rem;"></i>
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

            
            <?php if($hocKyId && count($monHocs) > 0): ?>
                <?php
                    $tongBuoiHocTatCa = 0;
                    $tongCoMat = 0;
                    $tongVang = 0;
                    $tongDiTre = 0;
                    $tongNghiPhep = 0;
                    $tongTyLe = 0;
                    $soMonCoDiemDanh = 0;
                    
                    foreach ($monHocs as $mh) {
                        if (isset($mh->tong_buoi_hoc) && $mh->tong_buoi_hoc > 0) {
                            $tongBuoiHocTatCa += $mh->tong_buoi_hoc;
                            $stats = $mh->diem_danh_stats;
                            if ($stats) {
                                $tongCoMat += $stats->co_mat ?? 0;
                                $tongVang += $stats->vang ?? 0;
                                $tongDiTre += $stats->di_tre ?? 0;
                                $tongNghiPhep += $stats->nghi_phep ?? 0;
                            }
                            $tongTyLe += $mh->ty_le_co_mat ?? 0;
                            $soMonCoDiemDanh++;
                        }
                    }
                    
                    $tyLeTrungBinh = $soMonCoDiemDanh > 0 ? round($tongTyLe / $soMonCoDiemDanh, 1) : 0;
                    $tyLeCoMatTong = $tongBuoiHocTatCa > 0 ? round(($tongCoMat / $tongBuoiHocTatCa) * 100, 1) : 0;
                ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-check"></i> Tổng kết điểm danh học kỳ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Tổng số buổi học</h6>
                                    <h3 class="mb-0 text-primary"><?php echo e($tongBuoiHocTatCa); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-success bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Có mặt</h6>
                                            <h4 class="mb-0 text-success"><?php echo e($tongCoMat); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Vắng</h6>
                                            <h4 class="mb-0 text-danger"><?php echo e($tongVang); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-warning bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Đi trễ</h6>
                                            <h4 class="mb-0 text-warning"><?php echo e($tongDiTre); ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-info bg-opacity-10 rounded mb-2">
                                            <h6 class="text-muted mb-1">Nghỉ phép</h6>
                                            <h4 class="mb-0 text-info"><?php echo e($tongNghiPhep); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Tỷ lệ có mặt trung bình:</span>
                                        <span class="badge bg-<?php echo e($tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger')); ?> fs-6">
                                            <?php echo e($tyLeCoMatTong); ?>%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-<?php echo e($tyLeCoMatTong >= 80 ? 'success' : ($tyLeCoMatTong >= 60 ? 'warning' : 'danger')); ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo e($tyLeCoMatTong); ?>%"
                                             aria-valuenow="<?php echo e($tyLeCoMatTong); ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?php echo e($tyLeCoMatTong); ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
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
                                    <th class="text-center">Điểm danh</th>
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
                                        <td class="text-center">
                                            <?php if(isset($item->tong_buoi_hoc) && $item->tong_buoi_hoc > 0): ?>
                                                <?php
                                                    $stats = $item->diem_danh_stats;
                                                    $coMat = $stats ? ($stats->co_mat ?? 0) : 0;
                                                    $vang = $stats ? ($stats->vang ?? 0) : 0;
                                                    $diTre = $stats ? ($stats->di_tre ?? 0) : 0;
                                                    $nghiPhep = $stats ? ($stats->nghi_phep ?? 0) : 0;
                                                    $tyLe = $item->ty_le_co_mat ?? 0;
                                                ?>
                                                <div class="small">
                                                    <div class="mb-1">
                                                        <span class="text-success">✓ <?php echo e($coMat); ?></span> /
                                                        <span class="text-danger">✗ <?php echo e($vang); ?></span> /
                                                        <span class="text-warning">⏱ <?php echo e($diTre); ?></span> /
                                                        <span class="text-info">☂ <?php echo e($nghiPhep); ?></span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-<?php echo e($tyLe >= 80 ? 'success' : ($tyLe >= 60 ? 'warning' : 'danger')); ?>">
                                                            <?php echo e($tyLe); ?>%
                                                        </span>
                                                        <small class="text-muted">/ <?php echo e($item->tong_buoi_hoc); ?> buổi</small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
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
                                        <td colspan="10" class="text-center text-muted py-4">
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

<?php echo $__env->make('layouts.layout-sinhvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\Downloads\DATN_2025_new\resources\views/sinhvien/diem/index.blade.php ENDPATH**/ ?>