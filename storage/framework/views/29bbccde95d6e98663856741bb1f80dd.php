<?php $__env->startSection('title', 'Bảng điểm tổng kết'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-heading">
                <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Bảng điểm tổng kết</h3>
                    <p class="text-subtitle text-muted"><?php echo e($lopHocPhan->ma_lop_hp); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?>

                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('giangvien.ket-qua-hoc-tap.index')); ?>">Kết quả học
                                    tập</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Bảng điểm</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Thông tin lớp học phần -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lớp học phần</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Mã lớp:</th>
                                    <td><strong><?php echo e($lopHocPhan->ma_lop_hp); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Môn học:</th>
                                    <td><?php echo e($lopHocPhan->monHoc->ma_mon); ?> - <?php echo e($lopHocPhan->monHoc->ten_mon); ?></td>
                                </tr>
                                <tr>
                                    <th>Số tín chỉ:</th>
                                    <td><?php echo e($lopHocPhan->monHoc->so_tin_chi); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Học kỳ:</th>
                                    <td><?php echo e($lopHocPhan->hocKy->ten_hoc_ky); ?> - <?php echo e($lopHocPhan->hocKy->nam_hoc); ?></td>
                                </tr>
                                <tr>
                                    <th>Sĩ số:</th>
                                    <td><span class="badge bg-info"><?php echo e($danhSachSinhVien->count()); ?> sinh viên</span></td>
                                </tr>
                                <tr>
                                    <th>Giảng viên:</th>
                                    <td>
                                        <?php $__currentLoopData = $lopHocPhan->giangViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-primary"><?php echo e($gv->ho_ten); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Thống kê tổng quan -->
        <section class="section">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Tổng sinh viên</h6>
                                    <h3 class="font-extrabold mb-0"><?php echo e($thongKe['tong_sv']); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Qua môn</h6>
                                    <h3 class="font-extrabold mb-0 text-success"><?php echo e($thongKe['sv_qua_mon']); ?></h3>
                                    <small class="text-muted"><?php echo e($thongKe['tong_sv'] > 0 ? round($thongKe['sv_qua_mon'] / $thongKe['tong_sv'] * 100, 1) : 0); ?>%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-x-circle-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Trượt môn</h6>
                                    <h3 class="font-extrabold mb-0 text-danger"><?php echo e($thongKe['sv_truot']); ?></h3>
                                    <small class="text-muted"><?php echo e($thongKe['tong_sv'] > 0 ? round($thongKe['sv_truot'] / $thongKe['tong_sv'] * 100, 1) : 0); ?>%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted font-semibold">Điểm TB</h6>
                                    <h3 class="font-extrabold mb-0 text-primary"><?php echo e(number_format($thongKe['diem_trung_binh'], 2)); ?></h3>
                                    <small class="text-muted">Hệ 10</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Phân bố điểm -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân bố điểm theo loại</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                            $phanBoDiemChu = $danhSachSinhVien->groupBy('diem_chu')->map(fn($g) => $g->count());
                        ?>
                        <?php $__currentLoopData = ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <h6 class="mb-1">Loại <?php echo e($loai); ?></h6>
                                    <h3 class="mb-0 
                                        <?php if($loai == 'A'): ?> text-success
                                        <?php elseif($loai == 'F'): ?> text-danger
                                        <?php else: ?> text-primary <?php endif; ?>">
                                        <?php echo e($phanBoDiemChu[$loai] ?? 0); ?>

                                    </h3>
                                    <small class="text-muted">sinh viên</small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bảng điểm chi tiết -->
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Bảng điểm chi tiết</h4>
                    
                    
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2">STT</th>
                                    <th rowspan="2">Mã SV</th>
                                    <th rowspan="2">Họ tên</th>
                                    <th rowspan="2">Lớp HC</th>
                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th colspan="<?php echo e($ch->so_cot); ?>" class="text-center">
                                            <?php echo e($ch->ten_dau_diem); ?><br>
                                            <small>(<?php echo e($ch->ty_le); ?>%)</small>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th rowspan="2">Hệ 10</th>
                                    <th rowspan="2">Hệ 4</th>
                                    <th rowspan="2">Chữ</th>
                                    <th rowspan="2">Kết quả</th>
                                </tr>
                                <tr>
                                    <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php for($i = 1; $i <= $ch->so_cot; $i++): ?>
                                            <th class="text-center">
                                                <?php if($ch->so_cot > 1): ?> Cột <?php echo e($i); ?> <?php else: ?> - <?php endif; ?>
                                            </th>
                                        <?php endfor; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $danhSachSinhVien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lhpsv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><strong><?php echo e($lhpsv->sinhVien->ma_sinh_vien); ?></strong></td>
                                        <td><?php echo e($lhpsv->sinhVien->ho_ten); ?></td>
                                        <td><?php echo e($lhpsv->sinhVien->lop_hanh_chinh ?? '-'); ?></td>
                                        
                                        <?php $__currentLoopData = $cauHinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php for($cot = 1; $cot <= $ch->so_cot; $cot++): ?>
                                                <?php
                                                    $diem = $lhpsv->danh_sach_diem->where('cau_hinh_id', $ch->id)->where('cot_diem', $cot)->first();
                                                ?>
                                                <td class="text-center">
                                                    <?php echo e($diem ? number_format($diem->diem_so, 2) : '-'); ?>

                                                </td>
                                            <?php endfor; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <td class="text-center">
                                            <?php if($lhpsv->diem_tong_ket): ?>
                                                <strong><?php echo e(number_format($lhpsv->diem_tong_ket, 2)); ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($lhpsv->ketQuaHocTap && $lhpsv->ketQuaHocTap->diem_he_4): ?>
                                                <?php echo e(number_format($lhpsv->ketQuaHocTap->diem_he_4, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($lhpsv->diem_chu): ?>
                                                <span class="badge bg-info"><?php echo e($lhpsv->diem_chu); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($lhpsv->diem_tong_ket): ?>
                                                <?php if($lhpsv->diem_tong_ket >= 4): ?>
                                                    <span class="badge bg-success">Qua môn</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Trượt</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Chưa có</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="20" class="text-center text-muted">Chưa có dữ liệu điểm</td>
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

<?php echo $__env->make('layouts.layout-giangvien', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\DATN_2025\resources\views/giangvien/ket-qua-hoc-tap/show.blade.php ENDPATH**/ ?>